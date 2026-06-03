<?php
require_once 'db_connect.php';
$pageTitle = 'Find a Doctor';
include 'header.php';

$allDoctors = fetch_all_doctors();

// Search and filter logic
$searchName = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterSpecialization = isset($_GET['specialization']) ? trim($_GET['specialization']) : '';
$filterMaxFee = isset($_GET['max_fee']) && is_numeric($_GET['max_fee']) ? floatval($_GET['max_fee']) : null;

$doctors = $allDoctors;
$uniqueSpecializations = [];

// Collect all unique specializations
foreach ($allDoctors as $doc) {
    if (!in_array($doc['specialization'], $uniqueSpecializations)) {
        $uniqueSpecializations[] = $doc['specialization'];
    }
}

// Apply filters
if ($searchName || $filterSpecialization || $filterMaxFee !== null) {
    $doctors = array_filter($doctors, function ($doc) use ($searchName, $filterSpecialization, $filterMaxFee) {
        $fullName = strtolower($doc['first_name'] . ' ' . $doc['last_name']);
        $searchTerm = strtolower($searchName);

        if ($searchName && strpos($fullName, $searchTerm) === false) {
            return false;
        }

        if ($filterSpecialization && $doc['specialization'] !== $filterSpecialization) {
            return false;
        }

        if ($filterMaxFee !== null && $doc['consultation_fee'] > $filterMaxFee) {
            return false;
        }

        return true;
    });
}
?>

<section class="container">
    <h1>Find a Specialist Doctor</h1>
    <p>Search and filter doctors by name, specialization, or consultation fee.</p>

    <!-- Search & Filter Form -->
    <div class="filter-section">
        <form method="get" class="search-form">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search doctor name..." value="<?php echo e($searchName); ?>">
                </div>

                <div class="form-group">
                    <select name="specialization">
                        <option value="">-- All Specializations --</option>
                        <?php foreach ($uniqueSpecializations as $spec): ?>
                            <option value="<?php echo e($spec); ?>" <?php echo $filterSpecialization === $spec ? 'selected' : ''; ?>>
                                <?php echo e($spec); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <select name="max_fee">
                        <option value="">-- All Fees --</option>
                        <option value="5000" <?php echo $filterMaxFee == 5000 ? 'selected' : ''; ?>>Up to LKR 5,000</option>
                        <option value="10000" <?php echo $filterMaxFee == 10000 ? 'selected' : ''; ?>>Up to LKR 10,000</option>
                        <option value="15000" <?php echo $filterMaxFee == 15000 ? 'selected' : ''; ?>>Up to LKR 15,000</option>
                        <option value="20000" <?php echo $filterMaxFee == 20000 ? 'selected' : ''; ?>>Up to LKR 20,000</option>
                    </select>
                </div>

                <button type="submit" class="button">Search</button>
                <a href="doctors.php" class="button secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- Results -->
    <?php if (!empty($doctors)): ?>
        <p style="margin-bottom: 20px; color: #666;">Found <?php echo count($doctors); ?> doctor(s)</p>
        <div class="doctor-grid">
            <?php foreach ($doctors as $doctor): ?>
                <article class="doctor-card">
                    <div class="doctor-card-header">
                        <h3><?php echo e('Dr. ' . $doctor['first_name'] . ' ' . $doctor['last_name']); ?></h3>
                        <span class="doctor-specialty"><?php echo e($doctor['specialization']); ?></span>
                    </div>
                    <div class="doctor-card-body">
                        <p><?php echo e($doctor['qualifications']); ?></p>
                        <div class="doctor-meta">
                            <span>⭐ <?php echo number_format($doctor['rating'], 1); ?>/5</span>
                            <span><?php echo (int)$doctor['experience_years']; ?> years exp</span>
                            <span>💰 LKR <?php echo number_format($doctor['consultation_fee'], 0); ?></span>
                        </div>
                    </div>
                    <div class="doctor-card-footer">
                        <a class="button" href="doctor-profile.php?id=<?php echo e($doctor['id']); ?>">View Profile</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">No doctors match your search. Try adjusting your filters.</div>
    <?php endif; ?>
</section>

<style>
    .filter-section {
        margin-bottom: 30px;
    }

    .search-form {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto auto;
        gap: 10px;
        align-items: flex-end;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group input,
    .form-group select {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .doctor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .doctor-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .doctor-card-header {
        background: #e3f2fd;
        padding: 15px;
    }

    .doctor-card-header h3 {
        margin: 0 0 5px 0;
    }

    .doctor-specialty {
        display: inline-block;
        background: #2196f3;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
    }

    .doctor-card-body {
        padding: 15px;
        flex: 1;
    }

    .doctor-card-body p {
        color: #666;
        margin: 0 0 10px 0;
    }

    .doctor-meta {
        display: flex;
        gap: 15px;
        font-size: 13px;
        color: #555;
    }

    .doctor-card-footer {
        padding: 15px;
        background: #fafafa;
    }

    .button.secondary {
        background: #999;
        color: white;
    }
</style>

<?php include 'footer.php'; ?>