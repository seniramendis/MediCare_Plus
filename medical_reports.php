<?php
require_once 'auth.php';
require_login();

$pageTitle = 'My Medical Reports';
include 'header.php';

$user = current_user();
$userId = $user['id'];

$reports = fetch_medical_reports_for_user($userId);
?>

<section class="container">
    <h1>Medical Reports</h1>
    <p>Download your medical reports below. If you have any issues, contact support.</p>

    <?php if (empty($reports)): ?>
        <div class="empty-state">No reports found.</div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>File</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $r): ?>
                    <tr>
                        <td><?php echo e($r['created_at']); ?></td>
                        <td><?php echo e($r['file_name']); ?></td>
                        <td><?php echo e($r['notes']); ?></td>
                        <td>
                            <a class="button" href="download_report.php?id=<?php echo urlencode($r['id']); ?>">Download</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>