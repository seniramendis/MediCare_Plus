<?php
// DO NOT CHANGE THESE VARIABLES UNLESS YOUR DB CONFIG CHANGES
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "medicare_databs"; // Based on your phpMyAdmin screenshot
$port = 3307; // This must be 3307 based on your error log

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
