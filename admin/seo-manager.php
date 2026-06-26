<?php
session_start();
include('includes/connection.php');
include('includes/activity-log.php');
error_reporting(0);
if (!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
    exit;
}

$id = $_SESSION['admin_id'];
$FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
$First = mysqli_fetch_array($FirstChar);
$Char = strtoupper($First['FirstName']);

// Add/Update SEO meta
if (isset($_POST['save_seo'])) {
    $pagePath = mysqli_real_escape_string($conn, $_POST['page_path']);
    $metaTitle = mysqli_real_escape_string($conn, $_POST['meta_title']);
    $metaDesc = mysqli_real_escape_string($conn, $_POST['meta_description']);
    $metaKeywords = mysqli_real_escape_string($conn, $_POST['meta_keywords']);
    $ogImage = mysqli_real_escape_string($conn, $_POST['og_image'] ?? '');

    $check = mysqli_query($conn, "SELECT id FROM `tbl_seo_meta` WHERE `page_path` = '$pagePath'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE `tbl_seo_meta` SET `meta_title`='$metaTitle', `meta_description`='$metaDesc', `meta_keywords`='$metaKeywords', `og_image`='$ogImage' WHERE `page_path`='$pagePath'");
    } else {
        mysqli_query($conn, "INSERT INTO `tbl_seo_meta` (`page_path`, `meta_title`, `meta_description`, `meta_keywords`, `og_image`) VALUES ('$pagePath', '$metaTitle', '$metaDesc', '$metaKeywords', '$ogImage')");
    }
    logActivity($conn, 'update_seo', 'Updated SEO meta for: ' . $pagePath);
    echo "<script>GihekeToast.showModal('success','SEO Saved','Meta data for " . addslashes($pagePath) . " has been updated.')</script>";
}

// Delete SEO entry
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $q = mysqli_query($conn, "SELECT page_path FROM `tbl_seo_meta` WHERE id=$did");
    $d = mysqli_fetch_assoc($q);
    mysqli_query($conn, "DELETE FROM `tbl_seo_meta` WHERE id=$did");
    logActivity($conn, 'delete_seo', 'Deleted SEO meta for: ' . ($d['page_path'] ?? ''));
    echo "<script>GihekeToast.showModal('success','SEO Entry Deleted','Meta data removed.')</script>";
}

$seoQuery = mysqli_query($conn, "SELECT * FROM `tbl_seo_meta` ORDER BY `page_path` ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>SEO Manager</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <style>
    .seo-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); padding: 32px; border: 1px solid rgba(0,0,0,0.04); }
    .seo-card h3 { font-size: 1.1rem; font-weight: 700; color: #3D47C9; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0; }
    .form-label-custom { font-weight: 600; font-size: 0.85rem; color: #555; margin-bottom: 6px; }
    .form-control-custom { width: 100%; padding: 10px 14px; border: 2px solid #e8e8e8; border-radius: 10px; font-size: 0.92rem; transition: all 0.2s; background: #fafafa; }
    .form-control-custom:focus { border-color: #525FE1; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(74,10,110,0.08); }
    textarea.form-control-custom { min-height: 80px; resize: vertical; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-grid-2 .full-w { grid-column: 1 / -1; }
    .btn-modern-primary { padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; border: none; background: linear-gradient(135deg,#525FE1,#3D47C9); color: #fff; transition: all 0.3s; }
    .btn-modern-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(82,95,225,0.3); }
    .seo-table { width: 100%; border-collapse: collapse; }
    .seo-table th { text-align: left; font-size: 0.8rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 16px; border-bottom: 2px solid #f0f0f0; }
    .seo-table td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; font-size: 0.88rem; color: #333; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .seo-table tr:hover td { background: #fafbff; }
    .btn-sm-icon { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state i { font-size: 3rem; color: #d0d5dd; margin-bottom: 16px; display: block; }
    .suggested-pages { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .suggested-pages .tag { padding: 4px 12px; border-radius: 16px; font-size: 0.78rem; background: #f0f0ff; color: #525FE1; cursor: pointer; border: 1px solid #d0d0ff; }
    .suggested-pages .tag:hover { background: #e0e0ff; }
    .preview-box { background: #f8f9fc; border-radius: 10px; padding: 14px; margin-top: 8px; font-size: 0.82rem; }
    .preview-box .p-title { color: #1a0dab; font-size: 1rem; }
    .preview-box .p-url { color: #006621; }
    .preview-box .p-desc { color: #545454; margin-top: 2px; }
  </style>
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center"><img src="assets/img/logo.png" alt=""><span class="d-none d-lg-block">Administration</span></a>
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
      <div class="pagetitle" style="margin-bottom:24px;">
        <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">SEO Manager</h1>
        <nav><ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item active" style="color:#888;">SEO</li>
        </ol></nav>
      </div>

      <div class="seo-card" style="margin-bottom:24px;">
        <h3><i class="bi bi-plus-circle"></i> Add / Edit SEO Meta</h3>
        <form method="post">
          <div class="form-grid-2">
            <div class="full-w">
              <label class="form-label-custom">Page Path</label>
              <input type="text" name="page_path" id="pagePath" class="form-control-custom" placeholder="/index.php" required>
              <div class="suggested-pages" id="suggestedPages" style="margin-top:8px;">
                <span class="tag" data-path="/index.php">/index.php</span>
                <span class="tag" data-path="/about.php">/about.php</span>
                <span class="tag" data-path="/contact.php">/contact.php</span>
                <span class="tag" data-path="/blog.php">/blog.php</span>
                <span class="tag" data-path="/admissions.php">/admissions.php</span>
                <span class="tag" data-path="/academics.php">/academics.php</span>
              </div>
            </div>
            <div class="full-w">
              <label class="form-label-custom">Meta Title</label>
              <input type="text" name="meta_title" id="metaTitle" class="form-control-custom" placeholder="Page title for search engines" maxlength="70">
            </div>
            <div class="full-w">
              <label class="form-label-custom">Meta Description</label>
              <textarea name="meta_description" id="metaDesc" class="form-control-custom" placeholder="Brief description for search results" maxlength="320"></textarea>
            </div>
            <div>
              <label class="form-label-custom">Meta Keywords (comma separated)</label>
              <input type="text" name="meta_keywords" class="form-control-custom" placeholder="school, education, rwanda">
            </div>
            <div>
              <label class="form-label-custom">OG Image URL</label>
              <input type="text" name="og_image" class="form-control-custom" placeholder="https://...">
            </div>
          </div>
          <div id="seoPreview" class="preview-box" style="display:none;">
            <div class="p-title" id="previewTitle">Meta Title</div>
            <div class="p-url" id="previewUrl">https://giheke-tss.ac.rw/page</div>
            <div class="p-desc" id="previewDesc">Meta Description</div>
          </div>
          <div style="margin-top:16px;text-align:right;">
            <button type="submit" name="save_seo" class="btn-modern-primary"><i class="bi bi-check2-circle"></i> Save SEO Meta</button>
          </div>
        </form>
      </div>

      <div class="seo-card">
        <h3><i class="bi bi-search-heart"></i> SEO Entries</h3>
        <?php if (mysqli_num_rows($seoQuery) > 0): ?>
        <table class="seo-table">
          <thead>
            <tr><th>Page Path</th><th>Meta Title</th><th>Description</th><th>Updated</th><th style="width:100px;">Actions</th></tr>
          </thead>
          <tbody>
            <?php while ($seo = mysqli_fetch_assoc($seoQuery)): ?>
            <tr>
              <td style="font-weight:600;color:#525FE1;"><?php echo htmlspecialchars($seo['page_path']); ?></td>
              <td><?php echo htmlspecialchars(strlen($seo['meta_title']??'') > 50 ? substr($seo['meta_title'],0,50).'...' : ($seo['meta_title']??'-')); ?></td>
              <td style="color:#888;font-size:0.82rem;"><?php echo htmlspecialchars(strlen($seo['meta_description']??'') > 80 ? substr($seo['meta_description'],0,80).'...' : ($seo['meta_description']??'-')); ?></td>
              <td style="font-size:0.82rem;color:#888;"><?php echo date('M d, Y', strtotime($seo['updated_at'])); ?></td>
              <td>
                <a href="seo-manager.php?edit=<?php echo $seo['id']; ?>" class="btn-sm-icon" style="background:#e8f0fe;color:#525FE1;"><i class="bi bi-pencil"></i></a>
                <a href="seo-manager.php?delete=<?php echo $seo['id']; ?>" class="btn-sm-icon" style="background:#fce4ec;color:#ef4444;" onclick="return confirm('Delete this SEO entry?')"><i class="bi bi-trash"></i></a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
          <i class="bi bi-search-heart"></i>
          <p>No SEO entries yet. Add meta data for your pages above.</p>
        </div>
        <?php endif; ?>
      </div>

      <?php if (isset($_GET['edit'])): 
        $eid = (int)$_GET['edit'];
        $eq = mysqli_query($conn, "SELECT * FROM `tbl_seo_meta` WHERE id=$eid");
        $editSeo = mysqli_fetch_assoc($eq);
        if ($editSeo):
      ?>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          document.getElementById('pagePath').value = '<?php echo addslashes($editSeo['page_path']); ?>';
          document.getElementById('metaTitle').value = '<?php echo addslashes($editSeo['meta_title']??''); ?>';
          document.getElementById('metaDesc').value = '<?php echo addslashes($editSeo['meta_description']??''); ?>';
          document.querySelector('input[name="meta_keywords"]').value = '<?php echo addslashes($editSeo['meta_keywords']??''); ?>';
          document.querySelector('input[name="og_image"]').value = '<?php echo addslashes($editSeo['og_image']??''); ?>';
          updatePreview();
        });
      </script>
      <?php endif; endif; ?>
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

    // Suggested page tags
    document.querySelectorAll('.tag').forEach(function(tag) {
      tag.addEventListener('click', function() {
        document.getElementById('pagePath').value = this.getAttribute('data-path');
        updatePreview();
      });
    });

    // Live preview
    function updatePreview() {
      const path = document.getElementById('pagePath').value;
      const title = document.getElementById('metaTitle').value;
      const desc = document.getElementById('metaDesc').value;
      const preview = document.getElementById('seoPreview');
      if (path || title || desc) {
        preview.style.display = 'block';
        document.getElementById('previewTitle').textContent = title || 'Meta Title';
        document.getElementById('previewUrl').textContent = 'https://giheke-tss.ac.rw' + (path || '/page');
        document.getElementById('previewDesc').textContent = desc || 'Meta Description';
      } else {
        preview.style.display = 'none';
      }
    }
    document.getElementById('metaTitle').addEventListener('input', updatePreview);
    document.getElementById('metaDesc').addEventListener('input', updatePreview);
    document.getElementById('pagePath').addEventListener('input', updatePreview);
  })();
  </script>
</body>
</html>
<?php $conn->close(); ?>
