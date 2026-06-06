<?php
include('db_connect.php');
include('header.php');

/**
 * Return a safe image filename — rejects URL schemes and path separators.
 */
function safe_image_filename(string $value, string $fallback): string
{
    $value = trim($value);
    if ($value === '' || preg_match('/[:\/\\\\]/', $value)) {
        return $fallback;
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$post_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;

if ($post_id) {
    $conn = get_db_connection();
    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ? AND status = 'published'");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $post = $res->fetch_assoc();
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<section style="max-width: 800px; margin: 50px auto; padding: 40px; background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    <?php if ($post): ?>

        <?php if (!empty($post['image_url'])): ?>
            <img src="images/<?php echo safe_image_filename($post['image_url'], 'default-blog.jpg'); ?>" style="width: 100%; max-height: 400px; object-fit: cover; border-radius: 10px; margin-bottom: 30px;" alt="Blog Header">
        <?php endif; ?>

        <h1 style="color: #2b6cb0; margin-bottom: 15px; font-size: 2.2rem;"><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h1>

        <p style="color: #718096; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px;">
            <strong>By:</strong> <?php echo htmlspecialchars($post['author'], ENT_QUOTES, 'UTF-8'); ?> |
            <strong>Published:</strong> <?php echo htmlspecialchars(date('F d, Y', strtotime($post['published_at'])), ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <div style="line-height: 1.8; color: #4a5568; font-size: 1.1rem;">
            <?php echo nl2br(htmlspecialchars($post['body'], ENT_QUOTES, 'UTF-8')); ?>
        </div>

        <div style="margin-top: 40px;">
            <a href="blog.php" style="background: #e2e8f0; color: #4a5568; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">&larr; Back to Posts</a>
        </div>

    <?php else: ?>
        <div style="text-align: center; color: #e53e3e;">
            <h2>Article Not Found</h2>
            <p>The post you are looking for does not exist.</p>
            <a href="blog.php">Return to Blog</a>
        </div>
    <?php endif; ?>
</section>

<?php include('footer.php'); ?>