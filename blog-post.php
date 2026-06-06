<?php
require_once 'auth.php';
$pageTitle = 'Blog Post';
include 'header.php';

$postId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $postId ? fetch_blog_post_by_id($postId) : null;

if (!$post) {
    echo '<section class="container"><div class="empty-state">Post not found.</div></section>';
    include 'footer.php';
    exit;
}

// Fetch author info
$conn = get_db_connection();
$authorQuery = 'SELECT first_name, last_name FROM users WHERE id = ? LIMIT 1';
$stmt = $conn->prepare($authorQuery);
$stmt->bind_param('i', $post['author_id']);
$stmt->execute();
$authorResult = $stmt->get_result();
$author = $authorResult->fetch_assoc();
$stmt->close();
$conn->close();
$authorName = $author ? $author['first_name'] . ' ' . $author['last_name'] : 'Unknown';
?>

<section class="container">
    <article class="blog-post">
        <h1><?php echo e($post['title']); ?></h1>
        <p class="meta">
            <strong>By:</strong> <?php echo e($authorName); ?> |
            <strong>Published:</strong> <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
        </p>

        <div class="blog-content">
            <?php echo nl2br(e($post['content'])); ?>
        </div>

        <div class="actions">
            <a href="blog.php" class="button">Back to Posts</a>
        </div>
    </article>
</section>

<style>
    .blog-post {
        max-width: 800px;
        margin: 0 auto;
    }

    .blog-post h1 {
        margin-bottom: 10px;
    }

    .blog-post .meta {
        color: #666;
        font-size: 14px;
        margin-bottom: 30px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 15px;
    }

    .blog-content {
        line-height: 1.8;
        color: #333;
        margin-bottom: 30px;
    }

    .actions {
        margin-top: 30px;
    }
</style>

<?php include 'footer.php'; ?>