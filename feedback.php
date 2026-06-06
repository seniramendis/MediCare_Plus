<?php
require_once 'auth.php';
require_role('patient');

$pageTitle = 'Leave Feedback';
include 'header.php';

$user = current_user();
$userId = $user['id'];
$patientData = fetch_patient_by_user_id($userId);
$patientId = $patientData ? $patientData['id'] : null;

// Get patient's past appointments with doctors (completed only)
$appointmentsQuery = "SELECT a.id, a.doctor_id, a.appointment_date, d.user_id as doctor_user_id, u.first_name, u.last_name 
                      FROM appointments a 
                      JOIN doctors d ON d.id = a.doctor_id 
                      JOIN users u ON u.id = d.user_id 
                      WHERE a.patient_id = ? AND a.status = 'completed' 
                      ORDER BY a.appointment_date DESC";

$conn = get_db_connection();
$appointments = [];
if ($conn && $patientId) {
    $stmt = $conn->prepare($appointmentsQuery);
    $stmt->bind_param('i', $patientId);
    $stmt->execute();
    $appointmentsResult = $stmt->get_result();
    $appointments = $appointmentsResult->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW) ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }

    $appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($appointmentId && $rating >= 1 && $rating <= 5) {
        $appointment = null;
        foreach ($appointments as $a) {
            if ($a['id'] == $appointmentId) {
                $appointment = $a;
                break;
            }
        }

        if ($appointment) {
            $doctorId = $appointment['doctor_id'];
            $success = create_feedback($appointmentId, $doctorId, $patientId, $rating, $comment);
            if ($success) {
                $success = true;
            } else {
                $error = 'Failed to save feedback. Please try again.';
            }
        } else {
            $error = 'Invalid appointment selected.';
        }
    } else {
        $error = 'Please select an appointment and rate 1-5 stars.';
    }
}
?>

<section class="container">
    <h1>Leave Feedback & Rating</h1>
    <p>Share your experience with your doctor to help us improve our services.</p>

    <?php if ($success): ?>
        <div class="alert alert-success">Thank you! Your feedback has been submitted.</div>
    <?php elseif ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <?php if (empty($appointments)): ?>
        <div class="empty-state">You have no completed appointments yet. Feedback can be left after your appointment.</div>
    <?php else: ?>
        <form method="post" class="form">
            <div class="form-group">
                <label for="appointment_id">Select Appointment:</label>
                <select id="appointment_id" name="appointment_id" required>
                    <option value="">-- Choose appointment --</option>
                    <?php foreach ($appointments as $a): ?>
                        <option value="<?php echo e($a['id']); ?>">
                            Dr. <?php echo e($a['first_name'] . ' ' . $a['last_name']); ?>
                            (<?php echo date('M d, Y', strtotime($a['appointment_date'])); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="rating">Rating:</label>
                <div class="rating-input">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="star-label">
                            <input type="radio" name="rating" value="<?php echo $i; ?>" required>
                            <span class="star">★</span>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="comment">Comment (optional):</label>
                <textarea id="comment" name="comment" rows="4" maxlength="500" placeholder="Share your experience..."></textarea>
            </div>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="button">Submit Feedback</button>
        </form>
    <?php endif; ?>
</section>

<style>
    .form-inline {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .form-inline select {
        flex: 1;
    }

    .rating-input {
        display: flex;
        gap: 10px;
        font-size: 28px;
    }

    .star-label {
        cursor: pointer;
    }

    .star-label input {
        display: none;
    }

    .star-label input:checked~.star {
        color: #ffc107;
    }

    .star {
        color: #ccc;
        transition: color 0.2s;
    }

    .alert {
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<?php include 'footer.php'; ?>