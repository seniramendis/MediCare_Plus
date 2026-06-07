<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db_connect.php';

$pageTitle = 'Doctor Profile | MediCare Plus';
$doctor = null;

$docId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($docId && isset($conn)) {
    $stmt = $conn->prepare("
        SELECT 
            d.id, 
            d.user_id,
            d.specialization, 
            d.consultation_fee, 
            d.availability,
            d.rating, 
            d.profile_image,
            d.bio,
            u.first_name,
            u.last_name,
            u.email
        FROM doctors d
        JOIN users u ON d.user_id = u.id
        WHERE d.id = ? AND u.status = 'active'
    ");
    if ($stmt) {
        $stmt->bind_param("i", $docId);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result ? $result->fetch_assoc() : null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Medicare Plus</title>
    <link rel="stylesheet" href="assets/css/HomeStyles.css?v=3.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy: #0d2b5e;
            --teal: #0aa698;
            --light-bg: #f5f8fa;
            --border: #e2e8f0;
            --text: #2d3748;
            --muted: #718096;
            --white: #ffffff;
        }
        body { font-family: 'DM Sans', Arial, sans-serif; background: var(--light-bg); color: var(--text); }
        .profile-hero {
            background: linear-gradient(135deg, var(--navy) 0%, #1a4a8a 55%, #0f6e62 100%);
            padding: 60px 8% 40px;
        }
        .profile-hero-inner {
            max-width: 900px; margin: 0 auto;
            display: flex; gap: 40px; align-items: center; flex-wrap: wrap;
        }
        .profile-avatar {
            width: 160px; height: 160px; border-radius: 50%;
            object-fit: cover; object-position: top center;
            border: 4px solid rgba(255,255,255,0.3);
            flex-shrink: 0; background: rgba(255,255,255,0.1);
        }
        .profile-hero-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            color: #fff; margin: 0 0 8px;
        }
        .profile-hero-text .spec-badge {
            display: inline-block; background: rgba(10,166,152,0.2);
            border: 1px solid rgba(10,166,152,0.4);
            color: #6ee7e0; font-size: 0.85rem; font-weight: 600;
            padding: 5px 16px; border-radius: 30px; margin-bottom: 14px;
        }
        .profile-hero-text .stars { color: #f6ad55; font-size: 1rem; letter-spacing: 2px; }
        .profile-body { max-width: 900px; margin: 0 auto; padding: 40px 8% 60px; }
        .profile-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
        @media(max-width:700px){ .profile-grid { grid-template-columns: 1fr; } }
        .profile-card {
            background: var(--white); border-radius: 16px; padding: 28px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }
        .profile-card h3 {
            font-family: 'Playfair Display', serif; font-size: 1.1rem;
            color: var(--navy); margin: 0 0 16px;
            padding-bottom: 12px; border-bottom: 2px solid var(--border);
        }
        .profile-card p { color: var(--muted); line-height: 1.7; margin: 0; }
        .detail-list { list-style: none; padding: 0; margin: 0; }
        .detail-list li {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 10px 0; border-bottom: 1px solid var(--border);
            font-size: 0.9rem;
        }
        .detail-list li:last-child { border-bottom: none; }
        .detail-list li i { color: var(--teal); width: 18px; flex-shrink: 0; margin-top: 2px; }
        .detail-list li strong { color: var(--navy); min-width: 110px; }
        .btn-book {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--navy); color: #fff;
            padding: 14px 32px; border-radius: 12px;
            font-family: 'DM Sans', sans-serif; font-weight: 600;
            text-decoration: none; margin-top: 24px;
            transition: background 0.2s, transform 0.2s;
        }
        .btn-book:hover { background: var(--teal); transform: translateY(-2px); }
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            color: var(--muted); font-size: 0.88rem; text-decoration: none;
            margin-bottom: 24px;
        }
        .btn-back:hover { color: var(--navy); }
        .not-found { text-align: center; padding: 80px 20px; }
        .not-found i { font-size: 4rem; color: var(--border); display: block; margin-bottom: 20px; }
        .not-found h2 { color: var(--navy); margin-bottom: 12px; }
        .not-found p { color: var(--muted); margin-bottom: 24px; }
    </style>
</head>
<body>
<?php include 'nav_only.php'; ?>

<?php if ($doctor): ?>
    <div class="profile-hero">
        <div class="profile-hero-inner">
            <?php
                $img_file = $doctor['profile_image'] ?? '';
                if (!empty($img_file) && !preg_match('#^https?://#', $img_file)) {
                    $img_src = 'assets/images/' . htmlspecialchars($img_file);
                } elseif (!empty($img_file)) {
                    $img_src = htmlspecialchars($img_file);
                } else {
                    $full_name = ($doctor['first_name'] ?? 'Doctor') . ' ' . ($doctor['last_name'] ?? '');
                    $img_src = 'https://ui-avatars.com/api/?name=' . urlencode($full_name) . '&size=300&background=0aa698&color=fff&bold=true&rounded=true';
                }
                $fallback_src = $img_src;
            ?>
            <img src="<?php echo $img_src; ?>"
                 alt="<?php echo htmlspecialchars('Dr. ' . $doctor['first_name'] . ' ' . $doctor['last_name']); ?>"
                 class="profile-avatar"
                 onerror="this.src='<?php echo $fallback_src; ?>'">
            <div class="profile-hero-text">
                <h1>Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h1>
                <span class="spec-badge"><i class="fa-solid fa-stethoscope"></i> <?php echo htmlspecialchars($doctor['specialization']); ?></span>
                <?php if ($doctor['rating'] > 0): ?>
                    <div>
                        <span class="stars"><?php echo str_repeat('★', (int)$doctor['rating']) . str_repeat('☆', 5 - (int)$doctor['rating']); ?></span>
                        <span style="color:rgba(255,255,255,0.7); font-size:0.88rem; margin-left:6px;"><?php echo number_format((float)$doctor['rating'], 1); ?> / 5.0</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="profile-body">
        <a href="doctors.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to all doctors</a>
        <div class="profile-grid">
            <div class="profile-card">
                <h3><i class="fa-solid fa-user-doctor" style="color:var(--teal);margin-right:8px;"></i>About</h3>
                <p><?php echo nl2br(htmlspecialchars($doctor['bio'] ?: 'No biography available for this doctor.')); ?></p>
                <a href="book_appointment.php?doctor_id=<?php echo (int)$doctor['id']; ?>" class="btn-book">
                    <i class="fa-solid fa-calendar-check"></i> Book an Appointment
                </a>
            </div>
            <div class="profile-card">
                <h3><i class="fa-solid fa-circle-info" style="color:var(--teal);margin-right:8px;"></i>Details</h3>
                <ul class="detail-list">
                    <li>
                        <i class="fa-solid fa-briefcase-medical"></i>
                        <div><strong>Specialization</strong><br><?php echo htmlspecialchars($doctor['specialization']); ?></div>
                    </li>
                    <li>
                        <i class="fa-solid fa-clock"></i>
                        <div><strong>Availability</strong><br><?php echo htmlspecialchars($doctor['availability'] ?: 'Contact for slots'); ?></div>
                    </li>
                    <li>
                        <i class="fa-solid fa-money-bill-wave"></i>
                        <div><strong>Consultation Fee</strong><br>LKR <?php echo number_format((float)$doctor['consultation_fee'], 0); ?></div>
                    </li>
                    <?php if ($doctor['rating'] > 0): ?>
                    <li>
                        <i class="fa-solid fa-star"></i>
                        <div><strong>Rating</strong><br><?php echo number_format((float)$doctor['rating'], 1); ?> / 5.0</div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="not-found">
        <i class="fa-solid fa-user-doctor"></i>
        <h2>Doctor Not Found</h2>
        <p>The requested doctor profile is unavailable or does not exist.</p>
        <a href="doctors.php" class="btn-book"><i class="fa-solid fa-arrow-left"></i> Browse All Doctors</a>
    </div>
<?php endif; ?>

<?php include 'footer_bare.php'; ?>
</body>
</html>
