<?php
session_start();
include('includes/connection.php');
error_reporting(0);
if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
    exit;
}
error_reporting(E_ERROR | E_PARSE);

$msg = '';
$error = '';

if(isset($_POST['submit'])) {
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $level = $_POST['level'] ?? '';
    $department = $_POST['department'] ?? '';
    $description = $_POST['description'] ?? '';
    $video_url = trim($_POST['video_url'] ?? '');

    if(empty($title) || empty($category)) {
        $error = "Title and category are required";
    } elseif(empty($_FILES['postimage']['name']) && empty($video_url)) {
        $error = "Please upload a PDF file or provide a YouTube URL";
    } elseif(!empty($video_url) && !preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/', $video_url)) {
        $error = "Invalid YouTube URL";
    } else {
        $file_path = '';
        $file_type = 'pdf';
        $file_size = 0;

        if(!empty($_FILES['postimage']['name'])) {
            $imgfile = $_FILES['postimage']['name'];
            $extension = strtolower(pathinfo($imgfile, PATHINFO_EXTENSION));
            if($extension != 'pdf') {
                $error = "Invalid format. Only PDF files are allowed";
            } else {
                $file_size = $_FILES['postimage']['size'];
                $max_size = 200 * 1024 * 1024;
                if($file_size > $max_size) {
                    $error = "File too large. Maximum size is 200MB.";
                } else {
                    $filename = md5($imgfile . time()) . '.' . $extension;
                    move_uploaded_file($_FILES['postimage']['tmp_name'], "uploads/books/" . $filename);
                    $file_path = 'admin/uploads/books/' . $filename;
                }
            }
        }

        if(empty($error)) {
            if(!empty($video_url)) {
                $file_type = 'video';
            }

            $featured_path = '';
            $featured_type = '';
            if(isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == UPLOAD_ERR_OK) {
                $img_name = $_FILES['featured_image']['name'];
                $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
                $allowed_img = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                if(in_array($img_ext, $allowed_img)) {
                    $img_filename = md5($img_name . time()) . '.' . $img_ext;
                    $feat_dir = 'uploads/featured/';
                    if(!is_dir($feat_dir)) mkdir($feat_dir, 0755, true);
                    move_uploaded_file($_FILES['featured_image']['tmp_name'], $feat_dir . $img_filename);
                    $featured_path = 'admin/' . $feat_dir . $img_filename;
                    $featured_type = $img_ext;
                }
            }

            $sql = "INSERT INTO `tbl_books` (id, title, category, level, department, file_path, file_type, file_size, video_url, description, featured_image, featured_image_type) VALUES ('', '$title', '$category', '$level', '$department', '$file_path', '$file_type', '$file_size', '$video_url', '$description', '$featured_path', '$featured_type')";
            $result = mysqli_query($conn, $sql);
            if($result) {
                $msg = "Content added successfully!";
            } else {
                $error = mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Dashboard</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="../img/giheke logo.webp" rel="apple-touch-icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between" style="width:100%;">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="" style="height:38px;border-radius:8px;">
        <span class="d-none d-lg-block" style="font-weight:800;color:#0F172A;font-size:1.1rem;">GIHEKE <span style="color:#525FE1;">Admin</span></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn" id="sidebarToggle"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center" style="list-style:none;margin:0;padding:0;gap:8px;">
        <?php include('includes/notifications.php'); ?>
        <li class="nav-item dropdown pe-3">
          <?php
            include 'includes/connection.php';
            $id = $_SESSION['admin_id'];
            $admin_res = mysqli_query($conn, "SELECT * FROM tbl_admins WHERE id = '$id'");
            $admin = mysqli_fetch_array($admin_res);
            $Char = strtoupper($admin['FirstName'] ?? 'A');
          ?>
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" style="color:#0F172A;font-weight:600;">
            <img src="admin-img/<?php echo $admin['ImageUrl'] ?? 'default.png'; ?>" alt="Profile" class="rounded-circle" style="width:36px;height:36px;border-radius:50%;border:2px solid #525FE1;">
            <span class="d-none d-md-block" style="margin-left:8px;"><?php echo substr($Char, 0,1) . ". " . ($admin['LastName'] ?? 'Admin'); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" style="border-radius:12px;border:1px solid #e8f0f5;box-shadow:0 8px 30px rgba(0,0,0,0.1);">
            <li class="dropdown-header"><h6 style="font-weight:700;"><?php echo ($admin['FirstName'] ?? '') . " " . ($admin['LastName'] ?? ''); ?></h6><span style="color:#525FE1;">School Admin</span></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="user-profile.php"><i class="bi bi-person"></i><span>My Profile</span></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="changePassword.php"><i class="bi bi-shuffle"></i><span>Change Password</span></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="logout.php"><i class="bi bi-box-arrow-right"></i><span>Sign Out</span></a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <?php include('includes/sidebar.php'); ?>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1><i class="bi bi-book me-2" style="color:#525FE1;"></i>Add Book/Resource</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Add Content</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="admin-content">
        <?php if($msg): ?>
          <div class="alert-modern alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> <?php echo htmlentities($msg); ?>
            <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'" style="margin-left:auto;"></button>
          </div>
        <?php endif; ?>
        <?php if($error): ?>
          <div class="alert-modern alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> <?php echo htmlentities($error); ?>
            <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'" style="margin-left:auto;"></button>
          </div>
        <?php endif; ?>

        <div class="admin-section-card">
          <div class="admin-section-header">
            <h3><i class="bi bi-plus-circle me-2" style="color:#525FE1;"></i>Book/Resource Details</h3>
          </div>
          <div class="p-4">
            <form class="row g-3 needs-validation" novalidate enctype="multipart/form-data" method="post">
              <div class="col-12">
                <label class="form-label">Title *</label>
                <input type="text" class="form-modern" name="title" placeholder="Enter title" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Category *</label>
                <select class="form-modern" name="category" id="addCategory" required>
                  <option value="">Select Category</option>
                  <option value="Book">Textbook</option>
                  <option value="Past Paper">Past Paper</option>
                  <option value="Video Tutorial">Video Tutorial</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Level</label>
                <select class="form-modern" name="level">
                  <option value="">Select Level</option>
                  <option value="L3">L3</option>
                  <option value="L4">L4</option>
                  <option value="L5">L5</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Trade</label>
                <select class="form-modern" name="department">
                  <option value="">Select Trade</option>
                  <?php
                  $trades = mysqli_query($conn, "SELECT name FROM tbl_trades WHERE is_active=1 ORDER BY sort_order ASC");
                  while($tr = mysqli_fetch_assoc($trades)):
                  ?>
                  <option value="<?php echo htmlspecialchars($tr['name']); ?>"><?php echo htmlspecialchars($tr['name']); ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">PDF File</label>
                <input type="file" class="form-modern" name="postimage" accept=".pdf" style="padding:8px;">
                <small class="text-muted">Max size: 200MB. Required for books and past papers.</small>
              </div>
              <div class="col-md-6" id="videoUrlGroup" style="display:none;">
                <label class="form-label">YouTube URL (for Video Tutorials)</label>
                <input type="url" class="form-modern" name="video_url" id="videoUrlInput" placeholder="https://youtube.com/watch?v=..." style="padding:8px;">
                <small class="text-muted">Paste the YouTube video link (file upload not needed)</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Featured Image (optional)</label>
                <input type="file" class="form-modern" name="featured_image" accept=".jpg,.jpeg,.png,.gif,.webp" style="padding:8px;">
                <small class="text-muted">Formats: JPG, PNG, GIF, WebP</small>
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea class="form-modern" name="description" rows="4" placeholder="Brief description..."></textarea>
              </div>
              <div class="col-12 text-end">
                <button type="submit" name="submit" class="btn-modern btn-modern-primary">
                  <i class="bi bi-check-lg"></i> Add Content
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="admin-footer">
    <div class="copyright">&copy; <?php echo date('Y'); ?> Copyright <strong><span>GIHEKE TSS</span></strong>. All Rights Reserved.</div>
    <div class="credits">Developed by <a href="https://devoma.vercel.app" target="_blank">Omar MBONABUCYA</a></div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center" id="backToTopAdmin">
    <i class="bi bi-arrow-up-short"></i>
  </a>
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

    const catSelect = document.getElementById('addCategory');
    const videoUrlGroup = document.getElementById('videoUrlGroup');
    if (catSelect && videoUrlGroup) {
      function toggleVideoUrl() {
        videoUrlGroup.style.display = catSelect.value === 'Video Tutorial' ? 'block' : 'none';
      }
      catSelect.addEventListener('change', toggleVideoUrl);
      toggleVideoUrl();
    }
  })();
  </script>
</body>
</html>