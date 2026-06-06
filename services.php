<?php
include('header.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services | MediCare Plus</title>
    <link rel="stylesheet" href="assets/css/HomeStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .services-header {
            text-align: center;
            padding: 60px 20px;
            background-color: #f0f4f8;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 50px 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .service-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, border-bottom 0.3s ease;
            border-bottom: 5px solid transparent;
        }

        .service-card:hover {
            transform: translateY(-10px);
            border-bottom: 5px solid #2b6cb0;
        }

        .service-icon {
            font-size: 3rem;
            color: #2b6cb0;
            margin-bottom: 20px;
            background: #ebf8ff;
            width: 90px;
            height: 90px;
            line-height: 90px;
            border-radius: 50%;
            display: inline-block;
        }

        .service-card h3 {
            color: #2d3748;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .service-card p {
            color: #718096;
            line-height: 1.6;
        }
    </style>
</head>

<body>

    <div class="services-header">
        <h1 style="color: #2b6cb0; font-size: 2.5rem; margin-bottom: 10px;">Medical Services</h1>
        <p style="color: #4a5568; font-size: 1.1rem;">Comprehensive healthcare solutions tailored to your needs.</p>
    </div>

    <div class="services-grid">
        <div class="service-card">
            <div class="service-icon"><i class="fas fa-heartbeat"></i></div>
            <h3>Cardiology</h3>
            <p>Advanced heart care, ECGs, and expert consultations with leading cardiologists to keep your heart healthy.</p>
        </div>
        <div class="service-card">
            <div class="service-icon"><i class="fas fa-microscope"></i></div>
            <h3>Laboratory Services</h3>
            <p>State-of-the-art diagnostic testing, blood work, and fast, accurate results delivered securely online.</p>
        </div>
        <div class="service-card">
            <div class="service-icon"><i class="fas fa-user-md"></i></div>
            <h3>General Consultations</h3>
            <p>Routine check-ups, preventive care, and expert medical advice for you and your entire family.</p>
        </div>
        <div class="service-card">
            <div class="service-icon"><i class="fas fa-x-ray"></i></div>
            <h3>Radiology & Imaging</h3>
            <p>High-resolution X-Rays, MRI, and CT scanning facilities utilizing the latest medical technology.</p>
        </div>
        <div class="service-card">
            <div class="service-icon"><i class="fas fa-baby"></i></div>
            <h3>Pediatrics</h3>
            <p>Specialized healthcare for infants, children, and adolescents by compassionate pediatric specialists.</p>
        </div>
        <div class="service-card">
            <div class="service-icon"><i class="fas fa-ambulance"></i></div>
            <h3>Emergency Care</h3>
            <p>24/7 urgent medical attention and trauma care equipped to handle all critical healthcare emergencies.</p>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>