<?php
session_start();
include('db_connect.php');

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(302);
    header("Location: Login.php");
    exit(0);
}

$user_id = $_SESSION['user_id'];

// 2. Safely get the Patient ID
$conn = get_db_connection();
$patient_id = null;
$patient_stmt = $conn->prepare("SELECT id FROM patients WHERE user_id = ?");
$patient_stmt->bind_param("i", $user_id);
$patient_stmt->execute();
$patient_res = $patient_stmt->get_result();
if ($patient_row = $patient_res->fetch_assoc()) {
    $patient_id = $patient_row['id'];
}

// 3. Directly fetch reports from the database using the actual schema
$reports = null;
if ($patient_id) {
    $report_query = "
        SELECT id, file_name, file_path, notes, uploaded_by, created_at
        FROM medical_reports
        WHERE patient_id = ?
        ORDER BY created_at DESC
    ";
    $report_stmt = $conn->prepare($report_query);
    $report_stmt->bind_param("i", $patient_id);
    $report_stmt->execute();
    $reports = $report_stmt->get_result();
}

/**
 * Return a safe file path for downloads — only allow alphanumeric characters,
 * underscores, hyphens, and a single dot for the extension.
 * Rejects URL schemes (javascript:, data:) and path traversal (../).
 *
 * @param string $value
 * @return string HTML-safe filename or empty string if invalid
 */
function safe_file_path(string $value): string
{
    $value = trim($value);
    // Only allow safe filenames: letters, numbers, underscore, hyphen, dot
    if ($value === '' || !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $value)) {
        return '';
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

include('header.php');
?>

<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 30px;
    }

    .sidebar-menu {
        background: #fff;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .sidebar-menu a {
        display: block;
        padding: 15px;
        color: #4a5568;
        text-decoration: none;
        font-weight: 600;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: 0.3s;
    }

    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: #ebf8ff;
        color: #2b6cb0;
    }

    .main-panel {
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .report-card {
        border-left: 4px solid #2b6cb0;
        background: #f7fafc;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .download-btn {
        background: #2b6cb0;
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.3s;
    }

    .download-btn:hover {
        background: #2c5282;
    }
</style>

<div class="dashboard-container">
    <div class="sidebar-menu">
        <h3 style="margin-bottom: 20px; color: #2d3748; padding-left: 15px;">My Portal</h3>
        <a href="dashboard_patient.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="medical_reports.php" class="active"><i class="fas fa-file-medical"></i> Lab Results</a>
    </div>

    <div class="main-panel">
        <h2 style="color: #2d3748; margin-bottom: 5px;">My Medical Reports</h2>
        <p style="color: #718096; margin-bottom: 30px;">Securely view and download your lab results.</p>

        <?php if (!$patient_id): ?>
            <p style="color: #e53e3e;">Your patient profile is incomplete.</p>
        <?php elseif ($reports && $reports->num_rows > 0): ?>
            <?php while ($report = $reports->fetch_assoc()): ?>
                <div class="report-card">
                    <div class="report-header">
                        <div>
                            <h3 style="color: #2b6cb0; margin-bottom: 5px;"><?php echo htmlspecialchars($report['file_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p style="color: #718096; font-size: 0.9rem;">Uploaded by <?php echo htmlspecialchars($report['uploaded_by'], ENT_QUOTES, 'UTF-8'); ?> on <?php echo htmlspecialchars(date('M d, Y', strtotime($report['created_at'])), ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <?php
                        $safePath = safe_file_path($report['file_path']);
                        ?>
                        <?php if ($safePath !== ''): ?>
                            <a href="uploads/<?php echo $safePath; ?>" class="download-btn" target="_blank" download>
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        <?php endif; ?>
                    </div>
                    <p style="color: #4a5568; line-height: 1.5;"><?php echo nl2br(htmlspecialchars($report['notes'] ?? '')); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #718096; background: #edf2f7; padding: 20px; border-radius: 8px; text-align: center;">No medical reports uploaded yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
include('footer.php');
?>