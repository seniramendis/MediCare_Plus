<?php
require_once 'db_connect.php';

$conn = get_db_connection();
if (!$conn) {
    http_response_code(500);
    error_log('add_doctors_safe.php: Database connection failed.');
    echo '<p style="color:red;">Database connection failed. Please check server logs.</p>';
    exit;
}

// List of 20 doctors (idempotent insert)
$doctors = [
    ['Chamara', 'Wijesekara', 'chamara@medicareplus.lk', 'Cardiology', 'MBBS (University of Colombo), MD in Cardiology', 15, 8500, 'Mon-Fri 9AM-5PM', 4.8],
    ['Priyanka', 'de Silva', 'priyanka@medicareplus.lk', 'Orthopedics', 'MBBS (University of Peradeniya), Orthopedic Specialist', 12, 7500, 'Tue-Thu 10AM-4PM', 4.6],
    ['Anura', 'Perera', 'anura@medicareplus.lk', 'Pediatrics', 'MBBS (University of Colombo), MD in Child Health', 10, 6500, 'Mon-Wed-Fri 9AM-12PM', 4.9],
    ['Jayantha', 'Kariyawasam', 'jayantha@medicareplus.lk', 'Neurology', 'MBBS (University of Kelaniya), MD in Neurology', 18, 9000, 'Mon-Thu 11AM-5PM', 4.7],
    ['Madhavi', 'Gunasekara', 'madhavi@medicareplus.lk', 'Dermatology', 'MBBS (University of Sri Jayewardenepura), Dermatology Specialist', 8, 6000, 'Wed-Fri 2PM-6PM', 4.5],
    ['Roshan', 'Mendis', 'roshan@medicareplus.lk', 'Oncology', 'MBBS (University of Colombo), MD in Cancer Medicine', 16, 10000, 'Tue-Thu 10AM-3PM', 4.9],
    ['Sithara', 'Rodrigues', 'sithara@medicareplus.lk', 'ENT', 'MBBS (University of Peradeniya), ENT Specialist', 11, 7000, 'Mon-Fri 3PM-6PM', 4.6],
    ['Induja', 'Fernando', 'induja@medicareplus.lk', 'Gynecology', 'MBBS (University of Colombo), MD in Obstetrics', 13, 8000, 'Tue-Wed-Thu 9AM-4PM', 4.8],
    ['Kumara', 'Dissanayake', 'kumara@medicareplus.lk', 'General Practice', 'MBBS (University of Kelaniya), General Medicine Specialist', 20, 5500, 'Mon-Fri 8AM-6PM', 4.7],
    ['Nirmala', 'de Silva', 'nirmala@medicareplus.lk', 'Psychiatry', 'MBBS (University of Colombo), MD in Psychiatry', 14, 7000, 'Tue-Fri 10AM-4PM', 4.8],
    ['Sanjaya', 'Fernando', 'sanjaya@medicareplus.lk', 'Gastroenterology', 'MBBS (University of Peradeniya), MD in Gastroenterology', 17, 9500, 'Mon-Thu 9AM-5PM', 4.9],
    ['Amila', 'Bandara', 'amila@medicareplus.lk', 'Pulmonology', 'MBBS (University of Sri Jayewardenepura), Respiratory Specialist', 13, 8000, 'Wed-Fri 10AM-4PM', 4.6],
    ['Ruwan', 'Jayasinghe', 'ruwan@medicareplus.lk', 'Urology', 'MBBS (University of Colombo), MD in Urology', 16, 8500, 'Mon-Wed-Fri 9AM-3PM', 4.7],
    ['Lakshmi', 'Jayasuriya', 'lakshmi@medicareplus.lk', 'Ophthalmology', 'MBBS (University of Kelaniya), Eye Care Specialist', 11, 7500, 'Tue-Thu-Sat 10AM-5PM', 4.8],
    ['Nilanthi', 'Perera', 'nilanthi@medicareplus.lk', 'Rheumatology', 'MBBS (University of Colombo), MD in Rheumatology', 12, 7800, 'Mon-Fri 9AM-4PM', 4.5],
    ['Ravi', 'Kumara', 'ravi@medicareplus.lk', 'Orthopedic Surgery', 'MBBS (University of Peradeniya), MD in Orthopedic Surgery', 19, 9500, 'Tue-Fri 10AM-6PM', 4.9],
    ['Malini', 'Rodrigues', 'malini@medicareplus.lk', 'Endocrinology', 'MBBS (University of Sri Jayewardenepura), Diabetes Specialist', 10, 7200, 'Mon-Wed-Fri 9AM-3PM', 4.6],
    ['Darshan', 'Fonseka', 'darshan@medicareplus.lk', 'Nephrology', 'MBBS (University of Colombo), MD in Nephrology', 15, 8200, 'Tue-Thu 10AM-4PM', 4.7],
    ['Samantha', 'Silva', 'samantha@medicareplus.lk', 'Infectious Diseases', 'MBBS (University of Kelaniya), ID Specialist', 14, 7500, 'Mon-Fri 9AM-5PM', 4.8],
    ['Vasantha', 'Wickramasinghe', 'vasantha@medicareplus.lk', 'Hematology', 'MBBS (University of Peradeniya), Hematology Specialist', 12, 7800, 'Wed-Fri 10AM-4PM', 4.6],
];

$insertedUsers = 0;
$insertedDoctors = 0;
$skipped = 0;

foreach ($doctors as $d) {
    list($first, $last, $email, $spec, $qual, $years, $fee, $avail, $rating) = $d;

    // Check if user exists
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if ($user) {
        $user_id = $user['id'];
    } else {
        // Insert user — default password is loaded from .env to avoid hardcoded credentials
        $envFile = __DIR__ . '/.env';
        $defaultPassword = 'changeme';
        if (is_readable($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (strpos($line, 'DOCTOR_DEFAULT_PASSWORD=') === 0) {
                    $defaultPassword = substr($line, strlen('DOCTOR_DEFAULT_PASSWORD='));
                    break;
                }
            }
        }
        $password_hash = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (first_name, last_name, email, password_hash, role, status, created_at) VALUES (?, ?, ?, ?, "doctor", "active", NOW())');
        $stmt->bind_param('ssss', $first, $last, $email, $password_hash);
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $insertedUsers++;
        } else {
            echo 'User insert failed for ' . htmlspecialchars($email) . ': ' . $stmt->error . '<br>';
            $stmt->close();
            continue;
        }
        $stmt->close();
    }

    // Check doctor profile exists for this user
    $stmt = $conn->prepare('SELECT id FROM doctors WHERE user_id = ? LIMIT 1');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $doc = $res->fetch_assoc();
    $stmt->close();

    if ($doc) {
        $skipped++;
        continue;
    }

    // Insert doctor profile
    $stmt = $conn->prepare('INSERT INTO doctors (user_id, specialization, qualifications, experience_years, consultation_fee, availability, rating) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('issiids', $user_id, $spec, $qual, $years, $fee, $avail, $rating);
    if ($stmt->execute()) {
        $insertedDoctors++;
    } else {
        echo 'Doctor insert failed for ' . htmlspecialchars($email) . ': ' . $stmt->error . '<br>';
    }
    $stmt->close();
}

echo "<h2>Done</h2>\n";
echo "<p>Users inserted: $insertedUsers</p>\n";
echo "<p>Doctors inserted: $insertedDoctors</p>\n";
echo "<p>Profiles skipped (already existed): $skipped</p>\n";
echo "<p><a href='doctors.php'>View Doctors</a></p>\n";

$conn->close();
