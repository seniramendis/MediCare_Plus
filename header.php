<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db_connect.php';

$isLoggedIn    = isset($_SESSION['user_id']);
$userRole      = $_SESSION['role'] ?? ($_SESSION['user_role'] ?? 'guest');
$dashboardLink = $isLoggedIn ? "dashboard_" . strtolower($userRole) . ".php" : "Login.php";
$currentPage   = basename($_SERVER['PHP_SELF']);
function navActive($page, $current) { return strpos($current, $page) !== false ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'MediCare Plus' ?></title>
    <link rel="stylesheet" href="assets/css/HomeStyles.css?v=<?= filemtime(__DIR__.'/assets/css/HomeStyles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; }
        /* Mobile nav */
        .hamburger { display:none; background:none; border:none; cursor:pointer; padding:8px; flex-direction:column; gap:5px; }
        .hamburger span { display:block; width:24px; height:2px; background:var(--primary-dark); border-radius:2px; transition:all .3s; }
        .hamburger.open span:nth-child(1) { transform:rotate(45deg) translate(5px,5px); }
        .hamburger.open span:nth-child(2) { opacity:0; }
        .hamburger.open span:nth-child(3) { transform:rotate(-45deg) translate(5px,-5px); }
        @media(max-width:768px){
            .hamburger { display:flex; }
            .nav-links {
                display:none; position:fixed; inset:70px 0 0 0; background:rgba(255,255,255,.98);
                flex-direction:column; align-items:center; justify-content:center;
                gap:20px; z-index:999; backdrop-filter:blur(10px);
            }
            .nav-links.open { display:flex; }
            .nav-links a { font-size:1.2rem; }
        }
        .nav-links a.active { color:var(--secondary-blue) !important; }
    </style>
</head>
<body>
<header class="main-header">
    <div class="header-container">
        <a href="Home.php" class="logo">
            <i class="fas fa-heartbeat"></i> MediCare Plus
        </a>
        <nav class="nav-links" id="navLinks">
            <a href="Home.php" class="<?= navActive('Home',$currentPage) ?>">Home</a>
            <a href="services.php" class="<?= navActive('services',$currentPage) ?>">Services</a>
            <a href="doctors.php" class="<?= navActive('doctors',$currentPage) ?>">Doctors</a>
            <a href="blog.php" class="<?= navActive('blog',$currentPage) ?>">Blog</a>
            <?php if ($isLoggedIn): ?>
                <a href="<?= htmlspecialchars($dashboardLink) ?>" class="btn-dashboard <?= navActive('dashboard',$currentPage) ?>">
                    <i class="fas fa-user-circle"></i> Dashboard
                </a>
                <a href="logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="Login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-signup">Sign Up</a>
            <?php endif; ?>
        </nav>
        <button class="hamburger" id="hamburger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
<main style="min-height:70vh;">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({duration:850, easing:'ease-in-out-quart', once:true, offset:50});
    const ham = document.getElementById('hamburger');
    const nav = document.getElementById('navLinks');
    ham.addEventListener('click', () => { ham.classList.toggle('open'); nav.classList.toggle('open'); });
    nav.querySelectorAll('a').forEach(a => a.addEventListener('click', () => { ham.classList.remove('open'); nav.classList.remove('open'); }));
</script>
