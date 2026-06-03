<?php
require_once 'auth.php';
require_role(['admin', 'doctor']);

$pageTitle = 'Create Blog Post';
include 'header.php';

$user = current_user();
$userId = $user['id'];

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    if (!$title || !$content) {
        $error = 'Title and content are required.';
    } else {
        $success = create_blog_post($title, $content, $userId);
        if (!$success) {
            $error = 'Failed to create post. Please try again.';
        }
    }
}
?>

<section class="container">
    <h1>Create Blog Post</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Blog post created successfully! <a href="blog.php">View all posts</a>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
        <form method="post" class="form">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required maxlength="200">
            </div>

            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" rows="10" required></textarea>
                <small>Use line breaks for paragraphs. HTML not supported.</small>
            </div>

            <button type="submit" class="button">Publish Post</button>
            <a href="blog.php" class="button secondary">Cancel</a>
        </form>
    <?php endif; ?>
</section>

<style>
    .alert {
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .button.secondary {
        background: #ccc;
        color: #333;
    }

    small {
        display: block;
        color: #666;
        margin-top: 5px;
    }
</style>

<?php include 'footer.php'; ?>