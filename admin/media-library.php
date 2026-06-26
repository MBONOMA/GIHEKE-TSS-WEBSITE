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

$uploadDir = 'assets/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle file upload
if (isset($_POST['upload_media']) && isset($_FILES['media_file'])) {
    $file = $_FILES['media_file'];
    $allowedTypes = ['image/jpeg','image/png','image/gif','image/webp','application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','text/plain','application/zip'];
    if (in_array($file['type'], $allowedTypes) && $file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
        $dest = $uploadDir . $safeName;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $alt = mysqli_real_escape_string($conn, $_POST['alt_text'] ?? '');
            $fname = mysqli_real_escape_string($conn, $file['name']);
            $ftype = mysqli_real_escape_string($conn, $file['type']);
            $fsize = (int)$file['size'];
            $fpath = mysqli_real_escape_string($conn, $dest);
            mysqli_query($conn, "INSERT INTO `tbl_media` (`file_name`, `file_path`, `file_type`, `file_size`, `alt_text`, `uploaded_by`) 
                                 VALUES ('$fname', '$fpath', '$ftype', $fsize, '$alt', '$id')");
            logActivity($conn, 'upload_media', 'Uploaded file: ' . $fname);
            echo "<script>GihekeToast.showModal('success','File Uploaded','" . addslashes($fname) . " has been uploaded.')</script>";
        }
    } else {
        echo "<script>GihekeToast.showModal('error','Upload Failed','File type not allowed or upload error.')</script>";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $q = mysqli_query($conn, "SELECT `file_path`, `file_name` FROM `tbl_media` WHERE id = $delId");
    if ($r = mysqli_fetch_assoc($q)) {
        if (file_exists($r['file_path'])) unlink($r['file_path']);
        mysqli_query($conn, "DELETE FROM `tbl_media` WHERE id = $delId");
        logActivity($conn, 'delete_media', 'Deleted file: ' . $r['file_name']);
        echo "<script>GihekeToast.showModal('success','File Deleted','" . addslashes($r['file_name']) . " has been removed.')</script>";
    }
}

$mediaQuery = mysqli_query($conn, "SELECT * FROM `tbl_media` ORDER BY `created_at` DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Media Library</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <style>
    .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
    .media-card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.04); transition: all 0.2s; }
    .media-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,0.1); }
    .media-card .preview { width: 100%; height: 160px; object-fit: cover; display: block; background: #f8f9fc; }
    .media-card .preview.pdf { display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #525FE1; background: #f0f0ff; }
    .media-card .info { padding: 14px; }
    .media-card .info .name { font-weight: 600; font-size: 0.85rem; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .media-card .info .meta { font-size: 0.75rem; color: #999; margin-top: 4px; }
    .media-card .info .actions { margin-top: 10px; display: flex; gap: 8px; }
    .media-card .info .actions a { padding: 4px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; text-decoration: none; }
    .btn-dl { background: #e8f0fe; color: #525FE1; }
    .btn-dl:hover { background: #d0d9ff; }
    .btn-del { background: #fce4ec; color: #ef4444; }
    .btn-del:hover { background: #f8d0d8; }
    .upload-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); padding: 32px; margin-bottom: 24px; border: 1px solid rgba(0,0,0,0.04); }
    .drop-zone { border: 2px dashed #d0d5dd; border-radius: 16px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.2s; background: #fafbfc; }
    .drop-zone:hover, .drop-zone.dragover { border-color: #525FE1; background: #f0f0ff; }
    .drop-zone i { font-size: 2.5rem; color: #525FE1; }
    .drop-zone p { color: #666; margin: 8px 0 0; font-size: 0.92rem; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state i { font-size: 3rem; color: #d0d5dd; margin-bottom: 16px; display: block; }
    .file-count { font-size: 0.85rem; color: #888; margin-bottom: 16px; }
  </style>
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt=""><span class="d-none d-lg-block">Administration</span>
      </a>
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
        <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">Media Library</h1>
        <nav><ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item active" style="color:#888;">Media Library</li>
        </ol></nav>
      </div>

      <div class="upload-card">
        <form method="post" enctype="multipart/form-data">
          <div class="drop-zone" id="dropZone">
            <i class="bi bi-cloud-upload"></i>
            <p>Drag & drop files here or click to browse</p>
            <input type="file" name="media_file" id="fileInput" style="display:none;" required>
          </div>
          <div style="margin-top:16px;display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
            <input type="text" name="alt_text" class="form-control-custom" placeholder="Alt text / description" style="flex:1;min-width:200px;padding:10px 14px;border:2px solid #e8e8e8;border-radius:10px;font-size:0.92rem;">
            <button type="submit" name="upload_media" class="btn-modern btn-modern-primary" style="padding:10px 24px;"><i class="bi bi-upload"></i> Upload</button>
          </div>
        </form>
      </div>

      <?php
      $total = mysqli_num_rows($mediaQuery);
      ?>
      <div class="file-count"><i class="bi bi-files"></i> <?php echo $total; ?> file(s)</div>

      <?php if ($total > 0): ?>
      <div class="media-grid">
        <?php while ($m = mysqli_fetch_assoc($mediaQuery)): 
          $isImage = strpos($m['file_type'], 'image/') === 0;
          $ext = strtolower(pathinfo($m['file_name'], PATHINFO_EXTENSION));
          $fileSize = $m['file_size'] > 1024 * 1024 ? round($m['file_size'] / (1024*1024), 1) . ' MB' : round($m['file_size'] / 1024, 1) . ' KB';
        ?>
        <div class="media-card">
          <?php if ($isImage): ?>
            <img class="preview" src="<?php echo $m['file_path']; ?>" alt="<?php echo htmlspecialchars($m['alt_text']?:$m['file_name']); ?>">
          <?php else: ?>
            <div class="preview pdf"><i class="bi bi-file-earmark-text"></i></div>
          <?php endif; ?>
          <div class="info">
            <div class="name" title="<?php echo htmlspecialchars($m['file_name']); ?>"><?php echo htmlspecialchars($m['file_name']); ?></div>
            <div class="meta"><?php echo strtoupper($ext); ?> &middot; <?php echo $fileSize; ?></div>
            <div class="actions">
              <a href="<?php echo $m['file_path']; ?>" target="_blank" class="btn-dl"><i class="bi bi-download"></i> View</a>
              <a href="media-library.php?delete=<?php echo $m['id']; ?>" class="btn-del" onclick="return confirmDelete('<?php echo addslashes($m['file_name']); ?>')"><i class="bi bi-trash"></i> Delete</a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-image"></i>
        <p>No media files yet. Upload images, PDFs, or documents above.</p>
      </div>
      <?php endif; ?>
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

    // Drop zone
    const dz = document.getElementById('dropZone');
    const fi = document.getElementById('fileInput');
    if (dz && fi) {
      dz.addEventListener('click', function() { fi.click(); });
      dz.addEventListener('dragover', function(e) { e.preventDefault(); dz.classList.add('dragover'); });
      dz.addEventListener('dragleave', function() { dz.classList.remove('dragover'); });
      dz.addEventListener('drop', function(e) {
        e.preventDefault();
        dz.classList.remove('dragover');
        if (e.dataTransfer.files.length) fi.files = e.dataTransfer.files;
      });
    }

    window.confirmDelete = function(name) {
      return confirm('Delete "' + name + '"? This cannot be undone.');
    };
  })();
  </script>
</body>
</html>
<?php $conn->close(); ?>
