<?php
require_once 'auth.php';
require_role('patient');

$pageTitle = 'Payment';
include 'header.php';

$appointmentId = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;

// Fetch appointment from DB for authoritative data
$appointment = $appointmentId ? fetch_appointment_by_id($appointmentId) : null;

// Security: patient may only pay for their own confirmed appointment
$currentUser = current_user();
if (
    !$appointment
    || (int)$appointment['patient_user_id'] !== (int)$currentUser['id']
    || !in_array($appointment['status'], ['confirmed'], true)
) {
    include 'header.php';
    echo '<div class="page-panel"><div class="content-panel"><div class="empty-state"><i class="fas fa-ban"></i><p>This appointment is not available for payment.</p><a href="dashboard_patient.php" class="button primary-button">Back to Dashboard</a></div></div></div>';
    include 'footer.php';
    exit;
}

$amount     = (float)$appointment['consultation_fee'];
$doctorName = 'Dr. ' . $appointment['doctor_name'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW) ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }

    $paymentMethod = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
    $cardName      = isset($_POST['card_name'])      ? trim($_POST['card_name'])      : '';
    $cardNumber    = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');

    if (!$paymentMethod || !$cardName || !$cardNumber) {
        $error = 'Please fill in all payment details.';
    } elseif (strlen($cardNumber) < 13 || strlen($cardNumber) > 19 || !ctype_digit($cardNumber)) {
        $error = 'Please enter a valid card number.';
    } else {
        // Mark appointment as completed in the database
        $updated = update_appointment_status($appointmentId, 'completed');
        if ($updated) {
            // Insert into payments table so doctor sees revenue
            $db = get_db_connection();
            $patientRow = fetch_patient_by_user_id($currentUser['id']);
            $patientId  = $patientRow ? (int)$patientRow['id'] : 0;
            $doctorId   = (int)$appointment['doctor_id'];
            $fee        = (float)$appointment['consultation_fee'];
            $pstmt = $db->prepare(
                "INSERT INTO payments (patient_id, doctor_id, amount, payment_method, status, description) VALUES (?, ?, ?, ?, 'paid', 'Medical Consultation')"
            );
            if ($pstmt) {
                $pstmt->bind_param('iids', $patientId, $doctorId, $fee, $paymentMethod);
                $pstmt->execute();
                $pstmt->close();
            }
            $success = true;
        } else {
            $error = 'Payment could not be recorded. Please try again.';
        }
    }
}
?>

<style>
    .pay-wrapper {
        min-height: 80vh;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f4fd 100%);
        padding: 48px 20px;
    }

    .pay-card {
        max-width: 560px;
        margin: 0 auto;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(43, 108, 176, .12);
        overflow: hidden;
    }

    .pay-header {
        background: linear-gradient(135deg, #1a56db 0%, #2b6cb0 100%);
        padding: 32px 40px 28px;
        color: #fff;
    }

    .pay-header .ph-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, .15);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 14px;
    }

    .pay-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .pay-header p {
        margin: 0;
        opacity: .85;
        font-size: .9rem;
    }

    .pay-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f0f7ff;
        border: 1px solid #bee3f8;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 28px;
    }

    .pay-summary .ps-info {
        font-size: .88rem;
        color: #4a5568;
    }

    .pay-summary .ps-info strong {
        display: block;
        color: #2d3748;
        font-size: .95rem;
    }

    .pay-summary .ps-amount {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1a56db;
    }

    .pay-body {
        padding: 36px 40px 40px;
    }

    .pay-label {
        display: block;
        font-size: .8rem;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 7px;
    }

    .pay-label .req {
        color: #e53e3e;
        margin-left: 2px;
    }

    .pay-input,
    .pay-select {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: .95rem;
        color: #2d3748;
        background: #f8fafc;
        box-sizing: border-box;
        transition: border-color .2s, box-shadow .2s;
        font-family: inherit;
    }

    .pay-input:focus,
    .pay-select:focus {
        outline: none;
        border-color: #1a56db;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(26, 86, 219, .1);
    }

    .pay-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .pay-fg {
        margin-bottom: 20px;
    }

    .pay-card-icons {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
    }

    .pay-card-icons img {
        height: 28px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    .pay-btn {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #1a56db, #2b6cb0);
        color: #fff;
        font-size: 1rem;
        font-weight: 700;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: opacity .2s, transform .15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 8px;
    }

    .pay-btn:hover {
        opacity: .9;
        transform: translateY(-1px);
    }

    .pay-cancel {
        display: block;
        text-align: center;
        margin-top: 14px;
        color: #718096;
        font-size: .88rem;
        text-decoration: none;
    }

    .pay-cancel:hover {
        color: #2d3748;
    }

    .pay-secure {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 20px;
        font-size: .78rem;
        color: #a0aec0;
    }

    .pay-secure i {
        color: #38a169;
    }

    /* Alerts */
    .pay-alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 22px;
        font-size: .9rem;
        font-weight: 500;
    }

    .pay-alert-success {
        background: #f0fff4;
        color: #276749;
        border: 1px solid #9ae6b4;
    }

    .pay-alert-error {
        background: #fff5f5;
        color: #9b2c2c;
        border: 1px solid #feb2b2;
    }

    /* Success state */
    .pay-success-body {
        padding: 48px 40px;
        text-align: center;
    }

    .pay-success-body .tick {
        width: 70px;
        height: 70px;
        background: #f0fff4;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #38a169;
        margin: 0 auto 20px;
    }

    .pay-success-body h2 {
        color: #276749;
        margin: 0 0 8px;
    }

    .pay-success-body p {
        color: #4a5568;
        margin: 0 0 6px;
    }

    .pay-success-body .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 28px;
        background: linear-gradient(135deg, #1a56db, #2b6cb0);
        color: #fff;
        padding: 12px 28px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: .95rem;
        transition: opacity .2s;
    }

    .pay-success-body .back-btn:hover {
        opacity: .9;
    }
</style>

<div class="pay-wrapper">
    <div class="pay-card">

        <div class="pay-header">
            <div class="ph-icon"><i class="fas fa-credit-card"></i></div>
            <h1>Secure Payment</h1>
            <p>Complete your consultation fee payment below.</p>
        </div>

        <?php if ($success): ?>
            <div class="pay-success-body">
                <div class="tick"><i class="fas fa-check"></i></div>
                <h2>Payment Successful!</h2>
                <p>Your appointment with <strong><?php echo e($doctorName); ?></strong> is now confirmed.</p>
                <p style="color:#718096;">Amount paid: <strong style="color:#1a56db;">LKR <?php echo number_format($amount, 0); ?></strong></p>
                <a href="dashboard_patient.php" class="back-btn"><i class="fas fa-home"></i> Back to Dashboard</a>
            </div>

        <?php else: ?>
            <div class="pay-body">

                <!-- Summary -->
                <div class="pay-summary">
                    <div class="ps-info">
                        <strong><?php echo e($doctorName); ?></strong>
                        <?php echo e($appointment['specialization']); ?> &mdash;
                        <?php echo e(date('M j, Y H:i', strtotime($appointment['appointment_date']))); ?>
                    </div>
                    <div class="ps-amount">LKR <?php echo number_format($amount, 0); ?></div>
                </div>

                <?php if ($error): ?>
                    <div class="pay-alert pay-alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo e($error); ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                    <!-- Payment Method -->
                    <div class="pay-fg">
                        <label class="pay-label" for="payment_method">Payment Method <span class="req">*</span></label>
                        <select id="payment_method" name="payment_method" class="pay-select" required>
                            <option value="">— Select method —</option>
                            <option value="credit">Credit Card</option>
                            <option value="debit">Debit Card</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>

                    <!-- Card Icons -->
                    <div class="pay-card-icons">
                        <svg width="44" height="28" viewBox="0 0 44 28" fill="none" xmlns="http://www.w3.org/2000/svg" style="border-radius:4px;border:1px solid #e2e8f0;">
                            <rect width="44" height="28" fill="#1A1F71" />
                            <circle cx="17" cy="14" r="8" fill="#EB001B" />
                            <circle cx="27" cy="14" r="8" fill="#F79E1B" />
                            <path d="M22 8.3a8 8 0 0 1 0 11.4A8 8 0 0 1 22 8.3z" fill="#FF5F00" />
                        </svg>
                        <svg width="44" height="28" viewBox="0 0 44 28" fill="none" xmlns="http://www.w3.org/2000/svg" style="border-radius:4px;border:1px solid #e2e8f0;">
                            <rect width="44" height="28" fill="#016FD0" /><text x="5" y="19" font-size="11" font-weight="bold" fill="white" font-family="Arial">AMEX</text>
                        </svg>
                        <svg width="44" height="28" viewBox="0 0 44 28" fill="none" xmlns="http://www.w3.org/2000/svg" style="border-radius:4px;border:1px solid #e2e8f0;">
                            <rect width="44" height="28" fill="#fff" /><text x="4" y="19" font-size="9" font-weight="bold" fill="#231F20" font-family="Arial">VISA</text>
                        </svg>
                    </div>

                    <!-- Cardholder Name -->
                    <div class="pay-fg">
                        <label class="pay-label" for="card_name">Cardholder Name <span class="req">*</span></label>
                        <input type="text" id="card_name" name="card_name" class="pay-input"
                            placeholder="Name as it appears on card" required autocomplete="cc-name">
                    </div>

                    <!-- Card Number -->
                    <div class="pay-fg">
                        <label class="pay-label" for="card_number">Card Number <span class="req">*</span></label>
                        <input type="text" id="card_number" name="card_number" class="pay-input"
                            placeholder="1234  5678  9012  3456"
                            maxlength="19" required autocomplete="cc-number"
                            oninput="formatCard(this)">
                    </div>

                    <!-- Expiry + CVV -->
                    <div class="pay-row">
                        <div class="pay-fg">
                            <label class="pay-label" for="expiry">Expiry Date <span class="req">*</span></label>
                            <input type="text" id="expiry" class="pay-input"
                                placeholder="MM / YY" maxlength="7" required autocomplete="cc-exp"
                                oninput="formatExpiry(this)">
                        </div>
                        <div class="pay-fg">
                            <label class="pay-label" for="cvv">CVV <span class="req">*</span></label>
                            <input type="text" id="cvv" class="pay-input"
                                placeholder="•••" maxlength="4" required autocomplete="cc-csc"
                                oninput="this.value=this.value.replace(/\D/g,'')">
                        </div>
                    </div>

                    <button type="submit" class="pay-btn">
                        <i class="fas fa-lock"></i> Pay LKR <?php echo number_format($amount, 0); ?>
                    </button>
                </form>

                <a href="dashboard_patient.php" class="pay-cancel">← Cancel &amp; return to dashboard</a>

                <div class="pay-secure">
                    <i class="fas fa-shield-alt"></i>
                    <span>This is a demonstration payment form. No actual charge is made. For production, integrate Stripe or PayHere.</span>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    function formatCard(el) {
        let v = el.value.replace(/\D/g, '').substring(0, 16);
        el.value = v.replace(/(.{4})/g, '$1  ').trim();
    }

    function formatExpiry(el) {
        let v = el.value.replace(/\D/g, '').substring(0, 4);
        if (v.length >= 3) v = v.substring(0, 2) + ' / ' + v.substring(2);
        el.value = v;
    }
</script>

<?php include 'footer.php'; ?>