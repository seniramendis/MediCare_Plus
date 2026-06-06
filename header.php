<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userRole   = isset($_SESSION['user_role']) ? strtolower($_SESSION['user_role'])
            : (isset($_SESSION['role'])      ? strtolower($_SESSION['role']) : '');
$dashboardLink = $userRole ? "dashboard_{$userRole}.php" : "Login.php";

// Unread count for nav badge (only when logged in)
$navUnread = 0;
if ($isLoggedIn) {
    try {
        $navUnread = get_unread_count($_SESSION['user_id']);
    } catch (Throwable $e) {
        $navUnread = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') : 'MediCare Plus'; ?></title>
    <link rel="stylesheet" href="assets/css/HomeStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <a href="Home.php" class="logo-link">
            <span class="logo-icon"><i class="fas fa-heartbeat"></i></span>
            <span class="logo-text">MediCare<span class="logo-plus"> Plus</span></span>
        </a>
        <nav class="main-nav">
            <a href="Home.php"><i class="fas fa-home"></i> Home</a>
            <a href="services.php"><i class="fas fa-stethoscope"></i> Services</a>
            <a href="doctors.php"><i class="fas fa-user-md"></i> Doctors</a>
            <a href="blog.php"><i class="fas fa-newspaper"></i> Blog</a>
            <?php if ($isLoggedIn): ?>
                <a href="<?php echo htmlspecialchars($dashboardLink); ?>" class="nav-dashboard">
                    <i class="fas fa-th-large"></i> Dashboard
                    <?php if ($navUnread > 0): ?>
                        <span class="nav-badge"><?php echo (int)$navUnread; ?></span>
                    <?php endif; ?>
                </a>
                <a href="logout.php" class="nav-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="Login.php" class="nav-login"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php" class="nav-signup"><i class="fas fa-user-plus"></i> Sign Up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="site-main">
