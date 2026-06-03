<?php
session_start();
require_once 'db_connect.php';

$pageTitle = 'Login | MediCare Plus Sri Lanka';
$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: '');
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW) ?: '');

    if (!$email) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (!$password) {
        $errors[] = 'Please enter your password.';
    }

    if (empty($errors)) {
        $user = fetch_user_by_email($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: dashboard_admin.php');
                exit;
            }

            if ($user['role'] === 'doctor') {
                header('Location: dashboard_doctor.php');
                exit;
            }

            header('Location: appointments.php');
            exit;
        }

        $errors[] = 'Login failed. Check your email and password and try again.';
    }
}

include 'header.php';
?>
<section class="auth-panel">
    <div class="auth-card card-shadow">
        <h2>Sign in to MediCare Plus</h2>
        <p class="auth-intro">Secure login for patients, doctors, and administrators.</p>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['registered']) && $_GET['registered'] === '1'): ?>
            <div class="success-box">
                Your account was created successfully. Please login below.
            </div>
        <?php endif; ?>

        <form action="Login.php" method="post" class="auth-form" novalidate>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="button primary-button">Login</button>
        </form>

        <p class="auth-footer">New to MediCare Plus? <a href="register.php">Create an account</a> or contact support if you need help.</p>
    </div>
</section>
<?php include 'footer.php'; ?>