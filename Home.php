<?php
session_start();
$pageTitle = 'Home';
$pageKey = 'home';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Medicare Plus' : 'Medicare Plus - Your Lifetime Partner in Health'; ?></title>
    <link rel="icon" href="images/Favicon.png" type="image/png">
    <link rel="stylesheet" href="assets/css/HomeStyles.css?v=3.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>
    <style>
        /* ====================================================
   HERO SECTION — Photo Transition Slideshow
   (replaces old animated blue canvas background)
   ==================================================== */
        :root {
            --mp-navy: #0d2b5e;
            --mp-teal: #0aa698;
            --mp-green: #2ecc71;
            --mp-light: #f0f7f6;
            --mp-white: #ffffff;
            --transition-speed: 1.4s;
        }

        * {
            box-sizing: border-box;
        }

        /* ---- Hero Wrapper ---- */
        .hero-slideshow {
            position: relative;
            width: 100%;
            height: 92vh;
            min-height: 560px;
            overflow: hidden;
        }

        /* Each slide = full-bleed photo */
        .hero-slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center top;
            opacity: 0;
            transform: scale(1.04);
            transition: opacity var(--transition-speed) ease-in-out,
                transform 8s ease-in-out;
            z-index: 0;
        }

        .hero-slide.active {
            opacity: 1;
            transform: scale(1);
            z-index: 1;
        }

        /* Dark overlay for text legibility */
        .hero-slide::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to right,
                    rgba(8, 28, 70, 0.72) 0%,
                    rgba(8, 28, 70, 0.38) 60%,
                    rgba(8, 28, 70, 0.10) 100%);
        }

        /* Slide images — uses Unsplash hospital / healthcare photos */
        .hero-slide:nth-child(1) {
            background-image: url('images/Slideshow1.png'),
                url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=1800&q=80');
        }

        .hero-slide:nth-child(2) {
            background-image: url('images/Slideshow2.png'),
                url('https://images.unsplash.com/photo-1551076805-e1869033e561?w=1800&q=80');
        }

        .hero-slide:nth-child(3) {
            background-image: url('images/Slideshow3.png'),
                url('https://images.unsplash.com/photo-1631217868264-e5b90bb7e133?w=1800&q=80');
        }

        .hero-slide:nth-child(4) {
            background-image: url('images/Slideshow4.png'),
                url('https://images.unsplash.com/photo-1504813184591-01572f98c85f?w=1800&q=80');
        }

        /* ---- Hero Content ---- */
        .hero-content {
            position: absolute;
            inset: 0;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 8% 80px;
            max-width: 860px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(6px);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.82rem;
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 30px;
            margin-bottom: 22px;
            width: fit-content;
            animation: fadeSlideUp 0.9s ease both;
        }

        .hero-headline {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(2.4rem, 5.5vw, 4.2rem);
            font-weight: 700;
            color: #fff;
            line-height: 1.12;
            margin: 0 0 20px;
            animation: fadeSlideUp 1.0s 0.15s ease both;
        }

        .hero-headline span {
            color: var(--mp-teal);
        }

        .hero-sub {
            font-family: 'DM Sans', sans-serif;
            font-size: clamp(1rem, 1.6vw, 1.18rem);
            font-weight: 300;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.65;
            margin: 0 0 38px;
            max-width: 530px;
            animation: fadeSlideUp 1.0s 0.3s ease both;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            animation: fadeSlideUp 1.0s 0.45s ease both;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 30px;
            border-radius: 50px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.28s ease;
            cursor: pointer;
        }

        .hero-btn-primary {
            background: var(--mp-teal);
            color: #fff;
            border: 2px solid var(--mp-teal);
        }

        .hero-btn-primary:hover {
            background: transparent;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(10, 166, 152, 0.35);
        }

        .hero-btn-outline {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.7);
        }

        .hero-btn-outline:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }

        /* ---- Slide navigation dots ---- */
        .hero-dots {
            position: absolute;
            bottom: 32px;
            left: 8%;
            z-index: 12;
            display: flex;
            gap: 10px;
        }

        .hero-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            border: none;
            cursor: pointer;
            padding: 0;
            transition: all 0.3s ease;
        }

        .hero-dot.active {
            width: 28px;
            border-radius: 4px;
            background: var(--mp-teal);
        }

        /* Prev / Next arrows */
        .hero-arrow {
            position: absolute;
            bottom: 24px;
            right: 8%;
            z-index: 12;
            display: flex;
            gap: 10px;
        }

        .hero-arrow button {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-arrow button:hover {
            background: var(--mp-teal);
        }

        /* Progress bar at the bottom */
        .hero-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: var(--mp-teal);
            width: 0%;
            z-index: 13;
            transition: none;
        }

        /* ---- Stats ribbon below hero ---- */
        .hero-stats-ribbon {
            background: var(--mp-navy);
            padding: 22px 8%;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 40px;
            border-right: 1px solid rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-item .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--mp-teal);
            line-height: 1;
        }

        .stat-item .stat-lbl {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.78rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* ====================================================
   SERVICES SECTION
   ==================================================== */
        .section-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .section-eyebrow {
            display: inline-block;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--mp-teal);
            margin-bottom: 10px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 3.5vw, 2.6rem);
            font-weight: 700;
            color: var(--mp-navy);
            margin: 0 0 14px;
        }

        .section-lead {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.05rem;
            color: #5a6370;
            max-width: 620px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* Carousel */
        .service-carousel-container {
            position: relative;
        }

        .service-prev,
        .service-next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 46px;
            height: 46px;
            color: var(--mp-navy);
            background-color: white;
            font-weight: bold;
            font-size: 18px;
            border-radius: 50%;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
            text-decoration: none;
            z-index: 100;
            transition: all 0.3s ease;
        }

        .service-prev {
            left: -22px;
        }

        .service-next {
            right: -22px;
        }

        .service-prev:hover,
        .service-next:hover {
            background-color: var(--mp-teal);
            color: white;
        }

        @media (max-width: 768px) {
            .service-prev {
                left: 0;
            }

            .service-next {
                right: 0;
            }
        }

        /* Service cards — refreshed */
        .service-card {
            background: #fff;
            border-radius: 16px;
            padding: 32px 24px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e8edf2;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--mp-teal), var(--mp-navy));
            transform: scaleX(0);
            transition: transform 0.35s ease;
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(13, 43, 94, 0.10);
        }

        .service-icon-container {
            width: 70px;
            height: 70px;
            margin: 0 auto 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            background: var(--mp-light);
            color: var(--mp-teal);
            transition: background 0.3s, color 0.3s;
        }

        .service-card:hover .service-icon-container {
            background: var(--mp-teal);
            color: #fff;
        }

        .service-card h4 {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--mp-navy);
            margin: 0 0 10px;
        }

        .service-card p {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: #6b7480;
            line-height: 1.6;
            margin: 0 0 18px;
        }

        .card-link {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--mp-teal);
            text-decoration: none;
            transition: gap 0.2s;
        }

        .card-link:hover {
            text-decoration: underline;
        }

        /* ====================================================
   DOCTOR CARDS — refreshed
   ==================================================== */
        .doctor-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e8edf2;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .doctor-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 48px rgba(13, 43, 94, 0.13);
        }

        .doctor-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .doctor-info {
            padding: 20px;
            text-align: center;
        }

        .doctor-info h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            color: var(--mp-navy);
            margin: 0 0 4px;
        }

        .doctor-title {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.85rem;
            color: #6b7480;
            margin: 0 0 6px;
        }

        .doctor-specialty {
            display: inline-block;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.74rem;
            font-weight: 600;
            color: var(--mp-teal);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            background: var(--mp-light);
            padding: 3px 12px;
            border-radius: 30px;
            margin-bottom: 14px;
        }

        /* ====================================================
   QUOTE SECTION
   ==================================================== */
        .quote {
            background: var(--mp-navy);
            padding: 52px 8%;
            text-align: center;
        }

        .quote h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.1rem, 2.2vw, 1.55rem);
            font-weight: 400;
            font-style: italic;
            color: rgba(255, 255, 255, 0.88);
            line-height: 1.7;
            max-width: 800px;
            margin: 0 auto 12px;
        }

        .quote p {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--mp-teal);
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ====================================================
   ANIMATION KEYFRAMES
   ==================================================== */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(26px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ====================================================
   RESPONSIVE
   ==================================================== */
        @media (max-width: 600px) {
            .hero-content {
                padding: 0 6% 80px;
            }

            .stat-item {
                padding: 0 18px;
            }

            .stat-item .stat-num {
                font-size: 1.5rem;
            }

            .hero-arrow {
                display: none;
            }
        }
    </style>
</head>

<body>
    <?php include 'nav_only.php'; ?>

    <!-- ================================================
     HERO — Photo transition slideshow
     ================================================ -->
    <section class="hero-slideshow" id="heroSlideshow" aria-label="Hospital hero section">

        <div class="hero-slide active" role="img" aria-label="Your partner for a lifetime of health"></div>
        <div class="hero-slide" role="img" aria-label="Visit us online at medicareplus.lk"></div>
        <div class="hero-slide" role="img" aria-label="Follow us on social media"></div>
        <div class="hero-slide" role="img" aria-label="Sri Lanka's number one digital health platform"></div>

        <!-- Content overlay -->
        <div class="hero-content">
            <span class="hero-badge">
                <i class="fa-solid fa-shield-halved"></i>
                Sri Lanka's #1 Digital Health Platform
            </span>

            <h1 class="hero-headline">
                Your Partner for a<br>
                <span>Lifetime of Health</span>
            </h1>

            <p class="hero-sub">
                World-class specialists. Seamless appointments. Secure medical records. All in one place — designed for you.
            </p>

            <div class="hero-actions">
                <a href="book_appointment.php" class="hero-btn hero-btn-primary">
                    <i class="fa-regular fa-calendar-check"></i> Book Appointment
                </a>
                <a href="find_a_doctor.php" class="hero-btn hero-btn-outline">
                    <i class="fa-solid fa-user-doctor"></i> Find a Doctor
                </a>
            </div>
        </div>

        <!-- Dots -->
        <div class="hero-dots" role="tablist" aria-label="Slide navigation">
            <button class="hero-dot active" onclick="goToSlide(0)" aria-label="Slide 1"></button>
            <button class="hero-dot" onclick="goToSlide(1)" aria-label="Slide 2"></button>
            <button class="hero-dot" onclick="goToSlide(2)" aria-label="Slide 3"></button>
            <button class="hero-dot" onclick="goToSlide(3)" aria-label="Slide 4"></button>
        </div>

        <!-- Prev / Next -->
        <div class="hero-arrow">
            <button onclick="changeHeroSlide(-1)" aria-label="Previous slide">&#10094;</button>
            <button onclick="changeHeroSlide(1)" aria-label="Next slide">&#10095;</button>
        </div>

        <!-- Progress bar -->
        <div class="hero-progress" id="heroProgress"></div>
    </section>

    <!-- Stats Ribbon -->
    <div class="hero-stats-ribbon">
        <div class="stat-item">
            <span class="stat-num">2,400+</span>
            <span class="stat-lbl">Patients Served</span>
        </div>
        <div class="stat-item">
            <span class="stat-num">48</span>
            <span class="stat-lbl">Specialist Doctors</span>
        </div>
        <div class="stat-item">
            <span class="stat-num">7</span>
            <span class="stat-lbl">Departments</span>
        </div>
        <div class="stat-item">
            <span class="stat-num">24/7</span>
            <span class="stat-lbl">Emergency Care</span>
        </div>
    </div>

    <!-- ================================================
     SERVICES
     ================================================ -->
    <main class="page-container-home" id="Services">
        <div class="section-header">
            <span class="section-eyebrow">What We Offer</span>
            <h1 class="section-title">Our Services</h1>
            <p class="section-lead">We provide world-class specialty care across a wide range of medical fields. Our expert teams use the latest technology to ensure the best possible outcomes.</p>
        </div>

        <div class="service-carousel-container">
            <div class="service-slide">
                <div class="service-grid-home">
                    <div class="service-card icon-cardiology">
                        <div class="service-icon-container"><i class="fa-solid fa-heart-pulse"></i></div>
                        <h4>Cardiology</h4>
                        <p>Expert care for your heart, including disease management and post-heart attack care.</p>
                        <a href="Cardiology Specialists.php" class="card-link">Learn More →</a>
                    </div>
                    <div class="service-card icon-pediatrics">
                        <div class="service-icon-container"><i class="fa-solid fa-child"></i></div>
                        <h4>Pediatrics</h4>
                        <p>Comprehensive health services for infants, children, and adolescents.</p>
                        <a href="pediatrics-doctors.php" class="card-link">Learn More →</a>
                    </div>
                    <div class="service-card icon-orthopedics">
                        <div class="service-icon-container"><i class="fa-solid fa-bone"></i></div>
                        <h4>Orthopedics</h4>
                        <p>Treatment for bone, joint, and sports-related injuries by leading surgeons.</p>
                        <a href="orthopedics-doctors.php" class="card-link">Learn More →</a>
                    </div>
                </div>
            </div>
            <div class="service-slide">
                <div class="service-grid-home">
                    <div class="service-card icon-dermatology">
                        <div class="service-icon-container"><i class="fa-solid fa-leaf"></i></div>
                        <h4>Dermatology</h4>
                        <p>Advanced care for all conditions of the skin, hair, and nails.</p>
                        <a href="dermatology-doctors.php" class="card-link">Learn More →</a>
                    </div>
                    <div class="service-card icon-general">
                        <div class="service-icon-container"><i class="fa-solid fa-stethoscope"></i></div>
                        <h4>General Consultations</h4>
                        <p>Primary care for routine check-ups, preventative care, and managing common illnesses.</p>
                        <a href="general-consultations.php" class="card-link">Learn More →</a>
                    </div>
                    <div class="service-card icon-diagnostic">
                        <div class="service-icon-container"><i class="fa-solid fa-microscope"></i></div>
                        <h4>Advanced Diagnostics</h4>
                        <p>State-of-the-art MRI, CT, and lab services for accurate and fast diagnosis.</p>
                        <a href="diagnostics.php" class="card-link">Learn More →</a>
                    </div>
                </div>
            </div>
            <div class="service-slide">
                <div class="service-grid-home service-grid-single">
                    <div class="service-card icon-emergency">
                        <div class="service-icon-container"><i class="fa-solid fa-truck-medical"></i></div>
                        <h4>Emergency Care</h4>
                        <p>Our 24/7 Emergency Room is equipped to handle all medical emergencies, from minor to critical.</p>
                        <a href="Emergency Care.php" class="card-link">Learn More →</a>
                    </div>
                </div>
            </div>
            <a class="service-prev" onclick="plusServiceSlides(-1)">&#10094;</a>
            <a class="service-next" onclick="plusServiceSlides(1)">&#10095;</a>
        </div>

        <div class="service-dots-container">
            <span class="service-dot" onclick="currentServiceSlide(1)"></span>
            <span class="service-dot" onclick="currentServiceSlide(2)"></span>
            <span class="service-dot" onclick="currentServiceSlide(3)"></span>
        </div>

        <div class="view-all-services-container">
            <a href="services.php" class="button">View All Services</a>
        </div>
    </main>

    <!-- ================================================
     QUOTE
     ================================================ -->
    <div class="quote">
        <h1>"The good physician treats the disease; the great physician treats the patient who has the disease."</h1>
        <p>— William Osler</p>
    </div>

    <!-- ================================================
     FEATURED SPECIALISTS
     ================================================ -->
    <section class="page-container-home doctor-preview-section">
        <div class="section-header">
            <span class="section-eyebrow">Our Team</span>
            <h1 class="section-title"><i class="fa-solid fa-user-doctor"></i> Featured Specialists</h1>
            <p class="section-lead">We make it easy to find the right expert. Meet our featured specialists or search our full directory by name and specialty.</p>
        </div>

        <div class="service-carousel-container doctor-carousel-container">
            <div class="service-slide doctor-slide" data-slide-index="1">
                <div class="doctor-slide-inner-grid">
                    <div class="doctor-card" data-name="Dr. Gotabhaya Ranasinghe" data-specialty="Cardiology">
                        <img src="images/Dr. Gotabhaya Ranasinghe.webp" alt="Dr. Gotabhaya Ranasinghe">
                        <div class="doctor-info">
                            <h4>Dr. Gotabhaya Ranasinghe</h4>
                            <p class="doctor-title">Senior Consultant Cardiologist</p>
                            <span class="doctor-specialty">Cardiology</span>
                            <a href="find_a_doctor.php#Dr. Gotabhaya Ranasinghe" class="button button-small">View Profile</a>
                        </div>
                    </div>
                    <div class="doctor-card" data-name="Prof. Shaman Rajindrajith" data-specialty="Pediatrics">
                        <img src="images/dr-shaman.png" alt="Prof. Shaman Rajindrajith">
                        <div class="doctor-info">
                            <h4>Prof. Shaman Rajindrajith</h4>
                            <p class="doctor-title">Consultant Pediatrician</p>
                            <span class="doctor-specialty">Pediatrics</span>
                            <a href="find_a_doctor.php#Prof. Shaman Rajindrajith" class="button button-small">View Profile</a>
                        </div>
                    </div>
                    <div class="doctor-card" data-name="Dr. Nayana Perera" data-specialty="Dermatology">
                        <img src="images/Nayana Perera.jpeg" alt="Dr. Nayana Perera">
                        <div class="doctor-info">
                            <h4>Dr. Nayana Perera</h4>
                            <p class="doctor-title">Head of Cosmetic Dermatology</p>
                            <span class="doctor-specialty">Dermatology</span>
                            <a href="find_a_doctor.php#Dr. Nayana Perera" class="button button-small">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="service-slide doctor-slide" data-slide-index="2">
                <div class="doctor-slide-inner-grid">
                    <div class="doctor-card" data-name="Dr. Ashan Abeyewardene" data-specialty="Orthopedics">
                        <img src="images/Ashan Abeyewardene.jpeg" alt="Dr. Ashan Abeyewardene">
                        <div class="doctor-info">
                            <h4>Dr. Ashan Abeyewardene</h4>
                            <p class="doctor-title">Head of Joint Replacement</p>
                            <span class="doctor-specialty">Orthopedics</span>
                            <a href="find_a_doctor.php#Dr. Ashan Abeyewardene" class="button button-small">View Profile</a>
                        </div>
                    </div>
                    <div class="doctor-card" data-name="Dr. Elena Fernando" data-specialty="General Practitioner">
                        <img src="images/Elena Fernando.jpeg" alt="Dr. Elena Fernando">
                        <div class="doctor-info">
                            <h4>Dr. Elena Fernando</h4>
                            <p class="doctor-title">Senior General Practitioner</p>
                            <span class="doctor-specialty">General Practitioner</span>
                            <a href="find_a_doctor.php#Dr. Elena Fernando" class="button button-small">View Profile</a>
                        </div>
                    </div>
                    <div class="doctor-card" data-name="Dr. Chandra Silva" data-specialty="Cardiology">
                        <img src="images/placeholder_doctor.png" alt="Dr. Chandra Silva">
                        <div class="doctor-info">
                            <h4>Dr. Chandra Silva</h4>
                            <p class="doctor-title">Consultant Cardiologist</p>
                            <span class="doctor-specialty">Cardiology</span>
                            <a href="find_a_doctor.php#Dr. Chandra Silva" class="button button-small">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            <a class="service-prev" onclick="plusDoctorSlides(-1)">&#10094;</a>
            <a class="service-next" onclick="plusDoctorSlides(1)">&#10095;</a>
        </div>

        <div class="service-dots-container doctor-dots-container">
            <span class="service-dot doctor-dot" onclick="currentDoctorSlide(1)"></span>
            <span class="service-dot doctor-dot" onclick="currentDoctorSlide(2)"></span>
        </div>

        <div class="view-all-services-container">
            <a href="find_a_doctor.php" class="button button-large">Search All Specialists →</a>
        </div>
    </section>

    <!-- ================================================
     LOCATION
     ================================================ -->
    <section class="page-container-home location-section">
        <div class="section-header">
            <span class="section-eyebrow">Find Us</span>
            <h1 class="section-title"><i class="fa-solid fa-location-dot"></i> Our Location &amp; Contact</h1>
            <p class="section-lead">Conveniently located with ample parking and accessibility. Visit us or get in touch below.</p>
        </div>

        <div class="location-content-grid">
            <div class="location-details">
                <h3>Medicare Plus Hospital</h3>
                <ul class="footer-contact">
                    <li><i class="fa-solid fa-map-marker-alt"></i> No. 84 St. Rita's Road, Mount Lavinia, Sri Lanka</li>
                    <li><i class="fa-solid fa-phone"></i><a href="tel:+94112499590">+94 11 2 499 590</a></li>
                    <li><i class="fa-solid fa-envelope"></i><a href="mailto:info@medicareplus.lk"> info@medicareplus.lk</a></li>
                    <li><i class="fa-solid fa-clock"></i> Open 24/7 for Emergency Services</li>
                </ul>
                <a href="https://www.google.com/maps/dir/?api=1&destination=Medicare+Plus,+No.+84+St.Rita's+Road,+Mount+Lavinia" target="_blank" class="button button-large location-button">
                    <i class="fa-solid fa-route"></i> Get Directions
                </a>
            </div>
            <div class="map-placeholder">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.4283597441464!2d79.8789504!3d6.828623599999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2458e0a75df89%3A0x67a3f87b328a6f67!2sSt%20Rita's%20Rd%2C%20Mount%20Lavinia!5e0!3m2!1sen!2slk!4v1700200000000!5m2!1sen!2slk"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

    <!-- ================================================
     ABOUT
     ================================================ -->
    <div class="About" id="AboutUs">
        <h1>ABOUT US</h1>
        <section class="AboutText">
            <p>Welcome to MediCare Plus, a leading private healthcare provider dedicated to delivering a comprehensive range of medical services. Our mission is to ensure the well-being of our community by offering accessible, efficient, and high-quality care.</p>
            <p>We provide a wide array of services to meet your needs, including:</p>
            <a class="service-link" onclick="openModal('General Medical Consultations', `<p><strong>Comprehensive care for your everyday health needs.</strong></p><p>Visit our general doctors for routine check-ups, preventative care, vaccinations, and treatment for common illnesses like colds, flu, and infections.</p>`, 'general-consultations.php')">
                <li>General Medical Consultations</li>
            </a>
            <a class="service-link" onclick="openModal('Specialist Treatments', `<h3><i class='fa-solid fa-stethoscope'></i> Expert Care by Leading Specialists</h3><p>MediCare Plus offers access to a network of highly qualified specialists across various fields including <strong>Cardiology, Neurology, Orthopedics,</strong> and more.</p>`, 'Specialist Treatments.php')">
                <li>Specialist Treatments</li>
            </a>
            <a class="service-link" onclick="openModal('Advanced Diagnostic Services', `<h3><i class='fa-solid fa-microscope'></i> Accurate Diagnosis, Faster Treatment</h3><ul><li>Advanced MRI and CT Scanners</li><li>Digital X-Ray and Ultrasound</li><li>Comprehensive Laboratory Services</li><li>ECG and Stress Testing</li></ul>`, 'diagnostics.php')">
                <li>Advanced Diagnostic Services</li>
            </a>
            <a class="service-link" onclick="openModal('Emergency Care', `<h3><i class='fa-solid fa-truck-medical'></i> 24/7 Emergency &amp; Trauma Care</h3><p><strong>Our Emergency Room is open 24 hours a day, 7 days a week.</strong></p><p>Staffed by highly trained emergency physicians equipped to handle all medical emergencies.</p>`, 'Emergency Care.php')">
                <li>Emergency Care</li>
            </a>
            <p>In response to rising patient expectations, we are proud to introduce our new interactive web platform — a cornerstone of our commitment to digital transformation in healthcare.</p>
        </section>
        <section class="AboutImage">
            <img src="images/Logo4.png" alt="Medicare Plus Logo">
        </section>
    </div>

    <!-- Modal -->
    <div id="infoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Modal Title</h2>
                <button type="button" class="modal-close-btn" id="modalCloseBtn" onclick="closeModal()">
                    <i class="fa-solid fa-times"></i> Close
                </button>
            </div>
            <div class="modal-body">
                <div id="modalContent"></div>
            </div>
            <div class="modal-footer">
                <a id="modalLearnMoreLink" href="#">Learn More <i class="fa-solid fa-arrow-right"></i></a>
                <button type="button" class="cancelbtn" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <?php include 'footer_bare.php'; ?>
    <script src="first.js"></script>

    <script>
        /* ====================================================
   HERO SLIDESHOW — Photo transition (replaces canvas)
   ==================================================== */
        (function() {
            var slides = document.querySelectorAll('.hero-slideshow .hero-slide');
            var dots = document.querySelectorAll('.hero-dots .hero-dot');
            var progressBar = document.getElementById('heroProgress');
            var current = 0;
            var total = slides.length;
            var INTERVAL = 5500; // ms between slides
            var timer, progressTimer;

            function showSlide(n) {
                slides[current].classList.remove('active');
                dots[current].classList.remove('active');
                current = (n + total) % total;
                slides[current].classList.add('active');
                dots[current].classList.add('active');
                resetProgress();
            }

            function resetProgress() {
                if (progressBar) {
                    progressBar.style.transition = 'none';
                    progressBar.style.width = '0%';
                    requestAnimationFrame(function() {
                        requestAnimationFrame(function() {
                            progressBar.style.transition = 'width ' + INTERVAL + 'ms linear';
                            progressBar.style.width = '100%';
                        });
                    });
                }
            }

            function startAuto() {
                clearInterval(timer);
                timer = setInterval(function() {
                    showSlide(current + 1);
                }, INTERVAL);
            }

            window.goToSlide = function(n) {
                showSlide(n);
                startAuto();
            };
            window.changeHeroSlide = function(dir) {
                showSlide(current + dir);
                startAuto();
            };

            showSlide(0);
            startAuto();
        })();
    </script>
</body>

</html>