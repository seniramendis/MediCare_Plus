<?php
$pageTitle = 'Our Services';
include 'header.php';

$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : null;
$categories = fetch_service_categories();
$services = fetch_services($selectedCategory);
?>

<section class="container">
    <h1>Healthcare Services</h1>
    <p>Browse our comprehensive range of medical services and specialties.</p>

    <!-- Category Filter -->
    <div class="filter-section">
        <h3>Filter by Category:</h3>
        <div class="category-buttons">
            <a href="services.php" class="button <?php echo $selectedCategory === null ? 'active' : ''; ?>">All Services</a>
            <?php foreach ($categories as $cat): ?>
                <a href="services.php?category=<?php echo urlencode($cat); ?>" class="button <?php echo $selectedCategory === $cat ? 'active' : ''; ?>">
                    <?php echo e($cat); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Services Grid -->
    <?php if (empty($services)): ?>
        <div class="empty-state">No services found in this category.</div>
    <?php else: ?>
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <h3><?php echo e($service['name']); ?></h3>
                    <p class="category-badge"><?php echo e($service['category']); ?></p>
                    <p class="description"><?php echo e($service['description']); ?></p>
                    <p class="price">Price: LKR <?php echo number_format($service['price'], 0); ?></p>
                    <a href="doctors.php" class="button">Book with Doctor</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<style>
    .filter-section {
        margin-bottom: 30px;
    }

    .category-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .category-buttons .button {
        padding: 8px 16px;
    }

    .category-buttons .button.active {
        background: #0066cc;
        color: white;
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .service-card {
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        background: #f9f9f9;
        display: flex;
        flex-direction: column;
    }

    .service-card h3 {
        margin: 0 0 10px 0;
    }

    .service-card .category-badge {
        display: inline-block;
        background: #e3f2fd;
        color: #1976d2;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        margin-bottom: 10px;
        width: fit-content;
    }

    .service-card .description {
        flex: 1;
        color: #555;
        margin-bottom: 10px;
    }

    .service-card .price {
        font-weight: bold;
        color: #2e7d32;
        margin-bottom: 10px;
    }
</style>

<?php include 'footer.php'; ?>