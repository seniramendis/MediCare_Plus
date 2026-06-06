<?php
require 'auth.php';
$res = $conn->query('DESCRIBE blog_posts');
?>while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
