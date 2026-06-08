<?php
require_once 'auth.php';
require_role('patient');

$pageTitle = 'Patient Dashboard | MediCare Plus';
$user = current_user();

$connection = get_db_connection();
$patient = null;
$upcomingAppointments = [];
$completedAppointments = [];
$unreadCount = 0;
$recentMessages = [];

if ($connection) {
    $stmt = $connection->prepare('SELECT id FROM patients WHERE user_id = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $patient = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        // Auto-create if missing
        if (!$patient) {
            $connection->query("INSERT IGNORE INTO patients (user_id) VALUES ({$_SESSION['user_id']})");
            $patient = ['id' => $connection->insert_id];
        }
    }

    if ($patient) {
        // Upcoming: not completed, future OR today
        $sq = "SELECT a.id, a.appointment_date, a.status, u.first_name, u.last_name, d.specialization, d.consultation_fee
               FROM appointments a
               JOIN doctors d ON d.id = a.doctor_id
               JOIN users u ON u.id = d.user_id
               WHERE a.patient_id = ? AND a.status != 'completed'
               ORDER BY a.appointment_date ASC LIMIT 5";
        $ss = $connection->prepare($sq);
        if ($ss) {
            $ss->bind_param('i', $patient['id']);
            $ss->execute();
            $upcomingAppointments = $ss->get_result()->fetch_all(MYSQLI_ASSOC);
            $ss->close();
        }

        // Completed appointments
        $sq2 = "SELECT a.id, a.appointment_date, a.status, u.first_name, u.last_name, d.specialization, d.consultation_fee
                FROM appointments a
                JOIN doctors d ON d.id = a.doctor_id
                JOIN users u ON u.id = d.user_id
                WHERE a.patient_id = ? AND a.status = 'completed'
                ORDER BY a.appointment_date DESC LIMIT 5";
        $ss2 = $connection->prepare($sq2);
        if ($ss2) {
            $ss2->bind_param('i', $patient['id']);
            $ss2->execute();
            $completedAppointments = $ss2->get_result()->fetch_all(MYSQLI_ASSOC);
            $ss2->close();
        }

        $unreadCount   = get_unread_count($_SESSION['user_id']);
        $recentMessages = array_slice(fetch_inbox($_SESSION['user_id']), 0, 5);
    }
}

include 'header.php';
?>
<style>
    .btn-pay {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #1a56db, #2b6cb0);
        color: #fff;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: .82rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: opacity .2s;
    }

    .btn-pay:hover {
        opacity: .85;
        color: #fff;
    }
</style>
<div class="page-panel">

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div>
            <h2>Welcome back, <?php echo e($user['first_name']); ?>! 👋</h2>
            <p>Manage your appointments, medical records, and messages from one secure place.</p>
        </div>
        <div class="welcome-banner-icon"><i class="fas fa-user-injured"></i></div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
            <strong><?php echo count($upcomingAppointments); ?></strong>
            <span>Upcoming Appointments</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-envelope"></i></div>
            <strong><?php echo (int)$unreadCount; ?></strong>
            <span>Unread Messages</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-file-medical"></i></div>
            <strong><a href="medical_reports.php">View</a></strong>
            <span>Medical Reports</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-comments"></i></div>
            <strong><a href="chat_engine.php">Open</a></strong>
            <span>Doctor Chat</span>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="page-actions">
        <a class="button primary-button" href="book_appointment.php"><i class="fas fa-plus"></i> Book Appointment</a>
        <a class="button outline-button" href="medical_reports.php"><i class="fas fa-file-medical"></i> My Reports</a>
        <a class="button outline-button" href="chat_engine.php"><i class="fas fa-comments"></i> Messages<?php if ($unreadCount > 0): ?> (<?php echo (int)$unreadCount; ?>)<?php endif; ?></a>
        <a class="button outline-button" href="feedback.php"><i class="fas fa-star"></i> Leave Feedback</a>
    </div>

    <!-- Upcoming Appointments -->
    <div class="content-panel">
        <h3><i class="fas fa-calendar-alt"></i> Upcoming Appointments</h3>
        <?php if (!empty($upcomingAppointments)): ?>
            <table class="card-table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialty</th>
                        <th>Date &amp; Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcomingAppointments as $appt): ?>
                        <tr>
                            <td><strong><?php echo e('Dr. ' . $appt['first_name'] . ' ' . $appt['last_name']); ?></strong></td>
                            <td><?php echo e($appt['specialization']); ?></td>
                            <td><i class="fas fa-clock" style="color:var(--text-muted);font-size:.8rem;margin-right:.3rem;"></i><?php echo e(date('M j, Y  H:i', strtotime($appt['appointment_date']))); ?></td>
                            <td><span class="status-pill <?php echo e($appt['status']); ?>"><?php echo ucfirst(e($appt['status'])); ?></span></td>
                            <td>
                                <?php if ($appt['status'] === 'confirmed'): ?>
                                    <a href="payment.php?appointment_id=<?php echo (int)$appt['id']; ?>&amount=<?php echo (int)$appt['consultation_fee']; ?>&doctor=<?php echo urlencode('Dr. ' . $appt['first_name'] . ' ' . $appt['last_name']); ?>"
                                        class="btn-pay"><i class="fas fa-credit-card"></i> Pay Now</a>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);font-size:.85rem;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-plus"></i>
                <p>No upcoming appointments. <a href="book_appointment.php">Book your first consultation</a>.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Completed Appointments -->
    <?php if (!empty($completedAppointments)): ?>
        <div class="content-panel">
            <h3><i class="fas fa-check-circle" style="color:#38a169;"></i> Completed Appointments</h3>
            <table class="card-table">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialty</th>
                        <th>Date &amp; Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($completedAppointments as $appt): ?>
                        <tr>
                            <td><strong><?php echo e('Dr. ' . $appt['first_name'] . ' ' . $appt['last_name']); ?></strong></td>
                            <td><?php echo e($appt['specialization']); ?></td>
                            <td><i class="fas fa-clock" style="color:var(--text-muted);font-size:.8rem;margin-right:.3rem;"></i><?php echo e(date('M j, Y  H:i', strtotime($appt['appointment_date']))); ?></td>
                            <td><span class="status-pill completed"><?php echo ucfirst(e($appt['status'])); ?></span></td>
                            <td><span style="color:#38a169;font-size:.85rem;font-weight:600;"><i class="fas fa-check-circle"></i> Paid</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Recent Messages -->
    <div class="content-panel">
        <h3><i class="fas fa-inbox"></i> Recent Messages</h3>
        <?php if (empty($recentMessages)): ?>
            <div class="empty-state">
                <i class="fas fa-comment-slash"></i>
                <p>No messages yet.</p>
            </div>
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

</div>
<?php include 'footer.php'; ?>