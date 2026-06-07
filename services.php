<?php
require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Our Services | MediCare Plus';
include 'header.php';

$services = fetch_services();
?>

<div class="page-container-home" style="padding-top: 120px; min-height: 80vh;">
    <h1 class="section-title" data-aos="fade-down">Our Premium Services</h1>
    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Comprehensive medical care tailored to your needs. Explore our specialized departments and treatment options.</p>

    <?php if (empty($services)): ?>
        <div style="text-align: center; padding: 50px; background: #fff; border-radius: 15px; box-shadow: var(--card-shadow);" data-aos="zoom-in">
            <h3 style="color: var(--text-muted);">No services available at the moment.</h3>
        </div>
    <?php else: ?>
        <div class="service-grid-home">
            <?php $delay = 100;
            foreach ($services as $service): ?>
                <div class="service-card" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="service-icon-container" style="color: var(--secondary-blue);">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <h4 style="color: var(--primary-dark); font-size: 1.4rem; margin-bottom: 10px;">
                        <?php echo htmlspecialchars($service['name']); ?>
                    </h4>
                    <p style="color: var(--accent-green); font-weight: bold; margin-bottom: 15px;">
                        Category: <?php echo htmlspecialchars($service['category']); ?>
                    </p>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($service['description']); ?>
                    </p>
                    <div style="background: var(--soft-bg); padding: 10px; border-radius: 8px; font-weight: bold; color: var(--primary-dark);">
                        Fee: LKR <?php echo number_format($service['price'], 2); ?>
                    </div>
                </div>
            <?php $delay += 100;
                if ($delay > 300) $delay = 100;
            endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>
<?php include 'footer.php'; ?>