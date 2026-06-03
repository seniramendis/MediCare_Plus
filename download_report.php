<?php
require_once 'auth.php';
require_login();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo 'Invalid request.';
    exit;
}

$reportId = (int) $_GET['id'];
$report = fetch_medical_report_by_id($reportId);
if (!$report) {
    http_response_code(404);
    echo 'Report not found.';
    exit;
}

$user = current_user();
$role = current_user_role();

// Authorization: patients may only download their own reports. Admins and doctors may download any.
if ($role === 'patient') {
    if ($report['patient_user_id'] != $user['id']) {
        http_response_code(403);
        echo 'Forbidden.';
        exit;
    }
}

$baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR;
$requested = $baseDir . $report['file_path'];

// Resolve realpath and ensure it's within baseDir to prevent traversal.
$realBase = realpath($baseDir);
$realRequested = realpath($requested);
if ($realRequested === false || strpos($realRequested, $realBase) !== 0) {
    http_response_code(404);
    echo 'File not available.';
    exit;
}

if (!is_file($realRequested) || !is_readable($realRequested)) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}

$downloadName = basename($report['file_name']);

// Send file headers
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($realRequested));
readfile($realRequested);
exit;
