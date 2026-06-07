<?php
$pageTitle = 'Health & Wellness Blog | MediCare Plus';
require_once 'auth.php';
include('header.php');

function safe_image_filename($value, $fallback) {
    $value = trim((string)$value);
    if ($value === '' || preg_match('/[:\\/\\\\]/', $value)) return $fallback;
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$query  = "SELECT * FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC";
$result = isset($conn) && $conn ? $conn->query($query) : false;
?>

<div class="blog-hero" data-aos="fade-up">
    <span class="section-tag light"><i class="fas fa-pen-nib"></i> Health Insights</span>
    <h1>Health &amp; Wellness Blog</h1>
    <p>Expert advice, medical tips, and the latest health news from our specialist team.</p>
</div>

<div class="blog-grid">
<?php
$categoryIcons = ['heart'=>'❤️','diabetes'=>'🩺','child'=>'👶','stress'=>'🧘','neuro'=>'🧠','ortho'=>'🦴','general'=>'🏥'];
if ($result && $result->num_rows > 0):
    $delay = 0;
    while ($post = $result->fetch_assoc()):
        $delay += 80;
        $hasImg = !empty($post['image_url']);
?>
    <div class="blog-card" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
        <div class="blog-img-wrap">
            <?php if ($hasImg): ?>
                <img src="assets/images/<?= safe_image_filename($post['image_url'], '') ?>"
                     alt="<?= htmlspecialchars($post['title']) ?>"
                     class="blog-img"
                     onerror="this.parentNode.innerHTML='<div class=blog-img-placeholder><i class=fas fa-newspaper></i></div>'">
            <?php else: ?>
                <div class="blog-img-placeholder"><i class="fas fa-newspaper"></i></div>
            <?php endif; ?>
        </div>
        <div class="blog-content">
            <span class="blog-date">
                <i class="fas fa-calendar-alt"></i>
                <?= date('M d, Y', strtotime($post['published_at'])) ?>
                &bull; By <?= htmlspecialchars($post['author']) ?>
            </span>
            <h3><?= htmlspecialchars($post['title']) ?></h3>
            <p><?= htmlspecialchars($post['excerpt'] ?? '') ?></p>
            <a href="blog-post.php?id=<?= (int)$post['id'] ?>" class="read-more">
                Read Article <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
<?php endwhile; else: ?>
    <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--text-muted);">
        <i class="fas fa-newspaper" style="font-size:3rem;opacity:.3;display:block;margin-bottom:16px;"></i>
        <p style="font-size:1.1rem;">No blog posts available yet. Check back soon!</p>
    </div>
<?php endif; ?>
</div>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({duration:800,once:true});</script>
<?php include('footer.php'); ?>
