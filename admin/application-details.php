<?php
session_start();
include('includes/connection.php');
error_reporting(E_ERROR | E_PARSE);
if (!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
    exit;
}

$app_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$app_id) {
    header('location:studentApplication.php');
    exit;
}

$app = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tbl_apply_student WHERE id='$app_id'"));
if (!$app) {
    header('location:studentApplication.php?error=Application not found');
    exit;
}

$id = $_SESSION['admin_id'];
$admin_row = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'"));
$Char = strtoupper($admin_row['FirstName']);
$reportPath = '../Student Report/' . $app['SchoolReport'];

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
                $body = '<h3>Dear ' . htmlspecialchars($name) . ',</h3><p>Congratulations! Your application to <strong>GIHEKE Technical Secondary School</strong> has been <strong>approved</strong>.</p>';
                if ($reason) $body .= '<p><strong>Admin Comment:</strong> ' . nl2br(htmlspecialchars($reason)) . '</p>';
                $body .= '<p>Please visit the school for further registration instructions.</p><br><p>Best regards,<br>GIHEKE TSS Administration</p>';
                $mail->Body = $body;
                $mail->send();
            } catch (\Exception $e) {}
        }
        header("location: application-details.php?id=$aid&msg=Application approved successfully");
    } else {
        header("location: application-details.php?id=$aid&error=Failed to approve");
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
                $body = '<h3>Dear ' . htmlspecialchars($name) . ',</h3><p>Thank you for your interest in <strong>GIHEKE Technical Secondary School</strong>.</p><p>After careful review, we regret to inform you that your application was <strong>not successful</strong> at this time.</p>';
                if ($reason) $body .= '<p><strong>Reason:</strong> ' . nl2br(htmlspecialchars($reason)) . '</p>';
                $body .= '<p>You may re-apply in the next admissions cycle.</p><br><p>Best regards,<br>GIHEKE TSS Administration</p>';
                $mail->Body = $body;
                $mail->send();
            } catch (\Exception $e) {}
        }
        header("location: application-details.php?id=$rid&msg=Application rejected");
    } else {
        header("location: application-details.php?id=$rid&error=Failed to reject");
    }
    exit;
}

if (isset($_POST['review_id'])) {
    $rid = intval($_POST['review_id']);
    $reason = mysqli_real_escape_string($conn, $_POST['review_reason'] ?? '');
    $update = mysqli_query($conn, "UPDATE tbl_apply_student SET Status='under_review', AdminFeedback='$reason' WHERE id='$rid'");
    if ($update) {
        $sel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT FirstName, LastName, Email FROM tbl_apply_student WHERE id='$rid'"));
        if ($sel) {
            $name = $sel['FirstName'] . ' ' . $sel['LastName'];
            require_once __DIR__ . '/../includes/smtp-config.php';
            try {
                $mail = getMailer();
                $mail->addAddress($sel['Email'], $name);
                $mail->Subject = 'GIHEKE TSS - Application Under Review';
                $body = '<h3>Dear ' . htmlspecialchars($name) . ',</h3><p>Your application to <strong>GIHEKE Technical Secondary School</strong> is currently <strong>under review</strong>.</p>';
                if ($reason) $body .= '<p><strong>Admin Comment:</strong> ' . nl2br(htmlspecialchars($reason)) . '</p>';
                $body .= '<p>We will get back to you shortly.</p><br><p>Best regards,<br>GIHEKE TSS Administration</p>';
                $mail->Body = $body;
                $mail->send();
            } catch (\Exception $e) {}
        }
        header("location: application-details.php?id=$rid&msg=Status updated to Under Review");
    } else {
        header("location: application-details.php?id=$rid&error=Failed to update status");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Application Details - GIHEKE TSS</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <style>
    .detail-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); padding: 32px; border: 1px solid rgba(0,0,0,0.04); }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .info-group { margin-bottom: 16px; }
    .info-group label { display: block; font-size: 0.75rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
    .info-group .value { font-size: 0.95rem; font-weight: 600; color: #0F172A; padding: 8px 12px; background: #f8fafc; border-radius: 8px; }
    .badge-status { display: inline-block; padding: 4px 16px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; }
    .badge-status.pending { background: #fef3c7; color: #d97706; }
    .badge-status.approved { background: #e8f5e9; color: #2e7d32; }
    .badge-status.rejected { background: #fce4ec; color: #c62828; }
    .badge-status.under_review { background: #e3f2fd; color: #1565c0; }
    .section-title { font-size: 1.1rem; font-weight: 800; color: #3D47C9; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #f0f0f0; display: flex; align-items: center; gap: 10px; }
    @media (max-width: 768px) { .info-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between" style="width:100%;">
      <a href="index.php" class="logo d-flex align-items-center"><img src="assets/img/logo.png" alt=""><span class="d-none d-lg-block" style="font-weight:800; color:#0F172A; font-size:1.1rem;">GIHEKE <span style="color:#525FE1;">Admin</span></span></a>
      <i class="bi bi-list toggle-sidebar-btn" id="sidebarToggle"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center" style="list-style:none;margin:0;padding:0;gap:6px;">
        <?php include('includes/notifications.php'); ?>
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" style="color:#0F172A; font-weight:600;">
            <img src="admin-img/<?php echo $admin_row['ImageUrl']; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block" style="margin-left:8px;"><?php echo substr($Char,0,1) .". ".htmlspecialchars($admin_row['LastName']); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" style="border-radius:12px; border:1px solid #e8f0f5; box-shadow: 0 8px 30px rgba(0,0,0,0.1);">
            <li class="dropdown-header"><h6 style="font-weight:700;"><?php echo htmlspecialchars($admin_row['FirstName']." ".$admin_row['LastName']); ?></h6><span style="color:#525FE1;">School Admin</span></li>
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
        <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">Application #<?php echo $app_id; ?></h1>
        <nav><ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item"><a href="studentApplication.php" style="color:#525FE1;">Applications</a></li>
          <li class="breadcrumb-item active" style="color:#888;">Details</li>
        </ol></nav>
      </div>

      <div class="detail-card">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div class="section-title" style="border:none;margin:0;padding:0;"><i class="bi bi-person-vcard"></i> Applicant Information</div>
          <span class="badge-status <?php echo strtolower($app['Status']); ?>"><?php echo htmlspecialchars($app['Status']); ?></span>
        </div>

        <div class="info-grid">
          <div class="info-group"><label>First Name</label><div class="value"><?php echo htmlspecialchars($app['FirstName']); ?></div></div>
          <div class="info-group"><label>Last Name</label><div class="value"><?php echo htmlspecialchars($app['LastName']); ?></div></div>
          <div class="info-group"><label>Email</label><div class="value"><?php echo htmlspecialchars($app['Email']); ?></div></div>
          <div class="info-group"><label>Contact</label><div class="value"><?php echo htmlspecialchars($app['Contact']); ?></div></div>
        </div>

        <div class="section-title" style="margin-top:28px;"><i class="bi bi-building"></i> Previous School</div>
        <div class="info-grid">
          <div class="info-group"><label>School Name</label><div class="value"><?php echo htmlspecialchars($app['SchoolName'] ?? 'N/A'); ?></div></div>
          <div class="info-group"><label>Trade Studied</label><div class="value"><?php echo htmlspecialchars($app['PreviousTrade'] ?? 'N/A'); ?></div></div>
          <div class="info-group"><label>Level</label><div class="value"><?php echo htmlspecialchars($app['PreviousLevel'] ?? 'N/A'); ?></div></div>
          <div class="info-group"><label>School Report</label><div class="value">
            <?php if (!empty($app['SchoolReport'])): ?>
              <a href="<?php echo htmlspecialchars($reportPath); ?>" target="_blank" class="btn btn-sm btn-primary" style="border-radius:8px;"><i class="bi bi-file-text"></i> View Report</a>
            <?php else: ?>
              <span class="text-muted">Not uploaded</span>
            <?php endif; ?>
          </div></div>
        </div>

        <div class="section-title" style="margin-top:28px;"><i class="bi bi-mortarboard"></i> Applied Program</div>
        <div class="info-grid">
          <div class="info-group"><label>Chosen Trade</label><div class="value"><?php echo htmlspecialchars($app['SchoolTrade']); ?></div></div>
          <div class="info-group"><label>Chosen Level</label><div class="value"><?php echo htmlspecialchars($app['SchoolLevel']); ?></div></div>
        </div>

        <div class="section-title" style="margin-top:28px;"><i class="bi bi-chat-quote"></i> Applicant Message</div>
        <div style="padding:16px;background:#f8fafc;border-radius:12px;font-size:0.92rem;color:#334155;line-height:1.6;">
          <?php echo nl2br(htmlspecialchars($app['Message'] ?? 'No message provided.')); ?>
        </div>

        <?php if (!empty($app['AdminFeedback'])): ?>
        <div class="section-title" style="margin-top:28px;"><i class="bi bi-chat-square-text"></i> Admin Feedback</div>
        <div style="padding:16px;background:#eef2ff;border-radius:12px;font-size:0.92rem;color:#334155;line-height:1.6;border-left:4px solid #525FE1;">
          <?php echo nl2br(htmlspecialchars($app['AdminFeedback'])); ?>
        </div>
        <?php endif; ?>

        <div style="margin-top:28px;display:flex;gap:10px;flex-wrap:wrap;">
          <a href="studentApplication.php?status=<?php echo htmlspecialchars(strtolower($app['Status'])); ?>" class="btn btn-secondary" style="border-radius:8px;"><i class="bi bi-arrow-left"></i> Back to Applications</a>
          <?php if (in_array($app['Status'], ['pending', 'under_review'])): ?>
            <button type="button" class="btn btn-success" style="border-radius:8px;" data-bs-toggle="modal" data-bs-target="#approveModal"><i class="bi bi-check-lg"></i> Approve</button>
            <button type="button" class="btn btn-danger" style="border-radius:8px;" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bi bi-x-lg"></i> Reject</button>
          <?php endif; ?>
          <?php if ($app['Status'] === 'pending'): ?>
            <button type="button" class="btn btn-warning" style="border-radius:8px;" data-bs-toggle="modal" data-bs-target="#reviewModal"><i class="bi bi-hourglass-split"></i> Under Review</button>
          <?php endif; ?>
        </div>

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
                  <p style="font-weight:600;margin-bottom:12px;">Approve application for <strong><?php echo htmlspecialchars($app['FirstName'].' '.$app['LastName']); ?></strong>?</p>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Reason for Approval (optional - sent via email)</label>
                    <textarea name="approve_reason" class="form-control" rows="3" placeholder="Write a brief reason or comment..."></textarea>
                  </div>
                  <input type="hidden" name="approve_id" value="<?php echo $app_id; ?>">
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
                  <p style="font-weight:600;margin-bottom:12px;">Reject application for <strong><?php echo htmlspecialchars($app['FirstName'].' '.$app['LastName']); ?></strong>?</p>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea name="reject_reason" class="form-control" rows="3" placeholder="Explain why the application is being rejected..." required></textarea>
                  </div>
                  <input type="hidden" name="reject_id" value="<?php echo $app_id; ?>">
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0;">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
                  <button type="submit" class="btn btn-danger" style="border-radius:8px;padding:8px 24px;"><i class="bi bi-x-lg"></i> Reject</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Under Review Modal -->
        <div class="modal fade" id="reviewModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;">
              <div class="modal-header" style="border-bottom:1px solid #e2e8f0;">
                <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-hourglass-split text-warning me-2"></i>Mark as Under Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <form method="post">
                <div class="modal-body">
                  <p style="font-weight:600;margin-bottom:12px;">Mark application for <strong><?php echo htmlspecialchars($app['FirstName'].' '.$app['LastName']); ?></strong> as <strong>Under Review</strong>?</p>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Comment (optional - sent via email)</label>
                    <textarea name="review_reason" class="form-control" rows="3" placeholder="Add a comment..."></textarea>
                  </div>
                  <input type="hidden" name="review_id" value="<?php echo $app_id; ?>">
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0;">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
                  <button type="submit" class="btn btn-warning" style="border-radius:8px;padding:8px 24px;"><i class="bi bi-hourglass-split"></i> Under Review</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> Copyright <strong><span>GIHEKE TSS</span></strong>. All Rights Reserved.
    <div class="credits">Developed by <a href="https://devoma.vercel.app" target="_blank">Omar MBONABUCYA</a></div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center" id="backToTopAdmin"><i class="bi bi-arrow-up-short"></i></a>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
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
  <?php if (isset($_GET['msg'])): ?>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    GihekeToast.showModal({title:'Success', message: '<?php echo htmlspecialchars($_GET['msg']); ?>', type:'success', buttonText:'OK'});
  });
  </script>
  <?php endif; ?>
  <?php if (isset($_GET['error'])): ?>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    GihekeToast.showToast({title:'Notice', message: '<?php echo htmlspecialchars($_GET['error']); ?>', type:'error'});
  });
  </script>
  <?php endif; ?>
  </script>
</body>
</html>
