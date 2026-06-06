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
    $stmt = $connection->prepare('SELECT d.id, d.specialization, d.qualifications, d.experience_years, d.consultation_fee, d.availability, d.rating FROM doctors d WHERE d.user_id = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $doctor = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    if ($doctor) {
        $sq = "SELECT a.appointment_date, a.status, u.first_name, u.last_name
               FROM appointments a
               JOIN patients p ON p.id = a.patient_id
               JOIN users u ON u.id = p.user_id
               WHERE a.doctor_id = ? AND a.appointment_date >= NOW()
               ORDER BY a.appointment_date ASC LIMIT 5";
        $ss = $connection->prepare($sq);
        if ($ss) {
            $ss->bind_param('i', $doctor['id']);
            $ss->execute();
            $doctorAppointments = $ss->get_result()->fetch_all(MYSQLI_ASSOC);
            $ss->close();
        }
        $unreadCount    = get_unread_count($_SESSION['user_id']);
        $recentMessages = array_slice(fetch_inbox($_SESSION['user_id']), 0, 5);
    }
}

include 'header.php';
?>
<div class="page-panel">

    <div class="welcome-banner">
        <div>
            <h2>Good day, Dr. <?php echo e($user['last_name']); ?>! 🩺</h2>
            <p>Review your schedule, patient messages, and manage your practice profile below.</p>
        </div>
        <div class="welcome-banner-icon"><i class="fas fa-user-md"></i></div>
    </div>

    <?php if ($doctor): ?>
    <div class="summary-grid">
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-stethoscope"></i></div>
            <strong><?php echo e($doctor['specialization']); ?></strong>
            <span>Specialization</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-briefcase-medical"></i></div>
            <strong><?php echo (int)$doctor['experience_years']; ?> yrs</strong>
            <span>Experience</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-tag"></i></div>
            <strong>LKR <?php echo number_format((float)$doctor['consultation_fee'], 0); ?></strong>
            <span>Consultation Fee</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-star"></i></div>
            <strong><?php echo number_format((float)$doctor['rating'], 1); ?>/5</strong>
            <span>Patient Rating</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-envelope"></i></div>
            <strong><?php echo (int)$unreadCount; ?></strong>
            <span>Unread Messages</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
            <strong><?php echo count($doctorAppointments); ?></strong>
            <span>Upcoming Appointments</span>
        </div>
    </div>

    <div class="page-actions">
        <a class="button primary-button" href="upload_report.php"><i class="fas fa-upload"></i> Upload Report</a>
        <a class="button outline-button" href="chat_engine.php"><i class="fas fa-comments"></i> Messages<?php if($unreadCount>0): ?> (<?php echo (int)$unreadCount; ?>)<?php endif; ?></a>
        <a class="button outline-button" href="appointments.php"><i class="fas fa-calendar-alt"></i> All Appointments</a>
    </div>

    <div class="content-panel">
        <h3><i class="fas fa-calendar-alt"></i> Upcoming Appointments</h3>
        <?php if (!empty($doctorAppointments)): ?>
            <table class="card-table">
                <thead>
                    <tr><th>Patient</th><th>Date &amp; Time</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($doctorAppointments as $visit): ?>
                        <tr>
                            <td><strong><?php echo e($visit['first_name'] . ' ' . $visit['last_name']); ?></strong></td>
                            <td><i class="fas fa-clock" style="color:var(--text-muted);font-size:.8rem;margin-right:.3rem;"></i><?php echo e(date('M j, Y H:i', strtotime($visit['appointment_date']))); ?></td>
                            <td><span class="status-pill <?php echo e($visit['status']); ?>"><?php echo ucfirst(e($visit['status'])); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-check"></i>
                <p>No upcoming appointments scheduled.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="content-panel">
        <h3><i class="fas fa-inbox"></i> Recent Messages</h3>
        <?php if (empty($recentMessages)): ?>
            <div class="empty-state">
                <i class="fas fa-comment-slash"></i>
                <p>No recent messages.</p>
            </div>
        <?php else: ?>
            <table class="card-table">
                <thead>
                    <tr><th>From</th><th>Subject</th><th>Date</th><th></th></tr>
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
        <div class="empty-state">
            <i class="fas fa-user-md"></i>
            <p>Your doctor profile is not yet configured. Please ask an administrator to complete your profile.</p>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php include 'footer.php'; ?>
