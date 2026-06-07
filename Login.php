<?php
session_start();
include 'db_connect.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $password   = $_POST['password'];
    $login_type = $_POST['login_type'];

    $sql    = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            if ($user['role'] == 'admin') {
                $error = "Security Alert: Admins must use the <a href='admin_login.php'>Admin Portal</a>.";
            } elseif ($login_type == 'doctor' && $user['role'] != 'doctor') {
                $error = "Access Denied. You are not authorized to access the Doctor Portal.";
            } elseif ($login_type == 'patient' && $user['role'] == 'doctor') {
                $error = "You are a Doctor. Please switch to the Doctor Login tab.";
            } else {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['full_name'];
                $_SESSION['role']     = $user['role'];
                if (isset($_SESSION['admin_id'])) unset($_SESSION['admin_id']);
                header("Location: " . ($user['role'] == 'doctor' ? "dashboard_doctor.php" : "dashboard_patient.php"));
                exit();
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Account not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Medicare Plus</title>
    <link rel="icon" href="images/Favicon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --navy: #0d2b5e;
            --teal: #0aa698;
            --teal-lt: #e6f7f6;
            --green: #2ecc71;
            --light-bg: #f5f8fa;
            --border: #e2e8f0;
            --text: #2d3748;
            --muted: #718096;
            --white: #fff;
            --doctor-accent: #0f6e62;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DM Sans', Arial, sans-serif;
            background: var(--light-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ================================================
   SPLIT SCREEN LAYOUT
   ================================================ */
        .auth-page {
            flex: 1;
            display: flex;
            min-height: calc(100vh - 72px);
        }

        /* Left panel — decorative */
        .auth-visual {
            flex: 1;
            background: linear-gradient(160deg, var(--navy) 0%, #1a4a8a 40%, #0f6e62 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 56px;
            position: relative;
            overflow: hidden;
        }

        .auth-visual::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 80% 20%, rgba(10, 166, 152, 0.22) 0%, transparent 50%),
                radial-gradient(circle at 10% 80%, rgba(255, 255, 255, 0.04) 0%, transparent 40%);
        }

        /* Floating decorative circles */
        .auth-visual .decor-circle {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .decor-circle.c1 {
            width: 280px;
            height: 280px;
            top: -80px;
            right: -80px;
        }

        .decor-circle.c2 {
            width: 180px;
            height: 180px;
            bottom: 60px;
            right: 60px;
        }

        .decor-circle.c3 {
            width: 120px;
            height: 120px;
            bottom: -30px;
            left: 60px;
            border-color: rgba(10, 166, 152, 0.2);
        }

        .auth-visual-inner {
            position: relative;
            z-index: 1;
        }

        .auth-logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 50px;
        }

        .auth-logo-area img {
            height: 40px;
        }

        .auth-logo-area span {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .auth-visual h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .auth-visual h2 em {
            color: var(--teal);
            font-style: normal;
        }

        .auth-visual p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.7;
            margin-bottom: 44px;
            max-width: 380px;
        }

        /* Trust badges */
        .trust-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .trust-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.92rem;
        }

        .trust-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--teal);
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        /* ---- Right panel — form ---- */
        .auth-form-panel {
            width: 440px;
            flex-shrink: 0;
            background: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 56px 48px;
            overflow-y: auto;
        }

        /* Tab switcher */
        .auth-tabs {
            display: flex;
            background: var(--light-bg);
            border-radius: 14px;
            padding: 4px;
            margin-bottom: 32px;
            gap: 2px;
        }

        .auth-tab {
            flex: 1;
            padding: 10px 0;
            border: none;
            border-radius: 11px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            background: transparent;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .auth-tab.active {
            background: var(--white);
            color: var(--navy);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        #tab-doctor.active {
            color: var(--doctor-accent);
        }

        /* Form header */
        .form-heading {
            margin-bottom: 28px;
        }

        .form-heading h1 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .form-heading p {
            font-size: 0.9rem;
            color: var(--muted);
        }

        /* Error message */
        .error-msg {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fff5f5;
            color: #c53030;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            border: 1px solid #fed7d7;
            line-height: 1.5;
        }

        .error-msg i {
            margin-top: 1px;
            flex-shrink: 0;
        }

        .error-msg a {
            color: #c53030;
            font-weight: 600;
        }

        /* Input groups */
        .input-group {
            margin-bottom: 18px;
        }

        .input-group label {
            display: block;
            font-size: 0.83rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 7px;
            letter-spacing: 0.2px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i.input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aab4be;
            font-size: 0.9rem;
            pointer-events: none;
        }

        .input-group input {
            width: 100%;
            height: 48px;
            padding: 0 42px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--text);
            background: var(--light-bg);
            transition: border 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(10, 166, 152, 0.12);
            background: var(--white);
        }

        /* Show/hide password toggle */
        .input-wrap .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #aab4be;
            font-size: 0.9rem;
            padding: 0;
            transition: color 0.2s;
        }

        .input-wrap .toggle-pw:hover {
            color: var(--teal);
        }

        /* Submit button */
        .auth-submit {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--white);
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 6px;
        }

        .auth-submit.patient-btn {
            background: var(--navy);
        }

        .auth-submit.patient-btn:hover {
            background: var(--teal);
            transform: translateY(-1px);
        }

        .auth-submit.doctor-btn {
            background: var(--doctor-accent);
        }

        .auth-submit.doctor-btn:hover {
            background: #0a5248;
            transform: translateY(-1px);
        }

        /* Footer links */
        .auth-foot {
            margin-top: 22px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }

        .auth-foot a {
            font-size: 0.88rem;
            color: var(--navy);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .auth-foot a:hover {
            color: var(--teal);
        }

        .auth-foot .divider {
            width: 40px;
            height: 1px;
            background: var(--border);
            margin: 2px auto;
        }

        /* Doctor-mode accent color overrides */
        body.doctor-mode .form-heading h1 {
            color: var(--doctor-accent);
        }

        body.doctor-mode .input-group input:focus {
            border-color: var(--doctor-accent);
            box-shadow: 0 0 0 3px rgba(15, 110, 98, 0.12);
        }

        /* ================================================
   RESPONSIVE
   ================================================ */
        @media (max-width: 920px) {
            .auth-visual {
                display: none;
            }

            .auth-form-panel {
                width: 100%;
                max-width: 480px;
                margin: 0 auto;
            }

            .auth-page {
                justify-content: center;
                padding: 30px 16px;
            }
        }

        @media (max-width: 480px) {
            .auth-form-panel {
                padding: 36px 28px;
            }
        }
    </style>
</head>

<body>
    <?php if (file_exists('header.php')) include 'header.php'; ?>

    <div class="auth-page">

        <!-- LEFT — Visual Panel -->
        <div class="auth-visual">
            <div class="decor-circle c1"></div>
            <div class="decor-circle c2"></div>
            <div class="decor-circle c3"></div>

            <div class="auth-visual-inner">
                <div class="auth-logo-area">
                    <img src="images/Logo.png" alt="Medicare Plus" onerror="this.style.display='none'">
                    <span>Medicare Plus</span>
                </div>

                <h2>Your health,<br>our <em>priority</em></h2>
                <p>Access your appointments, medical records, and specialist consultations — all from one secure place.</p>

                <ul class="trust-list">
                    <li>
                        <div class="trust-icon"><i class="fa-solid fa-shield-halved"></i></div>
                        <span>Secure & HIPAA-compliant platform</span>
                    </li>
                    <li>
                        <div class="trust-icon"><i class="fa-solid fa-calendar-check"></i></div>
                        <span>Book appointments 24/7 instantly</span>
                    </li>
                    <li>
                        <div class="trust-icon"><i class="fa-solid fa-file-medical"></i></div>
                        <span>Access digital prescriptions anytime</span>
                    </li>
                    <li>
                        <div class="trust-icon"><i class="fa-solid fa-comments"></i></div>
                        <span>Chat securely with your doctor</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- RIGHT — Form Panel -->
        <div class="auth-form-panel">

            <!-- Tabs -->
            <div class="auth-tabs" role="tablist">
                <button class="auth-tab active" id="tab-patient" role="tab" onclick="switchTab('patient')">
                    <i class="fa-solid fa-user"></i> Patient
                </button>
                <button class="auth-tab" id="tab-doctor" role="tab" onclick="switchTab('doctor')">
                    <i class="fa-solid fa-user-doctor"></i> Doctor
                </button>
            </div>

            <!-- Heading -->
            <div class="form-heading">
                <h1 id="formTitle">Welcome back</h1>
                <p id="formDesc">Sign in to access your patient dashboard and appointments.</p>
            </div>

            <!-- Error -->
            <?php if ($error): ?>
                <div class="error-msg">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="" autocomplete="on">
                <input type="hidden" name="login_type" id="loginType" value="patient">

                <div class="input-group">
                    <label for="emailField">Email Address</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope input-icon"></i>
                        <input type="email" id="emailField" name="email" required placeholder="name@example.com"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <div class="input-group">
                    <label for="passwordField">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="passwordField" name="password" required placeholder="Enter your password">
                        <button type="button" class="toggle-pw" onclick="togglePw()" aria-label="Show/hide password">
                            <i class="fa-regular fa-eye" id="pwIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-submit patient-btn" id="submitBtn">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span id="submitLabel">Sign In to My Account</span>
                </button>
            </form>

            <div class="auth-foot">
                <div id="registerBox">
                    <a href="register.php">Don't have an account? <span style="color:var(--teal)">Create one →</span></a>
                </div>
                <div class="divider"></div>
                <a href="admin_login.php" style="color:var(--muted); font-weight:400; font-size:0.8rem;">
                    <i class="fa-solid fa-shield-halved"></i> Admin Portal
                </a>
            </div>

        </div><!-- /auth-form-panel -->
    </div><!-- /auth-page -->

    <?php if (file_exists('footer.php')) include 'footer.php'; ?>

    <script>
        function switchTab(type) {
            var isDoctor = (type === 'doctor');
            document.getElementById('tab-patient').classList.toggle('active', !isDoctor);
            document.getElementById('tab-doctor').classList.toggle('active', isDoctor);
            document.getElementById('loginType').value = type;
            document.getElementById('formTitle').textContent = isDoctor ? 'Doctor Portal' : 'Welcome back';
            document.getElementById('formDesc').textContent = isDoctor ?
                'Authorized medical personnel only.' :
                'Sign in to access your patient dashboard and appointments.';
            document.getElementById('submitLabel').textContent = isDoctor ? 'Access Doctor Dashboard' : 'Sign In to My Account';
            var btn = document.getElementById('submitBtn');
            btn.className = 'auth-submit ' + (isDoctor ? 'doctor-btn' : 'patient-btn');
            document.getElementById('registerBox').style.display = isDoctor ? 'none' : 'block';
            document.body.classList.toggle('doctor-mode', isDoctor);
        }

        function togglePw() {
            var f = document.getElementById('passwordField');
            var icon = document.getElementById('pwIcon');
            if (f.type === 'password') {
                f.type = 'text';
                icon.className = 'fa-regular fa-eye-slash';
            } else {
                f.type = 'password';
                icon.className = 'fa-regular fa-eye';
            }
        }

        <?php if (isset($_POST['login_type']) && $_POST['login_type'] == 'doctor'): ?>
            switchTab('doctor');
        <?php endif; ?>
    </script>
</body>

</html>