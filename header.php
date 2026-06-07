<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? ($_SESSION['user_role'] ?? 'guest');
$dashboardLink = $isLoggedIn ? "dashboard_" . strtolower($userRole) . ".php" : "Login.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare Plus</title>
    <!-- CRITICAL FIX: The ?v=time() forces the browser to drop the old cache -->
    <link rel="stylesheet" href="assets/css/HomeStyles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header class="main-header">
        <div class="header-container">
            <a href="Home.php" class="logo">
                <i class="fas fa-heartbeat"></i> MediCare Plus
            </a>
            <nav class="nav-links">
                <a href="Home.php">Home</a>
                <a href="services.php">Services</a>
                <a href="doctors.php">Doctors</a>
                <a href="blog.php">Blog</a>

                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo htmlspecialchars($dashboardLink); ?>" class="btn-dashboard"><i class="fas fa-user-circle"></i> Dashboard</a>
                    <a href="logout.php" class="btn-logout">Logout</a>
                <?php else: ?>
                    <a href="Login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-signup">Sign Up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>