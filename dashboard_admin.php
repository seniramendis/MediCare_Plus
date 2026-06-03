<?php
require_once 'auth.php';
require_role('admin');

$pageTitle = 'Admin Dashboard | MediCare Plus';
$user = current_user();

$connection = get_db_connection();

function fetch_count($connection, $sql)
{
    $result = $connection->query($sql);
    return $result ? (int) $result->fetch_row()[0] : 0;
}

$totalPatients = fetch_count($connection, 'SELECT COUNT(*) FROM patients');
$totalDoctors = fetch_count($connection, 'SELECT COUNT(*) FROM doctors');
$totalAppointments = fetch_count($connection, 'SELECT COUNT(*) FROM appointments');
$totalMessages = fetch_count($connection, 'SELECT COUNT(*) FROM messages');

$unreadCount = 0;
$recentMessages = [];
if ($connection && isset($_SESSION['user_id'])) {
    $unreadCount = get_unread_count($_SESSION['user_id']);
    $recentMessages = fetch_inbox($_SESSION['user_id']);
    if (!empty($recentMessages)) {
        $recentMessages = array_slice($recentMessages, 0, 5);
    }
}

include 'header.php';
?>
<section class="page-panel">
    <div class="page-title">Admin Dashboard</div>
    <p class="content-panel">Welcome back, <?php echo e($user['first_name'] . ' ' . $user['last_name']); ?>. Monitor system activity, manage users, and track core performance metrics from here.</p>

    <div class="summary-grid">
        <div class="summary-card">
            <strong><?php echo $totalPatients; ?></strong>
            <span>Registered patients</span>
        </div>
        <div class="summary-card">
            <strong><?php echo $totalDoctors; ?></strong>
            <span>Active doctors</span>
        </div>
        <div class="summary-card">
            <strong><?php echo $totalAppointments; ?></strong>
            <span>Scheduled appointments</span>
        </div>
        <div class="summary-card">
            <strong><?php echo $totalMessages; ?></strong>
            <span>Inbox messages</span>
        </div>
        <div class="summary-card">
            <strong><?php echo (int)$unreadCount; ?></strong>
            <span>Your unread messages</span>
        </div>
    </div>

    <div class="content-panel">
        <div class="page-actions">
            <a class="button primary-button" href="manage_users.php">Manage Users</a>
            <a class="button outline-button" href="inbox.php">View Inbox</a>
            <a class="button outline-button" href="financials.php">Financial Overview</a>
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
    </div>
</section>
<?php include 'footer.php'; ?>