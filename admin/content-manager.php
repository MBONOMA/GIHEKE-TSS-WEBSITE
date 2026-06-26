<?php
session_start();
include('includes/connection.php');
include('includes/activity-log.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
    exit;
}

$id = $_SESSION['admin_id'];
$FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
$First = mysqli_fetch_array($FirstChar);
$Char = strtoupper($First['FirstName']);

function getContentCount($conn, $table, $where = '1') {
    $check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (!$check || mysqli_num_rows($check) == 0) return 0;
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM `$table` WHERE $where"));
    return $r['c'] ?? 0;
}

// Get content counts for dashboard
$contentStats = [];
$contentStats['blog'] = getContentCount($conn, 'tblposts', "Is_Active=1");
$contentStats['gallery'] = getContentCount($conn, 'tbl_gallery_post');
$contentStats['categories'] = getContentCount($conn, 'tbl_school_category');
$contentStats['applications'] = getContentCount($conn, 'tbl_apply_student', "Status='pending'");
$contentStats['books'] = getContentCount($conn, 'tbl_books');
$contentStats['quiz'] = getContentCount($conn, 'tbl_question_test');
$contentStats['modules'] = getContentCount($conn, 'tbl_quiz_modules');
$contentStats['works'] = getContentCount($conn, 'tbl_works');

$ann = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Announcement FROM `tbl_announcement` LIMIT 1"));
$contentStats['announcement'] = ($ann && !empty(trim($ann['Announcement']??''))) ? 1 : 0;

$contentStats['messages'] = getContentCount($conn, 'tbl_student_message');
$contentStats['nav'] = getContentCount($conn, 'tbl_navigation', 'is_active=1');
$contentStats['seo'] = getContentCount($conn, 'tbl_seo_meta');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Content Manager - Site Management</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <style>
    .cm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px; }
    .cm-card { background: #fff; border-radius: 18px; padding: 24px; border: 1px solid rgba(0,0,0,0.04); box-shadow: 0 4px 16px rgba(0,0,0,0.04); transition: all 0.25s; text-decoration: none; display: block; position: relative; overflow: hidden; }
    .cm-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.08); }
    .cm-card .icon-wrap { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 14px; }
    .cm-card h3 { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .cm-card p { font-size: 0.82rem; color: #888; margin: 0; }
    .cm-card .count { position: absolute; top: 18px; right: 18px; background: #f1f5f9; padding: 2px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: #525FE1; }
    .cm-card .count.warn { background: #fef3c7; color: #d97706; }
    .section-title { font-size: 1.1rem; font-weight: 700; color: #3D47C9; margin: 28px 0 16px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0; }
    .section-title:first-of-type { margin-top: 0; }
    .page-header-card { background: linear-gradient(135deg, #525FE1 0%, #3D47C9 100%); border-radius: 20px; padding: 32px; color: #fff; margin-bottom: 24px; }
    .page-header-card h1 { font-size: 1.6rem; font-weight: 800; margin: 0 0 6px; color: #fff; }
    .page-header-card p { font-size: 0.92rem; opacity: 0.85; margin: 0; }
  </style>
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center"><img src="assets/img/logo.png" alt=""><span class="d-none d-lg-block" style="font-weight:800;color:#0F172A;font-size:1.1rem;">GIHEKE <span style="color:#525FE1;">Admin</span></span></a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <?php include('includes/notifications.php'); ?>
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="admin-img/<?php echo $First['ImageUrl'] ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo substr($Char,0,1) .". ".$First['LastName']; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header"><h6><?php echo $First['FirstName']." ".$First['LastName']; ?></h6><span>School Admin</span></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="user-profile.php"><i class="bi bi-person"></i><span>My Profile</span></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="logout.php"><i class="bi bi-box-arrow-right"></i><span>Sign Out</span></a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <?php include('includes/sidebar.php'); ?>
  <main id="main" class="main">
    <div style="padding:24px;">
      <div class="page-header-card">
        <h1><i class="bi bi-grid-3x3-gap"></i> Content Manager</h1>
        <p>Manage all frontend content from one place — news, gallery, applications, quiz, books, navigation, SEO, and more.</p>
      </div>

      <h3 class="section-title"><i class="bi bi-globe"></i> Site Configuration</h3>
      <div class="cm-grid">
        <a href="site-settings.php" class="cm-card">
          <div class="icon-wrap" style="background:#eef2ff;color:#525FE1;"><i class="bi bi-gear"></i></div>
          <h3>Site Settings</h3>
          <p>Site name, contact, hero, about, CTA text</p>
          <span class="count"><i class="bi bi-pencil"></i> Edit</span>
        </a>
        <a href="manage-trades.php" class="cm-card">
          <div class="icon-wrap" style="background:#f0fdf4;color:#16a34a;"><i class="bi bi-tools"></i></div>
          <h3>Trade Programs</h3>
          <p>7 technical trade programs on homepage</p>
          <span class="count"><i class="bi bi-pencil"></i> Edit</span>
        </a>
        <a href="manage-staff.php" class="cm-card">
          <div class="icon-wrap" style="background:#fef3c7;color:#d97706;"><i class="bi bi-people"></i></div>
          <h3>Staff Members</h3>
          <p>School team displayed on homepage</p>
          <span class="count"><i class="bi bi-pencil"></i> Edit</span>
        </a>
        <a href="manage-facilities.php" class="cm-card">
          <div class="icon-wrap" style="background:#fce7f3;color:#db2777;"><i class="bi bi-building"></i></div>
          <h3>Facilities</h3>
          <p>School facilities on homepage</p>
          <span class="count"><i class="bi bi-pencil"></i> Edit</span>
        </a>
        <a href="manage-features.php" class="cm-card">
          <div class="icon-wrap" style="background:#e0f2fe;color:#0284c7;"><i class="bi bi-star"></i></div>
          <h3>Features</h3>
          <p>"Why Choose Us" feature cards</p>
          <span class="count"><i class="bi bi-pencil"></i> Edit</span>
        </a>
        <a href="manage-values.php" class="cm-card">
          <div class="icon-wrap" style="background:#f3e5f5;color:#7b1fa2;"><i class="bi bi-heart"></i></div>
          <h3>Core Values</h3>
          <p>School core values on homepage</p>
          <span class="count"><i class="bi bi-pencil"></i> Edit</span>
        </a>
        <a href="navigation-manager.php" class="cm-card">
          <div class="icon-wrap" style="background:#f0fdf4;color:#16a34a;"><i class="bi bi-list-nested"></i></div>
          <h3>Navigation</h3>
          <p>Menu links & structure</p>
          <span class="count"><?php echo $contentStats['nav']; ?> links</span>
        </a>
        <a href="seo-manager.php" class="cm-card">
          <div class="icon-wrap" style="background:#fef3c7;color:#d97706;"><i class="bi bi-search-heart"></i></div>
          <h3>SEO Manager</h3>
          <p>Meta titles, descriptions, keywords</p>
          <span class="count"><?php echo $contentStats['seo']; ?> pages</span>
        </a>
        <a href="media-library.php" class="cm-card">
          <div class="icon-wrap" style="background:#fce7f3;color:#db2777;"><i class="bi bi-images"></i></div>
          <h3>Media Library</h3>
          <p>Upload & manage images, PDFs, documents</p>
          <span class="count"><i class="bi bi-upload"></i> Upload</span>
        </a>
      </div>

      <h3 class="section-title"><i class="bi bi-newspaper"></i> Content</h3>
      <div class="cm-grid">
        <a href="manage-blogpost.php" class="cm-card">
          <div class="icon-wrap" style="background:#eef2ff;color:#525FE1;"><i class="bi bi-newspaper"></i></div>
          <h3>Blog & News</h3>
          <p>School news articles & updates</p>
          <span class="count"><?php echo $contentStats['blog']; ?> posts</span>
        </a>
        <a href="manage-gallerypost.php" class="cm-card">
          <div class="icon-wrap" style="background:#f0fdf4;color:#16a34a;"><i class="bi bi-images"></i></div>
          <h3>Gallery</h3>
          <p>School photo gallery</p>
          <span class="count"><?php echo $contentStats['gallery']; ?> images</span>
        </a>
        <a href="manage-category.php" class="cm-card">
          <div class="icon-wrap" style="background:#fef3c7;color:#d97706;"><i class="bi bi-folder"></i></div>
          <h3>Categories</h3>
          <p>Gallery & news categories</p>
          <span class="count"><?php echo $contentStats['categories']; ?> categories</span>
        </a>
        <a href="announce.php" class="cm-card">
          <div class="icon-wrap" style="background:#fce7f3;color:#db2777;"><i class="bi bi-megaphone"></i></div>
          <h3>Announcements</h3>
          <p>Site-wide announcement bar</p>
          <span class="count <?php echo $contentStats['announcement']?'':'warn'; ?>"><?php echo $contentStats['announcement']?'Active':'Inactive'; ?></span>
        </a>
      </div>

      <h3 class="section-title"><i class="bi bi-mortarboard"></i> Academics</h3>
      <div class="cm-grid">
        <a href="studentApplication.php" class="cm-card">
          <div class="icon-wrap" style="background:#eef2ff;color:#525FE1;"><i class="bi bi-folder-check"></i></div>
          <h3>Applications</h3>
          <p>Student admission applications</p>
          <span class="count warn"><?php echo $contentStats['applications']; ?> pending</span>
        </a>

          <div class="icon-wrap" style="background:#fce7f3;color:#db2777;"><i class="bi bi-book"></i></div>
          <h3>Books & Past Papers</h3>
          <p>E-learning resources</p>
          <span class="count"><?php echo $contentStats['books']; ?> items</span>
        </a>

          <div class="icon-wrap" style="background:#e0f2fe;color:#0284c7;"><i class="bi bi-journal-text"></i></div>
          <h3>Assignments</h3>
          <p>School assignments & works</p>
          <span class="count"><?php echo $contentStats['works']; ?> items</span>
        </a>
      </div>

      <h3 class="section-title"><i class="bi bi-people"></i> People</h3>
      <div class="cm-grid">
        <a href="manage-students.php" class="cm-card">
          <div class="icon-wrap" style="background:#eef2ff;color:#525FE1;"><i class="bi bi-mortarboard"></i></div>
          <h3>Students</h3>
          <p>Manage enrolled students</p>
          <span class="count"><i class="bi bi-person"></i> View</span>
        </a>
        <a href="manage-trainers.php" class="cm-card">
          <div class="icon-wrap" style="background:#f0fdf4;color:#16a34a;"><i class="bi bi-people"></i></div>
          <h3>Trainers</h3>
          <p>Manage teachers & staff</p>
          <span class="count"><i class="bi bi-person"></i> View</span>
        </a>
        <a href="studentMessage.php" class="cm-card">
          <div class="icon-wrap" style="background:#fef3c7;color:#d97706;"><i class="bi bi-chat-dots"></i></div>
          <h3>Messages</h3>
          <p>Student messages & inquiries</p>
          <span class="count"><?php echo $contentStats['messages']; ?> messages</span>
        </a>
      </div>

      <h3 class="section-title"><i class="bi bi-activity"></i> Monitoring</h3>
      <div class="cm-grid">
        <a href="activity-logs.php" class="cm-card">
          <div class="icon-wrap" style="background:#f3e5f5;color:#7b1fa2;"><i class="bi bi-clock-history"></i></div>
          <h3>Activity Logs</h3>
          <p>All admin actions & changes</p>
          <span class="count"><i class="bi bi-search"></i> Review</span>
        </a>
        <a href="index.php" class="cm-card">
          <div class="icon-wrap" style="background:#e0f2fe;color:#0284c7;"><i class="bi bi-speedometer2"></i></div>
          <h3>Dashboard</h3>
          <p>Admin overview & stats</p>
          <span class="count"><i class="bi bi-arrow-right"></i> Go</span>
        </a>
      </div>
    </div>
  </main>

  <footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> Copyright <strong><span>GIHEKE TSS</span></strong>. All Rights Reserved.
    <div class="credits">Developed by <a href="https://devoma.vercel.app" target="_blank">Omar MBONABUCYA</a></div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center" id="backToTopAdmin"><i class="bi bi-arrow-up-short"></i></a>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/giheke-toast.js"></script>
  <script>
  (function() {
    'use strict';
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('toggled');
        main.classList.toggle('full-width', sidebar.classList.contains('toggled'));
      });
    }
    const backToTop = document.getElementById('backToTopAdmin');
    if (backToTop) {
      window.addEventListener('scroll', function() {
        backToTop.style.opacity = window.scrollY > 300 ? '1' : '0';
        backToTop.style.visibility = window.scrollY > 300 ? 'visible' : 'hidden';
      });
      backToTop.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }
  })();
  </script>
</body>
</html>
<?php $conn->close(); ?>
