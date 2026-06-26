<?php
  session_start();
  include('includes/connection.php');
  error_reporting(E_ERROR | E_PARSE);
  if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
  }

  $msg = '';
  $error = '';

  if(isset($_POST['submit'])) {
    $NewsTitle = trim($_POST['NewsTitle'] ?? '');
    $NewsCategory = $_POST['NewsCategory'] ?? '';
    $PostDetails = $_POST['PostDescription'] ?? '';
    $mediaType = $_POST['MediaType'] ?? 'image';

    $arr = explode(" ", $NewsTitle);
    $url = implode("-", $arr);
    $status = 1;
    $PostedBy = "GIHEKE TSS SCHOOL";

    $allowedExtImages = ["jpg", "jpeg", "png", "gif", "webp"];
    $allowedExtVideos = ["mp4", "webm", "avi", "mov", "wmv", "flv", "mkv", "3gp"];

    if($mediaType === 'video'){
        $mediafile = $_FILES["postmedia"]["name"] ?? '';
        $extension = strtolower(pathinfo($mediafile, PATHINFO_EXTENSION));
        if(empty($mediafile)){
            $error = "Please select a video file";
        } elseif(!in_array($extension, $allowedExtVideos)){
            $error = "Invalid video format. Allowed: mp4, webm, avi, mov, wmv, flv, mkv, 3gp";
        } else {
            $mediaNewFile = md5(time() . $mediafile) . '.' . $extension;
            if(move_uploaded_file($_FILES["postmedia"]["tmp_name"], "Blog Gallery/" . $mediaNewFile)){
                $NewsTitle = str_replace("'", "'", $NewsTitle);
                $PostDetails = str_replace("'", "'", $PostDetails);
                $sql = "INSERT INTO tblposts (PostTitle, CategoryId, PostDetails, PostUrl, Is_Active, PostImage, postedBy, MediaType)
                        VALUES ('$NewsTitle', '$NewsCategory', '$PostDetails', '$url', '$status', '$mediaNewFile', '$PostedBy', 'video')";
                $result = mysqli_query($conn, $sql);
                if($result){ $msg = "News added successfully"; }
                else { $error = "Error: " . mysqli_error($conn); }
            } else { $error = "Failed to upload video"; }
        }
    } else {
        $imgfile = $_FILES["postimage"]["name"] ?? '';
        $extension = strtolower(pathinfo($imgfile, PATHINFO_EXTENSION));
        if(empty($imgfile)){
            $error = "Featured image is required";
        } elseif(!in_array($extension, $allowedExtImages)){
            $error = "Invalid format. Only jpg/jpeg/png/gif/webp formats allowed";
        } else {
            $imgnewfile = md5(time() . $imgfile) . '.' . $extension;
            if(move_uploaded_file($_FILES["postimage"]["tmp_name"], "Blog Gallery/" . $imgnewfile)){
                $NewsTitle = str_replace("'", "'", $NewsTitle);
                $PostDetails = str_replace("'", "'", $PostDetails);
                $sql = "INSERT INTO tblposts (PostTitle, CategoryId, PostDetails, PostUrl, Is_Active, PostImage, postedBy, MediaType)
                        VALUES ('$NewsTitle', '$NewsCategory', '$PostDetails', '$url', '$status', '$imgnewfile', '$PostedBy', 'image')";
                $result = mysqli_query($conn, $sql);
                if($result){ $msg = "News added successfully"; }
                else { $error = "Error: " . mysqli_error($conn); }
            } else { $error = "Failed to upload image"; }
        }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Dashboard - GIHEKE TSS</title>

  <link href="../img/giheke logo.webp" rel="icon">
  <link href="../img/giheke logo.webp" rel="apple-touch-icon">

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <link href="../assets/css/admin-panel.css" rel="stylesheet">
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
      <h1><i class="bi bi-newspaper me-2" style="color:#525FE1;"></i>Add News Post</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Add News</li>
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
            <h3><i class="bi bi-plus-circle me-2" style="color:#525FE1;"></i>News Details</h3>
          </div>
          <div class="p-4">
            <form class="row g-3 needs-validation" novalidate enctype="multipart/form-data" method="post">
              <div class="col-12">
                <label class="form-label">News Title *</label>
                <input type="text" class="form-modern" name="NewsTitle" placeholder="Enter news title" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Category *</label>
                <select class="form-modern" name="NewsCategory" required>
                  <option value="" disabled selected>Select Category</option>
                  <?php
                    include 'includes/connection.php';
                    $sql = "SELECT * FROM tbl_school_category";
                    $result = mysqli_query($conn, $sql);
                    while($cat = mysqli_fetch_array($result)){
                      echo '<option value="'.$cat['id'].'">'.$cat['CategoryName'].'</option>';
                    }
                  ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Media Type *</label>
                <select class="form-modern" name="MediaType" required onchange="toggleBlogMediaInput()">
                  <option value="" disabled selected>Select Type</option>
                  <option value="image">Image (JPG, PNG, GIF, WebP)</option>
                  <option value="video">Video (MP4, WebM, AVI, MOV, WMV, FLV, MKV, 3GP)</option>
                </select>
              </div>
              <div class="col-md-6" id="blogImageInput">
                <label class="form-label">Featured Image *</label>
                <input type="file" class="form-modern" name="postimage" accept=".jpg,.jpeg,.png,.gif,.webp" style="padding:8px;" required>
                <small class="text-muted">Max size: 5MB. Formats: JPG, PNG, GIF, WebP</small>
              </div>
              <div class="col-md-6 d-none" id="blogVideoInput">
                <label class="form-label">Featured Video (Max 200MB)</label>
                <input type="file" class="form-modern" name="postmedia" accept=".mp4,.webm,.avi,.mov,.wmv,.flv,.mkv,.3gp" style="padding:8px;">
                <small class="text-muted">Max size: 200MB. Supported: MP4, WebM, AVI, MOV, WMV, FLV, MKV, 3GP</small>
              </div>
              <div class="col-12">
                <label class="form-label">News Content *</label>
                <textarea class="form-modern" name="PostDescription" rows="8" placeholder="Write your news content here..." required></textarea>
              </div>
              <div class="col-12 text-end">
                <button type="submit" name="submit" class="btn-modern btn-modern-primary">
                  <i class="bi bi-check-lg"></i> Publish News
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

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/giheke-toast.js"></script>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center" id="backToTopAdmin">
    <i class="bi bi-arrow-up-short"></i>
  </a>
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

function toggleBlogMediaInput(){
  var type = document.querySelector('select[name="MediaType"]')?.value;
  var imgInput = document.getElementById('blogImageInput');
  var vidInput = document.getElementById('blogVideoInput');
  var imgFile = document.querySelector('input[name="postimage"]');
  var vidFile = document.querySelector('input[name="postmedia"]');
  if(type === 'video'){
    imgInput.classList.add('d-none');
    vidInput.classList.remove('d-none');
    if(imgFile) imgFile.required = false;
    if(vidFile) vidFile.required = true;
  } else {
    imgInput.classList.remove('d-none');
    vidInput.classList.add('d-none');
    if(imgFile) imgFile.required = true;
    if(vidFile) vidFile.required = false;
  }
}
</script>
</body>
</html>