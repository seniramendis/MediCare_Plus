<?php
require 'db_connect.php';
$conn = get_db_connection();
$res = $conn->query('DESCRIBE blog_posts');
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
$conn->close();
?>