<?php
// db_connect.php
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

// RESTORED CORE FUNCTIONS
function fetch_all_users()
{
    global $conn;
    return $conn->query("SELECT * FROM users")->fetch_all(MYSQLI_ASSOC);
}
function get_unread_count($user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM messages WHERE recipient_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}
function fetch_inbox($user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM messages WHERE recipient_id = ? ORDER BY sent_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function send_message($sender_id, $recipient_id, $subject, $body)
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, recipient_id, subject, body) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $sender_id, $recipient_id, $subject, $body);
    return $stmt->execute();
}

function fetch_services()
{
    global $conn;
    $result = $conn->query("SELECT id, name, category, description, price FROM services ORDER BY category, name");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
