<?php
// functions.php — all shared helper functions
if (!defined('DB_CONNECT_LOADED')) {
    require_once 'db_connect.php';
}

function fetch_all_users()
{
    $conn = get_db_connection();
    return $conn->query("SELECT id, first_name, last_name, email, role FROM users ORDER BY first_name")->fetch_all(MYSQLI_ASSOC);
}

function send_message($sender_id, $recipient_id, $subject, $body)
{
    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, recipient_id, subject, body, sent_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
    $stmt->bind_param("iiss", $sender_id, $recipient_id, $subject, $body);
    return $stmt->execute();
}

function get_unread_count($user_id)
{
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM messages WHERE recipient_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? (int)$row['total'] : 0;
}

// Returns inbox messages with sender name joined in
function fetch_inbox($user_id)
{
    $conn = get_db_connection();
    $stmt = $conn->prepare(
        "SELECT m.*, u.first_name, u.last_name
         FROM messages m
         JOIN users u ON u.id = m.sender_id
         WHERE m.recipient_id = ?
         ORDER BY m.sent_at DESC"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetch_user_by_email($email)
{
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function create_user($first_name, $last_name, $email, $password, $role)
{
    $conn = get_db_connection();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $hash, $role);
    return $stmt->execute();
}

function create_patient_profile($user_id)
{
    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO patients (user_id) VALUES (?)");
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}
