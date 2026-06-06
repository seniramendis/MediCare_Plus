<?php
require_once 'auth.php';
require_role('patient');
$pageTitle = 'Book Appointment | MediCare Plus';
$errors = [];
$success = '';
$selectedDoctorId = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT);
$doctor = $selectedDoctorId ? fetch_doctor_by_id($selectedDoctorId) : null;
$patient = fetch_patient_by_user_id($_SESSION['user_id']);
$availableDoctors = fetch_all_doctors();
$appointmentDate = '';
$notes = '';

if (!$patient) {
    $errors[] = 'Your patient record is not available. Please contact support.';
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $submittedToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW) ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }

    $selectedDoctorId = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
    $appointmentDate = trim(filter_input(INPUT_POST, 'appointment_date', FILTER_UNSAFE_RAW) ?: '');
    $notes = trim(filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');

    if (!$selectedDoctorId) {
        $errors[] = 'Please select a doctor for the appointment.';
    }

    if (!$appointmentDate) {
        $errors[] = 'Please choose a date and time for your appointment.';
    }

    $doctor = $selectedDoctorId ? fetch_doctor_by_id($selectedDoctorId) : null;
    if (!$doctor) {
        $errors[] = 'The selected doctor could not be found. Please choose another provider.';
    }

    $appointmentDateTime = null;
    if (empty($errors)) {
        $appointmentDateTime = DateTime::createFromFormat('Y-m-d\TH:i', $appointmentDate);
        if (!$appointmentDateTime) {
            $errors[] = 'Invalid appointment date format. Please use the date picker.';
        } elseif ($appointmentDateTime < new DateTime()) {
            $errors[] = 'Appointment date must be in the future.';
        }
    }

    if (empty($errors) && doctor_has_conflict($selectedDoctorId, $appointmentDateTime->format('Y-m-d H:i:s'))) {
        $errors[] = 'This doctor already has an appointment at the selected time. Please choose another slot.';
    }

    if (empty($errors)) {
        $created = create_appointment($patient['id'], $selectedDoctorId, $appointmentDateTime->format('Y-m-d H:i:s'), $notes);
        if ($created) {
            $success = 'Your appointment request has been submitted. The doctor will confirm the time shortly.';
            $appointmentDate = '';
            $notes = '';
        } else {
            $errors[] = 'Unable to schedule the appointment. Please try again later.';
        }
    }
}

include 'header.php';
?>
<section class="page-panel">
    <div class="page-title">Book Appointment</div>
    <div class="content-panel">
        <p>Select a doctor and reserve an available consultation slot quickly and securely.</p>
    </div>

    <?php if ($success): ?>
        <div class="success-box">
            <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="content-panel">
        <form action="book_appointment.php" method="post" class="booking-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-group">
                <label for="doctor_id">Choose a doctor</label>
                <select id="doctor_id" name="doctor_id" required>
                    <option value="">Select a provider</option>
                    <?php foreach ($availableDoctors as $entry): ?>
                        <option value="<?php echo (int) $entry['id']; ?>" <?php echo ($selectedDoctorId === (int) $entry['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars('Dr. ' . $entry['first_name'] . ' ' . $entry['last_name'] . ' — ' . $entry['specialization'] . ' (LKR ' . number_format((float) $entry['consultation_fee'], 0) . ')', ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="appointment_date">Preferred date and time</label>
                <input type="datetime-local" id="appointment_date" name="appointment_date" value="<?php echo htmlspecialchars($appointmentDate, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="notes">Notes for the doctor (optional)</label>
                <textarea id="notes" name="notes" rows="4"><?php echo htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <button type="submit" class="button primary-button">Submit request</button>
        </form>
    </div>

    <?php if ($doctor): ?>
        <div class="content-panel">
            <h3>Booking details</h3>
            <div class="profile-card card-shadow">
                <h4><?php echo htmlspecialchars('Dr. ' . $doctor['first_name'] . ' ' . $doctor['last_name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <p><?php echo htmlspecialchars($doctor['specialization'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Fee:</strong> LKR <?php echo number_format((float) $doctor['consultation_fee'], 0); ?></p>
                <p><strong>Availability:</strong> <?php echo htmlspecialchars($doctor['availability'] ?: 'Contact support for availability details.', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php include 'footer.php'; ?>