<?php
require_once 'auth.php';
$pageTitle = 'Doctor Profile | MediCare Plus';
$doctorId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$doctor = null;

if ($doctorId) {
    $doctor = fetch_doctor_by_id($doctorId);
}

include 'header.php';
?>
<section class="page-panel">
    <?php if (!$doctor): ?>
        <div class="page-title">Doctor profile not found</div>
        <div class="content-panel">
            <p>The requested doctor profile is unavailable. <a href="doctors.php">Return to the doctor directory</a>.</p>
        </div>
    <?php else: ?>
        <div class="page-title"><?php echo htmlspecialchars('Dr. ' . $doctor['first_name'] . ' ' . $doctor['last_name'], ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="content-panel">
            <p class="doctor-intro">Specialist in <?php echo htmlspecialchars($doctor['specialization'], ENT_QUOTES, 'UTF-8'); ?> with <?php echo (int) $doctor['experience_years']; ?> years of clinical experience.</p>
            <div class="profile-grid">
                <div class="profile-card card-shadow">
                    <h3>About</h3>
                    <p><?php echo nl2br(htmlspecialchars($doctor['qualifications'], ENT_QUOTES, 'UTF-8')); ?></p>
                </div>
                <div class="profile-card card-shadow">
                    <h3>Details</h3>
                    <ul class="profile-list">
                        <li><strong>Specialization:</strong> <?php echo htmlspecialchars($doctor['specialization'], ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><strong>Experience:</strong> <?php echo (int) $doctor['experience_years']; ?> years</li>
                        <li><strong>Consultation fee:</strong> LKR <?php echo number_format((float) $doctor['consultation_fee'], 0); ?></li>
                        <li><strong>Rating:</strong> <?php echo number_format((float) $doctor['rating'], 1); ?>/5</li>
                        <li><strong>Availability:</strong> <?php echo htmlspecialchars($doctor['availability'] ?: 'Please contact support for weekly slots.', ENT_QUOTES, 'UTF-8'); ?></li>
                    </ul>
                </div>
            </div>
            <div class="page-actions">
                <a class="button primary-button" href="book_appointment.php?doctor_id=<?php echo (int) $doctor['id']; ?>">Book this doctor</a>
                <a class="button outline-button" href="doctors.php">Back to directory</a>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php include 'footer.php'; ?>