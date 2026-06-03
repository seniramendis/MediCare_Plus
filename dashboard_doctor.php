<?php
require_once 'auth.php';
require_role('doctor');

$pageTitle = 'Doctor Dashboard | MediCare Plus';
$user = current_user();

$connection = get_db_connection();
$doctor = null;
$doctorAppointments = [];
$unreadCount = 0;
$recentMessages = [];

if ($connection) {
    $query = 'SELECT d.id, d.specialization, d.qualifications, d.experience_years, d.consultation_fee, d.availability, d.rating FROM doctors d WHERE d.user_id = ? LIMIT 1';
    $statement = $connection->prepare($query);
    if ($statement) {
        $statement->bind_param('i', $_SESSION['user_id']);
        $statement->execute();
        $doctor = $statement->get_result()->fetch_assoc();
        $statement->close();
    }

    if ($doctor) {
        $scheduleQuery = "SELECT a.appointment_date, a.status, u.first_name, u.last_name FROM appointments a JOIN patients p ON p.id = a.patient_id JOIN users u ON u.id = p.user_id WHERE a.doctor_id = ? AND a.appointment_date >= NOW() ORDER BY a.appointment_date ASC LIMIT 5";
        $scheduleStatement = $connection->prepare($scheduleQuery);
        if ($scheduleStatement) {
            $scheduleStatement->bind_param('i', $doctor['id']);
            $scheduleStatement->execute();
            $doctorAppointments = $scheduleStatement->get_result()->fetch_all(MYSQLI_ASSOC);
            $scheduleStatement->close();
        }
        // messaging summaries
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
    <div class="page-title">Doctor Dashboard</div>
    <p class="content-panel">Hello, Dr. <?php echo e($user['last_name']); ?>. Review your schedule, patient messages, and your practice profile below.</p>

    <?php if ($doctor): ?>
        <div class="summary-grid">
            <div class="summary-card">
                <strong><?php echo e($doctor['specialization']); ?></strong>
                <span>Specialization</span>
            </div>
            <div class="summary-card">
                <strong><?php echo e($doctor['experience_years']); ?> yrs</strong>
                <span>Practice experience</span>
            </div>
            <div class="summary-card">
                <strong>$<?php echo number_format((float) $doctor['consultation_fee'], 2); ?></strong>
                <span>Consultation fee</span>
            </div>
            <div class="summary-card">
                <strong><?php echo number_format((float) $doctor['rating'], 1); ?>/5</strong>
                <span>Average patient rating</span>
            </div>
            <div class="summary-card">
                <strong><?php echo (int)$unreadCount; ?></strong>
                <span>Unread messages</span>
            </div>
        </div>

        <div class="content-panel">
            <h3>Upcoming appointments</h3>
            <?php if (!empty($doctorAppointments)): ?>
                <table class="card-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doctorAppointments as $visit): ?>
                            <tr>
                                <td><?php echo e($visit['first_name'] . ' ' . $visit['last_name']); ?></td>
                                <td><?php echo e(date('M j, Y H:i', strtotime($visit['appointment_date']))); ?></td>
                                <td><span class="status-pill <?php echo e($visit['status']); ?>"><?php echo ucfirst($visit['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="content-panel">No upcoming appointments have been scheduled yet.</p>
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
    <?php else: ?>
        <div class="content-panel">
            <p>Your doctor profile is not yet configured. Please ask an administrator to complete your profile information.</p>
        </div>
    <?php endif; ?>
</section>
<?php include 'footer.php'; ?>