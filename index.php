<?php
session_start();
include('includes/connection.php');

// Load site settings
$siteSettings = [];
$ssq = mysqli_query($conn, "SELECT setting_key, setting_value FROM tbl_site_settings");
if ($ssq) {
    while ($ssr = mysqli_fetch_assoc($ssq)) {
        $siteSettings[$ssr['setting_key']] = $ssr['setting_value'];
    }
}
$siteName = $siteSettings['site_name'] ?? 'GIHEKE TSS';
$siteTagline = $siteSettings['site_tagline'] ?? 'Technical Secondary School';

function publicImagePath($storedPath, $fallback = 'img/giheke logo.webp') {
    if (!empty($storedPath) && file_exists(__DIR__ . '/' . $storedPath)) {
        return $storedPath;
    }
    return $fallback;
}

// Load structured homepage content
$trades = mysqli_query($conn, "SELECT * FROM `tbl_trades` WHERE is_active=1 ORDER BY sort_order ASC");
$staffMembers = mysqli_query($conn, "SELECT * FROM `tbl_staff_members` WHERE is_active=1 ORDER BY sort_order ASC");
$facilities = mysqli_query($conn, "SELECT * FROM `tbl_facilities` WHERE is_active=1 ORDER BY sort_order ASC");
$features = mysqli_query($conn, "SELECT * FROM `tbl_features` WHERE is_active=1 ORDER BY sort_order ASC");
$coreValues = mysqli_query($conn, "SELECT * FROM `tbl_core_values` WHERE is_active=1 ORDER BY sort_order ASC");

$pageTitle = $siteName . ' - ' . $siteTagline;
$pageDescription = $siteSettings['seo_description'] ?? $siteName . ' in Rusizi District, Rwanda empowers students with practical technical skills in Software Development, Network and internet technology, Electrical Technology, Building Construction, Professional Accounting, and more.';
$pageKeywords = $siteName . ', Technical Secondary School, Rusizi, Rwanda, TVET, Software Development, Network and internet technology, Electrical Technology, Building Construction, Professional Accounting, Electronics, Computer Systems';
include('assets/seo-meta.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo htmlspecialchars($siteName); ?></title>
    <link href="img/giheke logo.webp" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/haip-theme.css">
    <link rel="stylesheet" href="admin/assets/css/giheke-toast.css">
</head>
<body>
<?php
$announce_query = "SELECT Announcement FROM `tbl_announcement`";
$announce_run = mysqli_query($conn, $announce_query);
$announce = ($announce_run && mysqli_num_rows($announce_run) > 0) ? mysqli_fetch_assoc($announce_run) : [];

$news_query = "SELECT id, PostTitle, PostDetails, PostImage, PostUrl, postedBy, PostingDate FROM tblposts WHERE Is_Active = 1 ORDER BY id DESC LIMIT 6";
$news_run = mysqli_query($conn, $news_query);
?>

<!-- ANNOUNCEMENT BAR -->
<div class="announcement-bar">
    <div class="announcement-inner">
        <span class="announcement-label">Announcement</span>
        <div class="announcement-text">
            <span class="announcement-scroll"><?php echo htmlspecialchars($announce['Announcement'] ?? ''); ?></span>
        </div>
    </div>
</div>

<?php include 'includes/haip-header.php'; ?>

<main id="main">
    <!-- HERO SECTION -->
    <section class="hero-haip parallax-section" id="hero" data-parallax-speed="0.15"<?php if (!empty($siteSettings['hero_bg_image'])): ?> style="background-image:linear-gradient(rgba(15,10,40,0.85),rgba(15,10,40,0.75)),url('<?php echo htmlspecialchars($siteSettings['hero_bg_image']); ?>');background-size:cover;background-position:center;"<?php endif; ?>>
        <div class="container-haip">
            <div class="hero-grid">
                <div class="hero-content">
                    <div class="hero-badge" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.15);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.3);border-radius:20px;padding:6px 18px;margin-bottom:20px;color:#fff;font-size:0.85rem;font-weight:600;">
                        <span class="pulse" style="width:8px;height:8px;background:#28a745;border-radius:50%;display:inline-block;animation:pulse 2s infinite;"></span> <?php echo htmlspecialchars($siteSettings['hero_badge_prefix']??'Welcome to'); ?> <?php echo htmlspecialchars($siteName); ?> <?php echo htmlspecialchars($siteSettings['hero_badge_suffix']??'School'); ?>
                    </div>
                    <h1><?php echo $siteSettings['hero_heading']??'Building Futures Through <span class="highlight">Technical Education</span> Excellence'; ?></h1>
                    <p class="hero-subtitle"><?php echo htmlspecialchars($siteSettings['hero_subtitle']??'GIHEKE Technical Secondary School empowers students with practical skills, discipline, and innovation for a brighter tomorrow.'); ?></p>
                    <div class="hero-buttons">
                        <a href="SchoolApplication.php" class="btn-haip btn-haip-primary"><?php echo htmlspecialchars($siteSettings['hero_btn_primary']??'Apply Now'); ?> <i class="bi bi-arrow-right"></i></a>
                        <a href="#about" class="btn-haip btn-haip-outline"><?php echo htmlspecialchars($siteSettings['hero_btn_secondary']??'Learn More'); ?> <i class="bi bi-chevron-down"></i></a>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <h3><span data-purecounter-start="0" data-purecounter-end="700" data-purecounter-duration="1.5" class="purecounter">0</span><span class="counter-suffix">+</span></h3>
                            <p><?php echo htmlspecialchars($siteSettings['hero_stat_1_label']??'Students Enrolled'); ?></p>
                        </div>
                        <div class="hero-stat">
                            <h3><span data-purecounter-start="0" data-purecounter-end="35" data-purecounter-duration="1.5" class="purecounter">0</span></h3>
                            <p><?php echo htmlspecialchars($siteSettings['hero_stat_2_label']??'Expert Teachers'); ?></p>
                        </div>
                        <div class="hero-stat">
                            <h3><span data-purecounter-start="0" data-purecounter-end="7" data-purecounter-duration="1.5" class="purecounter">0</span></h3>
                            <p><?php echo htmlspecialchars($siteSettings['hero_stat_3_label']??'Trade Programs'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-visual-inner">
                        <div class="hero-float-badge" style="top:5%;right:-8%;">
                            <i class="bi bi-star-fill"></i> <?php echo htmlspecialchars($siteSettings['hero_badge_1']??'15+ Years Excellence'); ?>
                        </div>
                        <div class="hero-float-badge" style="bottom:10%;left:-10%;">
                            <i class="bi bi-people-fill"></i> <?php echo htmlspecialchars($siteSettings['hero_badge_2']??'700+ Students'); ?>
                        </div>
                        <div class="hero-visual-card">
                            <img src="<?php echo htmlspecialchars($siteSettings['site_logo'] ?? 'img/giheke logo.webp'); ?>" alt="GIHEKE School">
                            <h4><?php echo htmlspecialchars($siteName); ?> Technical Secondary School</h4>
                            <p><?php echo htmlspecialchars($siteTagline); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION MODERN -->
    <section id="about" class="about-new">

        <!-- 1. Section Header -->
        <div class="about-header-section">
            <div class="container-haip">
                <div class="about-header-content">
                    <h2 class="about-title"><?php echo htmlspecialchars($siteSettings['about_label']??'About GIHEKE TSS'); ?></h2>
                    <div class="about-title-underline"></div>
                    <p class="about-subtitle"><?php echo htmlspecialchars($siteSettings['about_subtitle']??'Empowering the next generation of technical leaders through hands-on education, discipline, and innovation in Rusizi District, Rwanda.'); ?></p>
                </div>
            </div>
        </div>

        <!-- 2. Vision, Mission, Motto, Objective, Slogan -->
        <div class="about-mission-section">
            <div class="container-haip">
                <div class="about-mvmos-grid">
                    <div class="about-mvmos-card">
                        <div class="about-mvmos-icon about-mvmos-icon-primary"><i class="bi bi-eye"></i></div>
                        <h4><?php echo htmlspecialchars($siteSettings['vision_title']??'Our Vision'); ?></h4>
                        <p><?php echo htmlspecialchars($siteSettings['vision_text']??'To provide high quality in technical skills by enhancing competent trainees.'); ?></p>
                    </div>
                    <div class="about-mvmos-card">
                        <div class="about-mvmos-icon about-mvmos-icon-gold"><i class="bi bi-flag"></i></div>
                        <h4><?php echo htmlspecialchars($siteSettings['mission_title']??'Our Mission'); ?></h4>
                        <p><?php echo htmlspecialchars($siteSettings['mission_text']??'To provide accessible, high-quality technical and vocational education that equips students with practical skills, critical thinking, and ethical values needed to excel in the modern workforce and contribute meaningfully to society.'); ?></p>
                    </div>
                    <div class="about-mvmos-card">
                        <div class="about-mvmos-icon about-mvmos-icon-teal"><i class="bi bi-chat-quote"></i></div>
                        <h4><?php echo htmlspecialchars($siteSettings['motto_title']??'Our Motto'); ?></h4>
                        <p><?php echo htmlspecialchars($siteSettings['motto_text']??'We\'re our country\'s solutions providers.'); ?></p>
                    </div>
                    <div class="about-mvmos-card">
                        <div class="about-mvmos-icon about-mvmos-icon-pink"><i class="bi bi-bullseye"></i></div>
                        <h4><?php echo htmlspecialchars($siteSettings['objective_title']??'Our Objective'); ?></h4>
                        <p><?php echo htmlspecialchars($siteSettings['objective_text']??'Empowering practical skills for better future.'); ?></p>
                    </div>
                    <div class="about-mvmos-card">
                        <div class="about-mvmos-icon about-mvmos-icon-cyan"><i class="bi bi-megaphone"></i></div>
                        <h4><?php echo htmlspecialchars($siteSettings['slogan_title']??'Our Slogan'); ?></h4>
                        <p><?php echo htmlspecialchars($siteSettings['slogan_text']??'From training to doing.'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Core Values -->
        <div class="about-values-section">
            <div class="container-haip">
                <div class="about-values-header">
                    <div class="about-values-header-icon"><i class="bi bi-heart-fill"></i></div>
                    <h4><?php echo htmlspecialchars($siteSettings['values_heading']??'Our Core Values'); ?></h4>
                </div>
                <div class="about-values-row">
                    <?php if ($coreValues && mysqli_num_rows($coreValues) > 0): ?>
                        <?php while ($v = mysqli_fetch_assoc($coreValues)): ?>
                            <div class="about-value-item">
                                <div class="about-value-icon-wrap"><i class="<?php echo htmlspecialchars($v['icon']); ?>"></i></div>
                                <span><?php echo htmlspecialchars($v['title']); ?></span>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 4. Our Story -->
        <div class="about-story-section-new">
            <div class="container-haip">
                <div class="about-story-layout">
                    <div class="about-story-content">
                        <span class="about-section-tag"><?php echo htmlspecialchars($siteSettings['story_label']??'Our Story'); ?></span>
                        <h3 class="about-story-heading"><?php echo htmlspecialchars($siteSettings['story_heading']??'Forging Paths Through Technical Education'); ?></h3>
                        <p class="about-story-lead"><?php echo htmlspecialchars($siteSettings['story_lead']??'Born in the heart of Giheke, our school rose from a vision to create world-class technical leaders.'); ?></p>
                        <p><?php echo htmlspecialchars($siteSettings['story_para_1']??'In 2017, a small group of educators and engineers gathered in Giheke Sector with one mission: to give every young person in Rusizi District a chance to learn by doing.'); ?></p>
                        <div class="about-pull-quote">
                            <p><?php echo htmlspecialchars($siteSettings['story_quote']??'"Education should not only open minds — it should build futures."'); ?></p>
                        </div>
                        <p><?php echo htmlspecialchars($siteSettings['story_para_2']??'Today, we serve over 700 students across seven technical trades. Our campuses are equipped with modern laboratories, computer labs, and workshops that mirror the demands of today\'s workforce.'); ?></p>
                        <p><?php echo htmlspecialchars($siteSettings['story_para_3']??'From our first intake of 120 students to today\'s thriving community, one value has never changed: every student leaves with a skill, a purpose, and the confidence to shape tomorrow.'); ?></p>
                    </div>
                    <div class="about-story-visual">
                        <div class="about-story-image-card">
                            <img src="<?php echo htmlspecialchars($siteSettings['site_logo'] ?? 'img/giheke logo.webp'); ?>" alt="GIHEKE TSS">
                            <div class="about-story-badge">
                                <span class="about-story-badge-number"><?php echo htmlspecialchars($siteSettings['story_badge_num']??'8+'); ?></span>
                                <span class="about-story-badge-text"><?php echo htmlspecialchars($siteSettings['story_badge_text']??'Years of Excellence'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Stats Row -->
        <div class="about-stats-section">
            <div class="container-haip">
                <div class="about-stats-row">
                    <div class="about-stat-item about-stat-students">
                        <div class="about-stat-icon"><i class="bi bi-people-fill"></i></div>
                        <div class="about-stat-number"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="700" data-purecounter-duration="2">0</span>+</div>
                        <div class="about-stat-label"><?php echo htmlspecialchars($siteSettings['hero_stat_1_label']??'Students Enrolled'); ?></div>
                    </div>
                    <div class="about-stat-item about-stat-trainers">
                        <div class="about-stat-icon"><i class="bi bi-person-video3"></i></div>
                        <div class="about-stat-number"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="35" data-purecounter-duration="2">0</span>+</div>
                        <div class="about-stat-label">Expert Trainers</div>
                    </div>
                    <div class="about-stat-item about-stat-trades">
                        <div class="about-stat-icon"><i class="bi bi-gear-wide-connected"></i></div>
                        <div class="about-stat-number"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="7" data-purecounter-duration="2">0</span></div>
                        <div class="about-stat-label">Technical Trades</div>
                    </div>
                    <div class="about-stat-item about-stat-years">
                        <div class="about-stat-icon"><i class="bi bi-award-fill"></i></div>
                        <div class="about-stat-number"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="8" data-purecounter-duration="2">0</span>+</div>
                        <div class="about-stat-label"><?php echo htmlspecialchars($siteSettings['story_badge_text']??'Years of Excellence'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Principal's Message -->
        <div class="about-principal-section">
            <div class="container-haip">
                <div class="about-principal-layout">
                    <div class="about-principal-photo">
                        <div class="about-principal-image-frame">
                            <img src="https://i.ibb.co/5gJhbrXw/principal.jpg" alt="Principal of GIHEKE TSS">
                        </div>
                    </div>
                    <div class="about-principal-message">
                        <span class="about-section-tag"><?php echo htmlspecialchars($siteSettings['principal_label']??"Principal\'s Message"); ?></span>
                        <h3><?php echo htmlspecialchars($siteSettings['principal_heading']??'Building Skills, Shaping Futures'); ?></h3>
                        <div class="about-principal-quote">
                            <p><?php echo htmlspecialchars($siteSettings['principal_msg_1']??'"At GIHEKE Technical Secondary School, we believe that every student possesses unique talents waiting to be discovered."'); ?></p>
                            <p><?php echo htmlspecialchars($siteSettings['principal_msg_2']??'"Our dedicated team of trainers works tirelessly to ensure that each student receives the guidance, support, and resources they need to succeed."'); ?></p>
                        </div>
                        <div class="about-principal-name">
                            <strong><?php echo htmlspecialchars($siteSettings['principal_name']??'KANYANDEGE Joseph Desire'); ?></strong>
                            <span><?php echo htmlspecialchars($siteSettings['principal_title']??'Principal, GIHEKE TSS'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Our Team -->
        <div class="about-team-section">
            <div class="container-haip">
                <div class="section-header">
                    <span class="section-label"><?php echo htmlspecialchars($siteSettings['staff_label']??'Our Team'); ?></span>
                    <h2 class="section-title"><?php echo htmlspecialchars($siteSettings['staff_title']??'Meet Our Staff'); ?></h2>
                    <p class="section-subtitle"><?php echo htmlspecialchars($siteSettings['staff_subtitle']??'Dedicated professionals committed to your success'); ?></p>
                </div>
                <div class="about-staff-carousel">
                    <div class="about-staff-track" id="staffTrack">
                        <?php if ($staffMembers && mysqli_num_rows($staffMembers) > 0): ?>
                            <?php 
                            $staffPerSlide = 3;
                            $staffAll = [];
                            while ($s = mysqli_fetch_assoc($staffMembers)) { $staffAll[] = $s; }
                            $staffChunks = array_chunk($staffAll, $staffPerSlide);
                            ?>
                            <?php foreach ($staffChunks as $chunk): ?>
                            <div class="about-staff-slide">
                                <?php foreach ($chunk as $s): ?>
                                <div class="about-staff-card">
                                    <div class="about-staff-avatar"><img src="<?php echo htmlspecialchars(publicImagePath($s['image'] ?? '', 'img/giheke logo.webp')); ?>" alt="<?php echo htmlspecialchars($s['name']); ?>"><span class="about-staff-initials" style="display:none;"><?php echo htmlspecialchars($s['initials']); ?></span></div>
                                    <h4 class="about-staff-name"><?php echo htmlspecialchars($s['name']); ?></h4>
                                    <span class="about-staff-role"><?php echo htmlspecialchars($s['role']); ?></span>
                                    <?php if ($s['phone']): ?>
                                    <span class="about-staff-phone"><i class="bi bi-telephone-fill"></i> <?php echo htmlspecialchars($s['phone']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button class="about-staff-nav about-staff-prev" id="staffPrev"><i class="bi bi-chevron-left"></i></button>
                    <button class="about-staff-nav about-staff-next" id="staffNext"><i class="bi bi-chevron-right"></i></button>
                    <div class="about-staff-dots" id="staffDots"></div>
                </div>
            </div>
        </div>

        <!-- 8. Facilities Grid -->
        <div class="about-facilities-section">
            <div class="container-haip">
                <div class="about-facilities-grid">
                    <?php if ($facilities && mysqli_num_rows($facilities) > 0): ?>
                        <?php while ($f = mysqli_fetch_assoc($facilities)): ?>
                        <div class="about-facility-card">
                            <div class="about-facility-img">
                                <img src="<?php echo htmlspecialchars(publicImagePath($f['image'] ?? '', 'img/giheke logo.webp')); ?>" alt="<?php echo htmlspecialchars($f['name']); ?>" loading="lazy">
                            </div>
                            <div class="about-facility-info">
                                <h4><?php echo htmlspecialchars($f['name']); ?></h4>
                                <p><?php echo htmlspecialchars($f['description']); ?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </section>

    <!-- PROGRAMS -->
    <section class="section-haip" id="team">
        <div class="container-haip">
            <div class="section-header">
                <span class="section-label"><?php echo htmlspecialchars($siteSettings['programs_label']??'Our Programs'); ?></span>
                <h2 class="section-title"><?php echo htmlspecialchars($siteSettings['programs_title']??'Specialized Technical Trades'); ?></h2>
                <p class="section-subtitle"><?php echo htmlspecialchars($siteSettings['programs_subtitle']??'Seven hands-on career programs built in partnership with industry'); ?></p>
            </div>
            <div class="programs-grid">
                <?php if ($trades && mysqli_num_rows($trades) > 0): ?>
                    <?php while ($t = mysqli_fetch_assoc($trades)): ?>
                    <div class="program-card">
                        <div class="program-card-img">
                            <img src="<?php echo htmlspecialchars(publicImagePath($t['image'] ?? '', 'img/giheke logo.webp')); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>">
                        </div>
                        <div class="program-card-body">
                            <h4><?php echo htmlspecialchars($t['name']); ?></h4>
                            <p><?php echo htmlspecialchars($t['description']); ?></p>
                        </div>
                        <div class="program-card-footer"><span><i class="bi bi-clock"></i> <?php echo htmlspecialchars($t['duration'] ?: ($siteSettings['programs_duration'] ?? '3 Years')); ?></span><a href="#team">Details <i class="bi bi-arrow-right"></i></a></div>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="section-haip alt-bg" id="features">
        <div class="container-haip">
            <div class="section-header">
                <span class="section-label"><?php echo htmlspecialchars($siteSettings['features_label']??'Why Choose Us'); ?></span>
                <h2 class="section-title"><?php echo htmlspecialchars($siteSettings['features_title']??'What Makes GIHEKE Special'); ?></h2>
                <p class="section-subtitle"><?php echo htmlspecialchars($siteSettings['features_subtitle']??'Unique learning combining theory with hands-on practice'); ?></p>
            </div>
            <div class="features-grid">
                <?php if ($features && mysqli_num_rows($features) > 0): ?>
                    <?php while ($f = mysqli_fetch_assoc($features)): ?>
                    <div class="feature-card"><div class="feature-icon"><i class="<?php echo htmlspecialchars($f['icon']); ?>"></i></div><h4><?php echo htmlspecialchars($f['title']); ?></h4><p><?php echo htmlspecialchars($f['description']); ?></p></div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>



    <!-- NEWS -->
    <section class="section-haip" id="news">
        <div class="container-haip">
            <div class="section-header">
                <span class="section-label"><?php echo htmlspecialchars($siteSettings['news_label']??'Latest News'); ?></span>
                <h2 class="section-title"><?php echo htmlspecialchars($siteSettings['news_title']??'School Updates'); ?></h2>
                <p class="section-subtitle"><?php echo htmlspecialchars($siteSettings['news_subtitle']??'Stay informed about GIHEKE Technical Secondary School'); ?></p>
            </div>
            <div class="news-grid">
                <?php if ($news_run && mysqli_num_rows($news_run) > 0): ?>
                    <?php while ($news = mysqli_fetch_assoc($news_run)): ?>
                        <div class="news-card">
                            <div class="news-card-img">
                                <img src="admin/Blog Gallery/<?php echo htmlspecialchars($news['PostImage']); ?>" alt="<?php echo htmlspecialchars($news['PostTitle']); ?>" onerror="this.style.display='none'">
                            </div>
                            <div class="news-card-body">
                                <div class="news-card-meta">
                                    <span><i class="bi bi-calendar3"></i> <?php echo date('M d, Y', strtotime($news['PostingDate'])); ?></span>
                                    <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($news['postedBy']); ?></span>
                                </div>
                                <h4><?php echo htmlspecialchars($news['PostTitle']); ?></h4>
                                <p><?php echo substr(strip_tags($news['PostDetails']), 0, 120); ?>...</p>
                                <a href="blog/blog.php" class="read-more">Read More <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center" style="grid-column:1/-1;padding:40px;color:#666;">
                        <i class="bi bi-newspaper" style="font-size:2.5rem;display:block;margin-bottom:12px;opacity:0.4;"></i>
                        <p><?php echo htmlspecialchars($siteSettings['news_empty']??'No news articles yet. Check back soon for updates!'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-haip">
        <div class="container-haip">
            <div class="cta-inner">
                <h2><?php echo htmlspecialchars($siteSettings['cta_heading']??'Apply for Admission at GIHEKE TSS'); ?></h2>
                <p><?php echo htmlspecialchars($siteSettings['cta_text']??'Take the first step toward a successful technical career. Applications are now open.'); ?></p>
                <div class="cta-buttons">
                    <a href="SchoolApplication.php" class="btn-cta-primary"><?php echo htmlspecialchars($siteSettings['hero_btn_primary']??'Apply Now'); ?> <i class="bi bi-arrow-right ms-2"></i></a>
                    <a href="#contact" class="btn-cta-outline">Contact Us</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT -->
    <section class="contact-haip" id="contact">
        <div class="container-haip">
            <div class="section-haip">
                <div class="section-header">
                    <span class="section-label"><?php echo htmlspecialchars($siteSettings['contact_label']??'Get In Touch'); ?></span>
                    <h2 class="section-title"><?php echo htmlspecialchars($siteSettings['contact_title']??'Contact Us'); ?></h2>
                    <p class="section-subtitle"><?php echo htmlspecialchars($siteSettings['contact_subtitle']??'Reach out for inquiries, applications, or visits'); ?></p>
                </div>
                <div class="contact-grid">
                    <div class="info-card">
                        <h3><?php echo htmlspecialchars($siteSettings['contact_info_heading']??'Contact Information'); ?></h3>
                        <div class="info-item">
                            <i class="bi bi-geo-alt"></i>
                            <div>
                                <div class="info-label"><?php echo htmlspecialchars($siteSettings['contact_info_location']??'Location'); ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($siteSettings['site_address'] ?? 'Rusizi District, Giheke Sector'); ?></div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-envelope"></i>
                            <div>
                                <div class="info-label"><?php echo htmlspecialchars($siteSettings['contact_info_email']??'Email'); ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($siteSettings['site_email'] ?? 'giheketss@gmail.com'); ?></div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-phone"></i>
                            <div>
                                <div class="info-label"><?php echo htmlspecialchars($siteSettings['contact_info_phone']??'Phone'); ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($siteSettings['site_phone'] ?? '+250 788 885 418'); ?></div>
                            </div>
                        </div>
                        <div style="margin-top:28px;">
                            <iframe style="border:0;width:100%;height:200px;border-radius:var(--radius);" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d724.7010181553296!2d28.967323481515976!3d-2.47602810229144!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19c2943a5a239ca9%3A0xd079009147d6c8a6!2sGiheke%20Technical%20Secondary%20School!5e1!3m2!1sen!2mrw!4v1700135722123!5m2!1sen!2mrw" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="contact-form-card">
                        <h3><?php echo htmlspecialchars($siteSettings['contact_form_heading']??'Send us a Message'); ?></h3>
                        <form role="form" class="php-email-form form-haip" id="contactForm" data-action="admin/contact-process.php">
                            <div class="form-group">
                                <input type="text" name="name" class="form-control-haip" id="fullName" placeholder="<?php echo htmlspecialchars($siteSettings['contact_form_name_placeholder']??'Your full name'); ?>" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-control-haip" id="email_id" placeholder="<?php echo htmlspecialchars($siteSettings['contact_form_email_placeholder']??'Your email'); ?>" required>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control-haip" name="message" id="message" rows="5" placeholder="<?php echo htmlspecialchars($siteSettings['contact_form_msg_placeholder']??'Your message...'); ?>" required></textarea>
                            </div>
                            <input type="hidden" name="action" value="contact">
                            <div class="text-center">
                                <button type="submit" class="btn-haip btn-haip-submit" id="contactSubmitBtn"><?php echo htmlspecialchars($siteSettings['contact_form_btn']??'Send Message'); ?> <i class="bi bi-send"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/haip-footer.php'; ?>

<script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/js/main.js"></script>
<script src="admin/assets/js/giheke-toast.js"></script>
<script>
(function() {
    // Contact Form AJAX
    var contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = document.getElementById('contactSubmitBtn');
            var originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
            btn.disabled = true;
            var formData = new FormData(this);
            fetch(this.getAttribute('data-action'), {
                method: 'POST',
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    if (window.GihekeToast) {
                        GihekeToast.showModal({title:'Message Sent',message:'Your message has been sent successfully. We will get back to you soon.',type:'success',buttonText:'OK'});
                    }
                    contactForm.reset();
                } else {
                    if (window.GihekeToast) {
                        GihekeToast.showToast({title:'Error',message:data.message || 'Something went wrong.',type:'error'});
                    }
                }
            })
            .catch(function() {
                if (window.GihekeToast) {
                    GihekeToast.showToast({title:'Error',message:'Network error. Please try again.',type:'error'});
                }
            })
            .finally(function() {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        });
    }

    'use strict';
    new PureCounter();
    AOS.init({ duration: 800, easing: 'ease-in-out', once: true });
    const navbar = document.getElementById('mainNavbar');
    const backToTop = document.getElementById('backToTop');
    const hamburger = document.getElementById('hamburgerBtn');
    const navLinks = document.getElementById('navLinks');
    const mobileOverlay = document.getElementById('mobileOverlay');

    window.addEventListener('scroll', function() {
        if (window.scrollY > 80) {
            navbar.classList.add('scrolled');
            backToTop.classList.add('visible');
        } else {
            navbar.classList.remove('scrolled');
            backToTop.classList.remove('visible');
        }
    });

    function openMobile() {
        navLinks.classList.add('mobile-open');
        hamburger.classList.add('active');
        mobileOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeMobile() {
        navLinks.classList.remove('mobile-open');
        hamburger.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', function() {
        if (navLinks.classList.contains('mobile-open')) { closeMobile(); } else { openMobile(); }
    });
    mobileOverlay.addEventListener('click', closeMobile);
    document.querySelectorAll('#navLinks a').forEach(function(link) {
        link.addEventListener('click', function() {
            if (navLinks.classList.contains('mobile-open')) { closeMobile(); }
        });
    });
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                const offset = navbar.offsetHeight + 10;
                window.scrollTo({ top: target.getBoundingClientRect().top + window.pageYOffset - offset, behavior: 'smooth' });
            }
        });
    });
    backToTop.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();

/* Staff Carousel */
(function() {
    const track = document.getElementById('staffTrack');
    const slides = track ? track.querySelectorAll('.about-staff-slide') : [];
    const prev = document.getElementById('staffPrev');
    const next = document.getElementById('staffNext');
    const dots = document.getElementById('staffDots');
    if (!track || slides.length === 0) return;
    let current = 0;
    function goTo(index) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        current = index;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        if (dots) {
            dots.querySelectorAll('span').forEach((d, i) => d.classList.toggle('active', i === current));
        }
    }
    if (prev) prev.addEventListener('click', () => goTo(current - 1));
    if (next) next.addEventListener('click', () => goTo(current + 1));
    if (dots) {
        slides.forEach((_, i) => {
            const dot = document.createElement('span');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goTo(i));
            dots.appendChild(dot);
        });
    }
    let autoplay = setInterval(() => goTo(current + 1), 5000);
    const carousel = document.querySelector('.about-staff-carousel');
    if (carousel) {
        carousel.addEventListener('mouseenter', () => clearInterval(autoplay));
        carousel.addEventListener('mouseleave', () => { autoplay = setInterval(() => goTo(current + 1), 5000); });
    }
})();
</script>
<script>
(function() {
    'use strict';
    var form = document.querySelector('.form-haip');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(form);
        fetch('admin/contact-process.php', {
            method: 'POST',
            body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (window.GihekeToast) {
                if (data.success) {
                    window.GihekeToast.showToast({ title: 'Success', message: data.message, type: 'success' });
                    form.reset();
                } else {
                    window.GihekeToast.showToast({ title: 'Error', message: data.message, type: 'error' });
                }
            }
        })
        .catch(function() {
            if (window.GihekeToast) {
                window.GihekeToast.showToast({ title: 'Error', message: 'Something went wrong. Please try again.', type: 'error' });
            }
        });
    });
})();
</script>
</body>
</html>
