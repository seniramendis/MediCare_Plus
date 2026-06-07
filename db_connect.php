<?php
// START SESSION IF NOT STARTED
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DATABASE CONNECTION (Localhost / XAMPP)
$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$dbname     = "medicare_databs";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// Single canonical helper — everything else lives in auth.php
if (!function_exists('get_db_connection')) {
    function get_db_connection()
    {
        global $conn;
        return $conn;
    }
}
