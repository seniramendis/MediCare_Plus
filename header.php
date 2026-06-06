<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Safely check if logged in and grab the role without throwing errors
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';

// Generate the dashboard link safely
$dashboardLink = $userRole ? "dashboard_" . $userRole . ".php" : "Login.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/HomeStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header style="background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 15px 5%;">
        <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
            <a href="Home.php" style="font-size: 1.5rem; font-weight: bold; color: #2b6cb0; text-decoration: none;">
                <i class="fas fa-heartbeat" style="color: #e53e3e;"></i> MediCare Plus
            </a>

            <nav style="display: flex; gap: 20px;">
                <a href="Home.php" style="color: #4a5568; text-decoration: none; font-weight: bold;">Home</a>
                <a href="services.php" style="color: #4a5568; text-decoration: none; font-weight: bold;">Services</a>
                <a href="doctors.php" style="color: #4a5568; text-decoration: none; font-weight: bold;">Doctors</a>
                <a href="blog.php" style="color: #4a5568; text-decoration: none; font-weight: bold;">Blog</a>

                <?php if ($isLoggedIn): ?>
                    <!-- Safely inject the dashboard link without breaking HTML -->
                    <a href="<?php echo htmlspecialchars($dashboardLink); ?>" style="color: #2b6cb0; text-decoration: none; font-weight: bold;"><i class="fas fa-user-circle"></i> Dashboard</a>
                    <a href="logout.php" style="color: #e53e3e; text-decoration: none; font-weight: bold;">Logout</a>
                <?php else: ?>
                    <a href="Login.php" style="color: #2b6cb0; text-decoration: none; font-weight: bold;">Login</a>
                    <a href="register.php" style="background: #2b6cb0; color: #fff; padding: 5px 15px; border-radius: 20px; text-decoration: none; font-weight: bold;">Sign Up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>