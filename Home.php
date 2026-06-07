<?php
require_once 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Home';
$pageKey = 'home';
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicare Plus - Your Lifetime Partner in Health</title>
    <link rel="stylesheet" href="assets/css/HomeStyles.css?v=2.0">
    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>
    <!-- AOS Animation Library CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body style="background-color: #f8fafc; margin: 0; padding: 0; overflow-x: hidden;">

    <!-- Premium Hero Section (Replaces the old slideshow) -->
    <section class="hero-section">
        <div class="hero-content" data-aos="zoom-in" data-aos-duration="1200">
            <h1>Your Partner for a <br><span>Lifetime of Health</span></h1>
            <p>Experience world-class healthcare with Sri Lanka's leading medical specialists. Advanced diagnostics, 24/7 emergency care, and a commitment to your well-being.</p>
            <a href="book_appointment.php" class="btn-premium"><i class="fa-solid fa-calendar-check"></i> Book an Appointment</a>
        </div>
    </section>

    <!-- Quote Section -->
    <div class="quote" style="padding: 80px 20px; background: #ffffff; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.03);" data-aos="fade-up">
        <h2 style="font-size: 2rem; color: #4a5568; font-style: italic; max-width: 900px; margin: 0 auto; line-height: 1.5;">"The good physician treats the disease; the great physician treats the patient who has the disease."</h2>
        <p style="margin-top: 25px; color: #2b6cb0; font-weight: bold; font-size: 1.2rem;">- William Osler</p>
    </div>

    <!-- Services Section -->
    <main class="page-container-home" id="Services">
        <h1 class="section-title" data-aos="fade-up">OUR SERVICES</h1>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">We provide world-class specialty care across a wide range of medical fields. Our expert teams use the latest technology to ensure the best possible outcomes.</p>

        <div class="service-grid-home">
            <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                <div class="service-icon-container" style="color: #e53e3e;"><i class="fa-solid fa-heart-pulse"></i></div>
                <h4>Cardiology</h4>
                <p>Expert care for your heart, including disease management and post-heart attack care.</p>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                <div class="service-icon-container" style="color: #3182ce;"><i class="fa-solid fa-child"></i></div>
                <h4>Pediatrics</h4>
                <p>Comprehensive health services for infants, children, and adolescents.</p>
            </div>

            <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                <div class="service-icon-container" style="color: #dd6b20;"><i class="fa-solid fa-bone"></i></div>
                <h4>Orthopedics</h4>
                <p>Treatment for bone, joint, and sports-related injuries.</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 50px;" data-aos="zoom-in">
            <a href="services.php" class="btn-premium" style="background: #1e3a8a;">Explore All Services</a>
        </div>
    </main>

    <!-- Doctors Preview Section -->
    <section class="page-container-home doctor-preview-section" style="background: #ffffff; border-radius: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); margin-bottom: 80px; padding-bottom: 60px;">
        <h1 class="section-title" data-aos="fade-up" style="padding-top: 40px;"><i class="fa-solid fa-user-doctor"></i> Featured Specialists</h1>

        <div class="doctor-slide-inner-grid">
            <div class="doctor-card" data-aos="fade-up" data-aos-delay="100">
                <img src="images/Dr. Gotabhaya Ranasinghe.webp" alt="Dr. Gotabhaya Ranasinghe">
                <div class="doctor-info">
                    <h4>Dr. Gotabhaya Ranasinghe</h4>
                    <p class="doctor-specialty" style="color: #e53e3e;">Cardiology</p>
                    <a href="doctors.php" class="profile-link">View Profile &rarr;</a>
                </div>
            </div>

            <div class="doctor-card" data-aos="fade-up" data-aos-delay="200">
                <img src="images/dr-shaman.png" alt="Prof. Shaman Rajindrajith">
                <div class="doctor-info">
                    <h4>Prof. Shaman Rajindrajith</h4>
                    <p class="doctor-specialty" style="color: #3182ce;">Pediatrics</p>
                    <a href="doctors.php" class="profile-link">View Profile &rarr;</a>
                </div>
            </div>

            <div class="doctor-card" data-aos="fade-up" data-aos-delay="300">
                <img src="images/Nayana Perera.jpeg" alt="Dr. Nayana Perera">
                <div class="doctor-info">
                    <h4>Dr. Nayana Perera</h4>
                    <p class="doctor-specialty" style="color: #dd6b20;">Dermatology</p>
                    <a href="doctors.php" class="profile-link">View Profile &rarr;</a>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;" data-aos="zoom-in">
            <a href="doctors.php" class="btn-premium">Search All Specialists</a>
        </div>
    </section>

    <!-- About Section -->
    <div class="About-section" id="AboutUs" data-aos="fade-up">
        <div class="AboutText" data-aos="fade-right" data-aos-delay="200">
            <h1>ABOUT MEDICARE PLUS</h1>
            <p>Welcome to MediCare Plus, a leading private healthcare provider dedicated to delivering a comprehensive range of medical services. Our mission is to ensure the well-being of our community by offering accessible, efficient, and high-quality care.</p>
            <p>In response to rising patient expectations and the need for improved service delivery, we are proud to introduce our new interactive web platform. This site is a cornerstone of our commitment to digital transformation in healthcare.</p>
        </div>
        <div class="AboutImage" data-aos="fade-left" data-aos-delay="400">
            <img src="images/Logo4.png" alt="Medicare Plus Logo">
        </div>
    </div>

    <!-- AOS JS Implementation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize the Scroll Animations
        AOS.init({
            duration: 900,
            easing: 'ease-in-out-cubic',
            once: true,
            offset: 50
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>