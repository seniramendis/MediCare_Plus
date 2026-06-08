<?php
require_once 'auth.php';
require_role('doctor');

// CSRF check
$submittedToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW) ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
    http_response_code(403);
    die('Invalid CSRF token.');
}

$appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
if (!$appointmentId) {
    header('Location: dashboard_doctor.php?error=invalid');
    exit;
}

// Fetch doctor record for this logged-in user
$conn = get_db_connection();
$dStmt = $conn->prepare('SELECT id FROM doctors WHERE user_id = ? LIMIT 1');
$dStmt->bind_param('i', $_SESSION['user_id']);
$dStmt->execute();
$doc = $dStmt->get_result()->fetch_assoc();

if (!$doc) {
    header('Location: dashboard_doctor.php?error=nodoc');
    exit;
}

// Only allow accepting a pending appointment that belongs to this doctor
$updated = update_appointment_status($appointmentId, 'confirmed', (int)$doc['id']);

if ($updated) {
    header('Location: dashboard_doctor.php?accepted=1');
} else {
    header('Location: dashboard_doctor.php?error=update');
}
exit;
