<?php
session_start();
require_once 'db_connect.php';

$pageTitle = 'Join MediCare Plus | Sri Lanka\'s Healthcare Platform';
$errors = [];
$firstName = '';
$lastName = '';
$email = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW) ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Invalid CSRF token.';
        exit(0);
    }

    $firstName = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');
    $lastName = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: '');
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW) ?: '');
    $confirmPassword = trim(filter_input(INPUT_POST, 'confirm_password', FILTER_UNSAFE_RAW) ?: '');

    if (!$firstName) {
        $errors[] = 'Please enter your first name.';
    }

    if (!$lastName) {
        $errors[] = 'Please enter your last name.';
    }

    if (!$email) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Your password must be at least 8 characters long.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Password and confirm password must match.';
    }

    if (empty($errors) && user_exists_by_email($email)) {
        $errors[] = 'An account with that email already exists. Please login instead.';
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $userId = create_user($firstName, $lastName, $email, $passwordHash, 'patient');

        if ($userId !== false) {
            if (!create_patient_profile($userId)) {
                error_log('Failed to create patient profile for user: ' . $userId);
            }
            http_response_code(302);
            header('Location: Login.php?registered=1');
            exit(0);
        }

        $errors[] = 'Unable to register at this time. Please try again later.';
    }
}

include 'header.php';
?>
<section class="auth-panel">
    <div class="auth-card card-shadow">
        <h2>Create your patient account</h2>
        <p class="auth-intro">Register securely to book appointments, access reports, and communicate with doctors.</p>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="post" class="auth-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="form-group half-width">
                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="button primary-button">Register</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="Login.php">Sign in</a> to continue.</p>
    </div>
</section>
<?php include 'footer.php'; ?>