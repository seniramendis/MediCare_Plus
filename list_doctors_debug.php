<?php
require_once 'db_connect.php';

$conn = get_db_connection();
if (!$conn) {
    die('Database connection failed');
}

$result = $conn->query('SELECT COUNT(*) AS cnt FROM doctors');
$count = 0;
if ($result) {
    $row = $result->fetch_assoc();
    $count = (int)$row['cnt'];
    $result->free();
}

echo "<h1>Doctors Debug</h1>\n";
echo "<p>Total doctor profiles: <strong>$count</strong></p>\n";

if ($count > 0) {
    $res = $conn->query('SELECT d.id, u.first_name, u.last_name, d.specialization, d.consultation_fee, d.experience_years, d.rating FROM doctors d JOIN users u ON u.id = d.user_id ORDER BY d.rating DESC LIMIT 20');
    if ($res) {
        echo "<table border='1' cellpadding='6' style='border-collapse:collapse'>\n";
        echo "<tr><th>ID</th><th>Name</th><th>Specialization</th><th>Fee (LKR)</th><th>Years</th><th>Rating</th></tr>\n";
        while ($r = $res->fetch_assoc()) {
            $name = htmlspecialchars($r['first_name'] . ' ' . $r['last_name']);
            echo "<tr><td>{$r['id']}</td><td>$name</td><td>" . htmlspecialchars($r['specialization']) . "</td><td>" . number_format($r['consultation_fee'], 0) . "</td><td>" . (int)$r['experience_years'] . "</td><td>" . number_format($r['rating'], 1) . "</td></tr>\n";
        }
        echo "</table>\n";
        $res->free();
    }
} else {
    echo "<p>No doctor profiles found.</p>\n";
}

echo "<p><a href='add_doctors_safe.php'>Run safe inserter (adds missing doctors)</a></p>\n";
echo "<p><a href='doctors.php'>Open Doctors page</a></p>\n";

$conn->close();
