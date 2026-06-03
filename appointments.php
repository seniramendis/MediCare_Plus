<?php
require_once 'auth.php';
$pageTitle = 'Appointments | MediCare Plus';
include 'header.php';
$user = current_user();
$role = current_user_role();
?>
<section class="page-panel">
    <div class="page-title">Appointments</div>
    <div class="content-panel">
        <?php if (!is_logged_in()): ?>
            <p>To book an appointment or view your schedule, please <a href="Login.php">sign in</a> or <a href="register.php">create a patient account</a>.</p>
        <?php elseif ($role === 'patient'): ?>
            <p>Find available doctors, set your preferred appointment date, and book your consultation online.</p>
            <div class="page-actions">
                <a class="button primary-button" href="book_appointment.php">Book Appointment</a>
                <a class="button outline-button" href="dashboard_patient.php">My Dashboard</a>
            </div>
        <?php elseif ($role === 'doctor'): ?>
            <p>Review your upcoming patient appointments and manage your schedule from below.</p>
            <div class="page-actions">
                <a class="button primary-button" href="dashboard_doctor.php">View Schedule</a>
                <a class="button outline-button" href="chat_engine.php">Messages</a>
            </div>
        <?php else: ?>
            <p>Your account can access appointment management once you sign in from the correct role. Contact support if you need help.</p>
        <?php endif; ?>
    </div>
</section>
<?php include 'footer.php'; ?>