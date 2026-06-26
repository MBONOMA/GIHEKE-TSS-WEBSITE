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

// Add navigation item
if (isset($_POST['add_nav'])) {
    $label = mysqli_real_escape_string($conn, $_POST['label']);
    $url = mysqli_real_escape_string($conn, $_POST['url']);
    $parent = $_POST['parent_id'] ? (int)$_POST['parent_id'] : 'NULL';
    $target = mysqli_real_escape_string($conn, $_POST['target'] ?? '_self');
    $icon = mysqli_real_escape_string($conn, $_POST['icon'] ?? '');
    $order = (int)($_POST['sort_order'] ?? 0);
    mysqli_query($conn, "INSERT INTO `tbl_navigation` (`label`, `url`, `parent_id`, `sort_order`, `target`, `icon`) 
                         VALUES ('$label', '$url', $parent, $order, '$target', '$icon')");
    logActivity($conn, 'add_nav_item', 'Added navigation link: ' . $label);
    echo "<script>GihekeToast.showModal('success','Link Added','Navigation link created successfully.')</script>";
}

// Update navigation item
if (isset($_POST['update_nav'])) {
    $nid = (int)$_POST['nav_id'];
    $label = mysqli_real_escape_string($conn, $_POST['label']);
    $url = mysqli_real_escape_string($conn, $_POST['url']);
    $parent = $_POST['parent_id'] ? (int)$_POST['parent_id'] : 'NULL';
    $target = mysqli_real_escape_string($conn, $_POST['target'] ?? '_self');
    $icon = mysqli_real_escape_string($conn, $_POST['icon'] ?? '');
    $order = (int)($_POST['sort_order'] ?? 0);
    $active = isset($_POST['is_active']) ? 1 : 0;
    mysqli_query($conn, "UPDATE `tbl_navigation` SET `label`='$label', `url`='$url', `parent_id`=$parent, `sort_order`=$order, `target`='$target', `icon`='$icon', `is_active`=$active WHERE id=$nid");
    logActivity($conn, 'update_nav_item', 'Updated navigation link: ' . $label);
    echo "<script>GihekeToast.showModal('success','Link Updated','Navigation link updated successfully.')</script>";
}

// Delete navigation item
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $q = mysqli_query($conn, "SELECT label FROM `tbl_navigation` WHERE id=$did");
    $d = mysqli_fetch_assoc($q);
    mysqli_query($conn, "UPDATE `tbl_navigation` SET `parent_id` = NULL WHERE `parent_id` = $did");
    mysqli_query($conn, "DELETE FROM `tbl_navigation` WHERE id=$did");
    logActivity($conn, 'delete_nav_item', 'Deleted navigation link: ' . ($d['label'] ?? ''));
    echo "<script>GihekeToast.showModal('success','Link Deleted','Navigation link removed.')</script>";
}

$navQuery = mysqli_query($conn, "SELECT * FROM `tbl_navigation` ORDER BY `sort_order` ASC, `label` ASC");

// Fetch parent list for dropdown
$parents = mysqli_query($conn, "SELECT * FROM `tbl_navigation` WHERE `parent_id` IS NULL ORDER BY `sort_order` ASC, `label` ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Navigation Manager</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <style>
    .nav-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); padding: 32px; border: 1px solid rgba(0,0,0,0.04); }
    .nav-card h3 { font-size: 1.1rem; font-weight: 700; color: #3D47C9; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0; }
    .form-label-custom { font-weight: 600; font-size: 0.85rem; color: #555; margin-bottom: 6px; }
    .form-control-custom { width: 100%; padding: 10px 14px; border: 2px solid #e8e8e8; border-radius: 10px; font-size: 0.92rem; transition: all 0.2s; background: #fafafa; }
    .form-control-custom:focus { border-color: #525FE1; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(74,10,110,0.08); }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-grid-2 .full-w { grid-column: 1 / -1; }
    .btn-modern-primary { padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; border: none; background: linear-gradient(135deg,#525FE1,#3D47C9); color: #fff; transition: all 0.3s; }
    .btn-modern-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(82,95,225,0.3); }
    .btn-sm-icon { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .nav-table { width: 100%; border-collapse: collapse; }
    .nav-table th { text-align: left; font-size: 0.8rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 16px; border-bottom: 2px solid #f0f0f0; }
    .nav-table td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; color: #333; }
    .nav-table tr:hover td { background: #fafbff; }
    .badge-active { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 0.72rem; font-weight: 600; }
    .badge-active.yes { background: #e8f5e9; color: #2e7d32; }
    .badge-active.no { background: #fce4ec; color: #c62828; }
    .child-item { padding-left: 24px !important; position: relative; }
    .child-item::before { content: '\21B3'; position: absolute; left: 8px; color: #ccc; }
    .inline-form { display: inline; }
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
        <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">Navigation Manager</h1>
        <nav><ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item active" style="color:#888;">Navigation</li>
        </ol></nav>
      </div>

      <div class="nav-card" style="margin-bottom:24px;">
        <h3><i class="bi bi-plus-circle"></i> Add New Link</h3>
        <form method="post">
          <div class="form-grid-2">
            <div><label class="form-label-custom">Label</label><input type="text" name="label" class="form-control-custom" required></div>
            <div><label class="form-label-custom">URL</label><input type="text" name="url" class="form-control-custom" required placeholder="/about.php"></div>
            <div><label class="form-label-custom">Parent</label>
              <select name="parent_id" class="form-control-custom">
                <option value="">No Parent (Top Level)</option>
                <?php while ($p = mysqli_fetch_assoc($parents)): ?>
                  <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['label']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div><label class="form-label-custom">Target</label>
              <select name="target" class="form-control-custom">
                <option value="_self">Same Tab</option>
                <option value="_blank">New Tab</option>
              </select>
            </div>
            <div><label class="form-label-custom">Icon (Bootstrap icon class)</label><input type="text" name="icon" class="form-control-custom" placeholder="bi bi-link"></div>
            <div><label class="form-label-custom">Sort Order</label><input type="number" name="sort_order" class="form-control-custom" value="0"></div>
          </div>
          <div style="margin-top:16px;text-align:right;">
            <button type="submit" name="add_nav" class="btn-modern-primary"><i class="bi bi-plus-lg"></i> Add Link</button>
          </div>
        </form>
      </div>

      <?php
      // Re-fetch parents for display
      $displayQuery = mysqli_query($conn, "SELECT * FROM `tbl_navigation` ORDER BY `sort_order` ASC, `label` ASC");
      $children = [];
      $topLevel = [];
      while ($n = mysqli_fetch_assoc($displayQuery)) {
          if ($n['parent_id']) {
              $children[$n['parent_id']][] = $n;
          } else {
              $topLevel[] = $n;
          }
      }
      ?>
      <div class="nav-card">
        <h3><i class="bi bi-list-nested"></i> Navigation Structure</h3>
        <?php if (count($topLevel) > 0 || count($children) > 0): ?>
        <table class="nav-table">
          <thead>
            <tr><th style="width:40px;">Order</th><th>Label</th><th>URL</th><th>Target</th><th>Status</th><th style="width:140px;">Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($topLevel as $item): ?>
            <tr>
              <td><?php echo $item['sort_order']; ?></td>
              <td><strong><?php echo htmlspecialchars($item['label']); ?></strong></td>
              <td style="color:#888;font-size:0.85rem;"><?php echo htmlspecialchars($item['url']); ?></td>
              <td><?php echo $item['target']; ?></td>
              <td><span class="badge-active <?php echo $item['is_active']?'yes':'no'; ?>"><?php echo $item['is_active']?'Active':'Inactive'; ?></span></td>
              <td>
                <a href="navigation-manager.php?edit=<?php echo $item['id']; ?>" class="btn-sm-icon" style="background:#e8f0fe;color:#525FE1;"><i class="bi bi-pencil"></i></a>
                <a href="navigation-manager.php?delete=<?php echo $item['id']; ?>" class="btn-sm-icon" style="background:#fce4ec;color:#ef4444;" onclick="return confirm('Delete this link and its children?')"><i class="bi bi-trash"></i></a>
              </td>
            </tr>
            <?php if (isset($children[$item['id']])): ?>
              <?php foreach ($children[$item['id']] as $child): ?>
              <tr>
                <td><?php echo $child['sort_order']; ?></td>
                <td class="child-item"><?php echo htmlspecialchars($child['label']); ?></td>
                <td style="color:#888;font-size:0.85rem;"><?php echo htmlspecialchars($child['url']); ?></td>
                <td><?php echo $child['target']; ?></td>
                <td><span class="badge-active <?php echo $child['is_active']?'yes':'no'; ?>"><?php echo $child['is_active']?'Active':'Inactive'; ?></span></td>
                <td>
                  <a href="navigation-manager.php?edit=<?php echo $child['id']; ?>" class="btn-sm-icon" style="background:#e8f0fe;color:#525FE1;"><i class="bi bi-pencil"></i></a>
                  <a href="navigation-manager.php?delete=<?php echo $child['id']; ?>" class="btn-sm-icon" style="background:#fce4ec;color:#ef4444;" onclick="return confirm('Delete this link?')"><i class="bi bi-trash"></i></a>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <p style="color:#999;text-align:center;padding:40px 0;">No navigation links yet. Add your first link above.</p>
        <?php endif; ?>
      </div>

      <?php if (isset($_GET['edit'])): 
        $eid = (int)$_GET['edit'];
        $eq = mysqli_query($conn, "SELECT * FROM `tbl_navigation` WHERE id=$eid");
        $editItem = mysqli_fetch_assoc($eq);
        if ($editItem):
      ?>
      <div class="nav-card" style="margin-top:24px;">
        <h3><i class="bi bi-pencil"></i> Edit Link</h3>
        <form method="post">
          <input type="hidden" name="nav_id" value="<?php echo $editItem['id']; ?>">
          <div class="form-grid-2">
            <div class="full-w"><label class="form-label-custom">Label</label><input type="text" name="label" class="form-control-custom" value="<?php echo htmlspecialchars($editItem['label']); ?>" required></div>
            <div><label class="form-label-custom">URL</label><input type="text" name="url" class="form-control-custom" value="<?php echo htmlspecialchars($editItem['url']); ?>" required></div>
            <div><label class="form-label-custom">Target</label>
              <select name="target" class="form-control-custom">
                <option value="_self" <?php echo $editItem['target']=='_self'?'selected':''; ?>>Same Tab</option>
                <option value="_blank" <?php echo $editItem['target']=='_blank'?'selected':''; ?>>New Tab</option>
              </select>
            </div>
            <div><label class="form-label-custom">Icon</label><input type="text" name="icon" class="form-control-custom" value="<?php echo htmlspecialchars($editItem['icon']??''); ?>"></div>
            <div><label class="form-label-custom">Sort Order</label><input type="number" name="sort_order" class="form-control-custom" value="<?php echo $editItem['sort_order']; ?>"></div>
            <div style="display:flex;align-items:flex-end;">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" <?php echo $editItem['is_active']?'checked':''; ?> style="width:18px;height:18px;">
                <span style="font-weight:600;font-size:0.85rem;color:#555;">Active</span>
              </label>
            </div>
          </div>
          <div style="margin-top:16px;display:flex;gap:10px;justify-content:flex-end;">
            <a href="navigation-manager.php" class="btn-modern-primary" style="background:#888;box-shadow:none;">Cancel</a>
            <button type="submit" name="update_nav" class="btn-modern-primary"><i class="bi bi-check2"></i> Update</button>
          </div>
        </form>
      </div>
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
  })();
  </script>
</body>
</html>
<?php $conn->close(); ?>
