<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : 'Guest';
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';

$primaryLinks = [
    'Home' => 'Home.php',
    'Services' => 'services.php',
    'Doctors' => 'doctors.php',
    'Blog' => 'blog.php',
    'Contact' => 'contact.php',
];

$roleLinks = [];
if ($userRole === 'admin') {
    $roleLinks = [
        'Dashboard' => 'dashboard_admin.php',
        'Manage Users' => 'manage_users.php',
        'Inbox' => 'inbox.php',
        'Finance' => 'financials.php',
        'Upload Report' => 'upload_report.php',
        'Create Blog' => 'create_blog.php',
    ];
} elseif ($userRole === 'doctor') {
    $roleLinks = [
        'Dashboard' => 'dashboard_doctor.php',
        'Appointments' => 'appointments.php',
        'Messages' => 'chat_engine.php',
        'Records' => 'medical_records.php',
        'Upload Report' => 'upload_report.php',
        'Create Blog' => 'create_blog.php',
    ];
} elseif ($userRole === 'patient') {
    $roleLinks = [
        'My Appointments' => 'appointments.php',
        'Reports' => 'medical_reports.php',
        'Messages' => 'chat_engine.php',
        'Feedback' => 'feedback.php',
    ];
}

$authLink = [];
if (isset($_SESSION['user_id'])) {
    $authLink = ['Logout' => 'logout.php'];
} else {
    $authLink = ['Login' => 'Login.php', 'Register' => 'register.php'];
}

$pageTitle = isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') : 'MediCare Plus | Sri Lanka\'s Healthcare Platform';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MediCare Plus - Sri Lanka's premier digital healthcare platform. Book appointments with specialists, access medical records, and receive trusted medical care in LKR.">
    <meta name="keywords" content="healthcare, doctors, Sri Lanka, appointments, medical records, LKR">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="assets/css/HomeStyles.css">
</head>

<body>
    <header class="site-header">
        <div class="branding-bar">
            <div class="logo-block">
                <a href="Home.php" class="logo-link">
                    <img src="assets/images/logo.svg" alt="MediCare Plus Logo" class="site-logo">
                    <div>
                        <span class="brand-name">MediCare Plus 🇱🇰</span>
                        <span class="brand-tagline">Sri Lanka's trusted healthcare platform</span>
                    </div>
                </a>
            </div>
            <div class="user-status">
                <span>Welcome, <strong><?php echo $userName; ?></strong></span>
                <?php if ($userRole !== 'guest'): ?>
                    <span class="role-pill"><?php echo ucfirst($userRole); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <nav class="primary-nav" aria-label="Primary navigation">
            <ul class="nav-list">
                <?php foreach ($primaryLinks as $label => $href): ?>
                    <li><a href="<?php echo $href; ?>"><?php echo $label; ?></a></li>
                <?php endforeach; ?>
                <?php foreach ($roleLinks as $label => $href): ?>
                    <li>
                        <a href="<?php echo $href; ?>"><?php echo $label; ?></a>
                        <?php if (isset($_SESSION['user_id']) && $href === 'chat_engine.php'): ?>
                            <span id="msg-unread-count" class="msg-badge" style="display:none">0</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <ul class="auth-list">
                <?php foreach ($authLink as $label => $href): ?>
                    <li><a class="button secondary-button" href="<?php echo $href; ?>"><?php echo $label; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div id="toast-container" aria-live="polite" aria-atomic="true"></div>
        <script>
            (function() {
                var previousCount = null;

                function showToast(message, href) {
                    var container = document.getElementById('toast-container');
                    if (!container) return;
                    var toast = document.createElement('div');
                    toast.className = 'toast-notification';
                    var link = document.createElement('a');
                    link.href = href || 'chat_engine.php';
                    link.textContent = message;
                    toast.appendChild(link);
                    container.appendChild(toast);
                    setTimeout(function() {
                        toast.classList.add('visible');
                    }, 10);
                    setTimeout(function() {
                        toast.classList.remove('visible');
                        setTimeout(function() {
                            if (container.contains(toast)) container.removeChild(toast);
                        }, 350);
                    }, 7000);
                }

                function updateUnread() {
                    fetch('unread_count.php', {
                            credentials: 'same-origin'
                        })
                        .then(function(res) {
                            return res.json();
                        })
                        .then(function(data) {
                            var badge = document.getElementById('msg-unread-count');
                            if (badge && data && data.ok) {
                                var n = parseInt(data.count, 10) || 0;
                                if (previousCount === null) {
                                    previousCount = n;
                                } else if (n > previousCount) {
                                    var diff = n - previousCount;
                                    showToast('You have ' + diff + ' new message' + (diff > 1 ? 's' : ''), 'chat_engine.php');
                                    previousCount = n;
                                } else {
                                    previousCount = n;
                                }

                                if (n > 0) {
                                    badge.textContent = n;
                                    badge.style.display = 'inline-block';
                                } else {
                                    badge.style.display = 'none';
                                }
                            }
                        }).catch(function() {});
                }

                if (document.getElementById('msg-unread-count')) {
                    updateUnread();
                    setInterval(updateUnread, 15000);
                }
            })();
        </script>
    </header>

    <main class="main-content">