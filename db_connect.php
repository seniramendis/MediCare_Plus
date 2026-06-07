<?php
// START SESSION IF NOT STARTED
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. HARDCODED CONNECTION (Fixes Localhost Port 3307)
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "medicare_databs";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

function get_db_connection()
{
    global $conn;
    return $conn;
}

// ==========================================
// 2. AUTHENTICATION & USERS CRUD
// ==========================================
function fetch_user_by_email($email)
{
    global $conn;
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function fetch_user_by_id($id)
{
    global $conn;
    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function user_exists_by_email($email)
{
    global $conn;
    $stmt = $conn->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function create_user($first_name, $last_name, $email, $password, $role = 'patient')
{
    global $conn;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (first_name, last_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, "active")');
    $stmt->bind_param('sssss', $first_name, $last_name, $email, $hash, $role);
    $stmt->execute();
    return $conn->insert_id;
}

function fetch_all_users()
{
    global $conn;
    return $conn->query('SELECT * FROM users WHERE status = "active" ORDER BY first_name, last_name')->fetch_all(MYSQLI_ASSOC);
}

// ==========================================
// 3. DOCTORS & PATIENTS CRUD
// ==========================================
function create_patient_profile($user_id)
{
    global $conn;
    $stmt = $conn->prepare('INSERT INTO patients (user_id) VALUES (?)');
    $stmt->bind_param('i', $user_id);
    return $stmt->execute();
}

function fetch_all_doctors()
{
    global $conn;
    return $conn->query('SELECT d.*, u.first_name, u.last_name, u.email FROM doctors d JOIN users u ON u.id = d.user_id WHERE u.status = "active"')->fetch_all(MYSQLI_ASSOC);
}

function fetch_doctor_by_id($doctor_id)
{
    global $conn;
    $stmt = $conn->prepare('SELECT d.*, u.first_name, u.last_name FROM doctors d JOIN users u ON u.id = d.user_id WHERE d.id = ? LIMIT 1');
    $stmt->bind_param('i', $doctor_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function fetch_doctor_by_user_id($user_id)
{
    global $conn;
    $stmt = $conn->prepare('SELECT d.*, u.first_name, u.last_name FROM doctors d JOIN users u ON u.id = d.user_id WHERE d.user_id = ? LIMIT 1');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function fetch_all_patients()
{
    global $conn;
    return $conn->query('SELECT p.*, u.first_name, u.last_name, u.email FROM patients p JOIN users u ON u.id = p.user_id')->fetch_all(MYSQLI_ASSOC);
}

function fetch_patient_by_user_id($user_id)
{
    global $conn;
    $stmt = $conn->prepare('SELECT * FROM patients WHERE user_id = ? LIMIT 1');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

// ==========================================
// 4. SERVICES & BLOGS CRUD
// ==========================================
function fetch_services()
{
    global $conn;
    $result = $conn->query('SELECT id, name, category, description, price FROM services ORDER BY category, name');
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function fetch_blog_posts()
{
    global $conn;
    $result = $conn->query('SELECT b.*, u.first_name, u.last_name FROM blog_posts b LEFT JOIN users u ON b.author_id = u.id ORDER BY b.created_at DESC');
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function fetch_blog_post_by_id($post_id)
{
    global $conn;
    $stmt = $conn->prepare('SELECT * FROM blog_posts WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

// ==========================================
// 5. MESSAGES, APPOINTMENTS & REPORTS
// ==========================================
function get_unread_count($user_id)
{
    global $conn;
    $stmt = $conn->prepare('SELECT COUNT(*) AS total FROM messages WHERE recipient_id = ? AND is_read = 0');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res ? $res['total'] : 0;
}

function fetch_inbox($user_id)
{
    global $conn;
    $stmt = $conn->prepare('SELECT m.*, u.first_name, u.last_name FROM messages m JOIN users u ON u.id = m.sender_id WHERE m.recipient_id = ? ORDER BY m.sent_at DESC');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function send_message($sender_id, $recipient_id, $subject, $body)
{
    global $conn;
    $stmt = $conn->prepare('INSERT INTO messages (sender_id, recipient_id, subject, body, is_read, sent_at) VALUES (?, ?, ?, ?, 0, NOW())');
    $stmt->bind_param('iiss', $sender_id, $recipient_id, $subject, $body);
    return $stmt->execute();
}

function create_appointment($patient_id, $doctor_id, $date_time, $notes = null)
{
    global $conn;
    $stmt = $conn->prepare('INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, notes) VALUES (?, ?, ?, "pending", ?)');
    $stmt->bind_param('iiss', $patient_id, $doctor_id, $date_time, $notes);
    return $stmt->execute();
}

function fetch_medical_reports_for_user($user_id)
{
    global $conn;
    $stmt = $conn->prepare('SELECT mr.* FROM medical_reports mr JOIN patients p ON p.id = mr.patient_id WHERE p.user_id = ? ORDER BY mr.created_at DESC');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function create_feedback($appointment_id, $doctor_id, $patient_id, $rating, $comment)
{
    global $conn;
    $stmt = $conn->prepare('INSERT INTO feedback (appointment_id, doctor_id, patient_id, rating, comment) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('iiiss', $appointment_id, $doctor_id, $patient_id, $rating, $comment);
    return $stmt->execute();
}
