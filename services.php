<?php
require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Our Services | MediCare Plus';
include 'header.php';

// ==========================================
// BULLETPROOF FETCH (Bypasses all conflicts)
// ==========================================
$services = [];
if (isset($conn) && $conn) {
    try {
        // SELECT * prevents crashes if your columns are named differently
        $result = $conn->query("SELECT * FROM services");
        if ($result) {
            $services = $result->fetch_all(MYSQLI_ASSOC);
        }
    } catch (Exception $e) {
        // Creates a safe error card instead of white-screening the whole page
        $services = [[
            'name' => 'Database Error',
            'category' => 'System',
            'description' => 'SQL Error: ' . $e->getMessage(),
            'price' => 0
        ]];
    }
}
?>

<div class="page-container-home" style="padding-top: 120px; min-height: 80vh;">
    <h1 class="section-title" data-aos="fade-down">Our Premium Services</h1>
    <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Comprehensive medical care tailored to your needs. Explore our specialized departments and treatment options.</p>

    <?php if (empty($services)): ?>
        <div style="text-align: center; padding: 50px; background: #fff; border-radius: 15px; box-shadow: var(--card-shadow);" data-aos="zoom-in">
            <h3 style="color: #e53e3e;">No services available or database connection failed.</h3>
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
                        <?php echo htmlspecialchars($service['name'] ?? $service['service_name'] ?? 'Unknown Service'); ?>
                    </h4>

                    <p style="color: var(--accent-green); font-weight: bold; margin-bottom: 15px;">
                        Category: <?php echo htmlspecialchars($service['category'] ?? 'General'); ?>
                    </p>

                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($service['description'] ?? 'No description available for this service.'); ?>
                    </p>

                    <div style="background: var(--soft-bg); padding: 10px; border-radius: 8px; font-weight: bold; color: var(--primary-dark);">
                        Fee: LKR <?php echo number_format($service['price'] ?? $service['fee'] ?? $service['cost'] ?? 0, 2); ?>
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