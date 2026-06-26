<?php
session_start();
include('includes/connection.php');
error_reporting(0);
if (!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
    exit;
}

$id = $_SESSION['admin_id'];
$FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
$First = mysqli_fetch_array($FirstChar);
$Char = strtoupper($First['FirstName']);

// Filter by action type
$actionFilter = isset($_GET['action']) ? mysqli_real_escape_string($conn, $_GET['action']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where = '';
if ($actionFilter) {
    $where .= " AND l.`action` = '$actionFilter'";
}
if ($search) {
    $where .= " AND (l.`description` LIKE '%$search%' OR l.`ip_address` LIKE '%$search%')";
}

$logQuery = mysqli_query($conn, "SELECT l.*, a.FirstName, a.LastName 
                                  FROM `tbl_activity_logs` l 
                                  LEFT JOIN `tbl_admins` a ON l.user_id = a.id
                                  WHERE 1=1 $where 
                                  ORDER BY l.`created_at` DESC 
                                  LIMIT 200");

// Get distinct action types for filter
$actionsQuery = mysqli_query($conn, "SELECT DISTINCT `action` FROM `tbl_activity_logs` ORDER BY `action` ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Activity Logs</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <style>
    .logs-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); padding: 24px; border: 1px solid rgba(0,0,0,0.04); }
    .filter-bar { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 20px; }
    .filter-bar select, .filter-bar input { padding: 8px 14px; border: 2px solid #e8e8e8; border-radius: 10px; font-size: 0.85rem; background: #fafafa; }
    .filter-bar select:focus, .filter-bar input:focus { border-color: #525FE1; outline: none; }
    .filter-bar .btn-filter { padding: 8px 18px; border-radius: 10px; font-weight: 600; font-size: 0.85rem; border: none; background: #525FE1; color: #fff; }
    .filter-bar .btn-filter:hover { background: #3D47C9; }
    .log-entry { display: flex; gap: 16px; padding: 14px 0; border-bottom: 1px solid #f5f5f5; align-items: flex-start; }
    .log-entry:last-child { border-bottom: none; }
    .log-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .log-icon.create { background: #e8f5e9; color: #2e7d32; }
    .log-icon.update { background: #e3f2fd; color: #1565c0; }
    .log-icon.delete { background: #fce4ec; color: #c62828; }
    .log-icon.upload { background: #fff3e0; color: #e65100; }
    .log-icon.default { background: #f3e5f5; color: #6a1b9a; }
    .log-content { flex: 1; min-width: 0; }
    .log-content .action { font-weight: 700; font-size: 0.88rem; color: #333; text-transform: capitalize; }
    .log-content .desc { font-size: 0.82rem; color: #666; margin-top: 2px; }
    .log-content .meta { font-size: 0.75rem; color: #aaa; margin-top: 4px; }
    .log-content .meta span { margin-right: 16px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state i { font-size: 3rem; color: #d0d5dd; margin-bottom: 16px; display: block; }
    .badge-action { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 0.72rem; font-weight: 600; }
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
        <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">Activity Logs</h1>
        <nav><ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item active" style="color:#888;">Activity Logs</li>
        </ol></nav>
      </div>

      <div class="logs-card">
        <form method="get" class="filter-bar">
          <select name="action">
            <option value="">All Actions</option>
            <?php while ($a = mysqli_fetch_assoc($actionsQuery)): ?>
              <option value="<?php echo htmlspecialchars($a['action']); ?>" <?php echo $actionFilter===$a['action']?'selected':''; ?>><?php echo htmlspecialchars(ucwords(str_replace('_',' ',$a['action']))); ?></option>
            <?php endwhile; ?>
          </select>
          <input type="text" name="search" placeholder="Search logs..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit" class="btn-filter"><i class="bi bi-funnel"></i> Filter</button>
          <?php if ($actionFilter || $search): ?>
            <a href="activity-logs.php" class="btn-filter" style="background:#888;text-decoration:none;">Clear</a>
          <?php endif; ?>
        </form>

        <?php if (mysqli_num_rows($logQuery) > 0): ?>
          <?php while ($log = mysqli_fetch_assoc($logQuery)): 
            $actionType = $log['action'];
            if (strpos($actionType, 'add') === 0 || strpos($actionType, 'create') === 0 || strpos($actionType, 'upload') === 0) $iconClass = 'create';
            elseif (strpos($actionType, 'update') === 0 || strpos($actionType, 'edit') === 0) $iconClass = 'update';
            elseif (strpos($actionType, 'delete') === 0 || strpos($actionType, 'remove') === 0) $iconClass = 'delete';
            elseif (strpos($actionType, 'upload') === 0) $iconClass = 'upload';
            else $iconClass = 'default';
            $userName = ($log['FirstName'] ?? '') ? $log['FirstName'] . ' ' . $log['LastName'] : 'System';
          ?>
          <div class="log-entry">
            <div class="log-icon <?php echo $iconClass; ?>">
              <?php if ($iconClass === 'create'): ?><i class="bi bi-plus-circle"></i>
              <?php elseif ($iconClass === 'update'): ?><i class="bi bi-pencil"></i>
              <?php elseif ($iconClass === 'delete'): ?><i class="bi bi-trash"></i>
              <?php elseif ($iconClass === 'upload'): ?><i class="bi bi-upload"></i>
              <?php else: ?><i class="bi bi-record-circle"></i>
              <?php endif; ?>
            </div>
            <div class="log-content">
              <div class="action"><?php echo htmlspecialchars(ucwords(str_replace('_',' ',$actionType))); ?></div>
              <div class="desc"><?php echo htmlspecialchars($log['description'] ?? ''); ?></div>
              <div class="meta">
                <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($userName); ?></span>
                <span><i class="bi bi-clock"></i> <?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?></span>
                <span><i class="bi bi-globe"></i> <?php echo $log['ip_address']; ?></span>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="bi bi-journal-text"></i>
            <p>No activity logs yet. Logs will appear as you manage the site.</p>
          </div>
        <?php endif; ?>
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
