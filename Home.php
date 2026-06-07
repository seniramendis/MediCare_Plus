<?php
require_once 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Home | MediCare Plus';
include 'header.php';
?>

<!-- ══════════ HERO ══════════ -->
<section class="hero-section" id="hero">
    <div class="hero-particles" id="particles"></div>
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
        <div class="hero-badge"><i class="fas fa-shield-halved"></i> Sri Lanka's #1 Digital Health Platform</div>
        <h1>Your Partner for a<br><span class="hero-highlight">Lifetime of Health</span></h1>
        <p>World-class specialists. Seamless appointments. Secure medical records. All in one place.</p>
        <div class="hero-cta-group">
            <a href="book_appointment.php" class="btn-hero-primary"><i class="fas fa-calendar-check"></i> Book Appointment</a>
            <a href="doctors.php" class="btn-hero-secondary"><i class="fas fa-user-doctor"></i> Find a Doctor</a>
        </div>
    </div>
    <div class="hero-scroll-indicator">
        <div class="scroll-mouse"><div class="scroll-wheel"></div></div>
        <span>Scroll to explore</span>
    </div>
</section>

<!-- ══════════ STATS COUNTER STRIP ══════════ -->
<section class="stats-strip">
    <div class="stats-grid">
        <div class="stat-item" data-aos="fade-up" data-aos-delay="0">
            <i class="fas fa-user-doctor"></i>
            <strong class="counter" data-target="120">0</strong><span>+</span>
            <p>Specialist Doctors</p>
        </div>
        <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
            <i class="fas fa-users"></i>
            <strong class="counter" data-target="15000">0</strong><span>+</span>
            <p>Patients Served</p>
        </div>
        <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
            <i class="fas fa-hospital"></i>
            <strong class="counter" data-target="25">0</strong><span>+</span>
            <p>Departments</p>
        </div>
        <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
            <i class="fas fa-star"></i>
            <strong>4.9</strong><span>/5</span>
            <p>Patient Rating</p>
        </div>
    </div>
</section>

<!-- ══════════ SERVICES ══════════ -->
<section class="home-section" id="services">
    <div class="section-header" data-aos="fade-up">
        <span class="section-tag">What We Offer</span>
        <h2 class="section-title">Our Premium Services</h2>
        <p class="section-subtitle">Comprehensive medical care tailored to your needs across specialised departments.</p>
    </div>
    <div class="services-grid">
        <?php
        $serviceIcons = [
            'Cardiology'         => ['fa-heart-pulse',    '#e53e3e'],
            'Neurology'          => ['fa-brain',          '#6b46c1'],
            'Paediatrics'        => ['fa-baby',           '#3182ce'],
            'General'            => ['fa-stethoscope',    '#2b6cb0'],
            'Laboratory'         => ['fa-flask',          '#d69e2e'],
            'Radiology'          => ['fa-x-ray',          '#4a5568'],
            'Pharmacy'           => ['fa-pills',          '#38a169'],
            'Emergency'          => ['fa-truck-medical',  '#e53e3e'],
            'Gynaecology'        => ['fa-venus',          '#d53f8c'],
            'Orthopaedics'       => ['fa-bone',           '#dd6b20'],
        ];
        $services = [];
        if (isset($conn) && $conn) {
            $r = $conn->query("SELECT * FROM services ORDER BY category, name LIMIT 6");
            if ($r) $services = $r->fetch_all(MYSQLI_ASSOC);
        }
        if (empty($services)) {
            $services = [
                ['name'=>'Cardiology','category'=>'Specialist','description'=>'Advanced heart care and ECG services.','price'=>2500,'icon'=>'fa-heart-pulse'],
                ['name'=>'Neurology','category'=>'Specialist','description'=>'Brain, spine and nervous system disorders.','price'=>3000,'icon'=>'fa-brain'],
                ['name'=>'Paediatrics','category'=>'Specialist','description'=>'Child healthcare from newborns through teens.','price'=>1800,'icon'=>'fa-baby'],
                ['name'=>'Laboratory Tests','category'=>'Diagnostics','description'=>'Full blood panels, urine and micro analysis.','price'=>800,'icon'=>'fa-flask'],
                ['name'=>'Emergency Care','category'=>'Emergency','description'=>'24/7 trauma and emergency services.','price'=>5000,'icon'=>'fa-truck-medical'],
                ['name'=>'Pharmacy','category'=>'Pharmacy','description'=>'In-house pharmacy with full medication range.','price'=>0,'icon'=>'fa-pills'],
            ];
        }
        $delay = 0;
        foreach ($services as $svc):
            $matched = null;
            foreach ($serviceIcons as $k => $v) {
                if (stripos($svc['name'], $k) !== false || stripos($svc['category'], $k) !== false) { $matched = $v; break; }
            }
            $icon  = $matched ? $matched[0] : ($svc['icon'] ?? 'fa-notes-medical');
            $color = $matched ? $matched[1] : '#3182ce';
            $delay += 80;
        ?>
        <div class="svc-card" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
            <div class="svc-icon" style="--ic:<?= $color ?>"><i class="fas <?= $icon ?>"></i></div>
            <h4><?= htmlspecialchars($svc['name']) ?></h4>
            <p><?= htmlspecialchars($svc['description'] ?? '') ?></p>
            <div class="svc-fee">LKR <?= number_format($svc['price'] ?? 0, 2) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="section-cta" data-aos="zoom-in">
        <a href="services.php" class="btn-outline-primary">View All Services <i class="fas fa-arrow-right"></i></a>
    </div>
</section>

<!-- ══════════ WHY CHOOSE US ══════════ -->
<section class="why-section">
    <div class="why-bg-image"></div>
    <div class="why-content">
        <div class="why-text" data-aos="fade-right">
            <span class="section-tag light">Why MediCare Plus</span>
            <h2>Healthcare You Can<br>Truly Trust</h2>
            <div class="why-list">
                <div class="why-item"><i class="fas fa-check-circle"></i><div><strong>Board-Certified Specialists</strong><p>Every doctor is fully vetted, certified and rated by real patients.</p></div></div>
                <div class="why-item"><i class="fas fa-check-circle"></i><div><strong>Instant Online Booking</strong><p>Book appointments in under 60 seconds, 24 hours a day.</p></div></div>
                <div class="why-item"><i class="fas fa-check-circle"></i><div><strong>Secure Medical Records</strong><p>Your data is encrypted end-to-end and always in your control.</p></div></div>
                <div class="why-item"><i class="fas fa-check-circle"></i><div><strong>24/7 Emergency Support</strong><p>Round-the-clock care for when it matters most.</p></div></div>
            </div>
            <a href="register.php" class="btn-hero-primary" style="display:inline-flex;margin-top:30px;">Get Started Free <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="why-image-stack" data-aos="fade-left" data-aos-delay="150">
            <div class="why-img-card main">
                <img src="https://images.unsplash.com/photo-1551076805-e1869033e561?w=600&q=80" alt="Doctor consulting patient">
            </div>
            <div class="why-img-card secondary">
                <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=400&q=80" alt="Medical lab">
            </div>
            <div class="why-stat-bubble">
                <strong>98%</strong><span>Satisfaction Rate</span>
            </div>
        </div>
    </div>
</section>

<!-- ══════════ FEATURED DOCTORS ══════════ -->
<section class="home-section">
    <div class="section-header" data-aos="fade-up">
        <span class="section-tag">Meet the Team</span>
        <h2 class="section-title">Featured Specialists</h2>
        <p class="section-subtitle">Our doctors are leaders in their fields — compassionate, experienced, and dedicated to you.</p>
    </div>
    <div class="doctors-grid">
        <?php
        $featuredDoctors = [];
        if (isset($conn) && $conn) {
            $r = $conn->query("SELECT d.*, u.first_name, u.last_name, d.specialization, d.rating, d.consultation_fee FROM doctors d JOIN users u ON d.user_id = u.id WHERE u.status='active' LIMIT 3");
            if ($r) $featuredDoctors = $r->fetch_all(MYSQLI_ASSOC);
        }
        $stockPhotos = [
            'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&q=80',
            'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&q=80',
            'https://images.unsplash.com/photo-1582750433449-648ed127bb54?w=400&q=80',
        ];
        if (empty($featuredDoctors)) {
            $featuredDoctors = [
                ['first_name'=>'Kasun','last_name'=>'Perera','specialization'=>'Cardiology','rating'=>4.8,'consultation_fee'=>2500,'id'=>1],
                ['first_name'=>'Nimasha','last_name'=>'Silva','specialization'=>'Neurology','rating'=>4.7,'consultation_fee'=>3000,'id'=>2],
                ['first_name'=>'Rohana','last_name'=>'Fernando','specialization'=>'Paediatrics','rating'=>4.9,'consultation_fee'=>1800,'id'=>3],
            ];
        }
        foreach ($featuredDoctors as $i => $doc):
            $photo = $stockPhotos[$i % count($stockPhotos)];
            $stars = round($doc['rating'] ?? 4.5);
        ?>
        <div class="doc-profile-card" data-aos="fade-up" data-aos-delay="<?= $i * 120 ?>">
            <div class="doc-img-wrap">
                <img src="<?= $photo ?>" alt="Dr. <?= htmlspecialchars($doc['first_name']) ?>">
                <div class="doc-badge"><?= htmlspecialchars($doc['specialization']) ?></div>
            </div>
            <div class="doc-profile-body">
                <h4>Dr. <?= htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']) ?></h4>
                <div class="doc-stars">
                    <?php for($s=0;$s<5;$s++) echo '<i class="fas fa-star'.($s < $stars ? '' : '-o').'"></i>'; ?>
                    <span><?= number_format($doc['rating'] ?? 4.5, 1) ?></span>
                </div>
                <p class="doc-fee">Consultation: <strong>LKR <?= number_format($doc['consultation_fee'] ?? 0) ?></strong></p>
                <a href="doctors.php" class="btn-doc-profile">View Profile <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="section-cta" data-aos="zoom-in">
        <a href="doctors.php" class="btn-outline-primary">Browse All Doctors <i class="fas fa-arrow-right"></i></a>
    </div>
</section>

<!-- ══════════ TESTIMONIALS ══════════ -->
<section class="testimonials-section">
    <div class="section-header" data-aos="fade-up">
        <span class="section-tag light">Patient Stories</span>
        <h2 class="section-title" style="color:#fff;">What Our Patients Say</h2>
    </div>
    <div class="testimonials-track-wrap" data-aos="fade-up" data-aos-delay="100">
        <div class="testimonials-track" id="testimonialsTrack">
            <?php
            $testimonials = [
                ['name'=>'Amara Wijesinghe','role'=>'Patient','avatar'=>'AW','text'=>'MediCare Plus changed how I manage my health. Booking with Dr. Perera online was effortless and the consultation was outstanding.','rating'=>5],
                ['name'=>'Sahan Bandara','role'=>'Patient','avatar'=>'SB','text'=>'The digital medical records feature is a game-changer. I can access all my reports from my phone any time. Truly world-class service.','rating'=>5],
                ['name'=>'Dilini Jayawardena','role'=>'Patient','avatar'=>'DJ','text'=>'I was nervous about online healthcare but MediCare Plus made everything seamless. The doctors are amazing and very attentive.','rating'=>5],
                ['name'=>'Ruwan Perera','role'=>'Patient','avatar'=>'RP','text'=>'Fast, reliable, and professional. Got an appointment in minutes and the follow-up care was exceptional. Highly recommended!','rating'=>5],
                ['name'=>'Nisha Fernando','role'=>'Patient','avatar'=>'NF','text'=>'As a busy professional, the convenience of MediCare Plus is unmatched. My entire family uses this platform and we love it.','rating'=>5],
            ];
            // Duplicate for seamless loop
            $all = array_merge($testimonials, $testimonials);
            foreach ($all as $t): ?>
            <div class="testi-card">
                <div class="testi-stars"><?= str_repeat('<i class="fas fa-star"></i>', $t['rating']) ?></div>
                <p>"<?= htmlspecialchars($t['text']) ?>"</p>
                <div class="testi-author">
                    <div class="testi-avatar"><?= $t['avatar'] ?></div>
                    <div><strong><?= $t['name'] ?></strong><span><?= $t['role'] ?></span></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ══════════ QUOTE STRIP ══════════ -->
<section class="quote-strip" data-aos="fade-up">
    <div class="quote-inner">
        <i class="fas fa-quote-left"></i>
        <blockquote>"The good physician treats the disease; the great physician treats the patient who has the disease."</blockquote>
        <cite>— William Osler, Father of Modern Medicine</cite>
    </div>
</section>

<!-- ══════════ CTA BANNER ══════════ -->
<section class="cta-banner" data-aos="zoom-in">
    <div class="cta-banner-inner">
        <div>
            <h2>Ready to Take Control of Your Health?</h2>
            <p>Join over 15,000 patients who trust MediCare Plus for their healthcare needs.</p>
        </div>
        <div class="cta-banner-btns">
            <a href="register.php" class="btn-hero-primary">Create Free Account</a>
            <a href="book_appointment.php" class="btn-hero-secondary" style="border-color:#fff;color:#fff;">Book Now</a>
        </div>
    </div>
</section>

<!-- AOS + Counter + Parallax JS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 900, easing: 'ease-in-out-quart', once: true, offset: 60 });

// ── Animated counters ──
const counters = document.querySelectorAll('.counter');
const speed = 2000;
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (!e.isIntersecting) return;
        const el = e.target, target = +el.dataset.target;
        const step = target / (speed / 16);
        let cur = 0;
        const tick = () => {
            cur = Math.min(cur + step, target);
            el.textContent = Math.floor(cur).toLocaleString();
            if (cur < target) requestAnimationFrame(tick);
        };
        tick();
        observer.unobserve(el);
    });
}, { threshold: 0.5 });
counters.forEach(c => observer.observe(c));

// ── Hero parallax ──
window.addEventListener('scroll', () => {
    const hero = document.getElementById('hero');
    if (hero) hero.style.backgroundPositionY = (window.scrollY * 0.4) + 'px';
});

// ── Testimonials infinite scroll ──
const track = document.getElementById('testimonialsTrack');
let pos = 0;
function scrollTestimonials() {
    pos += 0.5;
    if (pos >= track.scrollWidth / 2) pos = 0;
    track.style.transform = `translateX(-${pos}px)`;
    requestAnimationFrame(scrollTestimonials);
}
scrollTestimonials();

// ── Particle background ──
const canvas = document.createElement('canvas');
const pc = document.getElementById('particles');
if (pc) {
    pc.appendChild(canvas);
    const ctx = canvas.getContext('2d');
    canvas.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;';
    const resize = () => { canvas.width = pc.offsetWidth; canvas.height = pc.offsetHeight; };
    resize(); window.addEventListener('resize', resize);
    const pts = Array.from({length:60}, () => ({
        x: Math.random()*canvas.width, y: Math.random()*canvas.height,
        vx: (Math.random()-.5)*.4, vy: (Math.random()-.5)*.4,
        r: Math.random()*2+1
    }));
    function drawParticles() {
        ctx.clearRect(0,0,canvas.width,canvas.height);
        pts.forEach(p => {
            p.x += p.vx; p.y += p.vy;
            if (p.x<0||p.x>canvas.width) p.vx *= -1;
            if (p.y<0||p.y>canvas.height) p.vy *= -1;
            ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
            ctx.fillStyle='rgba(255,255,255,0.5)'; ctx.fill();
        });
        pts.forEach((a,i) => pts.slice(i+1).forEach(b => {
            const d=Math.hypot(a.x-b.x,a.y-b.y);
            if(d<120){ctx.beginPath();ctx.moveTo(a.x,a.y);ctx.lineTo(b.x,b.y);
            ctx.strokeStyle=`rgba(255,255,255,${0.15*(1-d/120)})`;ctx.stroke();}
        }));
        requestAnimationFrame(drawParticles);
    }
    drawParticles();
}
</script>

<?php include 'footer.php'; ?>
