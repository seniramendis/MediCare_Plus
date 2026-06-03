<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'error' => 'unauthenticated', 'count' => 0]);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$count = get_unread_count($userId);

echo json_encode(['ok' => true, 'count' => $count]);
