<?php
require_once 'db_connect.php';
$res = $conn->query('DESCRIBE doctors');
while ($row = $res->fetch_assoc()) { print_r($row); }
$res = $conn->query('DESCRIBE users');
while ($row = $res->fetch_assoc()) { print_r($row); }
