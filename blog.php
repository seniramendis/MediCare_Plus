<?php
$pageTitle = 'Health & Wellness Blog | MediCare Plus';
require_once 'auth.php';
include('header.php');
require_once 'db_connect.php';

/**
 * Return a safe image filename — only allow plain filenames with no path
 * traversal and no URL scheme (e.g. javascript:, data:).
 *
 * @param string $value
 * @param string $fallback
 * @return string HTML-safe relative image path
 */
function safe_image_filename($value, $fallback)
{
    $value = trim((string)$value);
    // Reject anything containing a URL scheme or path separators
    if ($value === '' || preg_match('/[:\/\\\\]/', $value)) {
        return $fallback;
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Direct database query
$query = "SELECT * FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC";
$result = $conn ? $conn->query($query) : false;
?>

<div style="text-align: center; padding: 60px 20px;">
    <h1 style="color: #2b6cb0; font-size: 2.5rem;">Health & Wellness Blog</h1>
</div>

<div class="blog-grid">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($post = $result->fetch_assoc()): ?>
            <div class="blog-card">
                <img src="images/<?php echo safe_image_filename($post['image_url'], 'default-blog.jpg'); ?>" alt="Blog Image" class="blog-img">

                <div class="blog-content">
                    <span class="blog-date"><?php echo date('M d, Y', strtotime($post['published_at'])); ?> • By <?php echo htmlspecialchars($post['author']); ?></span>
                    <h3 style="color: #2d3748; margin-bottom: 10px;"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p style="color: #718096;"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                    <a href="blog-post.php?id=<?php echo (int)$post['id']; ?>" class="read-more">Read Article <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; width: 100%;">No blog posts available.</p>
    <?php endif; ?>
</div>

<?php
include('footer.php');
?>