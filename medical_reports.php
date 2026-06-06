<?php
require_once 'auth.php';
require_login();

$user_id = $_SESSION['user_id'];
$patient = fetch_patient_by_user_id($user_id);
$patient_id = $patient ? $patient['id'] : null;

$reports = [];
if ($patient_id) {
    $reports = fetch_medical_reports_for_user($user_id);
}

include('header.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Lab Results | MediCare Plus</title>
    <link rel="stylesheet" href="assets/css/HomeStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
</head>

<body style="background-color: #f7fafc;">

    <div class="dashboard-container">
        <div class="sidebar-menu">
            <h3 style="margin-bottom: 20px; color: #2d3748; padding-left: 15px;">My Portal</h3>
            <a href="dashboard_patient.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="book_appointment.php"><i class="fas fa-calendar-plus"></i> Book Appointment</a>
            <a href="medical_reports.php" class="active"><i class="fas fa-file-medical"></i> Lab Results</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="logout.php" style="color: #e53e3e;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="main-panel">
            <h2 style="color: #2d3748; margin-bottom: 5px;">My Medical Reports</h2>
            <p style="color: #718096; margin-bottom: 30px;">Securely view and download your lab results and prescriptions.</p>

            <?php if (!$patient_id): ?>
                <p style="color: #e53e3e;">Your patient profile is incomplete.</p>
            <?php elseif ($reports && count($reports) > 0): ?>
                <?php foreach ($reports as $report): ?>
                    <div class="report-card">
                        <div class="report-header">
                            <div>
                                <h3 style="color: #2b6cb0; margin-bottom: 5px;"><?php echo htmlspecialchars($report['file_name']); ?></h3>
                                <p style="color: #718096; font-size: 0.9rem;">Uploaded by <?php echo htmlspecialchars($report['uploaded_by']); ?> on <?php echo date('M d, Y', strtotime($report['created_at'])); ?></p>
                            </div>
                            <?php if (!empty($report['file_path'])): ?>
                                <a href="download_report.php?id=<?php echo htmlspecialchars($report['id']); ?>" class="download-btn" target="_blank">
                                    <i class="fas fa-download"></i> Download File
                                </a>
                            <?php endif; ?>
                        </div>
                        <p style="color: #4a5568; line-height: 1.5;"><?php echo nl2br(htmlspecialchars($report['notes'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #718096; background: #edf2f7; padding: 20px; border-radius: 8px; text-align: center;">No medical reports have been uploaded to your account yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>