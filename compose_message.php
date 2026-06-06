<?php
require_once 'auth.php';
require_login();
$pageTitle = 'Compose Message | MediCare Plus';
$user = current_user();
$errors = [];
$success = '';
$to = filter_input(INPUT_GET, 'to', FILTER_VALIDATE_INT);
$recipients = fetch_all_users();

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    $submittedToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW) ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }

    $recipientId = filter_input(INPUT_POST, 'recipient_id', FILTER_VALIDATE_INT);
    $subject = trim(filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');
    $body = trim(filter_input(INPUT_POST, 'body', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');

    if (!$recipientId) {
        $errors[] = 'Select a recipient.';
    }
    if (!$subject) {
        $errors[] = 'Enter a subject.';
    }
    if (!$body) {
        $errors[] = 'Enter a message body.';
    }

    if (empty($errors)) {
        if (send_message($user['id'], $recipientId, $subject, $body)) {
            $success = 'Message sent successfully.';
        } else {
            $errors[] = 'Unable to send message at this time.';
        }
    }
}

include 'header.php';
?>
<section class="page-panel">
    <div class="page-title">Compose Message</div>
    <div class="content-panel">
        <?php if ($success): ?>
            <div class="success-box"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul><?php foreach ($errors as $e) {
                        echo '<li>' . htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . '</li>';
                    } ?></ul>
            </div>
        <?php endif; ?>

        <form action="compose_message.php" method="post" class="booking-form">
            <div class="form-group">
                <label for="recipient_id">Recipient</label>
                <select id="recipient_id" name="recipient_id" required>
                    <option value="">Select recipient</option>
                    <?php foreach ($recipients as $r): if ($r['id'] == $user['id']) continue; ?>
                        <option value="<?php echo (int)$r['id']; ?>" <?php echo ($to && $to == $r['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name'] . ' (' . $r['role'] . ')', ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="body">Message</label>
                <textarea id="body" name="body" rows="6" required></textarea>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="button primary-button">Send</button>
        </form>
    </div>
</section>
<?php include 'footer.php'; ?>