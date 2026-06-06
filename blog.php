<?php
require_once 'auth.php'; // ensure authentication functions are available if header needs it, though public
include('header.php');

$posts = fetch_blog_posts();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Blog | MediCare Plus</title>
    <link rel="stylesheet" href="assets/css/HomeStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .blog-header {
            text-align: center;
            padding: 60px 20px;
            background-color: #f0f4f8;
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            padding: 50px 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .blog-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease;
        }

        .blog-card:hover {
            transform: translateY(-8px);
        }

        .blog-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .blog-content {
            padding: 25px;
        }

        .blog-date {
            color: #e53e3e;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: block;
        }

        .blog-title {
            color: #2d3748;
            font-size: 1.4rem;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .blog-excerpt {
            color: #718096;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .read-more {
            display: inline-block;
            color: #2b6cb0;
            font-weight: bold;
            text-decoration: none;
        }

        .read-more:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="blog-header">
        <h1 style="color: #2b6cb0; font-size: 2.5rem; margin-bottom: 10px;">Health & Wellness Blog</h1>
        <p style="color: #4a5568; font-size: 1.1rem;">Latest medical news, tips, and insights from our experts.</p>
    </div>

    <div class="blog-grid">
        <?php if ($posts && count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="blog-card">
                    <img src="images/<?php echo !empty($post['image_url']) ? htmlspecialchars($post['image_url']) : 'default-blog.jpg'; ?>" alt="Blog Image" class="blog-img">

                    <div class="blog-content">
                        <span class="blog-date"><?php echo date('M d, Y', strtotime($post['created_at'])); ?> • By <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?></span>
                        <h3 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p class="blog-excerpt"><?php echo htmlspecialchars(substr($post['content'], 0, 100)) . '...'; ?></p>
                        <a href="blog-post.php?id=<?php echo $post['id']; ?>" class="read-more">Read Full Article <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; width: 100%; color: #718096;">No blog posts available at the moment.</p>
        <?php endif; ?>
    </div>

    <?php include('footer.php'); ?>
</body>

</html>