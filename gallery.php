<?php
$pageTitle = 'School Gallery - GIHEKE TSS';
$pageDescription = 'Explore photos of GIHEKE Technical Secondary School campus, workshops, classrooms, and student activities in Rusizi District, Rwanda.';
$pageKeywords = 'GIHEKE gallery, school photos, technical school, Rwanda education, campus';
include('includes/connection.php');
$pageUrl = 'https://localhost/Giheke/gallery.php';
include('assets/seo-meta.php');

$announce_query = "SELECT Announcement FROM `tbl_announcement` LIMIT 1";
$announce_run = mysqli_query($conn, $announce_query);
$announce = mysqli_fetch_assoc($announce_run);

// Count stats for gallery
$total_photos_q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tbl_gallery_post");
$total_photos = $total_photos_q ? mysqli_fetch_assoc($total_photos_q)['c'] : 0;

$events_q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tbl_gallery_post gp JOIN tbl_school_category sc ON gp.CategoryNameId = sc.id WHERE LOWER(sc.CategoryName) LIKE '%event%'");
$total_events = $events_q ? mysqli_fetch_assoc($events_q)['c'] : 0;

$activities_q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tbl_gallery_post gp JOIN tbl_school_category sc ON gp.CategoryNameId = sc.id WHERE LOWER(sc.CategoryName) LIKE '%activit%'");
$total_activities = $activities_q ? mysqli_fetch_assoc($activities_q)['c'] : 0;

$achievements_q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tbl_gallery_post gp JOIN tbl_school_category sc ON gp.CategoryNameId = sc.id WHERE LOWER(sc.CategoryName) LIKE '%achievement%'");
$total_achievements = $achievements_q ? mysqli_fetch_assoc($achievements_q)['c'] : 0;

$album_id = isset($_GET['album']) ? (int)$_GET['album'] : 0;
$cats = mysqli_query($conn, "SELECT * FROM tbl_school_category ORDER BY id DESC");
$albums_q = mysqli_query($conn, "SELECT tbl_school_category.id as catid, tbl_school_category.CategoryName, tbl_school_category.CategoryDescription,
(SELECT ImageUrl FROM tbl_gallery_post WHERE tbl_gallery_post.CategoryNameId = tbl_school_category.id ORDER BY tbl_gallery_post.id DESC LIMIT 1) as cover,
(SELECT COUNT(*) FROM tbl_gallery_post WHERE tbl_gallery_post.CategoryNameId = tbl_school_category.id) as photo_count
FROM tbl_school_category
HAVING cover IS NOT NULL
ORDER BY tbl_school_category.id DESC");

$total_videos_q = mysqli_query($conn, "SELECT COUNT(*) as c FROM tbl_gallery_post WHERE MediaType='video'");
$total_videos = $total_videos_q ? mysqli_fetch_assoc($total_videos_q)['c'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moments That Define Excellence | GIHEKE TSS Gallery</title>
    <link href="img/giheke logo.webp" rel="icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/haip-theme.css" rel="stylesheet">
    <style>
        :root {
            --g-primary: #4f46e5;
            --g-primary-2: #7c3aed;
            --g-amber: #f59e0b;
            --g-radius: 14px;
            --g-radius-lg: 20px;
        }
        .gallery-hero {
            position: relative;
            padding: 110px 0 130px;
            background: linear-gradient(160deg, #0f172a 0%, #1e1b4b 40%, #312e81 100%);
            overflow: hidden; color: #fff; text-align: center;
        }
        .gallery-hero::before {
            content: '';
            position: absolute; top: -40%; right: -15%;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(255,255,255,0.07), transparent 55%);
            border-radius: 50%;
            animation: heroFloat 10s ease-in-out infinite;
        }
        .gallery-hero::after {
            content: '';
            position: absolute; bottom: -30%; left: -10%;
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
        .gallery-hero-content { position: relative; z-index: 2; }
        .gallery-hero-content h1 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 900; margin-bottom: 12px; letter-spacing: -0.03em; }
        .gallery-hero-content p { opacity: 0.85; font-size: 1.1rem; max-width: 640px; margin: 0 auto 24px; }
        .gallery-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
        .gallery-hero-actions a {
            padding: 11px 22px; border-radius: 999px; font-weight: 700; font-size: 0.88rem;
            color: #fff; text-decoration: none; transition: all 0.25s; display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);
        }
        .gallery-hero-actions a:hover { transform: translateY(-3px); background: rgba(255,255,255,0.22); color: #fff; }
        .gallery-search { max-width: 600px; margin: 28px auto 0; position: relative; }
        .gallery-search input {
            width: 100%; padding: 15px 22px; border-radius: 999px; border: 2px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.95); color: #0f172a; font-size: 0.95rem; outline: none;
            transition: all 0.25s;
        }
        .gallery-search input:focus { border-color: #fff; box-shadow: 0 0 0 4px rgba(255,255,255,0.15); background: #fff; }

        .stats-bar {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px;
            max-width: 1000px; margin: -52px auto 48px; padding: 0 20px; position: relative; z-index: 5;
        }
        .stat-card {
            background: rgba(255,255,255,0.96); border-radius: var(--g-radius-lg); padding: 22px 18px; text-align: center;
            border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 10px 28px rgba(0,0,0,0.08); transition: all 0.28s;
        }
        .stat-card:hover { transform: translateY(-6px); box-shadow: 0 20px 44px rgba(0,0,0,0.12); }
        .stat-icon { width: 44px; height: 44px; border-radius: 14px; margin: 0 auto 10px; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .stat-val { font-size: 1.4rem; font-weight: 900; color: #0f172a; }
        .stat-label { font-size: 0.72rem; color: #64748b; margin-top: 4px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }

        .section-head { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 22px; flex-wrap: wrap; gap: 12px; }
        .section-head h3 { font-size: 1.25rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px; }
        .section-head h3 i { color: var(--g-primary); }

        .cat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .cat-card {
            background: #fff; border-radius: var(--g-radius-lg); overflow: hidden;
            border: 1px solid #e2e8f0; box-shadow: 0 8px 22px rgba(0,0,0,0.05); transition: all 0.28s; cursor: pointer;
        }
        .cat-card:hover { transform: translateY(-6px); box-shadow: 0 20px 44px rgba(0,0,0,0.1); border-color: #c7d2fe; }
        .cat-card-img { height: 180px; overflow: hidden; position: relative; background: #f1f5f9; }
        .cat-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.45s; }
        .cat-card:hover .cat-card-img img { transform: scale(1.1); }
        .cat-card-body { padding: 16px 18px; }
        .cat-card-body h5 { font-weight: 800; color: #0f172a; margin-bottom: 4px; font-size: 0.95rem; }
        .cat-card-body small { color: #64748b; font-size: 0.78rem; }
        .cat-card-body .photo-count { color: var(--g-primary); font-weight: 700; font-size: 0.78rem; }

        .filter-pillbar { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; margin-bottom: 32px; }
        .filter-pill {
            padding: 10px 24px; border-radius: 999px; font-weight: 700; font-size: 0.85rem;
            cursor: pointer; transition: all 0.25s; border: 2px solid #e2e8f0; background: #fff; color: #475569;
        }
        .filter-pill:hover, .filter-pill.active { background: var(--g-primary); color: #fff; border-color: var(--g-primary); box-shadow: 0 6px 18px rgba(79,70,229,0.3); }

        .photo-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .photo-card {
            border-radius: var(--g-radius); overflow: hidden; position: relative; aspect-ratio: 1;
            background: #f1f5f9; cursor: pointer; box-shadow: 0 6px 18px rgba(0,0,0,0.05); transition: all 0.28s;
        }
        .photo-card:hover { transform: translateY(-4px); box-shadow: 0 18px 38px rgba(0,0,0,0.1); }
        .photo-card img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.45s; }
        .photo-card:hover img { transform: scale(1.08); }
        .photo-card .photo-badge {
            position: absolute; top: 10px; left: 10px; padding: 5px 12px; border-radius: 999px;
            background: rgba(15,23,42,0.75); color: #fff; font-size: 0.7rem; font-weight: 700;
            backdrop-filter: blur(6px); opacity: 0; transition: opacity 0.3s;
        }
        .photo-card:hover .photo-badge { opacity: 1; }

        .back-bar { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
        .back-bar a { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 999px; font-weight: 700; font-size: 0.88rem; color: var(--g-primary); background: #fff; border: 2px solid #e2e8f0; text-decoration: none; transition: all 0.2s; }
        .back-bar a:hover { border-color: var(--g-primary); background: #eef2ff; }

        .empty-state { text-align: center; padding: 80px 20px; }
        .empty-state i { font-size: 3.5rem; display: block; margin-bottom: 16px; opacity: 0.25; color: var(--g-primary); }
        .empty-state h3 { color: #0f172a; font-weight: 800; margin-bottom: 8px; }
        .empty-state p { color: #64748b; }

        @media (max-width: 1024px) {
            .cat-grid { grid-template-columns: repeat(2, 1fr); }
            .photo-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 768px) {
            .gallery-hero { padding: 90px 0 110px; }
            .stats-bar { grid-template-columns: repeat(2, 1fr); margin-top: -40px; }
            .photo-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 576px) {
            .cat-grid { grid-template-columns: 1fr; }
            .photo-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .gallery-hero { padding: 70px 0 90px; }
            .gallery-hero-content h1 { font-size: 1.4rem; }
            .gallery-hero-content p { font-size: 0.85rem; }
            .stats-bar { grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: -30px; padding: 0 12px; }
            .stat-card { padding: 14px 10px; }
            .stat-icon { width: 34px; height: 34px; font-size: 0.95rem; }
            .stat-val { font-size: 1.1rem; }
            .stat-label { font-size: 0.6rem; }
            .photo-grid { grid-template-columns: 1fr; gap: 10px; }
            .cat-card-img { height: 150px; }
            .section-head h3 { font-size: 1rem; }
            .filter-pill { padding: 7px 14px; font-size: 0.75rem; }
        }
    </style>
</head>
<body>

<?php include 'includes/haip-header.php'; ?>

<!-- HERO -->
<section class="gallery-hero">
    <div class="container-haip">
        <div class="gallery-hero-content" data-aos="fade-up">
            <span class="section-label" style="color: rgba(255,255,255,0.75);">Our Memories</span>
            <h1>Moments That Define Excellence</h1>
            <p>Explore the achievements, events, learning experiences, and memorable moments that shape our school community.</p>
            <div class="gallery-hero-actions">
                <a href="#albums"><i class="bi bi-images"></i> Explore Gallery</a>
                <a href="#categories"><i class="bi bi-calendar-event"></i> View Events</a>
                <a href="#categories"><i class="bi bi-trophy"></i> Student Achievements</a>
                <a href="#categories"><i class="bi bi-building"></i> Campus Life</a>
            </div>
            <div class="gallery-search">
                <input type="text" id="gallerySearch" placeholder="Search photos, events, activities..." aria-label="Search gallery">
            </div>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="stats-bar" data-aos="fade-up">
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-images"></i></div>
        <div class="stat-val" data-count="<?php echo $total_photos; ?>"><?php echo $total_photos; ?></div>
        <div class="stat-label">Photos</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);"><i class="bi bi-calendar-event"></i></div>
        <div class="stat-val" data-count="<?php echo $total_events; ?>"><?php echo $total_events; ?></div>
        <div class="stat-label">Events</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);"><i class="bi bi-people"></i></div>
        <div class="stat-val" data-count="<?php echo $total_activities; ?>"><?php echo $total_activities; ?></div>
        <div class="stat-label">Activities</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);"><i class="bi bi-trophy"></i></div>
        <div class="stat-val" data-count="<?php echo $total_achievements; ?>"><?php echo $total_achievements; ?></div>
        <div class="stat-label">Achievements</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);"><i class="bi bi-play-circle"></i></div>
        <div class="stat-val" data-count="<?php echo $total_videos; ?>"><?php echo $total_videos; ?></div>
        <div class="stat-label">Videos</div>
    </div>
</div>

<?php if($album_id > 0):
    $album_query = mysqli_query($conn, "SELECT * FROM tbl_school_category WHERE id = '$album_id'");
    $album = mysqli_fetch_assoc($album_query);
    if($album):
?>
<!-- ALBUM DETAIL VIEW -->
<section class="section-haip" style="padding-top: 36px;">
    <div class="container-haip">
        <div class="back-bar">
            <a href="gallery.php"><i class="bi bi-arrow-left"></i> Back to Gallery</a>
            <div>
                <h2 style="font-weight: 900; color: #0f172a; margin: 0; font-size: 1.4rem;"><?php echo htmlspecialchars($album['CategoryName']); ?></h2>
                <small style="color: #64748b; font-weight: 600;"><?php echo htmlspecialchars($album['CategoryDescription'] ?? 'Gallery photos'); ?></small>
            </div>
        </div>
        <div class="photo-grid" id="photoGrid">
            <?php
            $photo_query = mysqli_query($conn, "SELECT tbl_gallery_post.id, tbl_gallery_post.ImageUrl, tbl_gallery_post.MediaType FROM tbl_gallery_post WHERE CategoryNameId = '$album_id' ORDER BY tbl_gallery_post.id DESC");
            while($photo = mysqli_fetch_assoc($photo_query)):
                $isVideo = !empty($photo['MediaType']) && $photo['MediaType'] === 'video';
                $ext = strtolower(pathinfo($photo['ImageUrl'], PATHINFO_EXTENSION));
                $videoExts = ['mp4','webm','avi','mov','wmv','flv','mkv','3gp'];
                if(!$isVideo && in_array($ext, $videoExts)) $isVideo = true;
            ?>
            <a href="admin/Gallery Images/<?php echo $photo['ImageUrl']; ?>" class="photo-card glightbox" data-gallery="album-<?php echo $album_id; ?>" data-category="<?php echo htmlspecialchars($album['CategoryName']); ?>" <?php echo $isVideo ? 'data-type="video"' : ''; ?>>
                <?php if($isVideo): ?>
                    <video src="admin/Gallery Images/<?php echo $photo['ImageUrl']; ?>" preload="metadata" muted style="width:100%;height:100%;object-fit:cover;"></video>
                    <span class="photo-badge"><i class="bi bi-play-fill"></i></span>
                <?php else: ?>
                    <img loading="lazy" src="admin/Gallery Images/<?php echo $photo['ImageUrl']; ?>" alt="<?php echo htmlspecialchars($album['CategoryName']); ?>">
                    <span class="photo-badge"><i class="bi bi-arrows-fullscreen"></i></span>
                <?php endif; ?>
            </a>
            <?php endwhile; ?>
        </div>
        <?php $hasMedia = $photo_query ? mysqli_num_rows($photo_query) > 0 : false; ?>
        <?php if(!$hasMedia): ?>
        <div class="empty-state"><i class="bi bi-image"></i><h3>No media yet</h3><p>Images and videos will appear here once added to this album.</p></div>
        <?php endif; ?>
    </div>
</section>

<?php else: ?>
<section class="section-haip" style="padding-top: 36px;">
    <div class="container-haip">
        <div class="empty-state">
            <i class="bi bi-folder2-open"></i>
            <h3>Album not found</h3>
            <p><a href="gallery.php" style="color: var(--g-primary);">Go back to gallery</a></p>
        </div>
    </div>
</section>
<?php endif; ?>

<?php else: ?>

<!-- CATEGORIES STRIP -->
<section class="section-haip" id="categories" style="padding-top: 36px;">
    <div class="container-haip">
        <div class="section-head" data-aos="fade-up">
            <h3><i class="bi bi-grid-1x2"></i> Gallery Categories</h3>
        </div>
        <div class="cat-grid" data-aos="fade-up">
            <?php while($cat = mysqli_fetch_assoc($cats)):
                $cntQ = mysqli_query($conn, "SELECT COUNT(*) FROM tbl_gallery_post WHERE CategoryNameId = '" . (int)$cat['id'] . "'");
                $catCount = $cntQ ? (int)mysqli_fetch_assoc($cntQ)['COUNT(*)'] : 0;
            ?>
            <a href="?album=<?php echo $cat['id']; ?>" class="cat-card" style="text-decoration:none;color:inherit;">
                <div class="cat-card-img">
                     <?php
                     $cov = mysqli_query($conn, "SELECT ImageUrl FROM tbl_gallery_post WHERE CategoryNameId = '" . (int)$cat['id'] . "' ORDER BY id DESC LIMIT 1");
                     $cc = mysqli_fetch_assoc($cov);
                     if($cc && !empty($cc['ImageUrl'])):
                     ?>
                     <img src="admin/Gallery Images/<?php echo htmlspecialchars($cc['ImageUrl']); ?>" alt="<?php echo htmlspecialchars($cat['CategoryName']); ?>" loading="lazy">
                     <?php else: ?>
                     <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:2.5rem;color:rgba(79,70,229,0.2);"><i class="bi bi-images"></i></div>
                     <?php endif; ?>
                </div>
                <div class="cat-card-body">
                    <h5><?php echo htmlspecialchars($cat['CategoryName']); ?></h5>
                    <small><?php echo htmlspecialchars($cat['CategoryDescription'] ?? ''); ?></small>
                    <div class="photo-count"><?php echo $catCount; ?> media <i class="bi bi-arrow-right"></i></div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- ALBUMS GRID -->
<section class="section-haip alt-bg" id="albums" style="padding-top: 36px;">
    <div class="container-haip">
        <div class="section-head" data-aos="fade-up">
            <h3><i class="bi bi-folder2-open"></i> Featured Albums</h3>
        </div>
        <?php if(mysqli_num_rows($albums_q) > 0): ?>
        <div class="cat-grid" data-aos="fade-up">
        <?php while($album = mysqli_fetch_assoc($albums_q)):
            $isVideo = !empty($album['cover']) && in_array(strtolower(pathinfo($album['cover'], PATHINFO_EXTENSION)), ['mp4','webm','avi','mov','wmv','flv','mkv','3gp']);
        ?>
        <a href="?album=<?php echo $album['catid']; ?>" class="cat-card" style="text-decoration:none;color:inherit;">
            <div class="cat-card-img">
                <?php if($isVideo): ?>
                    <video src="admin/Gallery Images/<?php echo htmlspecialchars($album['cover']); ?>" preload="metadata" muted style="width:100%;height:100%;object-fit:cover;"></video>
                <?php else: ?>
                    <img src="admin/Gallery Images/<?php echo htmlspecialchars($album['cover']); ?>" alt="<?php echo htmlspecialchars($album['CategoryName']); ?>" loading="lazy">
                <?php endif; ?>
            </div>
            <div class="cat-card-body">
                <h5><?php echo htmlspecialchars($album['CategoryName']); ?></h5>
                <div class="photo-count"><?php echo (int)$album['photo_count']; ?> items <i class="bi bi-arrow-right"></i></div>
            </div>
        </a>
        <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="empty-state" data-aos="fade-up"><i class="bi bi-journal-bookmark"></i><h3>No albums yet</h3><p>Galleries will appear here once created by administration.</p></div>
        <?php endif; ?>
    </div>
</section>

<?php endif; ?>

<?php include 'includes/haip-footer.php'; ?>

<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
AOS.init({ duration: 650, once: true, easing: 'ease-out' });

(function() {
    var counters = document.querySelectorAll('.stat-val[data-count]');
    var did = false;
    function tick() {
        if (did) return;
        var el = document.querySelector('.stats-bar');
        if (!el) return;
        if (el.getBoundingClientRect().top < window.innerHeight - 80) {
            did = true;
            counters.forEach(function(node) {
                var target = parseInt(node.getAttribute('data-count'), 10) || 0;
                var start = performance.now();
                (function step(now) {
                    var p = Math.min((now - start) / 1400, 1);
                    node.textContent = Math.floor((1 - Math.pow(1 - p, 3)) * target).toLocaleString();
                    if (p < 1) requestAnimationFrame(step);
                })(start);
            });
        }
    }
    window.addEventListener('scroll', tick, { passive: true });
    tick();
})();

document.querySelectorAll('.filter-tab-g').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-tab-g').forEach(function(b) { b.classList.remove('active'); });
        this.classList.add('active');
        var filter = this.dataset.filter;
        document.querySelectorAll('.album-card').forEach(function(card) {
            card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
        });
    });
});

(function() {
    var search = document.getElementById('gallerySearch');
    if (!search) return;
    search.addEventListener('keyup', function(e) {
        var q = this.value.toLowerCase().trim();
        document.querySelectorAll('.cat-card').forEach(function(card) {
            var txt = (card.textContent || '').toLowerCase();
            card.style.display = (!q || txt.indexOf(q) !== -1) ? '' : 'none';
        });
        document.querySelectorAll('.album-card').forEach(function(card) {
            var txt = (card.textContent || '').toLowerCase();
            card.style.display = (!q || txt.indexOf(q) !== -1) ? '' : 'none';
        });
    });
})();

GLightbox({ selector: '.glightbox', touchNavigation: true, loop: true });
</script>
</body>
</html>