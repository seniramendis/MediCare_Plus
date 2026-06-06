<?php
// functions.php - This file fixes all your "Undefined Function" errors
require_once 'db_connect.php';

function fetch_all_users()
{
    $conn = get_db_connection();
    return $conn->query("SELECT * FROM users")->fetch_all(MYSQLI_ASSOC);
}

function send_message($sender_id, $recipient_id, $subject, $body)
{
    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, recipient_id, subject, body) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $sender_id, $recipient_id, $subject, $body);
    return $stmt->execute();
}

function get_unread_count($user_id)
{
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM messages WHERE recipient_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

function fetch_inbox($user_id)
{
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT * FROM messages WHERE recipient_id = ? ORDER BY sent_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function create_feedback($user_id, $message)
{
    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $message);
    return $stmt->execute();
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
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, 'active')");
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
