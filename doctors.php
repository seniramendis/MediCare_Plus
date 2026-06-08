<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (file_exists('db_connect.php')) {
    include 'db_connect.php';
}
$pageTitle = 'Find a Doctor';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Medicare Plus</title>
    <link rel="icon" href="images/Favicon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/HomeStyles.css?v=3.0">
    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --navy: #0d2b5e;
            --teal: #0aa698;
            --teal-light: #e6f7f6;
            --green: #2ecc71;
            --light-bg: #f5f8fa;
            --border: #e2e8f0;
            --text: #2d3748;
            --muted: #718096;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'DM Sans', Arial, sans-serif;
            background: var(--light-bg);
            color: var(--text);
        }

        /* ================================================
   PAGE HERO BANNER
   ================================================ */
        .doctors-hero {
            background: linear-gradient(135deg, var(--navy) 0%, #1a4a8a 55%, #0f6e62 100%);
            padding: 72px 8% 80px;
            position: relative;
            overflow: hidden;
        }

        .doctors-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 80% 20%, rgba(10, 166, 152, 0.18) 0%, transparent 50%),
                radial-gradient(circle at 15% 80%, rgba(255, 255, 255, 0.04) 0%, transparent 40%);
        }

        .doctors-hero-inner {
            position: relative;
            z-index: 1;
            max-width: 680px;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--teal);
            background: rgba(10, 166, 152, 0.12);
            border: 1px solid rgba(10, 166, 152, 0.3);
            padding: 5px 14px;
            border-radius: 30px;
            margin-bottom: 18px;
        }

        .doctors-hero h1 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(2rem, 4.5vw, 3.2rem);
            font-weight: 700;
            color: #fff;
            margin: 0 0 14px;
            line-height: 1.14;
        }

        .doctors-hero p {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.7;
            margin: 0;
        }

        /* Quick stat chips */
        .hero-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 30px;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 0.84rem;
            font-weight: 500;
            padding: 7px 16px;
            border-radius: 30px;
        }

        .hero-chip i {
            color: var(--teal);
        }

        /* ================================================
   SEARCH & FILTER BAR
   ================================================ */
        .search-section {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 28px 8%;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .search-row {
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input-wrap {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .search-input-wrap i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 0.95rem;
        }

        .search-input-wrap input {
            width: 100%;
            height: 48px;
            padding: 0 16px 0 44px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: var(--text);
            background: var(--light-bg);
            transition: border 0.2s, box-shadow 0.2s;
        }

        .search-input-wrap input:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 3px rgba(10, 166, 152, 0.12);
            background: var(--white);
        }

        /* Specialty pills */
        .specialty-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 14px 8% 0;
        }

        .spec-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 30px;
            border: 1.5px solid var(--border);
            background: var(--white);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .spec-pill:hover,
        .spec-pill.active {
            border-color: var(--teal);
            background: var(--teal-light);
            color: var(--teal);
        }

        .spec-pill .pill-count {
            background: var(--light-bg);
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 20px;
            transition: background 0.2s, color 0.2s;
        }

        .spec-pill.active .pill-count {
            background: rgba(10, 166, 152, 0.2);
            color: var(--teal);
        }

        /* ================================================
   DOCTORS GRID
   ================================================ */
        .doctors-main {
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 8% 60px;
        }

        .results-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .results-count {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .results-count strong {
            color: var(--navy);
        }

        .sort-select {
            height: 38px;
            padding: 0 16px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.88rem;
            color: var(--text);
            background: var(--white);
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23718096'%3E%3Cpath d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 36px;
        }

        .doctor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 28px;
        }

        /* Doctor Card — premium redesign */
        .doctor-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .doctor-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 56px rgba(13, 43, 94, 0.13);
        }

        /* Colored top accent per specialty */
        .doctor-card[data-specialty="Cardiology"] .doc-specialty-bar {
            background: #e53e3e;
        }

        .doctor-card[data-specialty="Dermatology"] .doc-specialty-bar {
            background: #d69e2e;
        }

        .doctor-card[data-specialty="Orthopedics"] .doc-specialty-bar {
            background: #2b6cb0;
        }

        .doctor-card[data-specialty="Pediatrics"] .doc-specialty-bar {
            background: #d53f8c;
        }

        .doctor-card[data-specialty="General Practitioner"] .doc-specialty-bar {
            background: var(--teal);
        }

        .doctor-card[data-specialty="Neurology"] .doc-specialty-bar {
            background: #6b46c1;
        }

        .doctor-card[data-specialty="Gynaecology"] .doc-specialty-bar {
            background: #e91e8c;
        }

        .doctor-card[data-specialty="ENT"] .doc-specialty-bar {
            background: #dd6b20;
        }

        .doctor-card[data-specialty="Endocrinology"] .doc-specialty-bar {
            background: #2f855a;
        }

        .doc-specialty-bar {
            height: 4px;
            background: var(--teal);
        }

        .doc-img-wrap {
            position: relative;
            background: var(--light-bg);
            height: 220px;
            overflow: hidden;
        }

        .doc-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            transition: transform 0.5s ease;
            display: block;
        }

        .doctor-card:hover .doc-img-wrap img {
            transform: scale(1.05);
        }

        /* Availability badge */
        .doc-avail {
            position: absolute;
            bottom: 12px;
            left: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.74rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.95);
            color: #276749;
            border: 1px solid rgba(39, 103, 73, 0.2);
        }

        .doc-avail .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #48bb78;
        }

        .doc-body {
            padding: 20px 20px 0;
            flex: 1;
        }

        .doc-body h4 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 4px;
            line-height: 1.3;
        }

        .doc-title {
            font-size: 0.85rem;
            color: var(--muted);
            margin: 0 0 10px;
        }

        .doc-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }

        .doc-tag {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            background: var(--teal-light);
            color: var(--teal);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .doc-rating {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
        }

        .stars {
            color: #f6ad55;
            font-size: 0.85rem;
            letter-spacing: 2px;
        }

        .rating-num {
            font-size: 0.82rem;
            color: var(--muted);
        }

        .doc-bio {
            font-size: 0.88rem;
            color: var(--muted);
            line-height: 1.55;
            margin: 0 0 16px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .doc-footer {
            padding: 16px 20px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 10px;
        }

        .doc-btn {
            flex: 1;
            padding: 10px 0;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.86rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .doc-btn-primary {
            background: var(--navy);
            color: #fff;
        }

        .doc-btn-primary:hover {
            background: var(--teal);
            transform: translateY(-1px);
        }

        .doc-btn-outline {
            background: transparent;
            color: var(--navy);
            border: 1.5px solid var(--border);
        }

        .doc-btn-outline:hover {
            border-color: var(--teal);
            color: var(--teal);
        }

        /* No results */
        #noResultsMessage {
            text-align: center;
            padding: 60px 20px;
            grid-column: 1 / -1;
            color: var(--muted);
        }

        #noResultsMessage i {
            font-size: 3rem;
            color: var(--border);
            display: block;
            margin-bottom: 16px;
        }

        /* ================================================
   RESPONSIVE
   ================================================ */
        @media (max-width: 700px) {
            .doctors-hero {
                padding: 50px 5% 60px;
            }

            .search-section,
            .specialty-pills,
            .doctors-main {
                padding-left: 5%;
                padding-right: 5%;
            }

            .doctor-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include 'nav_only.php'; ?>

    <!-- ================================================
     PAGE HERO
     ================================================ -->
    <div class="doctors-hero">
        <div class="doctors-hero-inner">
            <span class="hero-eyebrow">
                <i class="fa-solid fa-stethoscope"></i> Our Medical Team
            </span>
            <h1>Find a Doctor</h1>
            <p>Search for specialists by name or filter by department. Every doctor at Medicare Plus is a leader in their field, committed to your health.</p>
            <div class="hero-chips">
                <span class="hero-chip"><i class="fa-solid fa-user-doctor"></i> 48 Specialists</span>
                <span class="hero-chip"><i class="fa-solid fa-hospital"></i> 9 Departments</span>
                <span class="hero-chip"><i class="fa-solid fa-star"></i> Highly Rated</span>
                <span class="hero-chip"><i class="fa-solid fa-calendar-check"></i> Online Booking</span>
            </div>
        </div>
    </div>

    <!-- ================================================
     SEARCH BAR (sticky)
     ================================================ -->
    <div class="search-section">
        <div class="search-row">
            <div class="search-input-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="doctorName" placeholder="Search by doctor's name…" autocomplete="off">
            </div>
            <select id="doctorSpecialty" class="sort-select" style="height:48px; border-radius:12px; min-width:200px; font-size:0.95rem;">
                <option value="all">All Specialties</option>
                <option value="Cardiology">Cardiology</option>
                <option value="Dermatology">Dermatology</option>
                <option value="Endocrinology">Endocrinology</option>
                <option value="ENT">ENT</option>
                <option value="General Practitioner">General Practitioner</option>
                <option value="Gynaecology">Gynaecology</option>
                <option value="Neurology">Neurology</option>
                <option value="Orthopedics">Orthopedics</option>
                <option value="Pediatrics">Pediatrics</option>
            </select>
        </div>
    </div>

    <!-- Specialty quick-filter pills -->
    <div class="specialty-pills" style="background:#fff; border-bottom:1px solid var(--border); padding-bottom:16px;">
        <button class="spec-pill active" data-specialty="all" onclick="filterByPill(this, 'all')">
            <i class="fa-solid fa-grid-2"></i> All Doctors
        </button>
        <button class="spec-pill" data-specialty="Cardiology" onclick="filterByPill(this, 'Cardiology')">
            <i class="fa-solid fa-heart-pulse"></i> Cardiology
        </button>
        <button class="spec-pill" data-specialty="Pediatrics" onclick="filterByPill(this, 'Pediatrics')">
            <i class="fa-solid fa-child"></i> Pediatrics
        </button>
        <button class="spec-pill" data-specialty="Orthopedics" onclick="filterByPill(this, 'Orthopedics')">
            <i class="fa-solid fa-bone"></i> Orthopedics
        </button>
        <button class="spec-pill" data-specialty="Dermatology" onclick="filterByPill(this, 'Dermatology')">
            <i class="fa-solid fa-leaf"></i> Dermatology
        </button>
        <button class="spec-pill" data-specialty="Neurology" onclick="filterByPill(this, 'Neurology')">
            <i class="fa-solid fa-brain"></i> Neurology
        </button>
        <button class="spec-pill" data-specialty="General Practitioner" onclick="filterByPill(this, 'General Practitioner')">
            <i class="fa-solid fa-stethoscope"></i> General
        </button>
        <button class="spec-pill" data-specialty="Gynaecology" onclick="filterByPill(this, 'Gynaecology')">
            <i class="fa-solid fa-venus"></i> Gynaecology
        </button>
        <button class="spec-pill" data-specialty="ENT" onclick="filterByPill(this, 'ENT')">
            <i class="fa-solid fa-ear-listen"></i> ENT
        </button>
    </div>

    <!-- ================================================
     DOCTOR LISTINGS
     ================================================ -->
    <main class="doctors-main">
        <div class="results-meta">
            <p class="results-count">Showing <strong id="resultCount">0</strong> doctors</p>
            <select class="sort-select" id="sortSelect" onchange="sortDoctors(this.value)">
                <option value="default">Sort: Default</option>
                <option value="name">Sort: Name (A–Z)</option>
                <option value="rating">Sort: Highest Rated</option>
            </select>
        </div>

        <div class="doctor-grid" id="allDoctorsList">
            <?php
            if (isset($conn)) {
                $sql = "SELECT d.id, d.user_id, d.specialization, d.rating, d.bio, d.profile_image, d.consultation_fee, d.availability,
                        u.first_name, u.last_name,
                        CONCAT(u.first_name, ' ', u.last_name) as full_name
                        FROM doctors d
                        JOIN users u ON d.user_id = u.id
                        WHERE u.status = 'active'
                        ORDER BY u.first_name ASC";
                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $doc_id   = (int)$row['id'];
                        $name     = htmlspecialchars($row['full_name']);
                        $specialty = htmlspecialchars($row['specialization']);
                        $title    = 'Specialist – ' . htmlspecialchars($row['specialization']);
                        $bio      = strip_tags($row['bio'] ?? '');
                        if (strlen($bio) > 100) $bio = substr($bio, 0, 100) . '...';
                        // Specialty → avatar background colour (UI-Avatars API)
                        $spec_colors = [
                            'Cardiology'           => 'e53e3e',
                            'Neurology'            => '6b46c1',
                            'Pediatrics'           => 'dd6b20',
                            'Paediatrics'          => 'dd6b20',
                            'Dermatology'          => 'd69e2e',
                            'Orthopedics'          => '2b6cb0',
                            'Orthopaedics'         => '2b6cb0',
                            'General Practitioner' => '2f855a',
                            'Gynaecology'          => 'c0286c',
                            'ENT'                  => 'c05621',
                            'Endocrinology'        => '276749',
                        ];
                        $avatar_bg  = $spec_colors[$row['specialization']] ?? '0aa698';
                        $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($row['first_name'] . '+' . $row['last_name']) . "&size=300&background={$avatar_bg}&color=fff&bold=true&font-size=0.38";
                        $img_file   = $row['profile_image'] ?? '';
                        // Use local file only if it's real (not the generic placeholder)
                        if (!empty($img_file) && $img_file !== 'default-doc.jpg' && !preg_match('/^https?:\/\//', $img_file)) {
                            $img = htmlspecialchars('assets/images/' . $img_file);
                        } elseif (!empty($img_file) && preg_match('/^https?:\/\//', $img_file)) {
                            $img = htmlspecialchars($img_file);
                        } else {
                            $img = $avatar_url;
                        }
                        $fallback_img = $avatar_url;
                        $rating   = $row['rating'] ? round($row['rating'], 1) : 0;
                        $count    = 0; // reviews table may not exist yet
                        $full_stars = floor($rating);
                        $has_half = ($rating - $full_stars) >= 0.5;
                        $stars_html = '';
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $full_stars) $stars_html .= '★';
                            elseif ($i == $full_stars + 1 && $has_half) $stars_html .= '★';
                            else $stars_html .= '☆';
                        }
            ?>
                        <div class="doctor-card" data-name="<?php echo $name; ?>" data-specialty="<?php echo $specialty; ?>" data-rating="<?php echo $rating; ?>">
                            <div class="doc-specialty-bar"></div>
                            <div class="doc-img-wrap">
                                <img src="<?php echo $img; ?>" alt="<?php echo $name; ?>" loading="lazy" onerror="this.src='<?php echo $fallback_img; ?>'">
                                <span class="doc-avail"><span class="dot"></span> Accepting Patients</span>
                            </div>
                            <div class="doc-body">
                                <h4>Dr. <?php echo $name; ?></h4>
                                <p class="doc-title"><?php echo $title; ?></p>
                                <div class="doc-tags">
                                    <span class="doc-tag"><?php echo $specialty; ?></span>
                                </div>
                                <?php if ($rating > 0): ?>
                                    <div class="doc-rating">
                                        <span class="stars"><?php echo $stars_html; ?></span>
                                        <span class="rating-num"><?php echo "$rating"; ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="doc-rating"><span class="rating-num" style="color:#aaa;">No reviews yet</span></div>
                                <?php endif; ?>
                                <?php if ($bio): ?>
                                    <p class="doc-bio"><?php echo htmlspecialchars($bio); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="doc-footer">
                                <a href="doctor-profile.php?id=<?php echo $doc_id; ?>" class="doc-btn doc-btn-primary">View Profile</a>
                                <a href="book_appointment.php?doctor_id=<?php echo $doc_id; ?>" class="doc-btn doc-btn-outline">Book</a>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    /* Fallback demo cards when DB not connected */
                    $demo_doctors = [
                        ['Dr. Gotabhaya Ranasinghe', 'Senior Consultant Cardiologist', 'Cardiology', 'https://ui-avatars.com/api/?name=Gotabhaya+Ranasinghe&size=300&background=e53e3e&color=fff&bold=true&rounded=true', 4.9, 142],
                        ['Prof. Shaman Rajindrajith', 'Consultant Pediatrician', 'Pediatrics', 'https://ui-avatars.com/api/?name=Shaman+Rajindrajith&size=300&background=3182ce&color=fff&bold=true&rounded=true', 4.8, 98],
                        ['Dr. Nayana Perera', 'Head of Cosmetic Dermatology', 'Dermatology', 'https://ui-avatars.com/api/?name=Nayana+Perera&size=300&background=d69e2e&color=fff&bold=true&rounded=true', 4.7, 76],
                        ['Dr. Ashan Abeyewardene', 'Head of Joint Replacement', 'Orthopedics', 'https://ui-avatars.com/api/?name=Ashan+Abeyewardene&size=300&background=dd6b20&color=fff&bold=true&rounded=true', 4.8, 113],
                        ['Dr. Elena Fernando', 'Senior General Practitioner', 'General Practitioner', 'https://ui-avatars.com/api/?name=Elena+Fernando&size=300&background=38a169&color=fff&bold=true&rounded=true', 4.6, 201],
                        ['Dr. Chandra Silva', 'Consultant Cardiologist', 'Cardiology', 'https://ui-avatars.com/api/?name=Chandra+Silva&size=300&background=6b46c1&color=fff&bold=true&rounded=true', 0, 0],
                    ];
                    foreach ($demo_doctors as $d) {
                        [$name, $title, $specialty, $img, $rating, $count] = $d;
                        $stars = $rating > 0 ? str_repeat('★', (int)$rating) . ($rating - (int)$rating >= 0.5 ? '★' : '') : '';
                        $stars = str_pad($stars, 5, '☆', STR_PAD_RIGHT);
                        $fallback_img = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=300&background=0aa698&color=fff&bold=true&rounded=true';
                    ?>
                        <div class="doctor-card" data-name="<?php echo $name; ?>" data-specialty="<?php echo $specialty; ?>" data-rating="<?php echo $rating; ?>">
                            <div class="doc-specialty-bar"></div>
                            <div class="doc-img-wrap">
                                <img src="<?php echo $img; ?>" alt="<?php echo $name; ?>" loading="lazy" onerror="this.src='<?php echo $fallback_img; ?>'">
                                <span class="doc-avail"><span class="dot"></span> Accepting Patients</span>
                            </div>
                            <div class="doc-body">
                                <h4><?php echo $name; ?></h4>
                                <p class="doc-title"><?php echo $title; ?></p>
                                <div class="doc-tags"><span class="doc-tag"><?php echo $specialty; ?></span></div>
                                <?php if ($rating > 0): ?>
                                    <div class="doc-rating">
                                        <span class="stars"><?php echo $stars; ?></span>
                                        <span class="rating-num"><?php echo "$rating ($count reviews)"; ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="doc-rating"><span class="rating-num" style="color:#aaa;">No reviews yet</span></div>
                                <?php endif; ?>
                            </div>
                            <div class="doc-footer">
                                <a href="find_a_doctor.php#<?php echo urlencode($name); ?>" class="doc-btn doc-btn-primary">View Profile</a>
                                <a href="book_appointment.php" class="doc-btn doc-btn-outline">Book</a>
                            </div>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>

        <div id="noResultsMessage" style="display:none;">
            <i class="fa-solid fa-user-doctor-hair-long"></i>
            <h3>No doctors found</h3>
            <p>Try adjusting your search or filter criteria.</p>
        </div>
    </main>

    <?php include 'footer_bare.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var nameInput = document.getElementById('doctorName');
            var specialtySelect = document.getElementById('doctorSpecialty');
            var list = document.getElementById('allDoctorsList');
            var noMsg = document.getElementById('noResultsMessage');
            var countEl = document.getElementById('resultCount');
            var activePill = null;

            function getCards() {
                return list ? Array.from(list.querySelectorAll('.doctor-card')) : [];
            }

            function filterDoctors() {
                var nameQ = nameInput.value.toLowerCase();
                var specQ = specialtySelect.value;
                var visible = 0;
                getCards().forEach(function(card) {
                    var name = (card.getAttribute('data-name') || '').toLowerCase();
                    var spec = card.getAttribute('data-specialty') || '';
                    var nm = name.includes(nameQ);
                    var sm = (specQ === 'all' || spec === specQ);
                    if (nm && sm) {
                        card.style.display = '';
                        visible++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                if (countEl) countEl.textContent = visible;
                if (noMsg) noMsg.style.display = visible ? 'none' : 'block';
            }

            window.filterByPill = function(btn, spec) {
                document.querySelectorAll('.spec-pill').forEach(function(p) {
                    p.classList.remove('active');
                });
                btn.classList.add('active');
                specialtySelect.value = spec;
                filterDoctors();
            };

            window.sortDoctors = function(val) {
                var cards = getCards();
                var sorted = cards.slice().sort(function(a, b) {
                    if (val === 'name') {
                        return (a.getAttribute('data-name') || '').localeCompare(b.getAttribute('data-name') || '');
                    }
                    if (val === 'rating') {
                        return parseFloat(b.getAttribute('data-rating') || 0) - parseFloat(a.getAttribute('data-rating') || 0);
                    }
                    return 0;
                });
                sorted.forEach(function(c) {
                    list.appendChild(c);
                });
            };

            if (nameInput) nameInput.addEventListener('keyup', filterDoctors);
            if (specialtySelect) specialtySelect.addEventListener('change', function() {
                var sel = this.value;
                document.querySelectorAll('.spec-pill').forEach(function(p) {
                    p.classList.toggle('active', p.getAttribute('data-specialty') === sel);
                });
                filterDoctors();
            });

            /* Initial count */
            filterDoctors();
        });
    </script>
</body>

</html>