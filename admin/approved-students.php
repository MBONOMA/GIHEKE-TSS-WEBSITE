<?php
session_start();
include('includes/connection.php');
error_reporting(E_ERROR | E_PARSE);
if (!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
    exit;
}

$id = $_SESSION['admin_id'];
$FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
$First = mysqli_fetch_array($FirstChar);
$Char = strtoupper($First['FirstName']);

$cnt_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tbl_apply_student WHERE Status='approved'"))['c'];

if (isset($_GET['deleteid'])) {
    $did = intval($_GET['deleteid']);
    mysqli_query($conn, "DELETE FROM tbl_apply_student WHERE id='$did' AND Status='approved'");
    header("location: approved-students.php?msg=Record deleted");
    exit;
}
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

$sql = "SELECT * FROM `tbl_apply_student` WHERE Status ='approved' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
$count = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Approved Students - GIHEKE TSS</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <style>
    .app-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); padding: 24px; border: 1px solid rgba(0,0,0,0.04); }
    .filter-tabs { display: flex; gap: 6px; margin-bottom: 20px; flex-wrap: wrap; }
    .filter-tabs a { padding: 8px 18px; border-radius: 10px; font-weight: 600; font-size: 0.82rem; text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
    .filter-tabs a.active { background: #525FE1; color: #fff; box-shadow: 0 4px 12px rgba(82,95,225,0.25); }
    .filter-tabs a:not(.active) { background: #f1f5f9; color: #64748b; }
    .filter-tabs a:not(.active):hover { background: #e2e8f0; }
    .app-table { width: 100%; border-collapse: collapse; }
    .app-table th { text-align: left; font-size: 0.75rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 14px; border-bottom: 2px solid #f0f0f0; white-space: nowrap; }
    .app-table td { padding: 12px 14px; border-bottom: 1px solid #f5f5f5; font-size: 0.88rem; color: #333; }
    .app-table tr:hover td { background: #fafbff; }
    .badge-status { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }
    .badge-status.approved { background: #e8f5e9; color: #2e7d32; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state i { font-size: 3rem; color: #d0d5dd; margin-bottom: 16px; display: block; }
    .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 20px; }
    .stat-card { background: #fff; border-radius: 16px; padding: 20px; text-align: center; border: 1px solid rgba(0,0,0,0.04); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .stat-card .num { font-size: 1.8rem; font-weight: 800; line-height: 1; color: #2e7d32; }
    .stat-card .label { font-size: 0.78rem; color: #888; margin-top: 4px; font-weight: 500; }
    .btn-action { padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.78rem; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s; }
    .btn-action.report { background: #e3f2fd; color: #1565c0; }
    .btn-action.report:hover { background: #bbdefb; }
    .btn-action.delete { background: #fce4ec; color: #c62828; }
    .btn-action.delete:hover { background: #f8d0d8; }
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
      <div class="pagetitle" style="margin-bottom:20px;">
        <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">Approved Students</h1>
        <nav><ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item"><a href="studentApplication.php" style="color:#525FE1;">Applications</a></li>
          <li class="breadcrumb-item active" style="color:#888;">Approved</li>
        </ol></nav>
      </div>

      <div class="stat-cards">
        <div class="stat-card"><div class="num"><?php echo $cnt_total; ?></div><div class="label">Approved Students</div></div>
      </div>

      <?php if ($msg): ?>
        <div class="alert-modern alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle me-1"></i> <strong>Done!</strong> <?php echo htmlentities($msg); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert-modern alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-octagon me-1"></i> <strong>Error!</strong> <?php echo htmlentities($error); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <div class="app-card">
        <div class="filter-tabs">
          <a href="studentApplication.php?status=approved"><i class="bi bi-check-circle"></i> All Approved</a>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
        <div style="overflow-x:auto;">
          <table class="app-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Previous School</th>
                <th>Applied For</th>
                <th>Message</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($call = mysqli_fetch_array($result)): 
                $reportPath = '../Student Report/' . $call['SchoolReport'];
              ?>
              <tr>
                <td style="color:#888;font-weight:600;"><?php echo $count++; ?></td>
                <td>
                  <strong><?php echo htmlspecialchars($call['FirstName'] . ' ' . $call['LastName']); ?></strong>
                  <div style="font-size:0.75rem;color:#888;"><?php echo htmlspecialchars($call['Email']); ?></div>
                </td>
                <td><?php echo htmlspecialchars($call['Contact']); ?></td>
                <td>
                  <div><?php echo htmlspecialchars($call['SchoolName']); ?></div>
                  <div style="font-size:0.75rem;color:#888;"><?php echo htmlspecialchars(($call['PreviousTrade'] ?? '') . ' - ' . ($call['PreviousLevel'] ?? '')); ?></div>
                </td>
                <td>
                  <strong><?php echo htmlspecialchars($call['SchoolTrade']); ?></strong>
                  <div style="font-size:0.75rem;color:#888;"><?php echo htmlspecialchars($call['SchoolLevel']); ?></div>
                </td>
                <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#888;font-size:0.82rem;" title="<?php echo htmlspecialchars($call['Message'] ?? ''); ?>">
                  <?php echo htmlspecialchars(strlen($call['Message']??'') > 40 ? substr($call['Message'],0,40).'...' : ($call['Message']??'-')); ?>
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <a href="<?php echo htmlspecialchars($reportPath); ?>" target="_blank" class="btn-action report"><i class="bi bi-file-text"></i> Report</a>
                    <a href="approved-students.php?deleteid=<?php echo $call['id']; ?>" class="btn-action delete" onclick="return confirm('Remove <?php echo htmlspecialchars(addslashes($call['FirstName'].' '.$call['LastName'])); ?> from approved list?')"><i class="bi bi-trash"></i></a>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <i class="bi bi-inbox"></i>
          <p>No approved students yet.</p>
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
<?php } ?>