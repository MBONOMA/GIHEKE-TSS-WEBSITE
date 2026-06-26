<?php
session_start();
include('includes/connection.php');
include('includes/activity-log.php');
error_reporting(E_ERROR | E_PARSE);
if (!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
    exit;
}

$id = $_SESSION['admin_id'];
$FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
$First = mysqli_fetch_array($FirstChar);
$Char = strtoupper($First['FirstName']);

// Auto-create AdminFeedback column if missing
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM `tbl_apply_student` LIKE 'AdminFeedback'");
if (mysqli_num_rows($check_col) == 0) {
    mysqli_query($conn, "ALTER TABLE `tbl_apply_student` ADD `AdminFeedback` TEXT NULL DEFAULT NULL AFTER `Status`");
}

// Handle approve/reject via POST with admin reason
if (isset($_POST['approve_id'])) {
    $aid = intval($_POST['approve_id']);
    $reason = mysqli_real_escape_string($conn, $_POST['approve_reason'] ?? '');
    $update = mysqli_query($conn, "UPDATE tbl_apply_student SET Status='approved', AdminFeedback='$reason' WHERE id='$aid'");
    if ($update) {
        $sel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT FirstName, LastName, Email FROM tbl_apply_student WHERE id='$aid'"));
        if ($sel) {
            $name = $sel['FirstName'] . ' ' . $sel['LastName'];
            require_once __DIR__ . '/../includes/smtp-config.php';
            try {
                $mail = getMailer();
                $mail->addAddress($sel['Email'], $name);
                $mail->Subject = 'GIHEKE TSS - Application Approved';
                $body = '<h3>Dear ' . htmlspecialchars($name) . ',</h3>
                    <p>Congratulations! Your application to <strong>GIHEKE Technical Secondary School</strong> has been <strong>approved</strong>.</p>';
                if ($reason) {
                    $body .= '<p><strong>Admin Comment:</strong> ' . nl2br(htmlspecialchars($reason)) . '</p>';
                }
                $body .= '<p>Please visit the school for further registration instructions.</p>
                    <br><p>Best regards,<br>GIHEKE TSS Administration</p>';
                $mail->Body = $body;
                $mail->send();
            } catch (\Exception $e) {}
        }
        header("location: studentApplication.php?status=pending&msg=Application approved successfully");
    } else {
        header("location: studentApplication.php?status=pending&error=Failed to approve");
    }
    exit;
}

if (isset($_POST['reject_id'])) {
    $rid = intval($_POST['reject_id']);
    $reason = mysqli_real_escape_string($conn, $_POST['reject_reason'] ?? '');
    $update = mysqli_query($conn, "UPDATE tbl_apply_student SET Status='rejected', AdminFeedback='$reason' WHERE id='$rid'");
    if ($update) {
        $sel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT FirstName, LastName, Email FROM tbl_apply_student WHERE id='$rid'"));
        if ($sel) {
            $name = $sel['FirstName'] . ' ' . $sel['LastName'];
            require_once __DIR__ . '/../includes/smtp-config.php';
            try {
                $mail = getMailer();
                $mail->addAddress($sel['Email'], $name);
                $mail->Subject = 'GIHEKE TSS - Application Status';
                $body = '<h3>Dear ' . htmlspecialchars($name) . ',</h3>
                    <p>Thank you for your interest in <strong>GIHEKE Technical Secondary School</strong>.</p>
                    <p>After careful review, we regret to inform you that your application was <strong>not successful</strong> at this time.</p>';
                if ($reason) {
                    $body .= '<p><strong>Reason:</strong> ' . nl2br(htmlspecialchars($reason)) . '</p>';
                }
                $body .= '<p>You may re-apply in the next admissions cycle.</p>
                    <br><p>Best regards,<br>GIHEKE TSS Administration</p>';
                $mail->Body = $body;
                $mail->send();
            } catch (\Exception $e) {}
        }
        header("location: studentApplication.php?status=pending&msg=Application rejected");
    } else {
        header("location: studentApplication.php?status=pending&error=Failed to reject");
    }
    exit;
}

// Handle status toggles with modals (POST), or fallback to GET for Approved.php/Rejected.php compatibility
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'pending';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where = "WHERE 1=1";
if ($statusFilter === 'pending') $where .= " AND Status = 'pending'";
elseif ($statusFilter === 'approved') $where .= " AND Status = 'approved'";
elseif ($statusFilter === 'rejected') $where .= " AND Status = 'rejected'";

if ($search) {
    $where .= " AND (FirstName LIKE '%$search%' OR LastName LIKE '%$search%' OR Email LIKE '%$search%' OR Contact LIKE '%$search%' OR SchoolTrade LIKE '%$search%')";
}

$sql = "SELECT * FROM `tbl_apply_student` $where ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

// Count stats
$cnt_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM `tbl_apply_student` WHERE Status='pending'"))['c'];
$cnt_approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM `tbl_apply_student` WHERE Status='approved'"))['c'];
$cnt_rejected = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM `tbl_apply_student` WHERE Status='rejected'"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Student Applications - Site Management</title>
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
    .filter-tabs a .badge-c { background: rgba(0,0,0,0.12); padding: 1px 8px; border-radius: 10px; font-size: 0.72rem; }
    .filter-tabs a.active .badge-c { background: rgba(255,255,255,0.2); }
    .search-bar { display: flex; gap: 10px; margin-bottom: 20px; position: relative; }
    .search-bar input { flex: 1; padding: 10px 16px; border: 2px solid #e8e8e8; border-radius: 12px; font-size: 0.9rem; background: #fafafa; transition: all 0.2s; }
    .search-bar input:focus { border-color: #525FE1; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(82,95,225,0.08); }
    .search-bar button { padding: 10px 20px; border-radius: 12px; font-weight: 600; border: none; background: #525FE1; color: #fff; }
    .search-bar button:hover { background: #3D47C9; }
    .search-advanced { position: relative; flex: 1; }
    .search-advanced input { width: 100%; padding: 10px 44px 10px 42px; border: 2px solid #e8e8e8; border-radius: 12px; font-size: 0.9rem; background: #fafafa; transition: all 0.2s; }
    .search-advanced input:focus { border-color: #525FE1; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(82,95,225,0.08); }
    .search-advanced .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .search-advanced .clear-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 24px; height: 24px; border-radius: 50%; border: none; background: #e2e8f0; color: #475569; cursor: pointer; display: none; align-items: center; justify-content: center; font-size: 12px; }
    .search-advanced .clear-btn:hover { background: #cbd5e1; }
    .search-advanced.has-value .clear-btn { display: inline-flex; }
    .search-dropdown { display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); z-index: 1050; max-height: 320px; overflow-y: auto; }
    .search-dropdown.show { display: block; }
    .search-dropdown .sd-section { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; }
    .search-dropdown .sd-section:last-child { border-bottom: none; }
    .search-dropdown .sd-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .search-dropdown .sd-chip { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 13px; background: #f1f5f9; color: #334155; border: none; cursor: pointer; margin: 3px 4px 3px 0; transition: all 0.15s; }
    .search-dropdown .sd-chip:hover { background: #e0e7ff; color: #4338ca; }
    .search-dropdown .sd-item { width: 100%; text-align: left; padding: 10px 14px; border: none; background: transparent; font-size: 14px; color: #334155; cursor: pointer; border-radius: 8px; margin: 2px 0; }
    .search-dropdown .sd-item:hover { background: #eff6ff; color: #1d4ed8; }
    .search-dropdown .sd-item strong { color: #1e40af; }
    .no-results-box { text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px solid #e5e7eb; margin-top: 16px; }
    .no-results-box i { font-size: 40px; display: block; margin-bottom: 12px; color: #cbd5e1; }
    .no-results-box h4 { color: #475569; font-weight: 600; margin-bottom: 6px; }
    .no-results-box p { font-size: 13px; max-width: 360px; margin: 0 auto 16px; color: #64748b; }
    @media (max-width: 768px) {
      .search-advanced { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: #fff; z-index: 1060; padding: 12px; display: flex; flex-direction: column; }
      .search-advanced input { font-size: 16px; padding: 12px 44px 12px 42px; border-radius: 10px; }
      .search-advanced .search-dropdown { position: static; border-radius: 12px; margin-top: 10px; max-height: calc(100vh - 120px); }
      .search-mobile-close { display: inline-flex !important; }
    }
    .app-table { width: 100%; border-collapse: collapse; }
    .app-table th { text-align: left; font-size: 0.75rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 14px; border-bottom: 2px solid #f0f0f0; white-space: nowrap; }
    .app-table td { padding: 12px 14px; border-bottom: 1px solid #f5f5f5; font-size: 0.88rem; color: #333; }
    .app-table tr:hover td { background: #fafbff; }
    .app-table .actions { display: flex; gap: 6px; flex-wrap: nowrap; }
    .badge-status { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }
    .badge-status.pending { background: #fef3c7; color: #d97706; }
    .badge-status.approved { background: #e8f5e9; color: #2e7d32; }
    .badge-status.rejected { background: #fce4ec; color: #c62828; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state i { font-size: 3rem; color: #d0d5dd; margin-bottom: 16px; display: block; }
    .btn-action { padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.78rem; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s; }
    .btn-action.approve { background: #e8f5e9; color: #2e7d32; }
    .btn-action.approve:hover { background: #c8e6c9; }
    .btn-action.reject { background: #fce4ec; color: #c62828; }
    .btn-action.reject:hover { background: #f8d0d8; }
    .btn-action.report { background: #e3f2fd; color: #1565c0; }
    .btn-action.report:hover { background: #bbdefb; }
    .btn-action.view { background: #f3e5f5; color: #7b1fa2; }
    .btn-action.view:hover { background: #e1bee7; }
    .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 20px; }
    .stat-card { background: #fff; border-radius: 16px; padding: 20px; text-align: center; border: 1px solid rgba(0,0,0,0.04); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .stat-card .num { font-size: 1.8rem; font-weight: 800; line-height: 1; }
    .stat-card .label { font-size: 0.78rem; color: #888; margin-top: 4px; font-weight: 500; }
    .stat-card.pending .num { color: #d97706; }
    .stat-card.approved .num { color: #2e7d32; }
    .stat-card.rejected .num { color: #c62828; }
    .stat-card.total .num { color: #525FE1; }
    .modal-confirm { border-radius: 16px; border: none; }
    .modal-confirm .modal-header { border-bottom: 1px solid #f0f0f0; padding: 20px 24px; }
    .modal-confirm .modal-body { padding: 24px; }
    .modal-confirm .modal-footer { border-top: 1px solid #f0f0f0; padding: 16px 24px; }
    @media (max-width: 768px) { .app-table { font-size: 0.82rem; } .app-table th, .app-table td { padding: 8px 10px; } }
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
        <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">Student Applications</h1>
        <nav><ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item"><a href="site-settings.php" style="color:#525FE1;">Site Management</a></li>
          <li class="breadcrumb-item active" style="color:#888;">Applications</li>
        </ol></nav>
      </div>

      <!-- Stats -->
      <div class="stat-cards">
        <div class="stat-card total"><div class="num"><?php echo $cnt_pending + $cnt_approved + $cnt_rejected; ?></div><div class="label">Total Applications</div></div>
        <div class="stat-card pending"><div class="num"><?php echo $cnt_pending; ?></div><div class="label">Pending</div></div>
        <div class="stat-card approved"><div class="num"><?php echo $cnt_approved; ?></div><div class="label">Approved</div></div>
        <div class="stat-card rejected"><div class="num"><?php echo $cnt_rejected; ?></div><div class="label">Rejected</div></div>
      </div>

      <div class="app-card">
        <!-- Filter Tabs -->
        <div class="filter-tabs">
          <a href="?status=pending" class="<?php echo $statusFilter==='pending'?'active':''; ?>"><i class="bi bi-hourglass-split"></i> Pending <span class="badge-c"><?php echo $cnt_pending; ?></span></a>
          <a href="?status=approved" class="<?php echo $statusFilter==='approved'?'active':''; ?>"><i class="bi bi-check-circle"></i> Approved <span class="badge-c"><?php echo $cnt_approved; ?></span></a>
          <a href="?status=rejected" class="<?php echo $statusFilter==='rejected'?'active':''; ?>"><i class="bi bi-x-circle"></i> Rejected <span class="badge-c"><?php echo $cnt_rejected; ?></span></a>
          <a href="?status=all" class="<?php echo $statusFilter==='all'?'active':''; ?>"><i class="bi bi-list-ul"></i> All <span class="badge-c"><?php echo $cnt_pending + $cnt_approved + $cnt_rejected; ?></span></a>
        </div>

        <!-- Search -->
        <form method="get" class="search-bar">
          <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>">
          <div class="search-advanced" id="searchAdvancedApp">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="appSearchInput" name="search" placeholder="Search by name, email, trade..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="button" class="clear-btn" id="appSearchClear"><i class="bi bi-x"></i></button>
            <div class="search-dropdown" id="appSearchDropdown">
              <div class="sd-section">
                <div class="sd-label"><i class="bi bi-clock-history"></i> Recent Searches</div>
                <div id="appRecentList"><p style="font-size:12px;color:#94a3b8;">No recent searches</p></div>
              </div>
              <div class="sd-section">
                <div class="sd-label"><i class="bi bi-arrow-up-circle"></i> Trending</div>
                <div id="appTrendingList"></div>
              </div>
            </div>
          </div>
          <button type="submit"><i class="bi bi-search"></i> Search</button>
        </form>

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
              <th>Status</th>
              <th>Message</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $count = 1;
            while ($call = mysqli_fetch_array($result)):
              $reportPath = '../Student Report/' . $call['SchoolReport'];
            ?>
            <tr>
              <td style="color:#888;font-weight:600;"><?php echo $count; ?></td>
              <td>
                <strong><?php echo htmlspecialchars($call['FirstName'] . ' ' . $call['LastName']); ?></strong>
                <div style="font-size:0.75rem;color:#888;"><?php echo htmlspecialchars($call['Email']); ?></div>
              </td>
              <td><?php echo htmlspecialchars($call['Contact']); ?></td>
              <td>
                <div><?php echo htmlspecialchars($call['SchoolName']); ?></div>
                <div style="font-size:0.75rem;color:#888;"><?php echo htmlspecialchars($call['PreviousTrade'] . ' - ' . $call['PreviousLevel']); ?></div>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($call['SchoolTrade']); ?></strong>
                <div style="font-size:0.75rem;color:#888;"><?php echo htmlspecialchars($call['SchoolLevel']); ?></div>
              </td>
              <td><span class="badge-status <?php echo strtolower($call['Status']); ?>"><?php echo htmlspecialchars($call['Status']); ?></span></td>
              <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#888;font-size:0.82rem;" title="<?php echo htmlspecialchars($call['Message'] ?? ''); ?>">
                <?php echo htmlspecialchars(strlen($call['Message']??'') > 40 ? substr($call['Message'],0,40).'...' : ($call['Message']??'-')); ?>
              </td>
              <td>
                <div class="actions">
                  <a href="application-details.php?id=<?php echo $call['id']; ?>" class="btn-action view"><i class="bi bi-eye"></i> Details</a>
                  <a href="<?php echo htmlspecialchars($reportPath); ?>" target="_blank" class="btn-action report"><i class="bi bi-file-text"></i> Report</a>
                  <?php if ($call['Status'] === 'pending'): ?>
                    <button type="button" class="btn-action approve" onclick="openApproveModal(<?php echo $call['id']; ?>, '<?php echo htmlspecialchars(addslashes($call['FirstName'].' '.$call['LastName'])); ?>')"><i class="bi bi-check-lg"></i> Accept</button>
                    <button type="button" class="btn-action reject" onclick="openRejectModal(<?php echo $call['id']; ?>, '<?php echo htmlspecialchars(addslashes($call['FirstName'].' '.$call['LastName'])); ?>')"><i class="bi bi-x-lg"></i> Reject</button>
                  <?php elseif ($call['Status'] === 'approved'): ?>
                    <span style="font-size:0.75rem;color:#2e7d32;"><i class="bi bi-check-circle-fill"></i> Approved</span>
                  <?php elseif ($call['Status'] === 'rejected'): ?>
                    <span style="font-size:0.75rem;color:#c62828;"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php $count++; endwhile; ?>
          </tbody>
        </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <i class="bi bi-inbox"></i>
          <p>No <?php echo htmlspecialchars($statusFilter); ?> applications found.</p>
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

  <!-- Approve Modal -->
  <div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:16px;">
        <div class="modal-header" style="border-bottom:1px solid #e2e8f0;">
          <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-check-circle text-success me-2"></i>Approve Application</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <p id="approveName" style="font-weight:600;margin-bottom:12px;"></p>
            <div class="mb-3">
              <label class="form-label fw-semibold">Reason for Approval (optional - sent via email)</label>
              <textarea name="approve_reason" class="form-control" rows="3" placeholder="Write a brief reason or comment..."></textarea>
            </div>
            <input type="hidden" name="approve_id" id="approveId" value="">
          </div>
          <div class="modal-footer" style="border-top:1px solid #e2e8f0;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
            <button type="submit" class="btn btn-success" style="border-radius:8px;padding:8px 24px;"><i class="bi bi-check-lg"></i> Approve</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Reject Modal -->
  <div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:16px;">
        <div class="modal-header" style="border-bottom:1px solid #e2e8f0;">
          <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-x-circle text-danger me-2"></i>Reject Application</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post">
          <div class="modal-body">
            <p id="rejectName" style="font-weight:600;margin-bottom:12px;"></p>
            <div class="mb-3">
              <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
              <textarea name="reject_reason" class="form-control" rows="3" placeholder="Explain why the application is being rejected..." required></textarea>
            </div>
            <input type="hidden" name="reject_id" id="rejectId" value="">
          </div>
          <div class="modal-footer" style="border-top:1px solid #e2e8f0;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
            <button type="submit" class="btn btn-danger" style="border-radius:8px;padding:8px 24px;"><i class="bi bi-x-lg"></i> Reject</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/giheke-toast.js"></script>
  <script>
  function openApproveModal(id, name) {
    document.getElementById('approveId').value = id;
    document.getElementById('approveName').textContent = 'Approve ' + name + '?';
    new bootstrap.Modal(document.getElementById('approveModal')).show();
  }
  function openRejectModal(id, name) {
    document.getElementById('rejectId').value = id;
    document.getElementById('rejectName').textContent = 'Reject ' + name + '?';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
  }
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

    const params = new URLSearchParams(window.location.search);
    if (params.get('msg')) {
      GihekeToast.showModal({title:'Success', message: params.get('msg'), type:'success', buttonText:'OK'});
    } else if (params.get('error')) {
      GihekeToast.showToast({title:'Notice', message: params.get('error'), type:'error'});
    }
  })();

  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('appSearchInput');
    const searchClear = document.getElementById('appSearchClear');
    const searchDropdown = document.getElementById('appSearchDropdown');
    const searchWrapper = document.getElementById('searchAdvancedApp');
    const recentList = document.getElementById('appRecentList');
    const trendingList = document.getElementById('appTrendingList');
    let debounceTimer = null;

    const trending = ['Pending Review', 'Approved', 'Rejected', 'New Application', 'Qualified'];
    let recent = [];
    try { const stored = localStorage.getItem('appRecentSearches'); if (stored) recent = JSON.parse(stored).slice(0, 5); } catch {}

    function saveRecent(q) {
      recent = [q, ...recent.filter(r => r !== q)].slice(0, 5);
      localStorage.setItem('appRecentSearches', JSON.stringify(recent));
      renderRecent();
    }

    function renderRecent() {
      if (!recentList) return;
      if (recent.length === 0) {
        recentList.innerHTML = '<p style="font-size:12px;color:#94a3b8;">No recent searches</p>';
        return;
      }
      recentList.innerHTML = recent.map(r => `<button type="button" class="sd-chip" data-search="${r}">${r}</button>`).join('');
      recentList.querySelectorAll('.sd-chip').forEach(btn => {
        btn.addEventListener('click', () => { searchInput.value = btn.dataset.search; searchInput.form.submit(); });
      });
    }

    function renderTrending() {
      if (!trendingList) return;
      trendingList.innerHTML = trending.map(t => `<button type="button" class="sd-chip" data-search="${t}">${t}</button>`).join('');
      trendingList.querySelectorAll('.sd-chip').forEach(btn => {
        btn.addEventListener('click', () => { searchInput.value = btn.dataset.search; searchInput.form.submit(); });
      });
    }

    function highlight(text, q) {
      if (!q) return text;
      const idx = text.toLowerCase().indexOf(q.toLowerCase());
      if (idx === -1) return text;
      return text.slice(0, idx) + '<strong>' + text.slice(idx, idx + q.length) + '</strong>' + text.slice(idx + q.length);
    }

    function showDropdown() { if (searchDropdown) searchDropdown.classList.add('show'); }
    function hideDropdown() { if (searchDropdown) searchDropdown.classList.remove('show'); }

    if (searchInput) {
      searchInput.addEventListener('focus', () => { renderRecent(); renderTrending(); showDropdown(); });
      searchInput.addEventListener('input', function() {
        const val = this.value;
        if (searchWrapper) searchWrapper.classList.toggle('has-value', val.length > 0);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => this.form.submit(), 300);
        showDropdown();
      });
      searchInput.addEventListener('blur', () => setTimeout(hideDropdown, 150));
      searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { this.value = ''; hideDropdown(); this.form.submit(); }
      });
    }
    if (searchClear) {
      searchClear.addEventListener('click', () => {
        searchInput.value = '';
        if (searchWrapper) searchWrapper.classList.remove('has-value');
        hideDropdown();
        searchInput.form.submit();
      });
    }
    document.addEventListener('click', function(e) {
      if (searchWrapper && !searchWrapper.contains(e.target)) hideDropdown();
    });
    renderRecent();
    renderTrending();
  });
  </script>
</body>
</html>
<?php $conn->close(); ?>
