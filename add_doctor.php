<?php
require_once 'auth.php';
require_role('admin');

$pageTitle = 'Add Doctor | MediCare Plus';
$user = current_user();
$errors = [];
$success = '';

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    $submittedToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW) ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }

    // Collect & sanitize inputs
    $firstName   = trim(filter_input(INPUT_POST, 'first_name',   FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');
    $lastName    = trim(filter_input(INPUT_POST, 'last_name',    FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');
    $email       = trim(filter_input(INPUT_POST, 'email',        FILTER_SANITIZE_EMAIL) ?: '');
    $password    = filter_input(INPUT_POST, 'password',          FILTER_UNSAFE_RAW) ?: '';
    $spec        = trim(filter_input(INPUT_POST, 'specialization', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');
    $qual        = trim(filter_input(INPUT_POST, 'qualifications', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');
    $years       = filter_input(INPUT_POST, 'experience_years',  FILTER_VALIDATE_INT);
    $fee         = filter_input(INPUT_POST, 'consultation_fee',  FILTER_VALIDATE_INT);
    $avail       = trim(filter_input(INPUT_POST, 'availability', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');
    $rating      = filter_input(INPUT_POST, 'rating',            FILTER_VALIDATE_FLOAT);

    // Validate
    if (!$firstName)                    $errors[] = 'First name is required.';
    if (!$lastName)                     $errors[] = 'Last name is required.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'A valid email address is required.';
    if (strlen($password) < 8)          $errors[] = 'Password must be at least 8 characters.';
    if (!$spec)                         $errors[] = 'Specialization is required.';
    if (!$qual)                         $errors[] = 'Qualifications are required.';
    if ($years === false || $years < 0) $errors[] = 'Experience years must be a non-negative number.';
    if ($fee === false || $fee < 0)     $errors[] = 'Consultation fee must be a non-negative number.';
    if (!$avail)                        $errors[] = 'Availability is required.';
    if ($rating === false || $rating < 0 || $rating > 5)
        $errors[] = 'Rating must be between 0 and 5.';

    if (empty($errors)) {
        // Check email not already taken
        if (user_exists_by_email($email)) {
            $errors[] = 'A user with that email already exists.';
        } else {
            // Create user account
            $newUserId = create_user($firstName, $lastName, $email, $password, 'doctor');

            if ($newUserId) {
                // Insert doctor profile
                $dbConn = get_db_connection();
                $stmt = $dbConn->prepare(
                    'INSERT INTO doctors (user_id, specialization, qualifications, experience_years, consultation_fee, availability, rating)
                     VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->bind_param('issiids', $newUserId, $spec, $qual, $years, $fee, $avail, $rating);
                if ($stmt->execute()) {
                    $success = "Dr. {$firstName} {$lastName} has been added successfully.";
                    // Reset form fields on success
                    $firstName = $lastName = $email = $password = $spec = $qual = $avail = '';
                    $years = $fee = 0;
                    $rating = 4.5;
                } else {
                    $errors[] = 'Doctor profile could not be saved: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $errors[] = 'User account could not be created.';
            }
        }
    }
}

// Default field values for fresh form
$firstName   = $firstName   ?? '';
$lastName    = $lastName    ?? '';
$email       = $email       ?? '';
$spec        = $spec        ?? '';
$qual        = $qual        ?? '';
$years       = $years       ?? '';
$fee         = $fee         ?? '';
$avail       = $avail       ?? '';
$rating      = $rating      ?? '4.5';

$specializations = [
    'Cardiology',
    'Dermatology',
    'Endocrinology',
    'ENT',
    'Gastroenterology',
    'General Practice',
    'Gynecology',
    'Hematology',
    'Infectious Diseases',
    'Nephrology',
    'Neurology',
    'Oncology',
    'Ophthalmology',
    'Orthopedics',
    'Orthopedic Surgery',
    'Pediatrics',
    'Psychiatry',
    'Pulmonology',
    'Rheumatology',
    'Urology',
    'Other',
];

include 'header.php';
?>

<style>
    .add-doctor-wrap {
        max-width: 780px;
        margin: 0 auto;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .form-grid .full-width {
        grid-column: 1 / -1;
    }

    .field-hint {
        font-size: 0.78rem;
        color: #888;
        margin-top: 4px;
    }

    .rating-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .rating-row input[type=range] {
        flex: 1;
        accent-color: var(--secondary-blue, #2563eb);
    }

    .rating-val {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--secondary-blue, #2563eb);
        min-width: 32px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 18px;
        color: var(--secondary-blue, #2563eb);
        font-weight: 500;
        text-decoration: none;
        font-size: 0.95rem;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .success-box {
        background: #d1fae5;
        border: 1px solid #6ee7b7;
        color: #065f46;
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .error-box {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 20px;
    }

    .error-box ul {
        margin: 6px 0 0 16px;
        padding: 0;
    }

    .section-divider {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #94a3b8;
        margin: 8px 0 4px;
        grid-column: 1 / -1;
        padding-bottom: 6px;
        border-bottom: 1px solid #e2e8f0;
    }
</style>

<section class="page-panel">
    <div class="add-doctor-wrap">
        <a class="back-link" href="dashboard_admin.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

        <div class="page-title">Add New Doctor</div>

        <div class="content-panel">
            <?php if ($success): ?>
                <div class="success-box">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                    &nbsp;<a href="doctors.php" style="margin-left:auto;font-weight:600;">View Doctors →</a>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <strong><i class="fas fa-exclamation-circle"></i> Please fix the following:</strong>
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="add_doctor.php" method="post" class="booking-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="form-grid">

                    <!-- Account section -->
                    <div class="section-divider"><i class="fas fa-user"></i> &nbsp;Account Details</div>

                    <div class="form-group">
                        <label for="first_name">First Name <span style="color:#e53e3e">*</span></label>
                        <input type="text" id="first_name" name="first_name" required
                            value="<?php echo htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="e.g. Chamara">
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name <span style="color:#e53e3e">*</span></label>
                        <input type="text" id="last_name" name="last_name" required
                            value="<?php echo htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="e.g. Perera">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address <span style="color:#e53e3e">*</span></label>
                        <input type="email" id="email" name="email" required
                            value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="doctor@medicareplus.lk">
                    </div>

                    <div class="form-group">
                        <label for="password">Temporary Password <span style="color:#e53e3e">*</span></label>
                        <input type="password" id="password" name="password" required
                            placeholder="Min. 8 characters" autocomplete="new-password">
                        <div class="field-hint">The doctor can change this after first login.</div>
                    </div>

                    <!-- Professional section -->
                    <div class="section-divider"><i class="fas fa-stethoscope"></i> &nbsp;Professional Details</div>

                    <div class="form-group">
                        <label for="specialization">Specialization <span style="color:#e53e3e">*</span></label>
                        <select id="specialization" name="specialization" required>
                            <option value="">— Select specialization —</option>
                            <?php foreach ($specializations as $s): ?>
                                <option value="<?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo ($spec === $s) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="experience_years">Years of Experience <span style="color:#e53e3e">*</span></label>
                        <input type="number" id="experience_years" name="experience_years" min="0" max="60" required
                            value="<?php echo htmlspecialchars((string)$years, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="e.g. 10">
                    </div>

                    <div class="form-group full-width">
                        <label for="qualifications">Qualifications <span style="color:#e53e3e">*</span></label>
                        <input type="text" id="qualifications" name="qualifications" required
                            value="<?php echo htmlspecialchars($qual, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="e.g. MBBS (University of Colombo), MD in Cardiology">
                    </div>

                    <div class="form-group">
                        <label for="consultation_fee">Consultation Fee (LKR) <span style="color:#e53e3e">*</span></label>
                        <input type="number" id="consultation_fee" name="consultation_fee" min="0" required
                            value="<?php echo htmlspecialchars((string)$fee, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="e.g. 3500">
                    </div>

                    <div class="form-group">
                        <label for="availability">Availability <span style="color:#e53e3e">*</span></label>
                        <input type="text" id="availability" name="availability" required
                            value="<?php echo htmlspecialchars($avail, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="e.g. Mon-Fri 9AM-5PM">
                    </div>

                    <div class="form-group full-width">
                        <label for="rating">Initial Rating</label>
                        <div class="rating-row">
                            <input type="range" id="rating" name="rating" min="0" max="5" step="0.1"
                                value="<?php echo htmlspecialchars((string)$rating, ENT_QUOTES, 'UTF-8'); ?>"
                                oninput="document.getElementById('ratingVal').textContent = parseFloat(this.value).toFixed(1)">
                            <span class="rating-val" id="ratingVal"><?php echo number_format((float)$rating, 1); ?></span>
                            <span style="color:#f59e0b;font-size:1.1rem;">&#9733;</span>
                        </div>
                        <div class="field-hint">Drag to set the starting rating (0–5). Typically 4.0–5.0 for new doctors.</div>
                    </div>

                </div><!-- .form-grid -->

                <div style="margin-top: 24px; display:flex; gap:12px; flex-wrap:wrap;">
                    <button type="submit" class="button primary-button">
                        <i class="fas fa-user-plus"></i> Add Doctor
                    </button>
                    <a href="dashboard_admin.php" class="button outline-button">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>