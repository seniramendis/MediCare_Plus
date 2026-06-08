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
            $safeFileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExt;
            $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR;
            $uploadPath = $uploadDir . $safeFileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $success = create_medical_report($patientId, $fileName, $safeFileName, $notes, $userName);
                if (!$success) {
                    unlink($uploadPath);
                    $error = 'Failed to save report. Please try again.';
                }
            } else {
                $error = 'Failed to upload file. Please try again.';
            }
        }
    }
}
?>

<style>
    .upload-page-wrapper {
        min-height: 80vh;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f4fd 100%);
        padding: 48px 20px;
    }

    .upload-card {
        max-width: 680px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(43, 108, 176, 0.12);
        overflow: hidden;
    }

    .upload-card-header {
        background: linear-gradient(135deg, #1a56db 0%, #2b6cb0 100%);
        padding: 36px 40px 30px;
        color: #fff;
    }

    .upload-card-header .header-icon {
        width: 52px;
        height: 52px;
        background: rgba(255,255,255,0.15);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 16px;
    }

    .upload-card-header h1 {
        font-size: 1.6rem;
        font-weight: 700;
        margin: 0 0 6px;
        letter-spacing: -0.3px;
    }

    .upload-card-header p {
        margin: 0;
        opacity: 0.85;
        font-size: 0.95rem;
    }

    .upload-card-body {
        padding: 36px 40px 40px;
    }

    /* Alerts */
    .ur-alert {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 18px;
        border-radius: 12px;
        margin-bottom: 28px;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .ur-alert-success {
        background: #f0fff4;
        color: #276749;
        border: 1px solid #9ae6b4;
    }

    .ur-alert-error {
        background: #fff5f5;
        color: #9b2c2c;
        border: 1px solid #feb2b2;
    }

    .ur-alert-icon {
        font-size: 1.2rem;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* Form */
    .ur-form-group {
        margin-bottom: 24px;
    }

    .ur-label {
        display: block;
        font-weight: 600;
        font-size: 0.875rem;
        color: #374151;
        margin-bottom: 8px;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .ur-label .required-star {
        color: #e53e3e;
        margin-left: 3px;
    }

    .ur-select,
    .ur-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        color: #2d3748;
        background: #f8fafc;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
        font-family: inherit;
    }

    .ur-select:focus,
    .ur-textarea:focus {
        outline: none;
        border-color: #1a56db;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1);
    }

    .ur-textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* File Drop Zone */
    .ur-file-zone {
        position: relative;
        border: 2px dashed #cbd5e0;
        border-radius: 14px;
        padding: 36px 20px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        background: #f8fafc;
    }

    .ur-file-zone:hover,
    .ur-file-zone.drag-over {
        border-color: #1a56db;
        background: #ebf4ff;
    }

    .ur-file-zone input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .ur-file-zone .zone-icon {
        font-size: 2.2rem;
        color: #a0aec0;
        margin-bottom: 12px;
        transition: color 0.2s;
    }

    .ur-file-zone:hover .zone-icon {
        color: #1a56db;
    }

    .ur-file-zone .zone-title {
        font-size: 1rem;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 4px;
    }

    .ur-file-zone .zone-sub {
        font-size: 0.82rem;
        color: #a0aec0;
    }

    .ur-file-zone .zone-selected {
        display: none;
        margin-top: 14px;
        background: #ebf8ff;
        border-radius: 8px;
        padding: 10px 16px;
        font-size: 0.875rem;
        color: #2b6cb0;
        font-weight: 500;
        align-items: center;
        gap: 8px;
    }

    .ur-file-zone .zone-selected.visible {
        display: inline-flex;
    }

    /* Allowed types badges */
    .ur-type-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 12px;
    }

    .ur-badge {
        background: #edf2f7;
        color: #4a5568;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.04em;
    }

    /* Submit button */
    .ur-submit-btn {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #1a56db 0%, #2b6cb0 100%);
        color: #fff;
        font-size: 1rem;
        font-weight: 700;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: opacity 0.2s, transform 0.15s;
        letter-spacing: 0.02em;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 8px;
    }

    .ur-submit-btn:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }

    .ur-submit-btn:active {
        transform: translateY(0);
    }

    .ur-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 28px 0;
    }

    /* Security note */
    .ur-security-note {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f7fafc;
        border-radius: 10px;
        padding: 13px 16px;
        margin-top: 20px;
        font-size: 0.82rem;
        color: #718096;
    }

    .ur-security-note i {
        color: #38a169;
        font-size: 1rem;
        flex-shrink: 0;
    }
</style>

<div class="upload-page-wrapper">
    <div class="upload-card">

        <div class="upload-card-header">
            <div class="header-icon"><i class="fas fa-file-medical-alt"></i></div>
            <h1>Upload Medical Report</h1>
            <p>Securely attach lab results, prescriptions, or visit summaries to a patient's profile.</p>
        </div>

        <div class="upload-card-body">

            <?php if ($success): ?>
                <div class="ur-alert ur-alert-success">
                    <span class="ur-alert-icon"><i class="fas fa-check-circle"></i></span>
                    <span>Report uploaded successfully! The patient can now access it from their portal.</span>
                </div>
            <?php elseif ($error): ?>
                <div class="ur-alert ur-alert-error">
                    <span class="ur-alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                    <span><?php echo e($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <!-- Patient -->
                <div class="ur-form-group">
                    <label class="ur-label" for="patient_id">
                        Patient <span class="required-star">*</span>
                    </label>
                    <select id="patient_id" name="patient_id" class="ur-select" required>
                        <option value="">— Select a patient —</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?php echo e($p['patient_id']); ?>">
                                <?php echo e($p['last_name'] . ', ' . $p['first_name'] . ' (' . $p['email'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- File Upload -->
                <div class="ur-form-group">
                    <label class="ur-label">
                        Report File <span class="required-star">*</span>
                    </label>
                    <div class="ur-file-zone" id="fileZone">
                        <input type="file" id="report_file" name="report_file"
                               accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png" required>
                        <div class="zone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="zone-title">Drag &amp; drop your file here</div>
                        <div class="zone-sub">or click to browse &mdash; max 10 MB</div>
                        <div class="zone-selected" id="selectedFile">
                            <i class="fas fa-paperclip"></i>
                            <span id="selectedFileName"></span>
                        </div>
                    </div>
                    <div class="ur-type-badges" style="margin-top:10px;">
                        <span class="ur-badge">PDF</span>
                        <span class="ur-badge">DOC</span>
                        <span class="ur-badge">DOCX</span>
                        <span class="ur-badge">TXT</span>
                        <span class="ur-badge">JPG</span>
                        <span class="ur-badge">PNG</span>
                    </div>
                </div>

                <div class="ur-divider"></div>

                <!-- Notes -->
                <div class="ur-form-group">
                    <label class="ur-label" for="notes">Clinical Notes <span style="font-weight:400;text-transform:none;color:#a0aec0;">(optional)</span></label>
                    <textarea id="notes" name="notes" class="ur-textarea" rows="3"
                              maxlength="500"
                              placeholder="Add context, diagnosis notes, or follow-up instructions…"></textarea>
                </div>

                <button type="submit" class="ur-submit-btn">
                    <i class="fas fa-upload"></i> Upload Report
                </button>
            </form>

            <div class="ur-security-note">
                <i class="fas fa-shield-alt"></i>
                <span>Files are scanned and validated server-side. Only PDF, DOC, DOCX, TXT, JPG, and PNG formats are accepted. Executable and script files are automatically rejected.</span>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    const input = document.getElementById('report_file');
    const zone  = document.getElementById('fileZone');
    const sel   = document.getElementById('selectedFile');
    const selName = document.getElementById('selectedFileName');

    function showFile(name) {
        selName.textContent = name;
        sel.classList.add('visible');
    }

    input.addEventListener('change', function () {
        if (this.files && this.files[0]) showFile(this.files[0].name);
    });

    zone.addEventListener('dragover', function (e) {
        e.preventDefault();
        zone.classList.add('drag-over');
    });

    zone.addEventListener('dragleave', function () {
        zone.classList.remove('drag-over');
    });

    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('drag-over');
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            input.files = e.dataTransfer.files;
            showFile(e.dataTransfer.files[0].name);
        }
    });
}());
</script>

<?php include 'footer.php'; ?>
