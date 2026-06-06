<?php
// Database connection and helper functions for MediCare Plus.
// Credentials are loaded from .env to avoid hardcoding sensitive values in source code.
(function () {
    $envFile = __DIR__ . DIRECTORY_SEPARATOR . '.env';
    if (!is_readable($envFile)) {
        error_log('db_connect.php: .env file not found or not readable at ' . $envFile);
        return;
    }
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        if (!defined($key)) {
            define($key, $value);
        }
    }
})();

// Fallback defaults so the app fails clearly if .env is missing
if (!defined('DB_HOST')) define('DB_HOST', '127.0.0.1');
if (!defined('DB_PORT')) define('DB_PORT', '3306');
if (!defined('DB_NAME')) define('DB_NAME', '');
if (!defined('DB_USER')) define('DB_USER', '');
if (!defined('DB_PASS')) define('DB_PASS', '');

/**
 * Open a new MySQL database connection.
 *
 * @return mysqli|null
 */
function get_db_connection()
{
    // Connect using explicit port to avoid "connection refused" when MySQL uses a non-default port.
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);
    if ($connection->connect_errno) {
        error_log('Database connection failed: ' . $connection->connect_error);
        return null;
    }

    $connection->set_charset('utf8mb4');
    return $connection;
}

/**
 * Fetch a single user record by email.
 *
 * @param string $email
 * @return array|null
 */
function fetch_user_by_email($email)
{
    $connection = get_db_connection();
    if (!$connection) {
        return null;
    }

    $query = 'SELECT id, first_name, last_name, email, password_hash, role FROM users WHERE email = ? LIMIT 1';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return null;
    }

    $statement->bind_param('s', $email);
    $statement->execute();
    $result = $statement->get_result();
    $user = $result->fetch_assoc();

    $statement->close();
    $connection->close();

    return $user ?: null;
}

/**
 * Fetch a single user record by id.
 *
 * @param int $id
 * @return array|null
 */
function fetch_user_by_id($id)
{
    $connection = get_db_connection();
    if (!$connection) {
        return null;
    }

    $query = 'SELECT id, first_name, last_name, email, role FROM users WHERE id = ? LIMIT 1';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return null;
    }

    $statement->bind_param('i', $id);
    $statement->execute();
    $result = $statement->get_result();
    $user = $result->fetch_assoc();

    $statement->close();
    $connection->close();

    return $user ?: null;
}

/**
 * Check whether a user already exists by email.
 *
 * @param string $email
 * @return bool
 */
function user_exists_by_email($email)
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'SELECT 1 FROM users WHERE email = ? LIMIT 1';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('s', $email);
    $statement->execute();
    $statement->store_result();
    $exists = $statement->num_rows > 0;

    $statement->close();
    $connection->close();

    return $exists;
}

/**
 * Create a new user record.
 *
 * @param string $firstName
 * @param string $lastName
 * @param string $email
 * @param string $passwordHash
 * @param string $role
 * @return int|false Returns the new user id or false on failure.
 */
function create_user($firstName, $lastName, $email, $passwordHash, $role = 'patient')
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'INSERT INTO users (first_name, last_name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('sssss', $firstName, $lastName, $email, $passwordHash, $role);
    $success = $statement->execute();
    $userId = $success ? $statement->insert_id : false;

    $statement->close();
    $connection->close();

    return $userId;
}

/**
 * Create a patient profile record for a new patient user.
 *
 * @param int $userId
 * @return bool
 */
function create_patient_profile($userId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'INSERT INTO patients (user_id, created_at) VALUES (?, NOW())';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('i', $userId);
    $success = $statement->execute();

    $statement->close();
    $connection->close();

    return $success;
}

/**
 * Fetch inbox summary for a user (latest message per correspondent).
 *
 * @param int $userId
 * @return array
 */
function fetch_inbox($userId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    $query = "SELECT m.id, m.sender_id, m.recipient_id, m.subject, m.body, m.is_read, m.sent_at, u.first_name, u.last_name
              FROM messages m
              JOIN users u ON u.id = m.sender_id
              WHERE m.recipient_id = ?
              ORDER BY m.sent_at DESC";

    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return [];
    }

    $statement->bind_param('i', $userId);
    $statement->execute();
    $result = $statement->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $statement->close();
    $connection->close();
    return $rows;
}

/**
 * Fetch conversation messages between two users.
 *
 * @param int $userId
 * @param int $otherUserId
 * @return array
 */
function fetch_conversation($userId, $otherUserId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    $query = 'SELECT m.id, m.sender_id, m.recipient_id, m.subject, m.body, m.is_read, m.sent_at, su.first_name AS sender_first, su.last_name AS sender_last
              FROM messages m
              JOIN users su ON su.id = m.sender_id
              WHERE (m.sender_id = ? AND m.recipient_id = ?) OR (m.sender_id = ? AND m.recipient_id = ?)
              ORDER BY m.sent_at ASC';

    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return [];
    }

    $statement->bind_param('iiii', $userId, $otherUserId, $otherUserId, $userId);
    $statement->execute();
    $result = $statement->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $statement->close();
    $connection->close();
    return $rows;
}

/**
 * Send a message from one user to another.
 *
 * @param int $senderId
 * @param int $recipientId
 * @param string $subject
 * @param string $body
 * @return bool
 */
function send_message($senderId, $recipientId, $subject, $body)
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'INSERT INTO messages (sender_id, recipient_id, subject, body, is_read, sent_at) VALUES (?, ?, ?, ?, 0, NOW())';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('iiss', $senderId, $recipientId, $subject, $body);
    $success = $statement->execute();

    $statement->close();
    $connection->close();
    return $success;
}

/**
 * Mark a message as read (only if recipient matches).
 *
 * @param int $messageId
 * @param int $userId
 * @return bool
 */
function mark_message_read($messageId, $userId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'UPDATE messages SET is_read = 1 WHERE id = ? AND recipient_id = ?';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('ii', $messageId, $userId);
    $success = $statement->execute();

    $statement->close();
    $connection->close();
    return $success;
}

/**
 * Get unread message count for a recipient user.
 *
 * @param int $userId
 * @return int
 */
function get_unread_count($userId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return 0;
    }

    $query = 'SELECT COUNT(*) AS unread FROM messages WHERE recipient_id = ? AND is_read = 0';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return 0;
    }

    $statement->bind_param('i', $userId);
    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    $count = isset($row['unread']) ? (int)$row['unread'] : 0;

    $statement->close();
    $connection->close();
    return $count;
}

/**
 * Mark all messages in a conversation as read for the given recipient.
 *
 * @param int $recipientId
 * @param int $senderId
 * @return bool
 */
function mark_conversation_read($recipientId, $senderId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'UPDATE messages SET is_read = 1 WHERE recipient_id = ? AND sender_id = ? AND is_read = 0';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('ii', $recipientId, $senderId);
    $success = $statement->execute();

    $statement->close();
    $connection->close();
    return $success;
}

/**
 * Fetch list of active users (for messaging recipients).
 *
 * @return array
 */
function fetch_all_users()
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    $query = 'SELECT id, first_name, last_name, email, role FROM users WHERE status = "active" ORDER BY first_name, last_name';
    $result = $connection->query($query);
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }

    $connection->close();
    return $rows;
}

/**
 * Fetch a patient record by user id.
 *
 * @param int $userId
 * @return array|null
 */
function fetch_patient_by_user_id($userId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return null;
    }

    $query = 'SELECT * FROM patients WHERE user_id = ? LIMIT 1';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return null;
    }

    $statement->bind_param('i', $userId);
    $statement->execute();
    $result = $statement->get_result();
    $patient = $result->fetch_assoc();

    $statement->close();
    $connection->close();

    return $patient ?: null;
}

/**
 * Fetch all doctor profiles with user information.
 *
 * @return array
 */
function fetch_all_doctors()
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    $query = 'SELECT d.id, d.specialization, d.qualifications, d.experience_years, d.consultation_fee, d.availability, d.rating, u.first_name, u.last_name FROM doctors d JOIN users u ON u.id = d.user_id WHERE u.status = "active" ORDER BY d.rating DESC, d.experience_years DESC';
    $result = $connection->query($query);
    $doctors = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        $result->free();
    }

    $connection->close();
    return $doctors;
}

/**
 * Fetch a doctor profile by user id (users.id).
 *
 * @param int $userId
 * @return array|null
 */
function fetch_doctor_by_user_id($userId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return null;
    }

    $query = 'SELECT d.id, d.specialization, d.qualifications, d.experience_years, d.consultation_fee, d.availability, d.rating, d.profile_image, u.first_name, u.last_name FROM doctors d JOIN users u ON u.id = d.user_id WHERE d.user_id = ? AND u.status = "active" LIMIT 1';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return null;
    }

    $statement->bind_param('i', $userId);
    $statement->execute();
    $result = $statement->get_result();
    $doctor = $result->fetch_assoc();

    $statement->close();
    $connection->close();

    return $doctor ?: null;
}

/**
 * Fetch a doctor profile by doctor id.
 *
 * @param int $doctorId
 * @return array|null
 */
function fetch_doctor_by_id($doctorId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return null;
    }

    $query = 'SELECT d.id, d.specialization, d.qualifications, d.experience_years, d.consultation_fee, d.availability, d.rating, u.first_name, u.last_name FROM doctors d JOIN users u ON u.id = d.user_id WHERE d.id = ? AND u.status = "active" LIMIT 1';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return null;
    }

    $statement->bind_param('i', $doctorId);
    $statement->execute();
    $result = $statement->get_result();
    $doctor = $result->fetch_assoc();

    $statement->close();
    $connection->close();

    return $doctor ?: null;
}

/**
 * Check if a doctor already has an appointment at a given date and time.
 *
 * @param int $doctorId
 * @param string $dateTime
 * @return bool
 */
function doctor_has_conflict($doctorId, $dateTime)
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'SELECT 1 FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND status != "cancelled" LIMIT 1';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('is', $doctorId, $dateTime);
    $statement->execute();
    $statement->store_result();
    $conflict = $statement->num_rows > 0;

    $statement->close();
    $connection->close();

    return $conflict;
}

/**
 * Insert a new appointment record.
 *
 * @param int $patientId
 * @param int $doctorId
 * @param string $dateTime
 * @param string|null $notes
 * @return bool
 */
function create_appointment($patientId, $doctorId, $dateTime, $notes = null)
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, notes, created_at) VALUES (?, ?, ?, "pending", ?, NOW())';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('iiss', $patientId, $doctorId, $dateTime, $notes);
    $success = $statement->execute();

    $statement->close();
    $connection->close();

    return $success;
}

/**
 * Fetch medical reports belonging to the patient user (by users.id).
 *
 * @param int $userId
 * @return array
 */
function fetch_medical_reports_for_user($userId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    $query = "SELECT mr.id, mr.patient_id, mr.file_name, mr.file_path, mr.notes, mr.uploaded_by, mr.created_at
              FROM medical_reports mr
              JOIN patients p ON p.id = mr.patient_id
              WHERE p.user_id = ?
              ORDER BY mr.created_at DESC";

    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return [];
    }

    $statement->bind_param('i', $userId);
    $statement->execute();
    $result = $statement->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $statement->close();
    $connection->close();
    return $rows;
}

/**
 * Fetch a single medical report row by its id with patient owner info.
 *
 * @param int $reportId
 * @return array|null
 */
function fetch_medical_report_by_id($reportId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return null;
    }

    $query = "SELECT mr.id, mr.patient_id, mr.file_name, mr.file_path, mr.notes, mr.uploaded_by, mr.created_at,
                     p.user_id AS patient_user_id
              FROM medical_reports mr
              JOIN patients p ON p.id = mr.patient_id
              WHERE mr.id = ? LIMIT 1";

    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return null;
    }

    $statement->bind_param('i', $reportId);
    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();

    $statement->close();
    $connection->close();
    return $row ?: null;
}

/**
 * Fetch all patients with user info for admin/doctor selection.
 *
 * @return array
 */
function fetch_all_patients()
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    $query = 'SELECT p.id AS patient_id, p.user_id, u.first_name, u.last_name, u.email FROM patients p JOIN users u ON u.id = p.user_id ORDER BY u.last_name, u.first_name';
    $result = $connection->query($query);
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }

    $connection->close();
    return $rows;
}

/**
 * Fetch medical reports for a given patient id.
 *
 * @param int $patientId
 * @return array
 */
function fetch_medical_reports_by_patient_id($patientId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    $query = 'SELECT id, patient_id, file_name, file_path, notes, uploaded_by, created_at FROM medical_reports WHERE patient_id = ? ORDER BY created_at DESC';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return [];
    }

    $statement->bind_param('i', $patientId);
    $statement->execute();
    $result = $statement->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $statement->close();
    $connection->close();
    return $rows;
}

/**
 * Create a feedback/rating record for an appointment.
 *
 * @param int $appointmentId
 * @param int $doctorId
 * @param int $patientId
 * @param int $rating (1-5)
 * @param string $comment
 * @return bool
 */
function create_feedback($appointmentId, $doctorId, $patientId, $rating, $comment)
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'INSERT INTO feedback (appointment_id, doctor_id, patient_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('iiiss', $appointmentId, $doctorId, $patientId, $rating, $comment);
    $success = $statement->execute();

    $statement->close();
    $connection->close();
    return $success;
}

/**
 * Fetch all feedback for a doctor (most recent first).
 *
 * @param int $doctorId
 * @return array
 */
function fetch_doctor_feedback($doctorId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    $query = 'SELECT f.id, f.rating, f.comment, f.created_at, u.first_name, u.last_name FROM feedback f JOIN users u ON u.id = f.patient_id WHERE f.doctor_id = ? ORDER BY f.created_at DESC LIMIT 10';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return [];
    }

    $statement->bind_param('i', $doctorId);
    $statement->execute();
    $result = $statement->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $statement->close();
    $connection->close();
    return $rows;
}

/**
 * Fetch all services with optional category filter.
 *
 * @param string|null $category
 * @return array
 */
function fetch_services($category = null)
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    if ($category) {
        $query = 'SELECT id, name, category, description, price FROM services WHERE category = ? ORDER BY name';
        $statement = $connection->prepare($query);
        if (!$statement) {
            error_log('Prepare failed: ' . $connection->error);
            $connection->close();
            return [];
        }
        $statement->bind_param('s', $category);
    } else {
        $query = 'SELECT id, name, category, description, price FROM services ORDER BY category, name';
        $statement = $connection->prepare($query);
        if (!$statement) {
            error_log('Prepare failed: ' . $connection->error);
            $connection->close();
            return [];
        }
    }

    $statement->execute();
    $result = $statement->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $statement->close();
    $connection->close();
    return $rows;
}

/**
 * Fetch distinct service categories.
 *
 * @return array
 */
function fetch_service_categories()
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    $query = 'SELECT DISTINCT category FROM services ORDER BY category';
    $result = $connection->query($query);
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row['category'];
        }
        $result->free();
    }

    $connection->close();
    return $rows;
}

/**
 * Upload medical report for a patient (by admin/doctor).
 *
 * @param int $patientId
 * @param string $fileName
 * @param string $filePath
 * @param string|null $notes
 * @param string $uploadedBy (user name)
 * @return bool
 */
function create_medical_report($patientId, $fileName, $filePath, $notes, $uploadedBy)
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'INSERT INTO medical_reports (patient_id, file_name, file_path, notes, uploaded_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('issss', $patientId, $fileName, $filePath, $notes, $uploadedBy);
    $success = $statement->execute();

    $statement->close();
    $connection->close();
    return $success;
}

/**
 * Fetch all blog posts, ordered by date descending.
 *
 * @return array
 */
function fetch_blog_posts()
{
    $connection = get_db_connection();
    if (!$connection) {
        return [];
    }

    $query = 'SELECT b.id, b.title, b.content, b.author_id, b.created_at, u.first_name, u.last_name FROM blog_posts b LEFT JOIN users u ON b.author_id = u.id ORDER BY b.created_at DESC';
    $result = $connection->query($query);
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }

    $connection->close();
    return $rows;
}

/**
 * Fetch a single blog post by id.
 *
 * @param int $postId
 * @return array|null
 */
function fetch_blog_post_by_id($postId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return null;
    }

    $query = 'SELECT id, title, content, author_id, created_at FROM blog_posts WHERE id = ? LIMIT 1';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return null;
    }

    $statement->bind_param('i', $postId);
    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();

    $statement->close();
    $connection->close();
    return $row ?: null;
}

/**
 * Create a blog post (admin/doctor only).
 *
 * @param string $title
 * @param string $content
 * @param int $authorId
 * @return bool
 */
function create_blog_post($title, $content, $authorId)
{
    $connection = get_db_connection();
    if (!$connection) {
        return false;
    }

    $query = 'INSERT INTO blog_posts (title, content, author_id, created_at) VALUES (?, ?, ?, NOW())';
    $statement = $connection->prepare($query);
    if (!$statement) {
        error_log('Prepare failed: ' . $connection->error);
        $connection->close();
        return false;
    }

    $statement->bind_param('ssi', $title, $content, $authorId);
    $success = $statement->execute();

    $statement->close();
    $connection->close();
    return $success;
}
