<?php
require_once 'auth.php';
$pageTitle = 'Doctor Profile | MediCare Plus';
$userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$doctor = null;

if ($userId) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("
        SELECT 
            d.id, 
            u.first_name, 
            u.last_name, 
            d.specialization, 
            d.experience_years, 
            d.qualifications, 
            d.consultation_fee, 
            d.rating, 
            d.availability,
            d.profile_image
        FROM doctors d
        JOIN users u ON d.user_id = u.id
        WHERE d.user_id = ? AND u.role = 'doctor' AND u.status = 'active'
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $doctor = $stmt->get_result()->fetch_assoc();
}

include 'header.php';
?>
<style>
    .doc-profile-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 20px auto;
        display: block;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .profile-header {
        text-align: center;
        margin-bottom: 30px;
    }
</style>
<section class="page-panel">
    <?php if (!$doctor): ?>
        <div class="page-title">Doctor profile not found</div>
        <div class="content-panel">
            <p>The requested doctor profile is unavailable. <a href="doctors.php">Return to the doctor directory</a>.</p>
        </div>
    <?php else: ?>
        <div class="content-panel" style="max-width: 800px; margin: 0 auto;">
            <div class="profile-header">
                <?php 
                    $imagePath = 'assets/images/' . ($doctor['profile_image'] ? htmlspecialchars($doctor['profile_image'], ENT_QUOTES, 'UTF-8') : 'default-doc.jpg'); 
                ?>
                <img src="<?php echo $imagePath; ?>" alt="Doctor Image" class="doc-profile-img" onerror="this.src='assets/images/default-doc.jpg'">
                <div class="page-title" style="margin-bottom: 10px;"><?php echo htmlspecialchars('Dr. ' . $doctor['first_name'] . ' ' . $doctor['last_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                <p class="doctor-intro" style="color: #4a5568; font-size: 1.1rem; margin-top: 0;">Specialist in <?php echo htmlspecialchars($doctor['specialization'], ENT_QUOTES, 'UTF-8'); ?> with <?php echo (int) $doctor['experience_years']; ?> years of clinical experience.</p>
            </div>
            
            <div class="profile-grid">
                <div class="profile-card card-shadow" style="background: #fff; padding: 25px; border-radius: 8px; margin-bottom: 20px;">
                    <h3 style="color: #2b6cb0; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 15px;">About</h3>
                    <p style="line-height: 1.6; color: #4a5568;"><?php echo nl2br(htmlspecialchars($doctor['qualifications'], ENT_QUOTES, 'UTF-8')); ?></p>
                </div>
                <div class="profile-card card-shadow" style="background: #fff; padding: 25px; border-radius: 8px;">
                    <h3 style="color: #2b6cb0; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 15px;">Details</h3>
                    <ul class="profile-list" style="list-style: none; padding: 0; line-height: 2;">
                        <li><strong>Specialization:</strong> <?php echo htmlspecialchars($doctor['specialization'], ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><strong>Experience:</strong> <?php echo (int) $doctor['experience_years']; ?> years</li>
                        <li><strong>Consultation fee:</strong> LKR <?php echo number_format((float) $doctor['consultation_fee'], 0); ?></li>
                        <li><strong>Rating:</strong> <span style="color: #ecc94b; font-weight: bold;"><i class="fas fa-star"></i> <?php echo number_format((float) $doctor['rating'], 1); ?></span>/5</li>
                        <li><strong>Availability:</strong> <?php echo htmlspecialchars($doctor['availability'] ?: 'Please contact support for weekly slots.', ENT_QUOTES, 'UTF-8'); ?></li>
                    </ul>
                </div>
            </div>
            <div class="page-actions" style="margin-top: 30px; text-align: center;">
                <a class="button primary-button" style="background: #3182ce; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 50px; font-weight: bold; margin-right: 15px;" href="book_appointment.php?doctor_id=<?php echo (int) $doctor['id']; ?>">Book this doctor</a>
                <a class="button outline-button" style="border: 2px solid #3182ce; color: #3182ce; padding: 10px 30px; text-decoration: none; border-radius: 50px; font-weight: bold;" href="doctors.php">Back to directory</a>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php include 'footer.php'; ?>