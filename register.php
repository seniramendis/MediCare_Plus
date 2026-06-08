<?php
session_start();
$error   = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (file_exists('db_connect.php')) {
        include 'db_connect.php';
        $full_name = trim($_POST['full_name']);
        $name_parts = explode(' ', $full_name, 2);
        $first_name = mysqli_real_escape_string($conn, $name_parts[0]);
        $last_name  = mysqli_real_escape_string($conn, $name_parts[1] ?? '');
        $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
        $password = $_POST['password'];
        $confirm  = $_POST['confirm_password'];

        if ($password !== $confirm) {
            $error = "Passwords do not match.";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long.";
        } else {
            $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
            if (mysqli_num_rows($check) > 0) {
                $error = "An account with this email already exists. <a href='Login.php'>Sign in instead?</a>";
            } else {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role, created_at)
                        VALUES ('$first_name','$last_name','$email','$hashed','patient', NOW())";
                if (mysqli_query($conn, $sql)) {
                    header("Location: Login.php?registered=1");
                    exit();
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Medicare Plus</title>
    <link rel="icon" href="images/Favicon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/HomeStyles.css?v=3.0">
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --navy: #0d2b5e;
            --teal: #0aa698;
            --teal-lt: #e6f7f6;
            --light-bg: #f5f8fa;
            --border: #e2e8f0;
            --text: #2d3748;
            --muted: #718096;
            --white: #fff;
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
   SPLIT SCREEN
   ================================================ */
        .auth-page {
            flex: 1;
            display: flex;
            min-height: calc(100vh - 72px);
        }

        .auth-visual {
            flex: 1;
            background: linear-gradient(160deg, #0a1e42 0%, var(--navy) 40%, #0f6e62 100%);
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
                radial-gradient(circle at 80% 15%, rgba(10, 166, 152, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 10% 85%, rgba(255, 255, 255, 0.04) 0%, transparent 40%);
        }

        .decor-circle {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.07);
        }

        .c1 {
            width: 280px;
            height: 280px;
            top: -80px;
            right: -80px;
        }

        .c2 {
            width: 160px;
            height: 160px;
            bottom: 80px;
            right: 80px;
        }

        .c3 {
            width: 100px;
            height: 100px;
            bottom: -20px;
            left: 80px;
            border-color: rgba(10, 166, 152, 0.18);
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
        }

        .auth-visual h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-weight: 700;
            color: #fff;
            line-height: 1.22;
            margin-bottom: 16px;
        }

        .auth-visual h2 em {
            color: var(--teal);
            font-style: normal;
        }

        .auth-visual p {
            font-size: 0.98rem;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.7;
            margin-bottom: 44px;
            max-width: 360px;
        }

        .steps-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .steps-list li {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.9rem;
        }

        .step-num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1.5px solid rgba(10, 166, 152, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--teal);
            font-size: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* ---- Form Panel ---- */
        .auth-form-panel {
            width: 500px;
            flex-shrink: 0;
            background: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 48px 44px 40px;
            overflow-y: auto;
        }

        .form-heading {
            margin-bottom: 26px;
        }

        .form-heading h1 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 6px;
        }

        .form-heading p {
            font-size: 0.9rem;
            color: var(--muted);
        }

        /* Decorative rule */
        .form-rule {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 22px 0 18px;
        }

        .form-rule span {
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            white-space: nowrap;
        }

        .form-rule::before,
        .form-rule::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* Error / Success */
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
            margin-top: 2px;
            flex-shrink: 0;
        }

        .error-msg a {
            color: #c53030;
            font-weight: 600;
        }

        .success-msg {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f0fff4;
            color: #276749;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            border: 1px solid #c6f6d5;
        }

        /* Form rows */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 0;
        }

        .input-group {
            margin-bottom: 16px;
        }

        .input-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
            letter-spacing: 0.2px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #aab4be;
            font-size: 0.85rem;
            pointer-events: none;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            height: 46px;
            padding: 0 38px;
            border: 1.5px solid var(--border);
            border-radius: 11px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.92rem;
            color: var(--text);
            background: var(--light-bg);
            transition: border 0.2s, box-shadow 0.2s, background 0.2s;
            appearance: none;
            -webkit-appearance: none;
        }

        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(10, 166, 152, 0.12);
            background: var(--white);
        }

        /* Password strength bar */
        .pw-strength-bar {
            height: 4px;
            border-radius: 4px;
            background: var(--border);
            margin-top: 6px;
            overflow: hidden;
        }

        .pw-strength-fill {
            height: 100%;
            border-radius: 4px;
            width: 0%;
            transition: width 0.3s ease, background 0.3s ease;
        }

        .pw-hint {
            font-size: 0.76rem;
            color: var(--muted);
            margin-top: 4px;
        }

        /* Toggle password */
        .toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #aab4be;
            font-size: 0.88rem;
            padding: 0;
            transition: color 0.2s;
        }

        .toggle-pw:hover {
            color: var(--teal);
        }

        /* Terms checkbox */
        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
            margin-top: 4px;
        }

        .terms-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 2px;
            accent-color: var(--teal);
            cursor: pointer;
        }

        .terms-row label {
            font-size: 0.84rem;
            color: var(--muted);
            line-height: 1.5;
        }

        .terms-row a {
            color: var(--navy);
            font-weight: 600;
            text-decoration: none;
        }

        .terms-row a:hover {
            color: var(--teal);
        }

        /* Submit */
        .auth-submit {
            width: 100%;
            height: 50px;
            background: var(--navy);
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
        }

        .auth-submit:hover {
            background: var(--teal);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(10, 166, 152, 0.3);
        }

        .auth-foot {
            margin-top: 18px;
            text-align: center;
            font-size: 0.88rem;
            color: var(--muted);
        }

        .auth-foot a {
            color: var(--navy);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-foot a:hover {
            color: var(--teal);
        }

        /* ================================================
   RESPONSIVE
   ================================================ */
        @media (max-width: 980px) {
            .auth-visual {
                display: none;
            }

            .auth-form-panel {
                width: 100%;
                max-width: 520px;
                margin: 0 auto;
            }

            .auth-page {
                justify-content: center;
                padding: 30px 16px;
                align-items: flex-start;
            }
        }

        @media (max-width: 500px) {
            .auth-form-panel {
                padding: 32px 22px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>

<body>
    <?php include 'nav_only.php'; ?>

    <div class="auth-page">

        <!-- LEFT VISUAL -->
        <div class="auth-visual">
            <div class="decor-circle c1"></div>
            <div class="decor-circle c2"></div>
            <div class="decor-circle c3"></div>

            <div class="auth-visual-inner">
                <div class="auth-logo-area">
                    <img src="images/Logo.png" alt="Medicare Plus" onerror="this.style.display='none'">
                    <span>Medicare Plus</span>
                </div>

                <h2>Start your health<br><em>journey today</em></h2>
                <p>Creating your account takes less than a minute. Then you can book appointments, access records, and chat with specialists.</p>

                <ul class="steps-list">
                    <li><span class="step-num">1</span> Create your secure account</li>
                    <li><span class="step-num">2</span> Find and book a specialist</li>
                    <li><span class="step-num">3</span> Receive care &amp; track your health</li>
                </ul>
            </div>
        </div>

        <!-- RIGHT FORM PANEL -->
        <div class="auth-form-panel">

            <div class="form-heading">
                <h1>Create your account</h1>
                <p>It's free and takes just a moment.</p>
            </div>

            <?php if ($error): ?>
                <div class="error-msg">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-msg">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" autocomplete="on" id="registerForm">

                <!-- Name & Phone -->
                <div class="form-row">
                    <div class="input-group">
                        <label for="full_name">Full Name</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input type="text" id="full_name" name="full_name" required placeholder="Jane Perera"
                                value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-phone input-icon"></i>
                            <input type="tel" id="phone" name="phone" placeholder="+94 77 123 4567"
                                value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" required placeholder="jane@example.com"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <!-- DOB & Gender -->
                <div class="form-row">
                    <div class="input-group">
                        <label for="dob">Date of Birth</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-calendar input-icon"></i>
                            <input type="date" id="dob" name="dob"
                                value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>">
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="gender">Gender</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-venus-mars input-icon"></i>
                            <select id="gender" name="gender">
                                <option value="">Select…</option>
                                <option value="Male" <?php echo (($_POST['gender'] ?? '') == 'Male')   ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (($_POST['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo (($_POST['gender'] ?? '') == 'Other')  ? 'selected' : ''; ?>>Other</option>
                                <option value="Prefer not to say">Prefer not to say</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-rule"><span>Set a password</span></div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" required placeholder="At least 8 characters" oninput="checkStrength(this.value)">
                        <button type="button" class="toggle-pw" onclick="togglePw('password','pwIcon1')" aria-label="Toggle password">
                            <i class="fa-regular fa-eye" id="pwIcon1"></i>
                        </button>
                    </div>
                    <div class="pw-strength-bar">
                        <div class="pw-strength-fill" id="strengthFill"></div>
                    </div>
                    <p class="pw-hint" id="strengthHint">Use 8+ characters with letters &amp; numbers</p>
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repeat password">
                        <button type="button" class="toggle-pw" onclick="togglePw('confirm_password','pwIcon2')" aria-label="Toggle confirm password">
                            <i class="fa-regular fa-eye" id="pwIcon2"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms -->
                <div class="terms-row">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a> of Medicare Plus</label>
                </div>

                <button type="submit" class="auth-submit">
                    <i class="fa-solid fa-user-plus"></i>
                    Create My Account
                </button>
            </form>

            <div class="auth-foot" style="margin-top:20px;">
                Already have an account? <a href="Login.php">Sign in →</a>
            </div>

        </div><!-- /auth-form-panel -->
    </div><!-- /auth-page -->

    <?php include 'footer_bare.php'; ?>

    <script>
        function togglePw(id, iconId) {
            var f = document.getElementById(id);
            var i = document.getElementById(iconId);
            if (f.type === 'password') {
                f.type = 'text';
                i.className = 'fa-regular fa-eye-slash';
            } else {
                f.type = 'password';
                i.className = 'fa-regular fa-eye';
            }
        }

        function checkStrength(val) {
            var fill = document.getElementById('strengthFill');
            var hint = document.getElementById('strengthHint');
            if (!fill) return;
            var score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            var pct = ['0%', '30%', '55%', '80%', '100%'][score];
            var color = ['', '#e53e3e', '#dd6b20', '#d69e2e', '#38a169'][score];
            var msg = ['', 'Weak — add uppercase letters', 'Fair — add a number', 'Good — add a symbol', 'Strong password ✓'][score];
            fill.style.width = pct;
            fill.style.background = color;
            hint.textContent = msg || 'Use 8+ characters with letters & numbers';
            hint.style.color = color || 'var(--muted)';
        }
    </script>
</body>

</html>