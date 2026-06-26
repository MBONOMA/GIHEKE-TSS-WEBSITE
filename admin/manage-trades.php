<?php
session_start();
include('includes/connection.php');
include('includes/activity-log.php');
error_reporting(0);
if (!isset($_SESSION['admin_id'])) { header('location:login.php'); exit; }

$adminId = $_SESSION['admin_id'];
$adminRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$adminId'"));
$Char = strtoupper($adminRow['FirstName'] ?? 'A');
$First = $adminRow;

$msg = '';
$error = '';
$uploadDir = '../assets/uploads/';
if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

function ensureImageColumn($conn, $table) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE 'image'");
    if (!$check || mysqli_num_rows($check) === 0) {
        mysqli_query($conn, "ALTER TABLE `$table` ADD COLUMN `image` VARCHAR(255) DEFAULT NULL");
    }
}

ensureImageColumn($conn, 'tbl_trades');
ensureImageColumn($conn, 'tbl_staff_members');
ensureImageColumn($conn, 'tbl_facilities');
ensureImageColumn($conn, 'tbl_features');
ensureImageColumn($conn, 'tbl_core_values');

function uploadImage($file, $existing = null) {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) return $existing;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (!in_array($ext, $allowed)) return false;
    $name = 'trade_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = dirname(__FILE__) . '/../assets/uploads/' . $name;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        if ($existing && file_exists(dirname(__FILE__) . '/../' . $existing)) unlink(dirname(__FILE__) . '/../' . $existing);
        return 'assets/uploads/' . $name;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_trade'])) {
    $id = (int)($_POST['id'] ?? 0);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $icon = mysqli_real_escape_string($conn, $_POST['icon']);
    $order = (int)($_POST['sort_order'] ?? 0);
    $active = isset($_POST['is_active']) ? 1 : 0;
    $image = $id > 0 ? (mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM `tbl_trades` WHERE id=$id"))['image'] ?? null) : null;
    $newImage = uploadImage($_FILES['image'], $image);
    if ($newImage === false && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) { $error = 'Invalid image file.'; }
    else {
        $url = mysqli_real_escape_string($conn, $_POST['url'] ?? '');
        $img = $newImage ? "'" . mysqli_real_escape_string($conn, $newImage) . "'" : ($image ? "'$image'" : "NULL");
        if ($id > 0) {
            mysqli_query($conn, "UPDATE `tbl_trades` SET `name`='$name',`description`='$desc',`duration`='$duration',`icon`='$icon',`image`=$img,`url`='$url',`sort_order`=$order,`is_active`=$active WHERE id=$id");
            $msg = 'Trade updated.';
        } else {
            mysqli_query($conn, "INSERT INTO `tbl_trades` (`name`,`description`,`duration`,`icon`,`image`,`url`,`sort_order`,`is_active`) VALUES ('$name','$desc','$duration','$icon',$img,'$url',$order,$active)");
            $msg = 'Trade added.';
        }
        if (!empty($conn->error)) $error = $conn->error;
    }
    logActivity($conn, $id > 0 ? 'update_trade' : 'add_trade', $name);
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM `tbl_trades` WHERE id=$id"));
    if ($r && $r['image'] && file_exists(dirname(__FILE__) . '/../' . $r['image'])) unlink(dirname(__FILE__) . '/../' . $r['image']);
    mysqli_query($conn, "DELETE FROM `tbl_trades` WHERE id=$id");
    $msg = 'Trade deleted.';
    logActivity($conn, 'delete_trade', "Deleted trade ID $id");
}
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_active FROM `tbl_trades` WHERE id=$id"));
    $new = $r['is_active'] ? 0 : 1;
    mysqli_query($conn, "UPDATE `tbl_trades` SET `is_active`=$new WHERE id=$id");
    $msg = 'Status toggled.';
}

$trades = mysqli_query($conn, "SELECT * FROM `tbl_trades` ORDER BY sort_order ASC");
$editRow = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $editRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `tbl_trades` WHERE id=$eid"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Manage Trade Programs</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <style>
    .content-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.04); overflow: hidden; }
    .card-header { padding: 18px 24px; font-weight: 700; font-size: 0.95rem; color: #3D47C9; background: #f8f9fc; border-bottom: 1px solid rgba(0,0,0,0.04); }
    .card-body { padding: 24px; }
    .form-label-custom { font-weight: 600; font-size: 0.85rem; color: #555; margin-bottom: 6px; display: block; }
    .form-control-custom { width: 100%; padding: 10px 14px; border: 2px solid #e8e8e8; border-radius: 10px; font-size: 0.92rem; transition: all 0.2s; background: #fafafa; }
    .form-control-custom:focus { border-color: #525FE1; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(74,10,110,0.08); }
    .btn-save { padding: 12px 32px; border-radius: 12px; font-weight: 700; font-size: 0.92rem; border: none; background: linear-gradient(135deg,#525FE1,#3D47C9); color: #fff; transition: all 0.3s; }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(82,95,225,0.3); }
    .table th { font-size: 0.82rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.03em; border-bottom: 2px solid #f0f0f0 !important; }
    .table td { vertical-align: middle; }
    .img-preview { width: 60px; height: 40px; object-fit: cover; border-radius: 8px; border: 2px solid #f0f0f0; }
    .existing-img-label { font-size: 0.82rem; color: #888; display: flex; align-items: center; gap: 8px; margin-top: 4px; }
    .page-header-card { background: linear-gradient(135deg, #525FE1 0%, #3D47C9 100%); border-radius: 20px; padding: 28px 32px; color: #fff; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .page-header-card h1 { font-size: 1.4rem; font-weight: 800; margin: 0; color: #fff; }
    .page-header-card p { font-size: 0.88rem; opacity: 0.85; margin: 4px 0 0; }
    .page-header-card .btn-back { background: rgba(255,255,255,0.2); color: #fff; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
    .page-header-card .btn-back:hover { background: rgba(255,255,255,0.3); }
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

      <div class="page-header-card">
        <div>
          <h1><i class="bi bi-tools"></i> Manage Trade Programs</h1>
          <p>Add, edit, or reorder the technical trade programs displayed on the homepage.</p>
        </div>
        <a href="site-settings.php" class="btn-back"><i class="bi bi-arrow-left"></i> Back to Settings</a>
      </div>

      <?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

      <div class="content-card">
        <div class="card-header"><i class="bi bi-plus-circle"></i> <?php echo $editRow ? 'Edit' : 'Add'; ?> Trade Program</div>
        <div class="card-body">
          <form method="POST" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="id" value="<?php echo $editRow['id'] ?? 0; ?>">
            <div class="col-md-6">
              <label class="form-label-custom">Program Name *</label>
              <input type="text" name="name" class="form-control-custom" value="<?php echo htmlspecialchars($editRow['name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-3">
              <label class="form-label-custom">Duration</label>
              <input type="text" name="duration" class="form-control-custom" value="<?php echo htmlspecialchars($editRow['duration'] ?? '3 Years'); ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label-custom">Icon (Bootstrap class)</label>
              <input type="text" name="icon" class="form-control-custom" value="<?php echo htmlspecialchars($editRow['icon'] ?? 'bi bi-tools'); ?>">
            </div>
            <div class="col-md-8">
              <label class="form-label-custom">Description</label>
              <textarea name="description" class="form-control-custom" rows="3"><?php echo htmlspecialchars($editRow['description'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label-custom">Course URL (for Browse Courses link)</label>
              <input type="url" name="url" class="form-control-custom" placeholder="https://..." value="<?php echo htmlspecialchars($editRow['url'] ?? ''); ?>">
              <small class="text-muted">Where visitors are taken when clicking "Browse courses"</small>
            </div>
            <div class="col-md-2">
              <label class="form-label-custom">Sort Order</label>
              <input type="number" name="sort_order" class="form-control-custom" value="<?php echo $editRow['sort_order'] ?? 0; ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <div class="form-check">
                <input type="checkbox" name="is_active" class="form-check-input" id="ta" <?php echo (!isset($editRow) || $editRow['is_active']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="ta">Active</label>
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label-custom">Program Image</label>
              <input type="file" name="image" class="form-control-custom" accept="image/*">
              <?php if (!empty($editRow['image'])): ?>
                <div class="existing-img-label"><img src="../<?php echo htmlspecialchars($editRow['image']); ?>" class="img-preview"> Existing: <?php echo basename($editRow['image']); ?></div>
              <?php endif; ?>
            </div>
            <div class="col-12">
              <button type="submit" name="save_trade" class="btn-save"><i class="bi bi-save"></i> <?php echo $editRow ? 'Update' : 'Add'; ?> Program</button>
              <?php if ($editRow): ?><a href="manage-trades.php" class="btn btn-secondary ms-2"><i class="bi bi-x"></i> Cancel</a><?php endif; ?>
            </div>
          </form>
        </div>
      </div>

      <div class="content-card mt-4">
        <div class="card-header"><i class="bi bi-list"></i> All Trade Programs</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead><tr><th>Image</th><th>Order</th><th>Program</th><th>Duration</th><th>URL</th><th>Active</th><th style="width:150px">Actions</th></tr></thead>
              <tbody>
                <?php if (mysqli_num_rows($trades) == 0): ?>
                  <tr><td colspan="7" class="text-center text-muted py-4">No trade programs yet.</td></tr>
                <?php else: ?>
                  <?php while ($t = mysqli_fetch_assoc($trades)): ?>
                    <tr>
                      <td><?php if ($t['image']): ?><img src="../<?php echo htmlspecialchars($t['image']); ?>" class="img-preview"><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
                      <td><?php echo $t['sort_order']; ?></td>
                      <td><strong><?php echo htmlspecialchars($t['name']); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars(substr($t['description'], 0, 80)); ?>...</small></td>
                      <td><?php echo htmlspecialchars($t['duration']); ?></td>
                      <td><?php if (!empty($t['url'])): ?><a href="<?php echo htmlspecialchars($t['url']); ?>" target="_blank" title="Visit URL"><i class="bi bi-box-arrow-up-right"></i></a><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
                      <td><?php echo $t['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Hidden</span>'; ?></td>
                      <td>
                        <a href="?edit=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?toggle=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-warning" title="Toggle"><i class="bi bi-eye<?php echo $t['is_active'] ? '-slash' : ''; ?>"></i></a>
                        <a href="?delete=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this trade program?')"><i class="bi bi-trash"></i></a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> GIHEKE TSS. All Rights Reserved.
  </footer>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center" id="backToTopAdmin"><i class="bi bi-arrow-up-short"></i></a>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/giheke-toast.js"></script>
  <script>
  (function(){
    const s=document.getElementById('sidebar'),m=document.getElementById('main'),t=document.getElementById('sidebarToggle');
    if(t){t.addEventListener('click',function(){s.classList.toggle('toggled');m.classList.toggle('full-width',s.classList.contains('toggled'));});}
    const b=document.getElementById('backToTopAdmin');
    if(b){window.addEventListener('scroll',function(){b.style.opacity=window.scrollY>300?'1':'0';b.style.visibility=window.scrollY>300?'visible':'hidden';});
    b.addEventListener('click',function(e){e.preventDefault();window.scrollTo({top:0,behavior:'smooth'});});}
  })();
  </script>
</body>
</html>
<?php $conn->close(); ?>
