<?php
require_once 'auth.php';
require_login();

/**
 * Send a plain-text error response and exit cleanly.
 */
function send_error(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $message;
    exit(0);
}

/**
 * Validate that a file path is a safe plain filename only —
 * no directory separators, no URL schemes, no traversal sequences.
 * Only alphanumeric characters, underscores, hyphens, and one dot are allowed.
 */
function is_safe_filename(string $value): bool
{
    return (bool) preg_match('/^[a-zA-Z0-9_\-]+\.[a-zA-Z0-9]{1,10}$/', $value);
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    send_error(400, 'Invalid request.');
}

$reportId = (int) $_GET['id'];
$report   = fetch_medical_report_by_id($reportId);
if (!$report) {
    send_error(404, 'Report not found.');
}

$user = current_user();
$role = current_user_role();

// Patients may only download their own reports; admins and doctors may download any.
if ($role === 'patient' && (int)$report['patient_user_id'] !== (int)$user['id']) {
    send_error(403, 'Forbidden.');
}

// Validate file_path against strict allowlist before building the filesystem path.
$filePath = $report['file_path'] ?? '';
if (!is_safe_filename($filePath)) {
    send_error(400, 'Invalid file reference.');
}

$baseDir   = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'reports');
if ($baseDir === false) {
    send_error(500, 'Upload directory not found.');
}

$realRequested = realpath($baseDir . DIRECTORY_SEPARATOR . $filePath);

// Ensure the resolved path is strictly within the uploads/reports directory.
if ($realRequested === false || strpos($realRequested, $baseDir . DIRECTORY_SEPARATOR) !== 0) {
    send_error(404, 'File not available.');
}

if (!is_file($realRequested) || !is_readable($realRequested)) {
    send_error(404, 'File not found.');
}

// Sanitise the download filename for Content-Disposition —
// strip any characters that could inject headers or break the quoted string.
$downloadName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($report['file_name'] ?? 'report'));

// Validate the extension against the allowed list to prevent serving unexpected file types.
$allowedExt = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
$ext        = strtolower(pathinfo($downloadName, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt, true)) {
    send_error(403, 'File type not permitted for download.');
}

// Map extensions to proper MIME types for correct browser handling.
$mimeMap = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'txt'  => 'text/plain; charset=UTF-8',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
];
$contentType = $mimeMap[$ext] ?? 'application/octet-stream';

// Stream the file to the browser.
header('Content-Description: File Transfer');
header('Content-Type: ' . $contentType);
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($realRequested));
readfile($realRequested);
exit(0);
