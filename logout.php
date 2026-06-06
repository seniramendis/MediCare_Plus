<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token before destroying the session
    $submittedToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW) ?? '';
    $sessionToken   = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';

    if (!$sessionToken || !hash_equals($sessionToken, $submittedToken)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }

    // Clear session data
    $_SESSION = [];

    // Expire the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();

    http_response_code(302);
    header('Location: Login.php');
    exit(0);
}

// GET request — show a confirmation form to prevent CSRF logout via image/link
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Out | MediCare Plus</title>
    <link rel="stylesheet" href="assets/css/HomeStyles.css">
    <style>
        .logout-box {
            max-width: 400px;
            margin: 100px auto;
            text-align: center;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .logout-box h2 {
            color: #2d3748;
            margin-bottom: 15px;
        }

        .logout-box p {
            color: #718096;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>
    <div class="logout-box">
        <h2>Sign Out</h2>
        <p>Are you sure you want to sign out of MediCare Plus?</p>
        <form method="post" action="logout.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="button primary-button">Yes, sign me out</button>
            <a href="Home.php" class="button outline-button" style="margin-left:10px;">Cancel</a>
        </form>
    </div>
</body>

</html>