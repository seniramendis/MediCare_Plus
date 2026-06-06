<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";

// Ensure this matches the exact database you are using!
// Based on your phpMyAdmin screenshot, it looks like you are using 'medicare_databs'
$dbname = "medicare_databs";

// CRITICAL FIX: Your XAMPP MySQL is running on port 3307, not the default 3306
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
// Do not add a closing PHP tag (
?>) here to prevent invisible space errors.