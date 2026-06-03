<?php
require_once 'auth.php';
require_login();

$pageTitle = 'Payment';
include 'header.php';

$appointmentId = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;
$doctorName = isset($_GET['doctor']) ? trim($_GET['doctor']) : 'Doctor';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
    $cardName = isset($_POST['card_name']) ? trim($_POST['card_name']) : '';
    $cardNumber = isset($_POST['card_number']) ? trim($_POST['card_number']) : '';

    if (!$paymentMethod || !$cardName || !$cardNumber) {
        $error = 'Please fill in all payment details.';
    } elseif (strlen($cardNumber) < 13) {
        $error = 'Invalid card number.';
    } else {
        // Simulate payment processing (no actual charge)
        $success = true;
    }
}
?>

<section class="container">
    <h1>Payment for Appointment</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <h3>Payment Processed Successfully!</h3>
            <p>Your appointment with <?php echo e($doctorName); ?> has been confirmed.</p>
            <p>Amount paid: <strong>LKR <?php echo number_format($amount, 0); ?></strong></p>
            <a href="dashboard_patient.php" class="button">Back to Dashboard</a>
        </div>
    <?php else: ?>
        <div class="payment-card">
            <h2>Consultation Fee</h2>
            <p>Doctor: <strong><?php echo e($doctorName); ?></strong></p>
            <p class="amount">Amount: <span>LKR <?php echo number_format($amount, 0); ?></span></p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form method="post" class="form">
                <div class="form-group">
                    <label for="payment_method">Payment Method:</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">-- Select method --</option>
                        <option value="credit">Credit Card</option>
                        <option value="debit">Debit Card</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="card_name">Card Name:</label>
                    <input type="text" id="card_name" name="card_name" required>
                </div>

                <div class="form-group">
                    <label for="card_number">Card Number:</label>
                    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="expiry">Expiry:</label>
                        <input type="text" id="expiry" placeholder="MM/YY" required>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV:</label>
                        <input type="text" id="cvv" placeholder="123" maxlength="3" required>
                    </div>
                </div>

                <button type="submit" class="button">Pay LKR <?php echo number_format($amount, 0); ?></button>
                <a href="book_appointment.php" class="button secondary">Cancel</a>
            </form>
        </div>

        <p style="margin-top: 20px; font-size: 12px; color: #999;">
            This is a demonstration payment form. No actual charges will be made. For a production system, integrate with Stripe, PayPal, or your payment provider.
        </p>
    <?php endif; ?>
</section>

<style>
    .payment-card {
        max-width: 500px;
        margin: 0 auto;
        border: 1px solid #ddd;
        padding: 30px;
        border-radius: 8px;
        background: #f9f9f9;
    }

    .payment-card h2 {
        margin-top: 0;
    }

    .payment-card .amount {
        font-size: 20px;
        font-weight: bold;
        color: #2e7d32;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .alert {
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .button.secondary {
        background: #ccc;
        color: #333;
    }
</style>

<?php include 'footer.php'; ?>