<?php
session_start();
include 'db_connect.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $sql    = "SELECT * FROM users WHERE email = '$email' AND role = 'admin'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: dashboard_admin.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Admin account not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — MediCare Plus</title>
    <link rel="icon" href="images/Favicon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/HomeStyles.css?v=3.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy: #0d2b5e; --teal: #0aa698; --light-bg: #f5f8fa;
            --border: #e2e8f0; --text: #2d3748; --muted: #718096; --white: #fff;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: var(--light-bg); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 16px; }
        .admin-card {
            background: var(--white); border-radius: 18px; box-shadow: 0 8px 40px rgba(13,43,94,.12);
            max-width: 440px; width: 100%; padding: 48px 44px;
        }
        .admin-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 32px; }
        .admin-logo i { color: var(--teal); font-size: 1.7rem; }
        .admin-logo span { font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--navy); font-weight: 700; }
        .badge { display: inline-block; background: rgba(13,43,94,.08); color: var(--navy); font-size: .72rem; font-weight: 600; letter-spacing: 1.4px; text-transform: uppercase; padding: 4px 12px; border-radius: 20px; margin-bottom: 16px; }
        h1 { font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--navy); margin-bottom: 6px; }
        p.sub { color: var(--muted); font-size: .9rem; margin-bottom: 32px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: .85rem; font-weight: 600; color: var(--text); margin-bottom: 7px; }
        input { width: 100%; padding: 12px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-size: .95rem; color: var(--text); transition: border-color .2s; }
        input:focus { outline: none; border-color: var(--teal); }
        .btn-admin { width: 100%; padding: 14px; background: var(--navy); color: #fff; font-size: 1rem; font-weight: 600; border: none; border-radius: 10px; cursor: pointer; transition: background .2s; margin-top: 8px; }
        .btn-admin:hover { background: #1a4a8a; }
        .error-box { background: #fff5f5; border: 1px solid #fed7d7; color: #c53030; border-radius: 10px; padding: 12px 16px; font-size: .88rem; margin-bottom: 20px; }
        .back-link { display: block; text-align: center; margin-top: 24px; color: var(--muted); font-size: .85rem; text-decoration: none; }
        .back-link:hover { color: var(--teal); }
    </style>
</head>
<body>
    <div class="admin-card">
        <div class="admin-logo">
            <i class="fas fa-heartbeat"></i>
            <span>MediCare Plus</span>
        </div>
        <span class="badge"><i class="fas fa-shield-alt"></i> &nbsp;Admin Portal</span>
        <h1>Admin Sign In</h1>
        <p class="sub">Restricted access — authorised personnel only.</p>

        <?php if ($error): ?>
            <div class="error-box"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="admin_login.php">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Admin Email</label>
                <input type="email" id="email" name="email" required placeholder="admin@medicareplus.lk">
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-admin"><i class="fas fa-sign-in-alt"></i> &nbsp;Sign In to Admin Panel</button>
        </form>

        <a href="Login.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to patient / doctor login</a>
    </div>
</body>
</html>
