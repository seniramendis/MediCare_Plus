<?php
require_once 'auth.php';
require_role('patient'); // Ensures user is logged in as patient

$pageTitle = 'Book Appointment - Medicare Plus';
$msg = "";
$patient = fetch_patient_by_user_id($_SESSION['user_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $patient) {
    $doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_VALIDATE_INT);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // Combine date and time to match the SQL DATETIME format
    $datetime_string = $date . ' ' . $time;
    $appointmentDateTime = DateTime::createFromFormat('Y-m-d h:i A', $datetime_string);

    if (!$doctor_id) {
        $msg = "<div style='background:#fee2e2; color:#c62828; padding:15px; border-radius:5px; margin-bottom:20px; border: 1px solid #fca5a5;'>Please select a doctor.</div>";
    } elseif (!$appointmentDateTime) {
        $msg = "<div style='background:#fee2e2; color:#c62828; padding:15px; border-radius:5px; margin-bottom:20px; border: 1px solid #fca5a5;'>Invalid date or time format.</div>";
    } elseif ($appointmentDateTime < new DateTime()) {
        $msg = "<div style='background:#fee2e2; color:#c62828; padding:15px; border-radius:5px; margin-bottom:20px; border: 1px solid #fca5a5;'>Appointment date must be in the future.</div>";
    } elseif (doctor_has_conflict($doctor_id, $appointmentDateTime->format('Y-m-d H:i:s'))) {
        $msg = "<div style='background:#fee2e2; color:#c62828; padding:15px; border-radius:5px; margin-bottom:20px; border: 1px solid #fca5a5;'>
                    <i class='fas fa-exclamation-circle'></i> 
                    <strong>Slot Unavailable:</strong> This doctor is already booked for $time on $date. Please choose a different time.
                </div>";
    } else {
        // Use the actual create_appointment function from auth.php
        $created = create_appointment($patient['id'], $doctor_id, $appointmentDateTime->format('Y-m-d H:i:s'), $reason);

        if ($created) {
            $msg = "<div style='background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:20px; border: 1px solid #c3e6cb;'>
                        <i class='fas fa-check-circle'></i> 
                        <strong>Success!</strong> Your appointment has been confirmed. View status in your <a href='dashboard_patient.php' style='color:#155724; font-weight:bold;'>Dashboard</a>.
                    </div>";
        } else {
            $msg = "<div style='background:#fee2e2; color:#c62828; padding:15px; border-radius:5px; margin-bottom:20px; border: 1px solid #fca5a5;'>Error: Unable to schedule the appointment. Please try again later.</div>";
        }
    }
} elseif (!$patient) {
    $msg = "<div style='background:#fee2e2; color:#c62828; padding:15px; border-radius:5px; margin-bottom:20px; border: 1px solid #fca5a5;'><strong>Error:</strong> Your patient record is not available. Please contact support.</div>";
}

$availableDoctors = fetch_all_doctors();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Appointment - Medicare Plus</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #1e3a8a;
            border-bottom: 3px solid #57c95a;
            display: inline-block;
            padding-bottom: 5px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }

        button {
            background: #57c95a;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 30px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
        }

        button:hover {
            background: #45a049;
        }
    </style>
    <link rel="icon" href="images/Favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <?php echo $msg; ?>

        <h2>Book an Appointment</h2>
        <p>Please fill in the form below. Our team will confirm your slot shortly.</p>

        <form method="POST" action="">
            <div class="form-grid">

                <div class="full-width">
                    <label>Select Doctor & Department</label>
                    <select name="doctor_id" required>
                        <option value="">-- Choose a Specialist --</option>
                        <?php
                        if (!empty($availableDoctors)) {
                            foreach ($availableDoctors as $doc) {
                                echo "<option value='" . $doc['id'] . "'>Dr. " . htmlspecialchars($doc['first_name'] . " " . $doc['last_name']) . " (" . htmlspecialchars($doc['specialization']) . ")</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No doctors available</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label>Preferred Date</label>
                    <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div>
                    <label>Preferred Time</label>
                    <select name="time" required>
                        <option value="09:00 AM">09:00 AM</option>
                        <option value="10:00 AM">10:00 AM</option>
                        <option value="11:00 AM">11:00 AM</option>
                        <option value="02:00 PM">02:00 PM</option>
                        <option value="03:00 PM">03:00 PM</option>
                        <option value="04:00 PM">04:00 PM</option>
                    </select>
                </div>

                <div class="full-width">
                    <label>Reason for Visit / Symptoms</label>
                    <textarea name="message" rows="4" placeholder="Describe your symptoms briefly..." required></textarea>
                </div>
            </div>

            <button type="submit"><i class="fas fa-calendar-check"></i> Confirm Booking</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>