<?php
// 1. Initialize Session and Database Connection globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// --- SECURITY & UTILITY FUNCTIONS ---
function require_login()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: Login.php");
        exit();
    }
}

function require_role($roles)
{
    require_login();
    if (!in_array($_SESSION['role'], (array)$roles)) {
        echo "<div style='padding: 50px; text-align: center; color: red;'><h1>Access Denied.</h1><p>You do not have permission to view this page.</p></div>";
        exit();
    }
}

function current_user()
{
    global $conn;
    if (!isset($_SESSION['user_id'])) return null;
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function current_user_role()
{
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

function e($string)
{
    // Sanitizes output to prevent XSS attacks
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function get_db_connection()
{
    global $conn;
    return $conn;
}

// --- PATIENT & DOCTOR FUNCTIONS ---
function fetch_patient_by_user_id($user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM patients WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function fetch_all_patients()
{
    global $conn;
    $query = "SELECT p.id AS patient_id, u.first_name, u.last_name, u.email 
              FROM patients p JOIN users u ON p.user_id = u.id";
    $result = $conn->query($query);
    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    return $patients;
}

// --- MEDICAL REPORT FUNCTIONS (Translates to your exact DB Schema) ---
function fetch_medical_reports_for_user($user_id)
{
    global $conn;
    $patient = fetch_patient_by_user_id($user_id);
    if (!$patient) return [];

    $query = "SELECT m.id, m.report_title AS file_name, m.report_description AS notes, 
                     m.file_path, m.created_at, u.first_name AS doc_first, u.last_name AS doc_last 
              FROM medical_reports m 
              JOIN doctors d ON m.doctor_id = d.id 
              JOIN users u ON d.user_id = u.id 
              WHERE m.patient_id = ? ORDER BY m.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $patient['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $reports = [];
    while ($row = $res->fetch_assoc()) {
        // Maps the AI's expected column to your real columns
        $row['uploaded_by'] = 'Dr. ' . $row['doc_first'] . ' ' . $row['doc_last'];
        $reports[] = $row;
    }
    return $reports;
}

function fetch_medical_report_by_id($report_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT m.*, p.user_id AS patient_user_id, m.report_title AS file_name FROM medical_reports m JOIN patients p ON m.patient_id = p.id WHERE m.id = ?");
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function create_medical_report($patientId, $fileName, $safeFileName, $notes, $userName)
{
    global $conn;
    // Get doctor ID for the logged in user
    $doc_stmt = $conn->prepare("SELECT id FROM doctors WHERE user_id = ?");
    $doc_stmt->bind_param("i", $_SESSION['user_id']);
    $doc_stmt->execute();
    $doc_res = $doc_stmt->get_result()->fetch_assoc();
    $doc_id = $doc_res ? $doc_res['id'] : 1;

    $stmt = $conn->prepare("INSERT INTO medical_reports (patient_id, doctor_id, report_title, report_description, file_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patientId, $doc_id, $fileName, $notes, $safeFileName);
    return $stmt->execute();
}

// --- BLOG FUNCTIONS (Translates to your exact DB Schema) ---
function fetch_blog_posts()
{
    global $conn;
    $result = $conn->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC");
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        // Quietly maps AI variables to your database columns
        $row['created_at'] = $row['published_at'];
        $row['first_name'] = $row['author'];
        $row['last_name'] = '';
        $row['content'] = $row['body'];
        $posts[] = $row;
    }
    return $posts;
}

function fetch_blog_post_by_id($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();
    if ($post) {
        $post['content'] = $post['body'];
        $post['created_at'] = $post['published_at'];
        $post['author_id'] = 1; // Safeguard map
    }
    return $post;
}

function create_blog_post($title, $content, $userId)
{
    global $conn;
    $user = current_user();
    $author = $user['first_name'] . ' ' . $user['last_name'];
    $excerpt = substr($content, 0, 150) . '...';

    $stmt = $conn->prepare("INSERT INTO blog_posts (title, excerpt, body, author, status) VALUES (?, ?, ?, ?, 'published')");
    $stmt->bind_param("ssss", $title, $excerpt, $content, $author);
    return $stmt->execute();
}
