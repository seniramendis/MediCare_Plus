<?php
require_once 'auth.php';
require_role(['admin', 'doctor']);

$pageTitle = 'Upload Medical Report';
include 'header.php';

$user = current_user();
$userName = $user['first_name'] . ' ' . $user['last_name'];
$patients = fetch_all_patients();

$success = false;
$error = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW) ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Invalid CSRF token.';
        exit(0);
    }

    $patientId = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    if (!$patientId) {
        $error = 'Please select a patient.';
    } elseif (!isset($_FILES['report_file']) || $_FILES['report_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a valid file.';
    } else {
        $file = $_FILES['report_file'];
        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];

        // Validate file: max 10MB, PDF/DOC/DOCX/TXT/JPG/PNG
        $maxSize    = 10 * 1024 * 1024;
        $allowedExt = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
        $allowedMime = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt'  => 'text/plain',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileMime = mime_content_type($fileTmp);

        if ($fileSize > $maxSize) {
            $error = 'File size exceeds 10MB limit.';
        } elseif (!in_array($fileExt, $allowedExt, true)) {
            $error = 'File type not allowed. Please upload PDF, DOC, DOCX, TXT, JPG, or PNG.';
        } elseif (!isset($allowedMime[$fileExt]) || $fileMime !== $allowedMime[$fileExt]) {
            $error = 'File content does not match the declared file type.';
        } else {
            // Build safe filename using only a timestamp and the validated extension —
            // never trust the original filename for the stored name.
            $safeFileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExt;
            $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR;
            $uploadPath = $uploadDir . $safeFileName;

            // Ensure directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $success = create_medical_report($patientId, $fileName, $safeFileName, $notes, $userName);
                if (!$success) {
                    unlink($uploadPath); // Delete file if DB insert fails
                    $error = 'Failed to save report. Please try again.';
                }
            } else {
                $error = 'Failed to upload file. Please try again.';
            }
        }
    }
}
?>

<section class="container">
    <h1>Upload Medical Report</h1>
    <p>Upload test results, prescriptions, or visit summaries for a patient.</p>

    <?php if ($success): ?>
        <div class="alert alert-success">Report uploaded successfully!</div>
    <?php elseif ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="patient_id">Select Patient:</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">-- Choose patient --</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?php echo e($p['patient_id']); ?>">
                        <?php echo e($p['last_name'] . ', ' . $p['first_name'] . ' (' . $p['email'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="report_file">Report File (max 10MB):</label>
            <input type="file" id="report_file" name="report_file" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png" required>
            <small>Allowed: PDF, DOC, DOCX, TXT, JPG, PNG</small>
        </div>

        <div class="form-group">
            <label for="notes">Notes (optional):</label>
            <textarea id="notes" name="notes" rows="3" maxlength="500" placeholder="Add any notes about this report..."></textarea>
        </div>

        <button type="submit" class="button">Upload Report</button>
    </form>
</section>

<style>
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

    small {
        display: block;
        color: #666;
        margin-top: 5px;
    }
</style>

<?php include 'footer.php'; ?>