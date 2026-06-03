<?php
require_once 'auth.php';
require_role('patient');

$pageTitle = 'Patient Dashboard | MediCare Plus';
$user = current_user();

$connection = get_db_connection();
$patient = null;
$patientAppointments = [];
$unreadCount = 0;
$recentMessages = [];

if ($connection) {
    $query = 'SELECT id FROM patients WHERE user_id = ? LIMIT 1';
    $statement = $connection->prepare($query);
    if ($statement) {
        $statement->bind_param('i', $_SESSION['user_id']);
        $statement->execute();
        $patient = $statement->get_result()->fetch_assoc();
        $statement->close();
    }

    if ($patient) {
        $scheduleQuery = "SELECT a.appointment_date, a.status, u.first_name, u.last_name, d.specialization FROM appointments a JOIN doctors d ON d.id = a.doctor_id JOIN users u ON u.id = d.user_id WHERE a.patient_id = ? AND a.appointment_date >= NOW() ORDER BY a.appointment_date ASC LIMIT 5";
        $scheduleStatement = $connection->prepare($scheduleQuery);
        if ($scheduleStatement) {
            $scheduleStatement->bind_param('i', $patient['id']);
            $scheduleStatement->execute();
            $patientAppointments = $scheduleStatement->get_result()->fetch_all(MYSQLI_ASSOC);
            $scheduleStatement->close();
        }
        // messaging summaries for patient
        $unreadCount = get_unread_count($_SESSION['user_id']);
        $recentMessages = fetch_inbox($_SESSION['user_id']);
        if (!empty($recentMessages)) {
            $recentMessages = array_slice($recentMessages, 0, 5);
        }
    }
}

include 'header.php';
?>
<section class="page-panel">
    <div class="page-title">Patient Dashboard</div>
    <p class="content-panel">Welcome back, <?php echo e($user['first_name']); ?>. Manage your booked visits, reports, and messages from one secure location.</p>

    <div class="summary-grid">
        <div class="summary-card">
            <strong><?php echo count($patientAppointments); ?></strong>
            <span>Upcoming appointments</span>
        </div>
        <div class="summary-card">
            <strong><a href="medical_reports.php">View reports</a></strong>
            <span>Latest medical documents</span>
        </div>
        <div class="summary-card">
            <strong><a href="chat_engine.php">Open chat</a></strong>
            <span>Connect with your doctor</span>
        </div>
        <div class="summary-card">
            <strong><?php echo (int)$unreadCount; ?></strong>
            <span>Unread messages</span>
        </div>
    </div>

    <div class="content-panel">
        <h3>Your next appointments</h3>
        <?php if (!empty($patientAppointments)): ?>
            <table class="card-table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialty</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patientAppointments as $appointment): ?>
                        <tr>
                            <td><?php echo e($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                            <td><?php echo e($appointment['specialization']); ?></td>
                            <td><?php echo e(date('M j, Y H:i', strtotime($appointment['appointment_date']))); ?></td>
                            <td><span class="status-pill <?php echo e($appointment['status']); ?>"><?php echo ucfirst($appointment['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="content-panel">You have no upcoming appointments yet. Book a new consultation from the appointments page.</p>
        <?php endif; ?>
    </div>

    <div class="content-panel">
        <h3>Recent messages</h3>
        <?php if (empty($recentMessages)): ?>
            <p>No recent messages.</p>
        <?php else: ?>
            <table class="card-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentMessages as $m): ?>
                        <tr>
                            <td><?php echo e($m['first_name'] . ' ' . $m['last_name']); ?></td>
                            <td><?php echo e($m['subject']); ?></td>
                            <td><?php echo e(date('M j, Y H:i', strtotime($m['sent_at']))); ?></td>
                            <td><a href="chat_engine.php?view_user=<?php echo (int)$m['sender_id']; ?>">Open</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>
<?php include 'footer.php'; ?>