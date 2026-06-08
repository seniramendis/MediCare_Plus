<?php
$pageTitle = 'Dermatology | MediCare Plus';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($conn) && file_exists(__DIR__ . '/db_connect.php')) require_once __DIR__ . '/db_connect.php';
include 'header.php';
?>
<style>
:root { --svc-color: #d69e2e; --svc-bg: rgba(214,158,46,0.08); }
.svc-hero {
    position: relative;
    background: url('https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=1600&q=80') center/cover no-repeat;
    min-height: 420px; display: flex; align-items: flex-end;
}
.svc-hero::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(to right, rgba(8,28,70,.82) 0%, rgba(8,28,70,.5) 60%, rgba(8,28,70,.2) 100%);
}
.svc-hero-inner { position: relative; z-index: 2; padding: 60px 8% 50px; max-width: 700px; }
.svc-icon-hero {
    width: 64px; height: 64px; border-radius: 50%;
    background: var(--svc-color); display: flex; align-items: center;
    justify-content: center; margin-bottom: 20px;
}
.svc-icon-hero i { font-size: 1.6rem; color: #fff; }
.svc-hero-inner h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2rem, 4vw, 3rem); color: #fff; margin: 0 0 14px;
}
.svc-hero-inner p { color: rgba(255,255,255,.85); font-size: 1.08rem; line-height: 1.65; margin: 0 0 28px; }
.svc-cta-btn {
    display: inline-flex; align-items: center; gap: 10px;
    background: var(--svc-color); color: #fff; padding: 13px 28px;
    border-radius: 50px; text-decoration: none; font-weight: 600; font-size: .95rem; transition: .25s;
}
.svc-cta-btn:hover { opacity: .88; transform: translateY(-2px); }
.svc-body { max-width: 1140px; margin: 0 auto; padding: 60px 5%; }
.svc-section-title { font-family: 'Playfair Display', serif; font-size: 1.7rem; color: #0d2b5e; margin: 0 0 12px; }
.svc-about {
    background: #fff; border-radius: 16px; padding: 40px;
    box-shadow: 0 2px 16px rgba(0,0,0,.06); margin-bottom: 48px;
    font-size: 1.05rem; line-height: 1.75; color: #4a5568;
    border-left: 4px solid var(--svc-color);
}
.svc-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; margin-bottom: 48px; }
@media(max-width:700px) { .svc-two-col { grid-template-columns: 1fr; } }
.svc-list-card { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 2px 16px rgba(0,0,0,.06); }
.svc-list-card h3 { font-size: 1.1rem; color: #0d2b5e; margin: 0 0 18px; font-weight: 700; }
.svc-list-card ul { list-style: none; padding: 0; margin: 0; }
.svc-list-card ul li {
    padding: 9px 0; border-bottom: 1px solid #f0f0f0;
    color: #4a5568; font-size: .97rem; display: flex; align-items: center; gap: 10px;
}
.svc-list-card ul li:last-child { border-bottom: none; }
.svc-list-card ul li::before {
    content: ''; width: 8px; height: 8px;
    border-radius: 50%; background: var(--svc-color); flex-shrink: 0;
}
.svc-doctors-title { font-family: 'Playfair Display', serif; font-size: 1.7rem; color: #0d2b5e; margin: 0 0 24px; }
.svc-doctors-grid { display: flex; flex-wrap: wrap; gap: 24px; margin-bottom: 48px; }
.svc-doc-card {
    background: #fff; border-radius: 16px; padding: 28px;
    box-shadow: 0 2px 16px rgba(0,0,0,.06);
    display: flex; align-items: center; gap: 20px; min-width: 260px; flex: 1;
}
.svc-doc-avatar { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid var(--svc-bg); }
.svc-doc-name { font-weight: 700; color: #0d2b5e; font-size: 1rem; margin: 0 0 4px; }
.svc-doc-title { font-size: .85rem; color: #718096; margin: 0 0 10px; }
.svc-doc-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--svc-bg); color: var(--svc-color);
    padding: 7px 16px; border-radius: 30px; text-decoration: none;
    font-size: .85rem; font-weight: 600; transition: .2s;
}
.svc-doc-btn:hover { background: var(--svc-color); color: #fff; }
.svc-book-banner {
    background: linear-gradient(135deg, #0d2b5e 0%, #0aa698 100%);
    border-radius: 20px; padding: 48px 40px; text-align: center; color: #fff;
}
.svc-book-banner h2 { font-family: 'Playfair Display', serif; font-size: 1.8rem; margin: 0 0 12px; }
.svc-book-banner p { opacity: .88; margin: 0 0 28px; font-size: 1rem; }
.svc-book-btn {
    display: inline-flex; align-items: center; gap: 10px;
    background: #fff; color: #0d2b5e; padding: 14px 32px;
    border-radius: 50px; text-decoration: none; font-weight: 700; font-size: .97rem; transition: .25s;
}
.svc-book-btn:hover { background: var(--svc-color); color: #fff; transform: translateY(-2px); }
</style>

<section class="svc-hero">
    <div class="svc-hero-inner">
        <div class="svc-icon-hero"><i class="fas fa-leaf"></i></div>
        <h1>Dermatology</h1>
        <p>Advanced skin, hair, and nail care for every skin type and concern.</p>
        <a href="book_appointment.php" class="svc-cta-btn">
            <i class="fas fa-calendar-check"></i> Book an Appointment
        </a>
    </div>
</section>

<div class="svc-body">

    <div class="svc-about">
        <h2 class="svc-section-title">About This Department</h2>
        <p>Our Dermatology department addresses the full spectrum of skin conditions from common rashes and acne to complex autoimmune skin diseases and skin cancer. We also offer cosmetic dermatology treatments to help you look and feel your best.</p>
    </div>

    <div class="svc-two-col">
        <div class="svc-list-card">
            <h3><i class="fas fa-check-circle" style="color:var(--svc-color);margin-right:8px;"></i>What We Offer</h3>
            <ul>
                    <li>Medical Dermatology</li>
                    <li>Cosmetic Dermatology</li>
                    <li>Laser Therapy</li>
                    <li>Skin Cancer Screening</li>
                    <li>Acne &amp; Rosacea Clinic</li>
                    <li>Hair &amp; Scalp Treatments</li>
            </ul>
        </div>
        <div class="svc-list-card">
            <h3><i class="fas fa-notes-medical" style="color:var(--svc-color);margin-right:8px;"></i>Conditions We Treat</h3>
            <ul>
                    <li>Acne &amp; Rosacea</li>
                    <li>Eczema &amp; Psoriasis</li>
                    <li>Skin Cancer</li>
                    <li>Alopecia</li>
                    <li>Vitiligo</li>
                    <li>Fungal Infections</li>
            </ul>
        </div>
    </div>

    <h2 class="svc-doctors-title">Meet Our Specialists</h2>
    <div class="svc-doctors-grid">
        <div class="svc-doc-card">
            <img src="https://ui-avatars.com/api/?name=Dr.+Nayana+Perera&size=150&background=d69e2e&color=fff&bold=true&rounded=true" alt="Dr. Nayana Perera" class="svc-doc-avatar">
            <div>
                <p class="svc-doc-name">Dr. Nayana Perera</p>
                <p class="svc-doc-title">Head of Cosmetic Dermatology</p>
                <a href="doctors.php" class="svc-doc-btn"><i class="fas fa-stethoscope"></i> See All Specialists</a>
            </div>
        </div>
    </div>

    <?php
    if (isset($conn) && $conn) {
        $spec = "Dermatology";
        $sql = "SELECT d.id, d.specialization, d.profile_image, u.first_name, u.last_name FROM doctors d JOIN users u ON d.user_id=u.id WHERE d.specialization LIKE ? AND u.status = 'active' LIMIT 6";
        $stmt = $conn->prepare($sql);
        $like = "%" . $spec . "%";
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
    ?>
    <h3 style="font-family:'DM Sans',sans-serif;font-size:1rem;color:#718096;margin:-24px 0 18px;font-weight:500;">Also in our system:</h3>
    <div class="svc-doctors-grid" style="margin-bottom:40px;">
        <?php while ($d = $res->fetch_assoc()) {
            $full = htmlspecialchars($d["first_name"] . " " . $d["last_name"]);
            $imgFile = $d["profile_image"] ?? "";
            if (!empty($imgFile) && !preg_match('/^https?:\/\/', $imgFile)) {
                $img = "assets/images/" . htmlspecialchars($imgFile);
            } elseif (!empty($imgFile)) {
                $img = htmlspecialchars($imgFile);
            } else {
                $img = "https://ui-avatars.com/api/?name=" . urlencode($full) . "&size=150&background=0aa698&color=fff&bold=true&rounded=true";
            }
        ?>
        <div class="svc-doc-card">
            <img src="<?php echo $img; ?>" alt="<?php echo $full; ?>" class="svc-doc-avatar"
                 onerror="this.src='https://ui-avatars.com/api/?name=Doctor&size=150&background=0aa698&color=fff&bold=true&rounded=true'">
            <div>
                <p class="svc-doc-name">Dr. <?php echo $full; ?></p>
                <p class="svc-doc-title"><?php echo htmlspecialchars($d["specialization"]); ?></p>
                <a href="doctor-profile.php?id=<?php echo (int)$d['id']; ?>" class="svc-doc-btn"><i class="fas fa-user"></i> View Profile</a>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } } ?>

    <div class="svc-book-banner">
        <h2>Ready to See a Specialist?</h2>
        <p>Book your appointment online in minutes. Our team will confirm your slot within 24 hours.</p>
        <a href="book_appointment.php" class="svc-book-btn">
            <i class="fas fa-calendar-check"></i> Book Appointment Now
        </a>
    </div>

</div>

<?php include 'footer.php'; ?>
