<?php
require_once 'auth.php';
require_role(['admin', 'doctor']);

$pageTitle = 'Patient Medical Reports (Admin)';
include 'header.php';

$patients = fetch_all_patients();
$selectedPatientId = isset($_GET['patient_id']) && is_numeric($_GET['patient_id']) ? (int)$_GET['patient_id'] : null;
$selectedPatient = null;
$reports = [];
if ($selectedPatientId) {
    // find patient info
    foreach ($patients as $p) {
        if ($p['patient_id'] == $selectedPatientId) {
            $selectedPatient = $p;
            break;
        }
    }
    $reports = fetch_medical_reports_by_patient_id($selectedPatientId);
}
?>

<section class="container">
    <h1>Patient Medical Reports</h1>

    <form method="get" class="form-inline">
        <label for="patient_id">Select Patient:</label>
        <select id="patient_id" name="patient_id">
            <option value="">-- choose patient --</option>
            <?php foreach ($patients as $p): ?>
                <option value="<?php echo e($p['patient_id']); ?>" <?php echo ($selectedPatientId && $selectedPatientId == $p['patient_id']) ? 'selected' : ''; ?>>
                    <?php echo e($p['last_name'] . ', ' . $p['first_name'] . ' (' . $p['email'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="button" type="submit">Load</button>
    </form>

    <?php if ($selectedPatient): ?>
        <h2>Reports for <?php echo e($selectedPatient['first_name'] . ' ' . $selectedPatient['last_name']); ?></h2>

        <?php if (empty($reports)): ?>
            <div class="empty-state">No reports uploaded for this patient.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>File</th>
                        <th>Notes</th>
                        <th>Uploaded By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $r): ?>
                        <tr>
                            <td><?php echo e($r['created_at']); ?></td>
                            <td><?php echo e($r['file_name']); ?></td>
                            <td><?php echo e($r['notes']); ?></td>
                            <td><?php echo e($r['uploaded_by']); ?></td>
                            <td>
                                <a class="button" href="download_report.php?id=<?php echo urlencode($r['id']); ?>">Download</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

</section>

<?php include 'footer.php'; ?>