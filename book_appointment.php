<?php
require_once 'auth.php';
require_role('patient');
$pageTitle = 'Book Appointment | MediCare Plus';
$errors = [];
$success = '';
$selectedDoctorId = filter_input(INPUT_GET, 'doctor_id', FILTER_VALIDATE_INT);
$doctor = $selectedDoctorId ? fetch_doctor_by_id($selectedDoctorId) : null;
$patient = fetch_patient_by_user_id($_SESSION['user_id']);

if (!$patient) {
    create_patient_profile($_SESSION['user_id']);
    $patient = fetch_patient_by_user_id($_SESSION['user_id']);
}
$availableDoctors = fetch_all_doctors();
$appointmentDate = '';
$notes = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $submittedToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW) ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }

    $selectedDoctorId = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
    $appointmentDate  = trim(filter_input(INPUT_POST, 'appointment_date', FILTER_UNSAFE_RAW) ?: '');
    $notes            = trim(filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');

    if (!$selectedDoctorId)  $errors[] = 'Please select a doctor for the appointment.';
    if (!$appointmentDate)   $errors[] = 'Please choose a date and time for your appointment.';

    $doctor = $selectedDoctorId ? fetch_doctor_by_id($selectedDoctorId) : null;
    if (!$doctor) $errors[] = 'The selected doctor could not be found. Please choose another provider.';

    $appointmentDateTime = null;
    if (empty($errors)) {
        $appointmentDateTime = DateTime::createFromFormat('Y-m-d\TH:i', $appointmentDate);
        if (!$appointmentDateTime) {
            $errors[] = 'Invalid appointment date format. Please use the date picker.';
        } elseif ($appointmentDateTime < new DateTime()) {
            $errors[] = 'Appointment date must be in the future.';
        }
    }

    if (empty($errors) && doctor_has_conflict($selectedDoctorId, $appointmentDateTime->format('Y-m-d H:i:s'))) {
        $errors[] = 'This doctor already has an appointment at the selected time. Please choose another slot.';
    }

    if (empty($errors)) {
        $created = create_appointment($patient['id'], $selectedDoctorId, $appointmentDateTime->format('Y-m-d H:i:s'), $notes);
        if ($created) {
            $success = 'Your appointment request has been submitted. The doctor will confirm the time shortly.';
            $appointmentDate = '';
            $notes = '';
        } else {
            $errors[] = 'Unable to schedule the appointment. Please try again later.';
        }
    }
}

include 'header.php';
?>

<style>
    /* ── Book Appointment Page Styles ── */
    .book-appt-page {
        min-height: calc(100vh - 70px);
        background: linear-gradient(160deg, #f0f7ff 0%, #f7fafc 50%, #f0fff4 100%);
        padding: 48px 0 80px;
    }

    .book-appt-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 24px;
    }

    /* ── Page Header ── */
    .book-appt-header {
        text-align: center;
        margin-bottom: 48px;
    }

    .book-appt-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(49, 130, 206, 0.1);
        color: #1e3a8a;
        border: 1px solid rgba(49, 130, 206, 0.25);
        border-radius: 50px;
        padding: 6px 18px;
        font-size: 0.82rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    .book-appt-badge i {
        color: #3182ce;
        font-size: 14px;
    }

    .book-appt-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(2rem, 4vw, 2.8rem);
        font-weight: 700;
        color: #1a202c;
        line-height: 1.2;
        margin-bottom: 14px;
    }

    .book-appt-title span {
        background: linear-gradient(90deg, #1e3a8a, #3182ce);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .book-appt-subtitle {
        color: #718096;
        font-size: 1.05rem;
        max-width: 520px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ── Progress Steps ── */
    .progress-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-bottom: 44px;
    }

    .step-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .step-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .step-circle.active {
        background: #1e3a8a;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.15);
    }

    .step-circle.done {
        background: #38a169;
        color: #fff;
    }

    .step-circle.pending {
        background: #e2e8f0;
        color: #a0aec0;
    }

    .step-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #4a5568;
    }

    .step-label.active {
        color: #1e3a8a;
    }

    .step-label.done {
        color: #38a169;
    }

    .step-label.pending {
        color: #a0aec0;
    }

    .step-connector {
        width: 60px;
        height: 2px;
        background: #e2e8f0;
        margin: 0 4px;
    }

    .step-connector.done {
        background: #38a169;
    }

    /* ── Layout Grid ── */
    .book-appt-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 28px;
        align-items: start;
    }

    @media (max-width: 880px) {
        .book-appt-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ── Card Base ── */
    .appt-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }

    .appt-card:hover {
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.10);
    }

    .appt-card-header {
        padding: 24px 28px 20px;
        border-bottom: 1px solid #f0f4f8;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .appt-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .appt-card-icon.blue {
        background: #ebf8ff;
        color: #3182ce;
    }

    .appt-card-icon.green {
        background: #f0fff4;
        color: #38a169;
    }

    .appt-card-icon.navy {
        background: #ebf4ff;
        color: #1e3a8a;
    }

    .appt-card-header-text h3 {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 2px;
    }

    .appt-card-header-text p {
        font-size: 0.82rem;
        color: #a0aec0;
    }

    .appt-card-body {
        padding: 28px;
    }

    /* ── Alert Boxes ── */
    .alert {
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        font-size: 0.92rem;
        line-height: 1.6;
    }

    .alert i {
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .alert-success {
        background: #f0fff4;
        border: 1px solid #c6f6d5;
        color: #276749;
    }

    .alert-success i {
        color: #38a169;
    }

    .alert-error {
        background: #fff5f5;
        border: 1px solid #fed7d7;
        color: #9b2c2c;
    }

    .alert-error i {
        color: #e53e3e;
    }

    .alert-error ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .alert-error ul li {
        margin-bottom: 4px;
    }

    .alert-error ul li:last-child {
        margin-bottom: 0;
    }

    /* ── Form ── */
    .booking-form .form-group {
        margin-bottom: 24px;
    }

    .booking-form label {
        display: block;
        font-size: 0.88rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
        letter-spacing: 0.2px;
    }

    .booking-form label .required-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        background: #e53e3e;
        border-radius: 50%;
        margin-left: 4px;
        vertical-align: middle;
        position: relative;
        top: -1px;
    }

    .form-input-wrap {
        position: relative;
    }

    .form-input-wrap i.field-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 16px;
        pointer-events: none;
        transition: color 0.2s;
    }

    .booking-form select,
    .booking-form input[type="datetime-local"],
    .booking-form textarea {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.94rem;
        color: #2d3748;
        background: #fafbfc;
        transition: all 0.2s ease;
        outline: none;
        font-family: 'Inter', 'DM Sans', sans-serif;
    }

    .booking-form select,
    .booking-form input[type="datetime-local"] {
        height: 50px;
        padding: 0 14px 0 42px;
        appearance: none;
        -webkit-appearance: none;
    }

    .booking-form textarea {
        padding: 14px 14px 14px 42px;
        resize: vertical;
        min-height: 110px;
        line-height: 1.6;
    }

    .form-input-wrap.textarea-wrap i.field-icon {
        top: 17px;
        transform: none;
    }

    .booking-form select:focus,
    .booking-form input[type="datetime-local"]:focus,
    .booking-form textarea:focus {
        border-color: #3182ce;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.12);
    }

    .booking-form select:focus+.focus-ring,
    .booking-form input:focus~.focus-ring {
        opacity: 1;
    }

    /* Custom select arrow */
    .select-wrap::after {
        content: '\f107';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        pointer-events: none;
        font-size: 13px;
    }

    /* Hint text */
    .form-hint {
        font-size: 0.78rem;
        color: #a0aec0;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .form-hint i {
        font-size: 12px;
    }

    /* Doctor preview strip (shows when a doctor is selected) */
    .doctor-preview-strip {
        background: linear-gradient(135deg, #ebf4ff, #f0fff4);
        border: 1px solid #bee3f8;
        border-radius: 12px;
        padding: 14px 16px;
        margin-top: 10px;
        display: none;
        align-items: center;
        gap: 14px;
        animation: fadeSlideIn 0.3s ease;
    }

    .doctor-preview-strip.visible {
        display: flex;
    }

    @keyframes fadeSlideIn {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .doc-preview-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a, #3182ce);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .doc-preview-info {
        flex: 1;
    }

    .doc-preview-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 2px;
    }

    .doc-preview-spec {
        font-size: 0.78rem;
        color: #718096;
    }

    .doc-preview-fee {
        text-align: right;
    }

    .doc-preview-fee-label {
        font-size: 0.7rem;
        color: #a0aec0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .doc-preview-fee-amount {
        font-size: 1rem;
        font-weight: 700;
        color: #1e3a8a;
    }

    /* ── Submit Button ── */
    .btn-submit {
        width: 100%;
        height: 54px;
        background: linear-gradient(135deg, #1e3a8a 0%, #3182ce 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        letter-spacing: 0.3px;
        position: relative;
        overflow: hidden;
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0);
        transition: background 0.2s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(30, 58, 138, 0.35);
    }

    .btn-submit:hover::before {
        background: rgba(255, 255, 255, 0.08);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .btn-submit i {
        font-size: 18px;
    }

    /* ── Sidebar Cards ── */

    /* Doctor Detail Card */
    .doctor-detail-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        margin-bottom: 20px;
        transition: box-shadow 0.3s;
    }

    .doctor-detail-card:hover {
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.10);
    }

    .doctor-card-top {
        background: linear-gradient(135deg, #1e3a8a 0%, #3182ce 100%);
        padding: 28px 24px 20px;
        text-align: center;
        position: relative;
    }

    .doctor-card-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: 3px solid rgba(255, 255, 255, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        color: #fff;
        margin: 0 auto 14px;
        letter-spacing: 1px;
    }

    .doctor-card-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 4px;
    }

    .doctor-card-spec {
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.8);
        background: rgba(255, 255, 255, 0.15);
        display: inline-block;
        padding: 3px 12px;
        border-radius: 50px;
    }

    .doctor-card-body {
        padding: 20px 24px;
    }

    .doctor-info-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f7fafc;
        font-size: 0.88rem;
    }

    .doctor-info-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .doctor-info-row i {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 14px;
    }

    .icon-blue {
        background: #ebf8ff;
        color: #3182ce;
    }

    .icon-green {
        background: #f0fff4;
        color: #38a169;
    }

    .icon-navy {
        background: #ebf4ff;
        color: #1e3a8a;
    }

    .icon-amber {
        background: #fffbeb;
        color: #d97706;
    }

    .doctor-info-row .info-label {
        color: #a0aec0;
        font-size: 0.78rem;
        display: block;
    }

    .doctor-info-row .info-value {
        color: #2d3748;
        font-weight: 600;
    }

    /* Info / Tips card */
    .tips-card {
        background: linear-gradient(135deg, #f0f7ff, #f0fff4);
        border: 1px solid #bee3f8;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .tips-card-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tips-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .tips-list li {
        font-size: 0.83rem;
        color: #4a5568;
        padding: 7px 0;
        border-bottom: 1px solid rgba(190, 227, 248, 0.5);
        display: flex;
        align-items: flex-start;
        gap: 10px;
        line-height: 1.5;
    }

    .tips-list li:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .tips-list li i {
        color: #3182ce;
        font-size: 13px;
        margin-top: 2px;
        flex-shrink: 0;
    }

    /* Trust badges */
    .trust-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 20px;
    }

    .trust-badge {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 12px;
        text-align: center;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .trust-badge:hover {
        border-color: #bee3f8;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    }

    .trust-badge i {
        font-size: 22px;
        margin-bottom: 6px;
        display: block;
    }

    .trust-badge-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: #718096;
        line-height: 1.3;
    }

    /* ── Responsive ── */
    @media (max-width: 600px) {
        .book-appt-container {
            padding: 0 16px;
        }

        .appt-card-body {
            padding: 20px;
        }

        .progress-steps {
            gap: 0;
        }

        .step-connector {
            width: 36px;
        }

        .trust-row {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div class="book-appt-page">
    <div class="book-appt-container">

        <!-- Page Header -->
        <div class="book-appt-header">
            <div class="book-appt-badge">
                <i class="fas fa-calendar-check"></i>
                Online Appointment Booking
            </div>
            <h1 class="book-appt-title">Book Your <span>Consultation</span></h1>
            <p class="book-appt-subtitle">Choose your doctor, pick a convenient time, and confirm your visit — all in under a minute.</p>
        </div>

        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="step-item">
                <div class="step-circle active"><i class="fas fa-user-md" style="font-size:14px;"></i></div>
                <span class="step-label active">Choose Doctor</span>
            </div>
            <div class="step-connector"></div>
            <div class="step-item">
                <div class="step-circle pending">2</div>
                <span class="step-label pending">Set Schedule</span>
            </div>
            <div class="step-connector"></div>
            <div class="step-item">
                <div class="step-circle pending">3</div>
                <span class="step-label pending">Confirm</span>
            </div>
        </div>

        <!-- Alerts -->
        <?php if ($success): ?>
            <div class="alert alert-success" role="alert" style="margin-bottom:28px;">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Appointment Submitted!</strong><br>
                    <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert" style="margin-bottom:28px;">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Please fix the following:</strong>
                    <ul style="margin-top:6px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Grid -->
        <div class="book-appt-grid">

            <!-- LEFT: Booking Form -->
            <div class="appt-card">
                <div class="appt-card-header">
                    <div class="appt-card-icon blue">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="appt-card-header-text">
                        <h3>Appointment Details</h3>
                        <p>Fill in the fields below to reserve your slot</p>
                    </div>
                </div>
                <div class="appt-card-body">
                    <form action="book_appointment.php" method="post" class="booking-form" id="bookingForm">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                        <!-- Doctor select -->
                        <div class="form-group">
                            <label for="doctor_id">
                                Select Your Doctor <span class="required-dot"></span>
                            </label>
                            <div class="form-input-wrap select-wrap">
                                <i class="fas fa-user-md field-icon"></i>
                                <select id="doctor_id" name="doctor_id" required onchange="updateDoctorPreview(this)">
                                    <option value="">— Choose a provider —</option>
                                    <?php foreach ($availableDoctors as $entry): ?>
                                        <option
                                            value="<?php echo (int) $entry['id']; ?>"
                                            data-name="Dr. <?php echo htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-spec="<?php echo htmlspecialchars($entry['specialization'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-fee="LKR <?php echo number_format((float)$entry['consultation_fee'], 0); ?>"
                                            data-initials="<?php echo strtoupper(substr($entry['first_name'], 0, 1) . substr($entry['last_name'], 0, 1)); ?>"
                                            <?php echo ($selectedDoctorId === (int) $entry['id']) ? 'selected' : ''; ?>>
                                            Dr. <?php echo htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name'], ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars($entry['specialization'], ENT_QUOTES, 'UTF-8'); ?> (LKR <?php echo number_format((float)$entry['consultation_fee'], 0); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Doctor preview strip -->
                            <div class="doctor-preview-strip" id="doctorPreviewStrip">
                                <div class="doc-preview-avatar" id="previewInitials">--</div>
                                <div class="doc-preview-info">
                                    <div class="doc-preview-name" id="previewName">—</div>
                                    <div class="doc-preview-spec" id="previewSpec">—</div>
                                </div>
                                <div class="doc-preview-fee">
                                    <div class="doc-preview-fee-label">Fee</div>
                                    <div class="doc-preview-fee-amount" id="previewFee">—</div>
                                </div>
                            </div>
                        </div>

                        <!-- Date & time -->
                        <div class="form-group">
                            <label for="appointment_date">
                                Preferred Date &amp; Time <span class="required-dot"></span>
                            </label>
                            <div class="form-input-wrap">
                                <i class="fas fa-clock field-icon"></i>
                                <input
                                    type="datetime-local"
                                    id="appointment_date"
                                    name="appointment_date"
                                    value="<?php echo htmlspecialchars($appointmentDate, ENT_QUOTES, 'UTF-8'); ?>"
                                    required
                                    min="<?php echo date('Y-m-d\TH:i'); ?>">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                The doctor will confirm or suggest an alternative time.
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group" style="margin-bottom:32px;">
                            <label for="notes">Notes for the Doctor <span style="font-weight:400; color:#a0aec0;">(optional)</span></label>
                            <div class="form-input-wrap textarea-wrap">
                                <i class="fas fa-comment-medical field-icon"></i>
                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows="4"
                                    placeholder="Describe your symptoms, concerns, or anything the doctor should know before your visit…"><?php echo htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-lock"></i>
                                Your notes are private and only visible to your selected doctor.
                            </div>
                        </div>

                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fas fa-calendar-check"></i>
                            Submit Appointment Request
                        </button>
                    </form>
                </div>
            </div>

            <!-- RIGHT: Sidebar -->
            <div class="sidebar">

                <!-- Doctor detail card (shown when pre-selected via URL) -->
                <?php if ($doctor): ?>
                    <div class="doctor-detail-card" id="doctorDetailCard">
                        <div class="doctor-card-top">
                            <div class="doctor-card-avatar">
                                <?php echo strtoupper(substr($doctor['first_name'], 0, 1) . substr($doctor['last_name'], 0, 1)); ?>
                            </div>
                            <div class="doctor-card-name">
                                Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div class="doctor-card-spec">
                                <?php echo htmlspecialchars($doctor['specialization'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>
                        <div class="doctor-card-body">
                            <div class="doctor-info-row">
                                <i class="fas fa-money-bill-wave icon-green"></i>
                                <div>
                                    <span class="info-label">Consultation Fee</span>
                                    <span class="info-value">LKR <?php echo number_format((float)$doctor['consultation_fee'], 0); ?></span>
                                </div>
                            </div>
                            <div class="doctor-info-row">
                                <i class="fas fa-calendar-alt icon-blue"></i>
                                <div>
                                    <span class="info-label">Availability</span>
                                    <span class="info-value"><?php echo htmlspecialchars($doctor['availability'] ?: 'Contact support', ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </div>
                            <div class="doctor-info-row">
                                <i class="fas fa-shield-alt icon-navy"></i>
                                <div>
                                    <span class="info-label">Status</span>
                                    <span class="info-value" style="color:#38a169;"><i class="fas fa-circle" style="font-size:8px; margin-right:4px;"></i>Accepting Patients</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Dynamic doctor card (populated by JS when no pre-selection) -->
                <div class="doctor-detail-card" id="jsDoctorCard" style="display:none;">
                    <div class="doctor-card-top">
                        <div class="doctor-card-avatar" id="jsDoctorAvatar">--</div>
                        <div class="doctor-card-name" id="jsDoctorName">—</div>
                        <div class="doctor-card-spec" id="jsDoctorSpec">—</div>
                    </div>
                    <div class="doctor-card-body">
                        <div class="doctor-info-row">
                            <i class="fas fa-money-bill-wave icon-green"></i>
                            <div>
                                <span class="info-label">Consultation Fee</span>
                                <span class="info-value" id="jsDoctorFee">—</span>
                            </div>
                        </div>
                        <div class="doctor-info-row">
                            <i class="fas fa-shield-alt icon-navy"></i>
                            <div>
                                <span class="info-label">Status</span>
                                <span class="info-value" style="color:#38a169;"><i class="fas fa-circle" style="font-size:8px; margin-right:4px;"></i>Accepting Patients</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="tips-card">
                    <div class="tips-card-title">
                        <i class="fas fa-lightbulb" style="color:#d97706;"></i>
                        Booking Tips
                    </div>
                    <ul class="tips-list">
                        <li><i class="fas fa-check"></i> Book at least 24 hours in advance for best availability.</li>
                        <li><i class="fas fa-check"></i> Mention any ongoing medications in your notes.</li>
                        <li><i class="fas fa-check"></i> You'll receive a confirmation once the doctor approves.</li>
                        <li><i class="fas fa-check"></i> You can reschedule from your Patient Dashboard.</li>
                    </ul>
                </div>

                <!-- Trust badges -->
                <div class="trust-row">
                    <div class="trust-badge">
                        <i class="fas fa-lock" style="color:#3182ce;"></i>
                        <div class="trust-badge-label">Secure &amp;<br>Private</div>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-user-shield" style="color:#38a169;"></i>
                        <div class="trust-badge-label">HIPAA<br>Compliant</div>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-clock" style="color:#d97706;"></i>
                        <div class="trust-badge-label">24/7<br>Booking</div>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-undo" style="color:#9f7aea;"></i>
                        <div class="trust-badge-label">Free<br>Reschedule</div>
                    </div>
                </div>

            </div><!-- /sidebar -->
        </div><!-- /grid -->
    </div><!-- /container -->
</div><!-- /page -->

<script>
    (function() {
        var select = document.getElementById('doctor_id');
        var strip = document.getElementById('doctorPreviewStrip');
        var jsCard = document.getElementById('jsDoctorCard');

        function updateDoctorPreview(sel) {
            var opt = sel.options[sel.selectedIndex];
            if (!opt || !opt.value) {
                if (strip) strip.classList.remove('visible');
                if (jsCard) jsCard.style.display = 'none';
                return;
            }
            var name = opt.getAttribute('data-name') || '—';
            var spec = opt.getAttribute('data-spec') || '—';
            var fee = opt.getAttribute('data-fee') || '—';
            var initials = opt.getAttribute('data-initials') || '??';

            // Update strip
            if (strip) {
                document.getElementById('previewInitials').textContent = initials;
                document.getElementById('previewName').textContent = name;
                document.getElementById('previewSpec').textContent = spec;
                document.getElementById('previewFee').textContent = fee;
                strip.classList.add('visible');
            }

            // Update sidebar JS card (only if no PHP-rendered card)
            <?php if (!$doctor): ?>
                if (jsCard) {
                    document.getElementById('jsDoctorAvatar').textContent = initials;
                    document.getElementById('jsDoctorName').textContent = name;
                    document.getElementById('jsDoctorSpec').textContent = spec;
                    document.getElementById('jsDoctorFee').textContent = fee;
                    jsCard.style.display = 'block';
                }
            <?php endif; ?>
        }

        // Expose globally for inline onchange
        window.updateDoctorPreview = updateDoctorPreview;

        // Run on page load if a doctor is pre-selected
        if (select && select.value) updateDoctorPreview(select);

        // Set datetime min to now
        var dtInput = document.getElementById('appointment_date');
        if (dtInput && !dtInput.value) {
            var now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            dtInput.min = now.toISOString().slice(0, 16);
        }

        // Submit button loading state
        var form = document.getElementById('bookingForm');
        var btn = document.getElementById('submitBtn');
        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting…';
                btn.disabled = true;
            });
        }
    })();
</script>

<?php include 'footer.php'; ?>