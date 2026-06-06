<?php
require_once 'auth.php';
require_login();
$pageTitle = 'Messages | MediCare Plus';
$user = current_user();
$inbox = fetch_inbox($user['id']);

include 'header.php';
?>
<section class="page-panel">
    <div class="page-title">Inbox</div>
    <div class="content-panel">
        <p>Your secure messages with doctors and patients.</p>
        <div class="page-actions">
            <a class="button primary-button" href="compose_message.php">Compose</a>
        </div>
    </div>

    <div class="content-panel">
        <?php if (empty($inbox)): ?>
            <p>You have no messages.</p>
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
                    <?php foreach ($inbox as $msg): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($msg['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($msg['sent_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><a href="chat_engine.php?view_user=<?php echo (int)$msg['sender_id']; ?>">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['view_user']) && is_numeric($_GET['view_user'])): ?>
        <?php
        $other     = (int) $_GET['view_user'];
        $otherUser = fetch_user_by_id($other);
        ?>
        <?php if ($otherUser): ?>
            <?php
            $conversation = fetch_conversation($user['id'], $other);
            mark_conversation_read($user['id'], $other);
            ?>
            <div class="content-panel">
                <h3>Conversation with <?php echo htmlspecialchars($otherUser['first_name'] . ' ' . $otherUser['last_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <div class="conversation">
                    <?php if (empty($conversation)): ?>
                        <p>No messages yet. Use compose to send the first message.</p>
                    <?php else: ?>
                        <?php foreach ($conversation as $m): ?>
                            <div class="message <?php echo htmlspecialchars(((int)$m['sender_id'] === (int)$user['id']) ? 'outgoing' : 'incoming', ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="message-meta">
                                    <strong><?php echo htmlspecialchars($m['sender_first'] . ' ' . $m['sender_last'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    &bull;
                                    <?php echo htmlspecialchars(date('M j, Y H:i', strtotime($m['sent_at'])), ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                                <div class="message-body"><?php echo nl2br(htmlspecialchars($m['body'], ENT_QUOTES, 'UTF-8')); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="page-actions">
                    <a class="button primary-button" href="compose_message.php?to=<?php echo (int)$other; ?>">Reply</a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?php include 'footer.php'; ?>
