<?php
session_start();
include('../admin/includes/connection.php');

$nid = isset($_GET['nid']) ? intval($_GET['nid']) : 0;
if ($nid === 0) {
    header('Location: blog.php');
    exit;
}

// Fetch article
$query = mysqli_query($conn, "
    SELECT tblposts.id as pid, tblposts.PostTitle as posttitle, tblposts.PostImage,
           tblposts.PostDetails as postdetails, tblposts.PostingDate as postingdate,
           tblposts.PostUrl as url, tblposts.viewCounter, tblposts.postedBy,
           tbl_school_category.CategoryName as category, tbl_school_category.id as cid
    FROM tblposts
    LEFT JOIN tbl_school_category ON tbl_school_category.id = tblposts.CategoryId
    WHERE tblposts.id = '$nid' AND tblposts.Is_Active=1
");
$post = mysqli_fetch_assoc($query);
if (!$post) {
    header('Location: blog.php');
    exit;
}

// Increment view counter
mysqli_query($conn, "UPDATE tblposts SET viewCounter = viewCounter + 1 WHERE id = '$nid'");
$views = $post['viewCounter'] + 1;

// Fetch related posts (same category, exclude current)
$related_query = mysqli_query($conn, "
    SELECT id, PostTitle, PostImage, PostingDate
    FROM tblposts
    WHERE CategoryId = (SELECT CategoryId FROM tblposts WHERE id = '$nid')
      AND id != '$nid' AND Is_Active=1
    ORDER BY id DESC LIMIT 4
");

$readingTime = max(1, ceil(str_word_count(strip_tags($post['postdetails'])) / 200));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo htmlspecialchars(substr(strip_tags($post['postdetails']), 0, 160)); ?>">
    <meta name="author" content="GIHEKE TSS">
    <meta property="og:title" content="<?php echo htmlspecialchars($post['posttitle']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(substr(strip_tags($post['postdetails']), 0, 160)); ?>">
    <meta property="og:image" content="../admin/Blog%20Gallery/<?php echo rawurlencode($post['PostImage']); ?>">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta name="twitter:card" content="summary_large_image">
    <title><?php echo htmlspecialchars($post['posttitle']); ?> | GIHEKE TSS</title>
    <link href="../img/giheke logo.webp" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/haip-theme.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; background: #f8fafc; color: #0f172a; line-height: 1.6; }

        .article-hero {
            position: relative; min-height: 420px;
            background: linear-gradient(160deg, #0f172a, #1e1b4b 50%, #312e81);
            display: flex; align-items: flex-end; overflow: hidden;
        }
        .article-hero::before {
            content: ''; position: absolute; inset: 0;
            background: url('../admin/Blog Gallery/<?php echo htmlspecialchars($post['PostImage']); ?>') center/cover no-repeat;
            opacity: 0.35; filter: saturate(0.8);
        }
        .article-hero::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(15,23,42,0.95) 0%, rgba(15,23,42,0.5) 50%, transparent 80%);
        }
        .article-hero .container { position: relative; z-index: 2; padding: 60px 0 50px; }

        .breadcrumb-nav { display: flex; gap: 8px; align-items: center; margin-bottom: 24px; flex-wrap: wrap; }
        .breadcrumb-nav a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; }
        .breadcrumb-nav a:hover { color: #fff; }
        .breadcrumb-nav span { color: rgba(255,255,255,0.4); font-size: 0.85rem; }
        .breadcrumb-nav .sep { color: rgba(255,255,255,0.3); font-size: 0.7rem; }

        .article-badge {
            display: inline-block; padding: 6px 18px; border-radius: 100px;
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
            background: rgba(245,158,11,0.9); color: #fff; margin-bottom: 18px;
            backdrop-filter: blur(6px); border: 1px solid rgba(255,255,255,0.15);
        }
        .article-title {
            font-size: clamp(1.8rem, 4vw, 3rem); font-weight: 800; line-height: 1.2;
            color: #fff; letter-spacing: -0.03em; max-width: 900px;
        }
        .article-meta {
            display: flex; gap: 20px; flex-wrap: wrap; margin-top: 24px;
            color: rgba(255,255,255,0.75); font-size: 0.88rem;
        }
        .article-meta i { margin-right: 6px; }

        .main-section { padding: 50px 0 70px; }
        .article-body {
            background: #fff; border-radius: 20px; padding: 48px 52px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid rgba(226,232,240,0.6);
        }
        .article-content {
            font-size: 1.1rem; line-height: 1.9; color: #334155;
            font-family: 'Plus Jakarta Sans', 'Georgia', serif;
        }
        .article-content p { margin-bottom: 1.6rem; }
        .article-content img {
            max-width: 100%; height: auto; border-radius: 16px;
            margin: 32px 0; box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }
        .article-content h2, .article-content h3 {
            font-family: 'Inter', sans-serif; color: #0f172a;
            font-weight: 700; margin-top: 40px; margin-bottom: 16px;
        }
        .article-content h2 { font-size: 1.6rem; }
        .article-content h3 { font-size: 1.3rem; }
        .article-content blockquote {
            border-left: 4px solid #4f46e5; padding: 24px 32px; margin: 32px 0;
            background: #f8fafc; border-radius: 0 16px 16px 0;
            font-style: italic; color: #475569; font-size: 1.05rem;
        }
        .article-content ul, .article-content ol { padding-left: 24px; margin-bottom: 1.6rem; }
        .article-content a { color: #4f46e5; text-decoration: underline; text-underline-offset: 2px; }
        .article-content a:hover { color: #3730a3; }

        .article-footer {
            margin-top: 40px; padding-top: 32px;
            border-top: 1px solid #e2e8f0;
        }
        .author-card {
            display: flex; align-items: center; gap: 18px; padding: 20px 24px;
            background: #f8fafc; border-radius: 14px; margin-bottom: 28px;
        }
        .author-avatar {
            width: 52px; height: 52px; border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 1.2rem; flex-shrink: 0;
        }
        .author-info h5 { font-weight: 700; color: #0f172a; font-size: 1rem; margin-bottom: 2px; }
        .author-info .role { color: #64748b; font-size: 0.82rem; }

        .share-section { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .share-section .label { font-size: 0.82rem; font-weight: 600; color: #64748b; margin-right: 8px; }
        .share-btn {
            width: 42px; height: 42px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1rem; text-decoration: none;
            transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1);
            border: none; cursor: pointer;
        }
        .share-btn:hover { transform: translateY(-3px) scale(1.05); }
        .share-btn.fb { background: #1877f2; }
        .share-btn.tw { background: #0f172a; }
        .share-btn.wa { background: #25d366; }
        .share-btn.li { background: #0a66c2; }
        .share-btn.copy {
            background: #e2e8f0; color: #475569; font-size: 1.1rem;
        }
        .share-btn.copy:hover { background: #cbd5e1; }

        .tags-section {
            display: flex; gap: 8px; flex-wrap: wrap; margin-top: 20px;
        }
        .tag {
            padding: 5px 14px; border-radius: 8px; font-size: 0.78rem;
            font-weight: 600; background: #f1f5f9; color: #64748b;
            text-decoration: none; transition: all 0.2s;
        }
        .tag:hover { background: #e2e8f0; color: #334155; }

        .sidebar-card {
            background: #fff; border-radius: 16px; padding: 28px; margin-bottom: 24px;
            border: 1px solid rgba(226,232,240,0.6);
        }
        .sidebar-card h5 {
            font-weight: 700; color: #0f172a; margin-bottom: 20px;
            padding-bottom: 12px; border-bottom: 2px solid #4f46e5;
            font-size: 1rem;
        }
        .related-item {
            display: flex; gap: 14px; padding: 12px 0;
            border-bottom: 1px solid #f1f5f9; text-decoration: none; color: inherit;
            transition: all 0.2s;
        }
        .related-item:last-child { border-bottom: none; }
        .related-item:hover { opacity: 0.8; }
        .related-item img {
            width: 72px; height: 72px; border-radius: 10px;
            object-fit: cover; flex-shrink: 0;
        }
        .related-item .info { flex: 1; }
        .related-item .info h6 {
            font-size: 0.88rem; font-weight: 600; line-height: 1.4;
            color: #0f172a; margin-bottom: 4px;
        }
        .related-item .info small { color: #94a3b8; font-size: 0.75rem; }

        .back-link {
            display: inline-flex; align-items: center; gap: 8px;
            color: #64748b; text-decoration: none; font-size: 0.9rem;
            font-weight: 500; margin-bottom: 28px; transition: color 0.2s;
        }
        .back-link:hover { color: #4f46e5; }

        .toast-msg {
            position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
            background: #0f172a; color: #fff; padding: 12px 28px;
            border-radius: 12px; font-size: 0.88rem; font-weight: 500;
            z-index: 9999; opacity: 0; transition: all 0.3s ease;
            pointer-events: none; box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        .toast-msg.show { opacity: 1; transform: translateX(-50%) translateY(0); }

        @media (max-width: 992px) {
            .article-hero { min-height: 320px; }
            .article-hero .container { padding: 40px 0 36px; }
            .article-body { padding: 32px 24px; border-radius: 16px; }
            .article-content { font-size: 1.05rem; }
        }
        @media (max-width: 576px) {
            .article-hero { min-height: 280px; }
            .article-hero .container { padding: 30px 16px 28px; }
            .article-body { padding: 24px 16px; border-radius: 12px; }
            .article-content { font-size: 1rem; }
            .article-meta { gap: 12px; font-size: 0.82rem; }
            .author-card { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<?php include '../includes/haip-header.php'; ?>

<article class="article-hero">
    <div class="container">
        <div class="breadcrumb-nav" data-aos="fade-up">
            <a href="../index.php">Home</a>
            <span class="sep"><i class="bi bi-chevron-right"></i></span>
            <a href="blog.php">News</a>
            <span class="sep"><i class="bi bi-chevron-right"></i></span>
            <span><?php echo htmlspecialchars($post['category']); ?></span>
        </div>
        <div data-aos="fade-up" data-aos-delay="100">
            <span class="article-badge"><?php echo htmlspecialchars($post['category']); ?></span>
            <h1 class="article-title"><?php echo htmlspecialchars($post['posttitle']); ?></h1>
            <div class="article-meta">
                <span><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($post['postedBy'] ?? 'School Administration'); ?></span>
                <span><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($post['postingdate'])); ?></span>
                <span><i class="bi bi-clock"></i> <?php echo $readingTime; ?> min read</span>
                <span><i class="bi bi-eye"></i> <?php echo $views; ?> views</span>
            </div>
        </div>
    </div>
</article>

<section class="main-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <a href="blog.php" class="back-link" data-aos="fade-right"><i class="bi bi-arrow-left"></i> Back to News</a>
                <div class="article-body" data-aos="fade-up">
                    <div class="article-content">
                        <?php echo $post['postdetails']; ?>
                    </div>
                    <div class="article-footer">
                        <div class="author-card">
                            <div class="author-avatar"><?php echo strtoupper(substr($post['postedBy'] ?? 'A', 0, 1)); ?></div>
                            <div class="author-info">
                                <h5><?php echo htmlspecialchars($post['postedBy'] ?? 'School Administration'); ?></h5>
                                <span class="role">School Administration &bull; GIHEKE TSS</span>
                            </div>
                        </div>
                        <div class="share-section">
                            <span class="label">Share article</span>
                            <a href="https://www.facebook.com/share.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn fb" aria-label="Share on Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="https://twitter.com/share?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn tw" aria-label="Share on Twitter"><i class="bi bi-twitter-x"></i></a>
                            <a href="https://web.whatsapp.com/send?text=<?php echo urlencode(htmlspecialchars($post['posttitle']) . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn wa" aria-label="Share on WhatsApp"><i class="bi bi-whatsapp"></i></a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn li" aria-label="Share on LinkedIn"><i class="bi bi-linkedin"></i></a>
                            <button class="share-btn copy" onclick="copyLink()" aria-label="Copy link"><i class="bi bi-link-45deg"></i></button>
                        </div>
                        <div class="tags-section">
                            <span class="tag">#<?php echo preg_replace('/\s+/', '', htmlspecialchars($post['category'])); ?></span>
                            <span class="tag">#SchoolNews</span>
                            <span class="tag">#GIHEKE</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sidebar-card" data-aos="fade-up" data-aos-delay="100">
                    <h5><i class="bi bi-newspaper me-2"></i>Related Articles</h5>
                    <?php if (mysqli_num_rows($related_query) > 0): ?>
                        <?php while ($rel = mysqli_fetch_assoc($related_query)): ?>
                        <a href="news-details.php?nid=<?php echo $rel['id']; ?>" class="related-item">
                            <img src="../admin/Blog Gallery/<?php echo htmlspecialchars($rel['PostImage']); ?>" alt="<?php echo htmlspecialchars($rel['PostTitle']); ?>" loading="lazy">
                            <div class="info">
                                <h6><?php echo htmlspecialchars($rel['PostTitle']); ?></h6>
                                <small><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($rel['PostingDate'])); ?></small>
                            </div>
                        </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color:#94a3b8; font-size:0.88rem; text-align:center; padding:20px 0;">No related articles found.</p>
                    <?php endif; ?>
                </div>

                <div class="sidebar-card" data-aos="fade-up" data-aos-delay="150">
                    <h5><i class="bi bi-arrow-left-circle me-2"></i>Back to</h5>
                    <a href="blog.php" class="btn w-100" style="background:#4f46e5; color:#fff; border-radius:12px; padding:12px; font-weight:600; border:none; text-decoration:none; text-align:center; display:block;">All News & Updates <i class="bi bi-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="toast" class="toast-msg">Link copied to clipboard!</div>

<?php include '../includes/haip-footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../assets/vendor/aos/aos.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
AOS.init({ duration: 700, once: true, offset: 50 });

function copyLink() {
    var input = document.createElement('input');
    input.value = window.location.href;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    var toast = document.getElementById('toast');
    toast.classList.add('show');
    setTimeout(function() { toast.classList.remove('show'); }, 2200);
}
</script>
</body>
</html>
