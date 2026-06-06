<?php
require_once 'auth.php';
require_role('admin');

$pageTitle = 'Admin Dashboard | MediCare Plus';
$user = current_user();

$connection = get_db_connection();

function fetch_count($conn, $sql) {
    $result = $conn->query($sql);
    return $result ? (int)$result->fetch_row()[0] : 0;
}

$totalPatients     = $connection ? fetch_count($connection, 'SELECT COUNT(*) FROM patients') : 0;
$totalDoctors      = $connection ? fetch_count($connection, 'SELECT COUNT(*) FROM doctors') : 0;
$totalAppointments = $connection ? fetch_count($connection, 'SELECT COUNT(*) FROM appointments') : 0;
$totalMessages     = $connection ? fetch_count($connection, 'SELECT COUNT(*) FROM messages') : 0;

$unreadCount = 0;
$recentMessages = [];
if ($connection && isset($_SESSION['user_id'])) {
    $unreadCount    = get_unread_count($_SESSION['user_id']);
    $recentMessages = array_slice(fetch_inbox($_SESSION['user_id']), 0, 5);
}

include 'header.php';
?>
<div class="page-panel">

    <div class="welcome-banner">
        <div>
            <h2>Admin Panel — <?php echo e($user['first_name'] . ' ' . $user['last_name']); ?> ⚙️</h2>
            <p>Monitor system activity, manage users, and track core performance metrics.</p>
        </div>
        <div class="welcome-banner-icon"><i class="fas fa-shield-alt"></i></div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-users"></i></div>
            <strong><?php echo $totalPatients; ?></strong>
            <span>Registered Patients</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-user-md"></i></div>
            <strong><?php echo $totalDoctors; ?></strong>
            <span>Active Doctors</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
            <strong><?php echo $totalAppointments; ?></strong>
            <span>Total Appointments</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-envelope"></i></div>
            <strong><?php echo $totalMessages; ?></strong>
            <span>Total Messages</span>
        </div>
        <div class="summary-card">
            <div class="card-icon"><i class="fas fa-bell"></i></div>
            <strong><?php echo (int)$unreadCount; ?></strong>
            <span>Your Unread Messages</span>
        </div>
    </div>

    <div class="page-actions">
        <a class="button primary-button" href="add_doctors_safe.php"><i class="fas fa-user-plus"></i> Add Doctor</a>
        <a class="button outline-button" href="chat_engine.php"><i class="fas fa-comments"></i> Inbox<?php if($unreadCount>0): ?> (<?php echo (int)$unreadCount; ?>)<?php endif; ?></a>
        <a class="button outline-button" href="admin_reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
        <a class="button outline-button" href="doctors.php"><i class="fas fa-stethoscope"></i> Manage Doctors</a>
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

</div>
<?php include 'footer.php'; ?>
