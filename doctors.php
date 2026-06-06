<?php
// Include the database connection and the navigation header
include('db_connect.php');
include('header.php');

// Fetch doctors by joining the 'users' and 'doctors' tables together
$conn = get_db_connection();
$query = "
    SELECT 
        u.id AS user_id, 
        d.id AS doc_id,
        CONCAT(u.first_name, ' ', u.last_name) AS name, 
        d.specialization
    FROM users u 
    JOIN doctors d ON u.id = d.user_id 
    WHERE u.role = 'doctor' AND u.status = 'active'
";
$result = $conn ? $conn->query($query) : false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Specialists | MediCare Plus</title>
    <link rel="stylesheet" href="assets/css/HomeStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Search Bar Styling */
        .search-container {
            text-align: center;
            margin: -30px auto 40px auto;
            position: relative;
            z-index: 10;
        }

        .doc-search-bar {
            width: 60%;
            max-width: 600px;
            padding: 15px 25px;
            border-radius: 50px;
            border: 1px solid #cbd5e0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .doc-search-bar:focus {
            border-color: #3182ce;
            box-shadow: 0 4px 20px rgba(49, 130, 206, 0.2);
        }

        /* Grid Layout */
        .doctor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            padding: 20px 5%;
            max-width: 1200px;
            margin: 0 auto 60px auto;
        }

        /* Individual Card Styling */
        .doctor-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            padding: 35px 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #edf2f7;
        }

        .doctor-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        /* Circular Profile Image */
        .doc-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 5px solid #ebf8ff;
            /* Light blue border */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* Typography inside cards */
        .doc-name {
            color: #2d3748;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .doc-specialty {
            color: #e53e3e;
            /* Distinctive red/pink for specialty */
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Icons */
        .doc-icons {
            margin-bottom: 25px;
            color: #a0aec0;
        }

        .doc-icons i {
            margin: 0 10px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .doc-icons i:hover {
            color: #2b6cb0;
            /* Highlight blue on hover */
        }

        /* View Profile Button */
        .view-profile-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #fff5f5;
            /* Light red background */
            color: #e53e3e;
            /* Red text */
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .view-profile-btn:hover {
            background: #e53e3e;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
        }
    </style>
</head>

<body>

    <div class="page-header" style="text-align: center; padding: 60px 20px; background-color: #f0f4f8;">
        <h1 style="color: #2b6cb0; font-size: 2.5rem; margin-bottom: 10px;">Find a Specialist Doctor</h1>
        <p style="color: #4a5568; font-size: 1.1rem;">Book an appointment with our highly qualified medical professionals.</p>
    </div>

    <div class="search-container">
        <input type="text" id="doctorSearch" placeholder="Search by name or specialty (e.g., Cardiology)..." class="doc-search-bar" onkeyup="filterDoctors()">
    </div>

    <div class="doctor-grid" id="doctorGrid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="doctor-card">
                    <?php
                    $imagePath = isset($row['profile_image']) && !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : 'default-doc.jpg';
                    ?>
                    <img src="assets/images/<?php echo $imagePath; ?>" alt="Doctor Image" class="doc-img">

                    <h3 class="doc-name">Dr. <?php echo htmlspecialchars($row['name']); ?></h3>
                    <p class="doc-specialty"><?php echo htmlspecialchars($row['specialization']); ?></p>

                    <div class="doc-icons">
                        <i class="fas fa-envelope" title="Message Doctor"></i>
                        <i class="fas fa-calendar-check" title="Check Availability"></i>
                    </div>

                    <a href="doctor-profile.php?id=<?php echo $row['user_id']; ?>" class="view-profile-btn">View Profile</a>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; width: 100%; padding: 40px;">
                <p style="color: #718096; font-size: 1.2rem;">No specialist doctors found at this time.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function filterDoctors() {
            let input = document.getElementById('doctorSearch').value.toLowerCase();
            let cards = document.getElementsByClassName('doctor-card');

            for (let i = 0; i < cards.length; i++) {
                let name = cards[i].getElementsByClassName('doc-name')[0].innerText.toLowerCase();
                let specialty = cards[i].getElementsByClassName('doc-specialty')[0].innerText.toLowerCase();

                if (name.includes(input) || specialty.includes(input)) {
                    cards[i].style.display = "block";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }
    </script>

    <?php
    // Include the footer at the bottom
    include('footer.php');
    ?>
</body>

</html>