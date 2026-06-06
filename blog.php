<?php
require_once 'auth.php';
$pageTitle = 'Health Blogs & Tips';
include 'header.php';

$posts = fetch_blog_posts();
?>

<section class="container">
    <h1>Health Blogs & Tips</h1>
    <p>Stay informed with our latest health articles and wellness tips.</p>

    <?php if (current_user_role() === 'admin' || current_user_role() === 'doctor'): ?>
        <div style="margin-bottom: 20px;">
            <a href="create_blog.php" class="button">Create New Post</a>
        </div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <div class="empty-state">No blog posts yet. Check back soon!</div>
    <?php else: ?>
        <div class="blog-list">
            <?php foreach ($posts as $post): ?>
                <article class="blog-preview">
                    <h2><?php echo e($post['title']); ?></h2>
                    <p class="meta">Published: <?php echo date('M d, Y', strtotime($post['created_at'])); ?></p>
                    <p class="excerpt"><?php echo nl2br(e(substr($post['content'], 0, 300))); ?>...</p>
                    <a href="blog-post.php?id=<?php echo urlencode($post['id']); ?>" class="button">Read More</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<style>
    .blog-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .blog-preview {
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        background: #fafafa;
    }

    .blog-preview h2 {
        margin: 0 0 10px 0;
    }

    .blog-preview .meta {
        color: #999;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .blog-preview .excerpt {
        color: #555;
        line-height: 1.6;
        margin-bottom: 15px;
    }
</style>

<?php include 'footer.php'; ?>