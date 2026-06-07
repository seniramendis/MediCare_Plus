<?php
require_once 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Our Services | MediCare Plus';
include 'header.php';

$serviceIcons = [
    'Cardiology'    => ['fa-heart-pulse',    '#e53e3e'],
    'Neurology'     => ['fa-brain',          '#6b46c1'],
    'Paediatrics'   => ['fa-baby',           '#3182ce'],
    'General'       => ['fa-stethoscope',    '#2b6cb0'],
    'Laboratory'    => ['fa-flask',          '#d69e2e'],
    'Radiology'     => ['fa-x-ray',          '#4a5568'],
    'Pharmacy'      => ['fa-pills',          '#38a169'],
    'Emergency'     => ['fa-truck-medical',  '#e53e3e'],
    'Gynaecology'   => ['fa-venus',          '#d53f8c'],
    'Orthopaedics'  => ['fa-bone',           '#dd6b20'],
];

$services = [];
if (isset($conn) && $conn) {
    try {
        $r = $conn->query("SELECT * FROM services ORDER BY category, name");
        if ($r) $services = $r->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {}
}
?>

<div class="page-hero" data-aos="fade-up">
    <span class="section-tag light"><i class="fas fa-hospital"></i> What We Offer</span>
    <h1>Our Premium Services</h1>
    <p>Comprehensive, specialised medical care tailored to every stage of your health journey.</p>
</div>

<div class="home-section" style="padding-top:60px;">
<?php if (empty($services)): ?>
    <div style="text-align:center;padding:60px;background:#fff;border-radius:18px;box-shadow:var(--card-shadow);">
        <i class="fas fa-database" style="font-size:3rem;color:var(--text-muted);opacity:.4;display:block;margin-bottom:16px;"></i>
        <h3 style="color:var(--text-muted);">No services found.</h3>
        <p style="color:var(--text-muted);margin-top:8px;">Please import <code>medicare_databs.sql</code> in phpMyAdmin.</p>
    </div>
<?php else: ?>
    <div class="services-grid">
        <?php $delay=0; foreach ($services as $svc):
            $matched = null;
            foreach ($serviceIcons as $k => $v) {
                if (stripos($svc['name'],$k)!==false || stripos($svc['category'],$k)!==false) { $matched=$v; break; }
            }
            $icon  = $matched ? $matched[0] : ($svc['icon'] ?? 'fa-notes-medical');
            $color = $matched ? $matched[1] : '#3182ce';
            $delay = ($delay >= 500) ? 0 : $delay + 80;
        ?>
        <div class="svc-card" style="--ic:<?= $color ?>" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
            <div class="svc-icon"><i class="fas <?= $icon ?>"></i></div>
            <h4><?= htmlspecialchars($svc['name']) ?></h4>
            <p class="svc-category" style="color:<?= $color ?>;font-weight:600;font-size:.82rem;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px;">
                <?= htmlspecialchars($svc['category'] ?? 'General') ?>
            </p>
            <p><?= htmlspecialchars($svc['description'] ?? '') ?></p>
            <div class="svc-fee">LKR <?= number_format($svc['price'] ?? 0, 2) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

    <div class="section-cta" data-aos="zoom-in" style="margin-top:60px;">
        <a href="book_appointment.php" class="btn-hero-primary"><i class="fas fa-calendar-check"></i> Book an Appointment</a>
    </div>
</div>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({duration:800,once:true,offset:60});</script>
<?php include 'footer.php'; ?>
