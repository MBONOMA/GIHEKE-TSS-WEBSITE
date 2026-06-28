<?php
session_start();
$pageTitle = 'E-Learning Library - Books & Past Papers';
$pageDescription = 'Access free study materials, textbooks, and past national examination papers organized by trade at GIHEKE Technical Secondary School.';
$pageKeywords = 'e-learning, books, past papers, textbooks, study materials, GIHEKE, Rwanda education, exam papers';
include('includes/connection.php');
include('assets/seo-meta.php');

$dept_list = mysqli_query($conn, "SELECT DISTINCT department FROM tbl_books WHERE 1 ORDER BY department");
$recent = mysqli_query($conn, "SELECT * FROM tbl_books ORDER BY created_at DESC LIMIT 10");
$count_check = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tbl_books");
$count_row = mysqli_fetch_assoc($count_check);
$trades_categories = mysqli_query($conn, "SELECT * FROM tbl_trades WHERE is_active=1 ORDER BY sort_order ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Learning Center | GIHEKE TSS</title>
    <link href="img/giheke logo.webp" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/haip-theme.css" rel="stylesheet">
    <style>
        :root {
            --el-primary: #4f46e5;
            --el-primary-2: #7c3aed;
            --el-accent: #0ea5e9;
            --el-amber: #f59e0b;
            --el-success: #10b981;
            --el-danger: #ef4444;
            --el-radius: 14px;
            --el-radius-lg: 20px;
        }
        .el-hero {
            position: relative;
            background: linear-gradient(160deg, #0f172a 0%, #1e1b4b 40%, #312e81 100%);
            padding: 110px 0 130px;
            overflow: hidden;
            color: #fff;
        }
        .el-hero::before {
            content: '';
            position: absolute;
            top: -40%; right: -15%;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(255,255,255,0.07), transparent 55%);
            border-radius: 50%;
            animation: heroFloat 10s ease-in-out infinite;
        }
        .el-hero::after {
            content: '';
            position: absolute;
            bottom: -30%; left: -10%;
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
        .el-hero-content { position: relative; z-index: 2; }
        .el-hero-content h1 { font-size: clamp(2rem, 4vw, 3rem); font-weight: 900; margin-bottom: 12px; letter-spacing: -0.03em; }
        .el-hero-content p { opacity: 0.85; font-size: 1.1rem; max-width: 640px; margin: 0 auto 28px; }
        .el-quick-actions { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
        .el-quick-btn {
            padding: 11px 22px; border-radius: 999px; font-weight: 700; font-size: 0.88rem;
            color: #fff; text-decoration: none; transition: all 0.25s; display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);
        }
        .el-quick-btn:hover { transform: translateY(-3px); background: rgba(255,255,255,0.22); color: #fff; }

        .search-card {
            max-width: 720px; margin: 36px auto 0;
            background: rgba(255,255,255,0.95); color: #0f172a;
            border-radius: 999px; padding: 7px; display: flex; gap: 7px;
            box-shadow: 0 22px 48px rgba(0,0,0,0.2);
            backdrop-filter: blur(14px); border: 1px solid rgba(255,255,255,0.25);
        }
        .search-card input {
            flex: 1; border: none; padding: 14px 22px; border-radius: 999px;
            font-size: 1rem; outline: none; background: transparent; color: inherit;
        }
        .search-card button {
            padding: 14px 30px; border-radius: 999px; background: linear-gradient(135deg, var(--el-primary), var(--el-primary-2));
            color: #fff; border: none; font-weight: 700; cursor: pointer; transition: all 0.25s;
            box-shadow: 0 8px 20px rgba(79,70,229,0.35);
        }
        .search-card button:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(79,70,229,0.5); color: #fff; }

        .section-head {
            display: flex; align-items: flex-end; justify-content: space-between;
            margin-bottom: 22px; flex-wrap: wrap; gap: 12px;
        }
        .section-head h3 { font-size: 1.3rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px; }
        .section-head h3 i { color: var(--el-primary); }

        .res-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .res-card {
            background: #fff; border-radius: var(--el-radius-lg); overflow: hidden;
            border: 1px solid #e2e8f0; box-shadow: 0 8px 24px rgba(0,0,0,0.05);
            transition: all 0.28s; display: flex; flex-direction: column;
        }
        .res-card:hover { transform: translateY(-8px); box-shadow: 0 22px 48px rgba(0,0,0,0.12); border-color: #c7d2fe; }
        .res-card-thumb {
            height: 170px; background: #f8fafc; position: relative; overflow: hidden;
            display: flex; align-items: center; justify-content: center; color: rgba(79,70,229,0.2); font-size: 2.8rem;
        }
        .res-card-thumb img { width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; transition: transform 0.45s; }
        .res-card:hover .res-card-thumb img { transform: scale(1.08); }
        .type-badge {
            position: absolute; top: 12px; right: 12px; padding: 5px 12px; border-radius: 999px;
            font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.4px;
            background: var(--el-amber); color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 2;
        }
        .type-badge.paper { background: var(--el-danger); }
        .res-card-body { padding: 18px; flex: 1; display: flex; flex-direction: column; }
        .res-cat { font-size: 0.7rem; font-weight: 800; color: var(--el-primary); text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; }
        .res-card-body h4 { font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; line-height: 1.35; }
        .res-meta { font-size: 0.78rem; color: #94a3b8; margin-bottom: 14px; }
        .res-actions { display: flex; gap: 8px; margin-top: auto; }
        .btn-res {
            padding: 9px 16px; border-radius: 10px; font-weight: 700; font-size: 0.78rem;
            text-decoration: none; transition: all 0.22s; display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer;
        }
        .btn-res-primary { background: #4f46e5; color: #fff; box-shadow: 0 6px 18px rgba(79,70,229,0.3); }
        .btn-res-primary:hover { background: #4338ca; color: #fff; transform: translateY(-2px); }
        .btn-res-ghost { background: #fff; color: #4f46e5; border: 2px solid #e0e7ff; }
        .btn-res-ghost:hover { background: #eef2ff; color: #4f46e5; }

        .filter-card {
            background: #fff; border-radius: var(--el-radius-lg); padding: 18px 22px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;
            display: flex; gap: 14px; flex-wrap: wrap; align-items: center; margin-bottom: 36px;
        }
        .filter-card label { font-weight: 800; color: #0f172a; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; }
        .filter-card select {
            padding: 10px 14px; border: 2px solid #e2e8f0; border-radius: 12px;
            font-size: 0.88rem; font-weight: 600; background: #f8fafc; outline: none; transition: all 0.2s; cursor: pointer;
        }
        .filter-card select:focus { border-color: var(--el-primary); background: #fff; box-shadow: 0 0 0 4px rgba(79,70,229,0.08); }

        .cat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .cat-card {
            background: #fff; border-radius: var(--el-radius-lg); padding: 26px 22px;
            border: 1px solid #e2e8f0; box-shadow: 0 8px 22px rgba(0,0,0,0.05);
            transition: all 0.28s; text-align: center; cursor: default;
        }
        .cat-card:hover { transform: translateY(-6px); box-shadow: 0 20px 44px rgba(0,0,0,0.1); border-color: #c7d2fe; }
        .cat-icon {
            width: 64px; height: 64px; border-radius: 18px; margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--el-primary), var(--el-primary-2));
            color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;
            box-shadow: 0 10px 24px rgba(79,70,229,0.3);
        }
        .cat-card:hover .cat-icon { transform: rotate(-6deg) scale(1.08); }
        .cat-card h4 { font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
        .cat-card p { font-size: 0.8rem; color: #64748b; margin: 0; }
        .cat-count { font-size: 0.72rem; color: #94a3b8; margin-top: 8px; font-weight: 700; }

        .detail-view {
            display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.7);
            backdrop-filter: blur(6px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;
        }
        .detail-view.active { display: flex; }
        .detail-card {
            background: #fff; border-radius: var(--el-radius-lg); max-width: 720px; width: 100%;
            max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 60px rgba(0,0,0,0.35);
            animation: slideUp 0.35s ease;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        .detail-header { padding: 22px 26px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: flex-start; }
        .detail-header h2 { font-size: 1.25rem; font-weight: 900; color: #0f172a; margin: 0; }
        .close-detail { background: none; border: none; font-size: 1.6rem; cursor: pointer; color: #94a3b8; transition: all 0.2s; }
        .close-detail:hover { color: var(--el-danger); transform: rotate(90deg); }
        .detail-body { padding: 26px; display: flex; gap: 26px; }
        .detail-cover { width: 180px; height: 240px; border-radius: var(--el-radius); background: linear-gradient(135deg, var(--el-primary), var(--el-primary-2)); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: rgba(255,255,255,0.5); flex-shrink: 0; box-shadow: 0 16px 38px rgba(79,70,229,0.25); }
        .detail-cover img { width: 100%; height: 100%; object-fit: cover; border-radius: var(--el-radius); }
        .detail-info { flex: 1; }
        .detail-meta { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 14px; }
        .detail-meta span { font-size: 0.82rem; color: #64748b; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; }
        .detail-info p { color: #334155; font-size: 0.92rem; line-height: 1.7; margin-bottom: 18px; }
        .detail-buttons { display: flex; gap: 10px; flex-wrap: wrap; }

        .empty-state { text-align: center; padding: 80px 20px; color: #64748b; }
        .empty-state i { font-size: 4rem; display: block; margin-bottom: 16px; opacity: 0.25; color: var(--el-primary); }
        .empty-state h3 { color: #0f172a; font-weight: 800; margin-bottom: 8px; }
        .empty-state p { color: #94a3b8; max-width: 480px; margin: 0 auto; }

        @media (max-width: 1024px) {
            .res-grid { grid-template-columns: repeat(2, 1fr); }
            .cat-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .el-hero { padding: 80px 0 90px; }
            .el-hero-content h1 { font-size: clamp(1.4rem, 5vw, 2rem); }
            .el-hero-content p { font-size: 0.9rem; margin-bottom: 20px; }
            .search-card { flex-direction: column; border-radius: 18px; background: transparent; box-shadow: none; border: 1px solid rgba(255,255,255,0.15); }
            .search-card input { border-radius: 14px; background: rgba(255,255,255,0.92); padding: 12px 18px; }
            .search-card button { border-radius: 14px; width: 100%; padding: 12px 24px; }
            .res-grid { grid-template-columns: 1fr; gap: 16px; }
            .cat-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
            .cat-card { padding: 20px 16px; }
            .cat-icon { width: 52px; height: 52px; font-size: 1.3rem; }
            .cat-card h4 { font-size: 0.85rem; }
            .cat-card p { font-size: 0.72rem; }
            .detail-body { flex-direction: column; }
            .detail-cover { width: 100%; height: 200px; }
            .filter-card { flex-direction: column; align-items: stretch; padding: 14px 16px; }
            .filter-card select { width: 100%; }
            .empty-state { padding: 40px 16px; }
            .empty-state i { font-size: 2.5rem; }
            .res-card-thumb { height: 150px; }
            .video-pip-btn { font-size: 0.75rem; padding: 6px 12px; bottom: 8px; right: 8px; }
        }
        @media (max-width: 480px) {
            .el-hero { padding: 70px 0 80px; }
            .el-hero-content h1 { font-size: 1.3rem; }
            .el-hero-content p { font-size: 0.82rem; }
            .el-quick-btn { font-size: 0.72rem; padding: 8px 14px; }
            .search-card { margin: 24px auto 0; }
            .search-card input { padding: 10px 16px; font-size: 0.85rem; }
            .cat-grid { grid-template-columns: 1fr; gap: 12px; }
            .cat-card { padding: 18px 14px; }
            .cat-icon { width: 48px; height: 48px; font-size: 1.1rem; }
            .section-head h3 { font-size: 0.95rem; }
            .res-card-thumb { height: 130px; }
            .detail-card { border-radius: 12px; margin: 10px; }
            .detail-header { padding: 16px 18px; }
            .detail-header h2 { font-size: 1rem; }
            .detail-body { padding: 16px; gap: 16px; }
            .detail-cover { height: 160px; }
            .detail-meta { gap: 10px; }
            .detail-meta span { font-size: 0.75rem; }
            .detail-info p { font-size: 0.82rem; }
            .filter-card label { font-size: 0.8rem; }
            .filter-card select { font-size: 0.8rem; padding: 8px 12px; }
            .res-card-body { padding: 14px; }
            .res-card-body h4 { font-size: 0.85rem; }
            .btn-res { padding: 7px 12px; font-size: 0.7rem; }
        }
        @media (max-width: 360px) {
            .el-hero { padding: 60px 0 70px; }
            .el-hero-content h1 { font-size: 1.1rem; }
            .el-quick-actions { gap: 8px; }
            .el-quick-btn { font-size: 0.65rem; padding: 6px 12px; }
            .res-card-thumb { height: 110px; }
        }

        .video-wrapper { position: relative; width: 100%; height: 100%; }
        .video-pip-btn {
            position: absolute; bottom: 12px; right: 12px; z-index: 10;
            padding: 8px 14px; border-radius: 8px; border: none;
            background: rgba(0,0,0,0.7); color: #fff; font-size: 0.82rem;
            cursor: pointer; display: flex; align-items: center; gap: 6px;
            backdrop-filter: blur(4px); transition: all 0.2s;
            opacity: 1; pointer-events: auto;
        }
        .video-pip-btn:hover { background: rgba(79,70,229,0.85); transform: scale(1.05); }
        .video-pip-btn i { font-size: 1rem; }
        .video-pip-btn.active { background: rgba(239,68,68,0.85); }
    </style>
</head>
<body>

<?php include('includes/haip-header.php'); ?>

<!-- HERO -->
<section class="el-hero">
    <div class="container-haip">
        <div class="el-hero-content" data-aos="fade-up">
            <span class="section-label" style="color: rgba(255,255,255,0.75);">Digital Learning Center</span>
            <h1>Digital Learning Center</h1>
            <p>Browse and download study materials, textbooks, and past national examination papers organized by trade at GIHEKE Technical Secondary School.</p>
            <div class="search-card">
                <input type="text" id="searchInput" placeholder="Search by title, subject, or level..." aria-label="Search books and past papers">
                <button onclick="filterResources()" aria-label="Search"><i class="bi bi-search"></i> Search</button>
            </div>
            <div class="el-quick-actions" style="margin-top: 22px;">
                <a href="#categories" class="el-quick-btn"><i class="bi bi-grid-1x2"></i> Browse Categories</a>
                <a href="#resources" class="el-quick-btn"><i class="bi bi-collection"></i> Learning Resources</a>
            </div>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section class="section-haip" style="padding-top: 24px;">
    <div class="container-haip">
        <div class="section-head" data-aos="fade-up">
            <h3><i class="bi bi-grid-1x2"></i> Learning Categories</h3>
        </div>
        <div class="cat-grid" data-aos="fade-up">
            <?php if ($trades_categories && mysqli_num_rows($trades_categories) > 0): ?>
            <?php 
            $icons = ['bi bi-code-slash','bi bi-router','bi bi-cpu','bi bi-building','bi bi-calculator','bi bi-lightning','bi bi-tools'];
            $i = 0;
            while($tc = mysqli_fetch_assoc($trades_categories)): 
                $icon = $icons[$i % count($icons)]; $i++;
            ?>
            <div class="cat-card">
                <div class="cat-icon"><i class="<?php echo $icon; ?>"></i></div>
                <h4><?php echo htmlspecialchars($tc['name']); ?></h4>
                <p><?php echo htmlspecialchars(mb_substr($tc['description'] ?? 'Develop practical skills in this hands-on program.', 0, 80)); ?>...</p>
                <a href="#resources" class="cat-count" onclick="browseCategory('<?php echo htmlspecialchars($tc['name'], ENT_QUOTES); ?>'); return false;">Browse courses <i class="bi bi-arrow-right"></i></a>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <div class="empty-state" style="grid-column:1/-1;"><i class="bi bi-grid-1x2"></i><h3>Categories coming soon</h3><p>Trade categories will appear once configured by the administration.</p></div>
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- MAIN LIBRARY FILTERS + GRID -->
<section class="section-haip" id="resources" style="padding-top: 36px;">
    <div class="container-haip">
        <div class="section-head" data-aos="fade-up">
            <h3><i class="bi bi-collection"></i> Learning Resources</h3>
        </div>
        <div class="filter-card" data-aos="fade-up">
            <label><i class="bi bi-funnel"></i> Filters</label>
            <select class="filter-select" id="typeFilter">
                <option value="all">All Types</option>
                <option value="Book">Books</option>
                <option value="Past Paper">Past Papers</option>
                <option value="Video Tutorial">Video Tutorials</option>
            </select>
            <select class="filter-select" id="deptFilter">
                <option value="all">All Trades</option>
                <?php while($d = mysqli_fetch_assoc($dept_list)): ?>
                <option value="<?php echo htmlspecialchars($d['department']); ?>"><?php echo htmlspecialchars($d['department']); ?></option>
                <?php endwhile; ?>
            </select>
            <select class="filter-select" id="levelFilter">
                <option value="all">All Levels</option>
                <?php
                $lvls = mysqli_query($conn, "SELECT DISTINCT level FROM tbl_books WHERE 1 ORDER BY level");
                while($l = mysqli_fetch_assoc($lvls)):
                ?>
                <option value="<?php echo htmlspecialchars($l['level']); ?>"><?php echo htmlspecialchars($l['level']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <?php if(mysqli_num_rows($recent) > 0): ?>
        <div data-aos="fade-up">
            <div class="section-head">
                <h3><i class="bi bi-clock-history"></i> Recently Added</h3>
            </div>
            <div class="res-grid" id="recentGrid">
                <?php while($r = mysqli_fetch_assoc($recent)):
                    $badgeClass = ($r['category'] == 'Past Paper') ? 'paper' : '';
                    $isVideo = ($r['file_type'] == 'video');
                    $isYoutube = $isVideo && !empty($r['video_url']);
                    $videoHref = $isYoutube ? $r['video_url'] : $r['file_path'];
                    $icon = $isVideo ? 'bi bi-play-circle' : (($r['category'] == 'Past Paper') ? 'bi bi-file-earmark-pdf' : 'bi bi-book');
                ?>
                <div class="res-card" data-type="<?php echo htmlspecialchars($r['category']); ?>" data-dept="<?php echo htmlspecialchars($r['department']); ?>" data-level="<?php echo htmlspecialchars($r['level']); ?>">
                    <div class="res-card-thumb">
                        <?php if (!empty($r['featured_image'])): ?>
                            <img src="<?php echo htmlspecialchars($r['featured_image']); ?>" alt="<?php echo htmlspecialchars($r['title']); ?>" loading="lazy">
                        <?php else: ?>
                            <i class="<?php echo $icon; ?>"></i>
                        <?php endif; ?>
                        <?php if ($isVideo): ?><span class="type-badge" style="background:#dc2626;">Video</span><?php else: ?><span class="type-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($r['category']); ?></span><?php endif; ?>
                    </div>
                    <div class="res-card-body">
                        <div class="res-cat"><?php echo htmlspecialchars($r['department']); ?></div>
                        <h4><?php echo htmlspecialchars($r['title']); ?></h4>
                        <div class="res-meta"><i class="bi bi-bar-chart"></i> <?php echo htmlspecialchars($r['level']); ?> <?php if (!empty($r['file_size'])): ?> &middot; <i class="bi bi-hdd"></i> <?php echo round($r['file_size'] / 1048576, 1); ?>MB<?php endif; ?></div>
                        <div class="res-actions">
                            <?php if ($isVideo): ?>
                            <a href="#" class="btn-res btn-res-primary" data-video-url="<?php echo htmlspecialchars($videoHref, ENT_QUOTES); ?>" data-video-youtube="<?php echo $isYoutube ? '1' : '0'; ?>" data-video-title="<?php echo htmlspecialchars($r['title'], ENT_QUOTES); ?>" onclick="openVideo(this.dataset.videoUrl, this.dataset.videoYoutube === '1', this.dataset.videoTitle); return false;"><i class="bi bi-play-circle"></i> Watch</a>
                            <?php if (!$isYoutube): ?><a href="<?php echo htmlspecialchars($r['file_path']); ?>" download class="btn-res btn-res-ghost"><i class="bi bi-download"></i></a><?php endif; ?>
                            <?php else: ?>
                            <a href="<?php echo htmlspecialchars($r['file_path']); ?>" target="_blank" class="btn-res btn-res-primary"><i class="bi bi-eye"></i> Read</a>
                            <a href="<?php echo htmlspecialchars($r['file_path']); ?>" download class="btn-res btn-res-ghost"><i class="bi bi-download"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php
        $dept_list2 = mysqli_query($conn, "SELECT DISTINCT department FROM tbl_books WHERE 1 ORDER BY department");
        while($dept = mysqli_fetch_assoc($dept_list2)):
            $dept_name = $dept['department'];
            $books = mysqli_query($conn, "SELECT * FROM tbl_books WHERE department = '$dept_name' ORDER BY created_at DESC LIMIT 10");
            if(mysqli_num_rows($books) > 0):
        ?>
        <div style="margin-top: 48px;" data-aos="fade-up">
            <div class="section-head">
                <h3><i class="bi bi-folder2-open"></i> <?php echo htmlspecialchars($dept_name); ?></h3>
            </div>
            <div class="res-grid">
                <?php while($b = mysqli_fetch_assoc($books)):
                    $badgeClass = ($b['category'] == 'Past Paper') ? 'paper' : '';
                    $isVideo = ($b['file_type'] == 'video');
                    $isYoutube = $isVideo && !empty($b['video_url']);
                    $videoHref = $isYoutube ? $b['video_url'] : $b['file_path'];
                    $icon = $isVideo ? 'bi bi-play-circle' : (($b['category'] == 'Past Paper') ? 'bi bi-file-earmark-pdf' : 'bi bi-book');
                ?>
                <div class="res-card" data-type="<?php echo htmlspecialchars($b['category']); ?>" data-dept="<?php echo htmlspecialchars($b['department']); ?>" data-level="<?php echo htmlspecialchars($b['level']); ?>">
                    <div class="res-card-thumb">
                        <?php if (!empty($b['featured_image'])): ?>
                            <img src="<?php echo htmlspecialchars($b['featured_image']); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>" loading="lazy">
                        <?php else: ?>
                            <i class="<?php echo $icon; ?>"></i>
                        <?php endif; ?>
                        <?php if ($isVideo): ?><span class="type-badge" style="background:#dc2626;">Video</span><?php else: ?><span class="type-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($b['category']); ?></span><?php endif; ?>
                    </div>
                    <div class="res-card-body">
                        <div class="res-cat"><?php echo htmlspecialchars($b['department']); ?></div>
                        <h4><?php echo htmlspecialchars($b['title']); ?></h4>
                        <div class="res-meta"><i class="bi bi-bar-chart"></i> <?php echo htmlspecialchars($b['level']); ?> <?php if (!empty($b['file_size'])): ?> &middot; <i class="bi bi-hdd"></i> <?php echo round($b['file_size'] / 1048576, 1); ?>MB<?php endif; ?></div>
                        <div class="res-actions">
                            <?php if ($isVideo): ?>
                            <a href="#" class="btn-res btn-res-primary" data-video-url="<?php echo htmlspecialchars($videoHref, ENT_QUOTES); ?>" data-video-youtube="<?php echo $isYoutube ? '1' : '0'; ?>" data-video-title="<?php echo htmlspecialchars($b['title'], ENT_QUOTES); ?>" onclick="openVideo(this.dataset.videoUrl, this.dataset.videoYoutube === '1', this.dataset.videoTitle); return false;"><i class="bi bi-play-circle"></i> Watch</a>
                            <?php if (!$isYoutube): ?><a href="<?php echo htmlspecialchars($b['file_path']); ?>" download class="btn-res btn-res-ghost"><i class="bi bi-download"></i></a><?php endif; ?>
                            <?php else: ?>
                            <a href="<?php echo htmlspecialchars($b['file_path']); ?>" target="_blank" class="btn-res btn-res-primary"><i class="bi bi-eye"></i> Read</a>
                            <a href="<?php echo htmlspecialchars($b['file_path']); ?>" download class="btn-res btn-res-ghost"><i class="bi bi-download"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; endwhile; ?>

        <?php if($count_row['cnt'] == 0): ?>
        <div class="empty-state" data-aos="fade-up">
            <i class="bi bi-journal-bookmark"></i>
            <h3>No resources yet</h3>
            <p>Books and past papers will appear here once added by the school administration.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Detail Modal -->
<div class="detail-view" id="detailView">
    <div class="detail-card">
        <div class="detail-header">
            <h2 id="detailTitle">Resource Title</h2>
            <button class="close-detail" onclick="closeDetail()" aria-label="Close">&times;</button>
        </div>
        <div class="detail-body" id="detailBookBody">
            <div class="detail-cover" id="detailCover"><i class="bi bi-book"></i></div>
            <div class="detail-info">
                <div class="detail-meta" id="detailMeta"></div>
                <p id="detailDesc">No description available.</p>
                <div class="detail-buttons" id="detailButtons"></div>
            </div>
        </div>
        <div class="detail-body" id="detailVideoBody" style="display:none;flex-direction:column;padding:0;">
            <div id="videoContainer" style="width:100%;aspect-ratio:16/9;background:#000;border-radius:var(--el-radius);overflow:hidden;"></div>
        </div>
    </div>
</div>

<?php include('includes/haip-footer.php'); ?>

<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
AOS.init({ duration: 650, once: true, easing: 'ease-out' });

function filterResources() {
    var type = document.getElementById('typeFilter').value;
    var dept = document.getElementById('deptFilter').value;
    var level = document.getElementById('levelFilter').value;
    var search = document.getElementById('searchInput').value.toLowerCase().trim();
    document.querySelectorAll('.res-card').forEach(function(card) {
        var t = type === 'all' || card.dataset.type === type;
        var d = dept === 'all' || card.dataset.dept === dept;
        var l = level === 'all' || card.dataset.level === level;
        var s = !search || card.querySelector('h4').textContent.toLowerCase().includes(search);
        card.style.display = (t && d && l && s) ? '' : 'none';
    });
}
document.getElementById('typeFilter').addEventListener('change', filterResources);
document.getElementById('deptFilter').addEventListener('change', filterResources);
document.getElementById('levelFilter').addEventListener('change', filterResources);
document.getElementById('searchInput').addEventListener('keyup', function(e) { if(e.key === 'Enter') filterResources(); });

function browseCategory(name) {
    var deptSelect = document.getElementById('deptFilter');
    var found = false;
    for (var i = 0; i < deptSelect.options.length; i++) {
        if (deptSelect.options[i].value === name) {
            deptSelect.selectedIndex = i;
            found = true;
            break;
        }
    }
    document.getElementById('typeFilter').selectedIndex = 0;
    document.getElementById('levelFilter').selectedIndex = 0;
    document.getElementById('searchInput').value = '';
    filterResources();
    var section = document.getElementById('resources');
    if (section) {
        var offset = document.getElementById('mainNavbar') ? document.getElementById('mainNavbar').offsetHeight + 10 : 70;
        window.scrollTo({ top: section.getBoundingClientRect().top + window.pageYOffset - offset, behavior: 'smooth' });
    }
}

function openDetail(title, dept, level, type, desc, filePath) {
    var bookBody = document.getElementById('detailBookBody');
    var videoBody = document.getElementById('detailVideoBody');
    var container = document.getElementById('videoContainer');
    container.innerHTML = '';
    bookBody.style.display = 'flex';
    videoBody.style.display = 'none';
    document.getElementById('detailTitle').textContent = title;
    document.getElementById('detailMeta').innerHTML = '<span><i class="bi bi-folder2"></i> ' + dept + '</span><span><i class="bi bi-bar-chart"></i> ' + level + '</span><span><i class="bi bi-tag"></i> ' + type + '</span>';
    document.getElementById('detailDesc').textContent = desc || 'No description available.';
    var fileName = filePath.split('/').pop();
    var fixedPath = 'admin/uploads/books/' + fileName;
    document.getElementById('detailButtons').innerHTML = '<a href="' + fixedPath + '" target="_blank" class="btn-res btn-res-primary"><i class="bi bi-eye"></i> Read Online</a><a href="' + fixedPath + '" download class="btn-res btn-res-ghost"><i class="bi bi-download"></i> Download</a>';
    document.getElementById('detailView').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openVideo(url, isYoutube, title) {
    var bookBody = document.getElementById('detailBookBody');
    var videoBody = document.getElementById('detailVideoBody');
    var container = document.getElementById('videoContainer');
    container.innerHTML = '';
    bookBody.style.display = 'none';
    videoBody.style.display = 'flex';
    document.getElementById('detailTitle').textContent = title || 'Video';
    if (isYoutube) {
        var videoId = '';
        var match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
        if (match) { videoId = match[1]; }
        if (videoId) {
            container.innerHTML = '<div class="video-wrapper"><iframe src="https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0" frameborder="0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen style="width:100%;height:100%;"></iframe><button class="video-pip-btn" aria-label="Picture-in-Picture"><i class="bi bi-pip"></i> PiP</button></div>';
        } else {
            container.innerHTML = '<div class="video-wrapper"><iframe src="' + url + '" frameborder="0" allow="autoplay" allowfullscreen style="width:100%;height:100%;"></iframe></div>';
        }
    } else {
        var wrapper = document.createElement('div');
        wrapper.className = 'video-wrapper';
        var video = document.createElement('video');
        video.controls = true;
        video.autoplay = true;
        video.preload = 'metadata';
        video.style.cssText = 'width:100%;height:100%;background:#000;';
        var source = document.createElement('source');
        source.src = url;
        video.appendChild(source);
        video.appendChild(document.createTextNode('Your browser does not support the video tag.'));
        wrapper.appendChild(video);
        var pipBtn = document.createElement('button');
        pipBtn.className = 'video-pip-btn';
        pipBtn.innerHTML = '<i class="bi bi-pip"></i> PiP';
        pipBtn.setAttribute('aria-label', 'Picture-in-Picture');
        pipBtn.onclick = function(e) { e.stopPropagation(); togglePiP(video, pipBtn); };
        wrapper.appendChild(pipBtn);
        container.appendChild(wrapper);
        video.addEventListener('enterpictureinpicture', function() { pipBtn.classList.add('active'); pipBtn.innerHTML = '<i class="bi bi-pip"></i> Exit PiP'; });
        video.addEventListener('leavepictureinpicture', function() { pipBtn.classList.remove('active'); pipBtn.innerHTML = '<i class="bi bi-pip"></i> PiP'; });
    }
    document.getElementById('detailView').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function togglePiP(video, btn) {
    if (video.webkitSetPresentationMode) {
        var isPip = video.webkitPresentationMode === 'picture-in-picture';
        video.webkitSetPresentationMode(isPip ? 'inline' : 'picture-in-picture');
        return;
    }
    if (document.pictureInPictureElement === video) {
        document.exitPictureInPicture().catch(function(){});
        return;
    }
    function doRequest() {
        video.requestPictureInPicture().catch(function(err) {
            if (err.name === 'NotAllowedError' || err.name === 'InvalidStateError') {
                video.play()['catch'](function(){});
                setTimeout(function() {
                    video.requestPictureInPicture()['catch'](function(e2) {
                        console.warn('PiP failed:', e2);
                    });
                }, 300);
            }
        });
    }
    doRequest();
}

function closeDetail() {
    if (document.pictureInPictureElement) {
        document.exitPictureInPicture().catch(function(){});
    }
    document.getElementById('detailView').classList.remove('active');
    document.getElementById('videoContainer').innerHTML = '';
    document.body.style.overflow = '';
}
document.getElementById('detailView').addEventListener('click', function(e) { if(e.target === this) closeDetail(); });
document.addEventListener('keydown', function(e) { if(e.key === 'Escape') closeDetail(); });
</script>
</body>
</html>