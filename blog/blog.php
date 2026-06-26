<?php
session_start();
$pageTitle = 'School News - Latest Updates & Events';
$pageDescription = 'Stay informed with the latest news, events, and updates from GIHEKE Technical Secondary School.';
$pageKeywords = 'GIHEKE news, school announcements, Rwanda education, technical school news, student events';
include('../admin/includes/connection.php');
include('../assets/seo-meta.php');

$feat_query = mysqli_query($conn, "SELECT tblposts.id as pid, tblposts.PostTitle as posttitle, tblposts.PostImage, tblposts.MediaType, tblposts.PostDetails, tblposts.PostingDate, tblposts.PostUrl, tbl_school_category.CategoryName as category FROM tblposts LEFT JOIN tbl_school_category ON tbl_school_category.id=tblposts.CategoryId WHERE tblposts.Is_Active=1 ORDER BY tblposts.id DESC LIMIT 1");
$featured = mysqli_fetch_assoc($feat_query);

$cat_result = mysqli_query($conn, "SELECT DISTINCT tbl_school_category.CategoryName, tbl_school_category.id FROM tbl_school_category INNER JOIN tblposts ON tblposts.CategoryId = tbl_school_category.id WHERE tblposts.Is_Active=1");

$pageno = isset($_GET['pageno']) ? (int)$_GET['pageno'] : 1;
if($pageno < 1) $pageno = 1;
$no_of_records_per_page = 6;
$offset = ($pageno-1) * $no_of_records_per_page;
$total_pages_sql = "SELECT COUNT(*) FROM tblposts WHERE Is_Active=1";
$result = mysqli_query($conn, $total_pages_sql);
$total_rows = mysqli_fetch_array($result)[0];
$total_pages = ceil($total_rows / $no_of_records_per_page);

$query = mysqli_query($conn, "SELECT tblposts.id as pid, tblposts.PostTitle as posttitle, tblposts.PostImage, tblposts.MediaType, tblposts.PostDetails, tblposts.PostingDate, tblposts.PostUrl, tbl_school_category.CategoryName as category, tbl_school_category.id as cid FROM tblposts LEFT JOIN tbl_school_category ON tbl_school_category.id=tblposts.CategoryId WHERE tblposts.Is_Active=1 ORDER BY tblposts.id DESC LIMIT $offset, $no_of_records_per_page");

$trending_query = mysqli_query($conn, "SELECT id, PostTitle, PostImage, MediaType, PostingDate FROM tblposts WHERE Is_Active=1 ORDER BY id DESC LIMIT 5");
$recent_query = mysqli_query($conn, "SELECT id, PostTitle, PostImage, MediaType, PostingDate FROM tblposts WHERE Is_Active=1 ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="GIHEKE TSS School News - Latest news, events, and updates">
    <title>School News & Updates | GIHEKE TSS</title>
    <link href="../img/giheke logo.webp" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/haip-theme.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --blog-accent: #4f46e5;
            --blog-accent-2: #7c3aed;
            --blog-accent-dark: #3730a3;
            --blog-amber: #f59e0b;
            --blog-radius: 16px;
            --blog-font: 'Inter', 'Segoe UI', system-ui, sans-serif;
        }
        body { font-family: var(--blog-font); background: #f8fafc; }
        .news-hero {
            position: relative;
            padding: 110px 0 120px;
            background: linear-gradient(160deg, #0f172a 0%, #1e1b4b 40%, #312e81 100%);
            overflow: hidden; color: #fff;
        }
        .news-hero::before {
            content: ''; position: absolute; top: -40%; right: -15%;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(255,255,255,0.08), transparent 55%);
            border-radius: 50%;
            animation: heroFloat 10s ease-in-out infinite;
        }
        .news-hero::after {
            content: ''; position: absolute; bottom: -30%; left: -10%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(14,165,233,0.18), transparent 55%);
            border-radius: 50%;
            animation: heroFloat 13s ease-in-out infinite reverse;
        }
        @keyframes heroFloat {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-30px, -35px) scale(1.06); }
            100% { transform: translate(0, 0) scale(1); }
        }
        .news-hero-content { position: relative; z-index: 2; text-align: center; }
        .news-hero-content h1 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 900; margin-bottom: 12px; letter-spacing: -0.03em; }
        .news-hero-content p { opacity: 0.85; font-size: 1.05rem; max-width: 620px; margin: 0 auto 24px; }
        .news-search-box {
            max-width: 520px; margin: 0 auto; position: relative;
            background: rgba(255,255,255,0.95); border-radius: 999px; padding: 5px;
            display: flex; gap: 6px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .news-search-box input { flex: 1; border: none; background: transparent; padding: 14px 18px; border-radius: 999px; font-size: 0.95rem; outline: none; color: #0f172a; }
        .news-search-box button { padding: 14px 22px; border-radius: 999px; background: linear-gradient(135deg, var(--blog-accent), var(--blog-accent-2)); color: #fff; border: none; font-weight: 700; cursor: pointer; transition: all 0.25s; }
        .news-search-box button:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(79,70,229,0.5); color: #fff; }

        .featured-article {
            position: relative; border-radius: var(--blog-radius); overflow: hidden;
            margin-top: -52px; z-index: 3; background: #fff; display: block; text-decoration: none;
            box-shadow: 0 18px 48px rgba(0,0,0,0.12); border: 1px solid #e2e8f0; transition: all 0.35s;
        }
        .featured-article:hover { transform: translateY(-8px); box-shadow: 0 24px 56px rgba(0,0,0,0.18); }
        .featured-article-img { position: relative; height: 420px; overflow: hidden; }
        .featured-article-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s; }
        .featured-article:hover .featured-article-img img { transform: scale(1.06); }
        .featured-article-overlay {
            position: absolute; bottom: 0; left: 0; right: 0; padding: 44px;
            background: linear-gradient(transparent 10%, rgba(15,23,42,0.85));
            color: #fff;
        }
        .featured-article-overlay .badge {
            display: inline-block; padding: 5px 14px; border-radius: 999px;
            font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px;
            background: var(--blog-amber); color: #fff; margin-bottom: 14px;
        }
        .featured-article-overlay h2 { font-size: 1.75rem; font-weight: 900; margin-bottom: 10px; line-height: 1.25; }
        .featured-article-overlay .meta { display: flex; gap: 16px; font-size: 0.85rem; opacity: 0.9; flex-wrap: wrap; }

        .filter-pillbar { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; margin: 32px 0 28px; }
        .filter-pill {
            padding: 10px 22px; border-radius: 999px; font-weight: 700; font-size: 0.85rem;
            cursor: pointer; transition: all 0.25s; border: 2px solid #e2e8f0; background: #fff; color: #475569;
        }
        .filter-pill:hover, .filter-pill.active { background: var(--blog-accent); color: #fff; border-color: var(--blog-accent); box-shadow: 0 6px 18px rgba(79,70,229,0.3); }

        .blog-card {
            background: #fff; border-radius: var(--blog-radius); overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.05); transition: all 0.28s;
            border: 1px solid #e2e8f0; height: 100%; display: flex; flex-direction: column;
        }
        .blog-card:hover { transform: translateY(-8px); box-shadow: 0 18px 40px rgba(0,0,0,0.12); border-color: #c7d2fe; }
        .blog-card-img { position: relative; height: 210px; overflow: hidden; }
        .blog-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.45s; }
        .blog-card:hover .blog-card-img img { transform: scale(1.08); }
        .blog-card-badge {
            position: absolute; top: 12px; left: 12px; padding: 5px 14px; border-radius: 999px;
            font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.4px;
            background: var(--blog-amber); color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .blog-card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        .blog-card-body .meta { display: flex; gap: 12px; font-size: 0.78rem; color: #64748b; margin-bottom: 10px; flex-wrap: wrap; }
        .blog-card-body h4 { font-size: 1.05rem; font-weight: 800; color: #0f172a; line-height: 1.35; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .blog-card-body h4 a { color: inherit; text-decoration: none; transition: color 0.2s; }
        .blog-card-body h4 a:hover { color: var(--blog-accent); }
        .blog-card-body p { font-size: 0.88rem; color: #64748b; line-height: 1.65; flex: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .blog-card-footer { padding: 14px 20px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .blog-card-footer .read-time { font-size: 0.75rem; color: #64748b; }
        .blog-card-footer a { font-weight: 700; font-size: 0.82rem; color: var(--blog-accent); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; }
        .blog-card-footer a:hover { gap: 8px; color: var(--blog-accent-dark); }

        .sidebar-widget {
            background: #fff; border-radius: var(--blog-radius); padding: 22px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 24px;
        }
        .sidebar-widget h4 { font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 2px solid #f1f5f9; display: flex; align-items: center; gap: 8px; }
        .trending-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f8fafc; text-decoration: none; color: inherit; transition: all 0.2s; }
        .trending-item:last-child { border-bottom: none; }
        .trending-item:hover { padding-left: 6px; }
        .trending-item .num { font-size: 1.1rem; font-weight: 900; color: var(--blog-accent); opacity: 0.2; min-width: 22px; }
        .trending-item .content h5 { font-size: 0.88rem; font-weight: 700; color: #0f172a; margin-bottom: 2px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .trending-item .content small { color: #64748b; font-size: 0.75rem; }

        .recents-item { display: flex; gap: 14px; padding: 12px 0; border-bottom: 1px solid #f8fafc; text-decoration: none; color: inherit; transition: all 0.2s; }
        .recents-item:last-child { border-bottom: none; }
        .recents-item:hover { padding-left: 6px; }
        .recents-item .thumb { width: 72px; height: 72px; border-radius: 12px; object-fit: cover; flex-shrink: 0; }
        .recents-item .content h5 { font-size: 0.88rem; font-weight: 700; color: #0f172a; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .recents-item .content small { color: #64748b; font-size: 0.75rem; }

        .pagination-modern { display: flex; gap: 6px; justify-content: center; margin-top: 40px; }
        .pagination-modern a, .pagination-modern span { display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 40px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; color: #475569; background: #fff; }
        .pagination-modern a:hover { border-color: var(--blog-accent); color: var(--blog-accent); background: #eef2ff; }
        .pagination-modern .active { background: var(--blog-accent); color: #fff; border-color: var(--blog-accent); }
        .pagination-modern .disabled { opacity: 0.4; pointer-events: none; }

        @media (max-width: 992px) {
            .featured-article-img { height: 320px; }
            .featured-article-overlay { padding: 24px; }
            .featured-article-overlay h2 { font-size: 1.4rem; }
        }
        @media (max-width: 768px) {
            .news-hero { padding: 80px 0 100px; }
            .news-search-box { border-radius: 18px; flex-direction: column; background: transparent; box-shadow: none; border: 1px solid rgba(255,255,255,0.2); }
            .news-search-box input { background: rgba(255,255,255,0.92); border-radius: 14px; }
            .news-search-box button { border-radius: 14px; width: 100%; }
            .featured-article-img { height: 220px; }
            .featured-article-overlay h2 { font-size: 1.2rem; }
        }
    </style>
</head>
<body>

<?php include '../includes/haip-header.php'; ?>

<!-- NEWS HERO -->
<section class="news-hero">
    <div class="container-haip">
        <div class="news-hero-content" data-aos="fade-up">
            <span class="section-label" style="color: rgba(255,255,255,0.8);"><i class="bi bi-newspaper me-1"></i> School Newsroom</span>
            <h1>School News & Updates</h1>
            <p>Stay informed with the latest announcements, achievements, events, innovations, and community stories.</p>
            <div class="news-search-box">
                <input type="text" id="newsSearch" placeholder="Search articles, announcements, events..." aria-label="Search news">
                <button onclick="filterGrid()"><i class="bi bi-search"></i> Search</button>
            </div>
            <div class="filter-pillbar" style="margin-top: 24px;">
                <button class="filter-pill active" data-filter="all">All Updates</button>
                <button class="filter-pill" data-filter="All">Announcements</button>
                <button class="filter-pill" data-filter="Academic News">Academic</button>
                <button class="filter-pill" data-filter="Student Achievements">Achievements</button>
                <button class="filter-pill" data-filter="Competitions">Competitions</button>
                <button class="filter-pill" data-filter="Sports">Sports</button>
                <button class="filter-pill" data-filter="Events">Events</button>
            </div>
        </div>
    </div>
</section>

<section class="section-haip" style="padding-top: 40px;">
    <div class="container-haip">
        <div class="row g-4">

            <!-- MAIN CONTENT -->
            <div class="col-lg-8">

                <?php if($featured): ?>
                <a href="news-details.php?nid=<?php echo $featured['pid']; ?>" class="featured-article" data-aos="fade-up">
                    <div class="featured-article-img">
                        <?php
                            $featMediaType = $featured['MediaType'] ?? 'image';
                            $featExt = strtolower(pathinfo($featured['PostImage'], PATHINFO_EXTENSION));
                            $isFeatVideo = $featMediaType === 'video' || in_array($featExt, ['mp4','webm','avi','mov','wmv','flv','mkv','3gp']);
                        ?>
                        <?php if($isFeatVideo): ?>
                            <video src="../admin/Blog Gallery/<?php echo htmlspecialchars($featured['PostImage']); ?>" preload="metadata" muted style="width:100%;height:100%;object-fit:cover;"></video>
                        <?php else: ?>
                            <img src="../admin/Blog Gallery/<?php echo htmlspecialchars($featured['PostImage']); ?>" alt="<?php echo htmlspecialchars($featured['posttitle']); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="featured-article-overlay">
                        <span class="badge"><?php echo htmlspecialchars($featured['category']); ?></span>
                        <h2><?php echo htmlspecialchars($featured['posttitle']); ?></h2>
                        <div class="meta">
                            <span><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($featured['PostingDate'])); ?></span>
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:3px 12px;background:rgba(255,255,255,0.18);border-radius:999px;font-weight:700;"><i class="bi bi-clock"></i> <?php echo ceil(str_word_count(strip_tags($featured['PostDetails']))/200); ?> min read</span>
                        </div>
                    </div>
                </a>
                <?php endif; ?>

                <div class="row g-4" id="blogGrid">
                    <?php while($row = mysqli_fetch_assoc($query)):
                        $readingTime = max(1, ceil(str_word_count(strip_tags($row['PostDetails']))/200));
                        $excerpt = strip_tags($row['PostDetails']);
                        $excerpt = mb_strlen($excerpt) > 150 ? mb_substr($excerpt, 0, 150).'...' : $excerpt;
                    ?>
                    <div class="col-md-6 blog-item" data-category="<?php echo htmlspecialchars($row['category']); ?>" data-aos="fade-up" data-aos-delay="50" style="transition: opacity 0.3s ease;">
                        <div class="blog-card">
                            <div class="blog-card-img">
                                <span class="blog-card-badge"><?php echo htmlspecialchars($row['category']); ?></span>
                                <?php $cardIsVideo = !empty($row['MediaType']) && $row['MediaType'] === 'video'; $cardExt = strtolower(pathinfo($row['PostImage'], PATHINFO_EXTENSION)); if(!$cardIsVideo && in_array($cardExt, ['mp4','webm','avi','mov','wmv','flv','mkv','3gp'])) $cardIsVideo = true; ?>
                                <?php if($cardIsVideo): ?>
                                    <video src="../admin/Blog Gallery/<?php echo htmlspecialchars($row['PostImage']); ?>" preload="metadata" muted style="width:100%;height:100%;object-fit:cover;"></video>
                                <?php else: ?>
                                    <img loading="lazy" src="../admin/Blog Gallery/<?php echo htmlspecialchars($row['PostImage']); ?>" alt="<?php echo htmlspecialchars($row['posttitle']); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="blog-card-body">
                                <div class="meta">
                                    <span><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($row['PostingDate'])); ?></span>
                                    <span class="duration-badge" style="display:inline-flex;align-items:center;gap:4px;padding:2px 10px;background:#f1f5f9;border-radius:999px;color:#475569;font-weight:600;font-size:0.75rem;"><i class="bi bi-clock"></i> <?php echo $readingTime; ?> min</span>
                                </div>
                                <h4><a href="news-details.php?nid=<?php echo $row['pid']; ?>"><?php echo htmlspecialchars($row['posttitle']); ?></a></h4>
                                <p><?php echo htmlspecialchars($excerpt); ?></p>
                            </div>
                            <div class="blog-card-footer">
                                <span class="read-time"><i class="bi bi-book"></i> <?php echo $readingTime; ?> min read</span>
                                <a href="news-details.php?nid=<?php echo $row['pid']; ?>">Read More <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <div class="pagination-modern" data-aos="fade-up">
                    <a href="?pageno=<?php echo max(1, $pageno-1); ?>" class="<?php echo $pageno <= 1 ? 'disabled' : ''; ?>"><i class="bi bi-chevron-left"></i></a>
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?pageno=<?php echo $i; ?>" class="<?php echo $pageno == $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <a href="?pageno=<?php echo min($total_pages, $pageno+1); ?>" class="<?php echo $pageno >= $total_pages ? 'disabled' : ''; ?>"><i class="bi bi-chevron-right"></i></a>
                </div>
                <?php endif; ?>
            </div>

            <!-- SIDEBAR -->
            <div class="col-lg-4">
                <div class="sidebar-widget" data-aos="fade-up">
                    <h4><i class="bi bi-search" style="color: var(--blog-accent);"></i> Search News</h4>
                     <form action="search.php" method="post" style="display:flex;gap:8px;">
                         <input type="text" name="searchtitle" class="form-control-haip" placeholder="Search for news..." required style="flex:1;padding:12px 16px;border-radius:10px;">
                         <button type="submit" class="btn-haip" style="border-radius:10px;padding:12px 20px;font-weight:700;background:linear-gradient(135deg, var(--blog-accent), #0ea5e9);color:#fff;border:none;">Search</button>
                     </form>
                </div>

                <div class="sidebar-widget" data-aos="fade-up" data-aos-delay="100">
                    <h4><i class="bi bi-fire" style="color: var(--blog-amber);"></i> Trending</h4>
                    <?php $num = 1; while($trend = mysqli_fetch_assoc($trending_query)): ?>
                    <a href="news-details.php?nid=<?php echo $trend['id']; ?>" class="trending-item">
                        <span class="num"><?php echo str_pad($num++, 2, '0', STR_PAD_LEFT); ?></span>
                        <div class="content">
                            <h5><?php echo htmlspecialchars($trend['PostTitle']); ?></h5>
                            <small><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($trend['PostingDate'])); ?></small>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>

                <div class="sidebar-widget" data-aos="fade-up" data-aos-delay="150">
                    <h4><i class="bi bi-clock-history" style="color: var(--blog-accent);"></i> Recent Posts</h4>
                    <?php while($recent = mysqli_fetch_assoc($recent_query)): ?>
                    <a href="news-details.php?nid=<?php echo $recent['id']; ?>" class="recents-item">
                        <img class="thumb" src="../admin/Blog Gallery/<?php echo htmlspecialchars($recent['PostImage']); ?>" alt="<?php echo htmlspecialchars($recent['PostTitle']); ?>" style="width:80px;height:80px;border-radius:10px;object-fit:cover;flex-shrink:0;">
                        <div class="content">
                            <h5 style="font-size:0.95rem;font-weight:700;"><?php echo htmlspecialchars($recent['PostTitle']); ?></h5>
                            <small style="font-size:0.8rem;"><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($recent['PostingDate'])); ?></small>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/haip-footer.php'; ?>

<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/aos/aos.js"></script>
<script>
AOS.init({ duration: 650, once: true, easing: 'ease-out' });

(function() {
    var tabs = document.querySelectorAll('.filter-pill');
    var items = document.querySelectorAll('.blog-item');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            var filter = this.getAttribute('data-filter');
            items.forEach(function(item) {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = '';
                    item.style.opacity = '0';
                    setTimeout(function() { item.style.opacity = '1'; }, 10);
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
})();

function filterGrid() {
    var q = (document.getElementById('newsSearch')?.value || '').toLowerCase().trim();
    document.querySelectorAll('.blog-item').forEach(function(item) {
        var txt = (item.textContent || '').toLowerCase();
        item.style.display = (!q || txt.indexOf(q) !== -1) ? '' : 'none';
    });
}


</script>
</body>
</html>