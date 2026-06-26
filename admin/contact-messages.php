<?php
session_start();
include('includes/connection.php');
error_reporting(0);
if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
} else {

// Add replied column if it doesn't exist
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM tbl_contact_messages LIKE 'replied'");
if (mysqli_num_rows($checkCol) == 0) {
    mysqli_query($conn, "ALTER TABLE tbl_contact_messages ADD COLUMN `replied` TINYINT(1) DEFAULT 0 AFTER `Message`");
}

// Handle reply submission
if (isset($_POST['reply_submit']) && isset($_POST['reply_id'])) {
    $replyId = (int)$_POST['reply_id'];
    $replyMessage = trim($_POST['reply_message'] ?? '');
    
    if (!empty($replyMessage)) {
        $msgQuery = mysqli_query($conn, "SELECT * FROM tbl_contact_messages WHERE id = '$replyId'");
        $msgRow = mysqli_fetch_assoc($msgQuery);
        
        if ($msgRow) {
            require_once __DIR__ . '/../includes/smtp-config.php';
            try {
                $mail = getMailer();
                $mail->addAddress($msgRow['Email'], $msgRow['FullName']);
                $mail->Subject = 'Re: Your Message to GIHEKE TSS';
                $mail->Body = '<h3>Dear ' . htmlspecialchars($msgRow['FullName']) . ',</h3>'
                    . '<p>Thank you for contacting GIHEKE TSS.</p>'
                    . '<hr><p><strong>Your original message:</strong><br>' . nl2br(htmlspecialchars($msgRow['Message'])) . '</p>'
                    . '<hr><p><strong>Our reply:</strong></p>'
                    . '<p>' . nl2br(htmlspecialchars($replyMessage)) . '</p>'
                    . '<br><p>Best regards,<br>GIHEKE TSS Administration</p>';
                $mail->send();
                mysqli_query($conn, "UPDATE tbl_contact_messages SET replied = 1 WHERE id = '$replyId'");
                header('location: contact-messages.php?msg=Reply+Sent+Successfully');
                exit;
            } catch (Exception $e) {
                header('location: contact-messages.php?error=Failed+to+send+reply');
                exit;
            }
        } else {
            header('location: contact-messages.php?error=Message+not+found');
            exit;
        }
    } else {
        header('location: contact-messages.php?error=Reply+message+cannot+be+empty');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Contact Messages - Admin GIHEKE TSS</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between" style="width:100%;">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Administration</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn" id="sidebarToggle"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center" style="list-style:none;margin:0;padding:0;gap:4px;">
        <?php include('includes/notifications.php'); ?>
        <li class="nav-item dropdown pe-3">
          <?php
            $id = $_SESSION['admin_id'];
            $FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
            $First = mysqli_fetch_array($FirstChar);
            $Char = strtoupper($First['FirstName']);
          ?>
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="admin-img/<?php echo $First['ImageUrl']; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo substr($Char, 0,1) .". ".$First['LastName']; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header"><h6><?php echo $First['FirstName']." ". $First['LastName']; ?></h6><span>School Admin</span></li>
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
      <h1>Contact Messages</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Contact Messages</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="admin-content">
        <?php if(isset($_GET['msg'])): ?>
          <div class="alert-modern alert-success"><i class="bi bi-check-circle"></i> <?php echo htmlentities($_GET['msg']); ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
          <div class="alert-modern alert-danger"><i class="bi bi-exclamation-triangle"></i> <?php echo htmlentities($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="admin-section-card">
          <div class="admin-section-header">
            <h3><i class="bi bi-envelope me-2" style="color:#525FE1;"></i>Messages from Contact Form</h3>
          </div>
          <div class="table-responsive-custom">
            <table class="table-modern">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Full Name</th>
                  <th>Email</th>
                  <th>Message</th>
                  <th>Date Sent</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $sql = "SELECT * FROM `tbl_contact_messages` ORDER BY id DESC";
                  $result = mysqli_query($conn, $sql);
                  $count = 1;
                  while($row = mysqli_fetch_array($result)):
                ?>
                  <tr>
                    <td><?php echo $count++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['FullName']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['Email']); ?></td>
                    <td style="max-width:300px;"><?php echo htmlspecialchars($row['Message']); ?></td>
                    <td><?php echo date('M d, Y h:i A', strtotime($row['CreatedAt'])); ?></td>
                    <td>
                      <?php if($row['replied'] == 1): ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Replied</span>
                      <?php else: ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <button class="btn-action btn-reply-modern" data-bs-toggle="modal" data-bs-target="#replyModal"
                        data-id="<?php echo $row['id']; ?>"
                        data-name="<?php echo htmlspecialchars($row['FullName']); ?>"
                        data-email="<?php echo htmlspecialchars($row['Email']); ?>"
                        data-message="<?php echo htmlspecialchars($row['Message']); ?>">
                        <i class="bi bi-reply"></i> Reply
                      </button>
                      <a href="deletecontents.php?deleteContactId=<?php echo $row['id']; ?>" class="btn-action btn-delete-modern" onclick="return confirm('Delete this message?')"><i class="bi bi-trash"></i> Delete</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
                <?php if(mysqli_num_rows($result) == 0): ?>
                  <tr><td colspan="7" class="text-center" style="padding:40px;color:#64748B;">No contact messages yet</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Reply Modal -->
  <div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form method="post" action="contact-messages.php">
          <input type="hidden" name="reply_id" id="reply_id" value="">
          <div class="modal-header">
            <h5 class="modal-title" id="replyModalLabel"><i class="bi bi-reply-fill me-2"></i>Reply to Message</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="fw-bold">To:</label>
              <p id="replyTo" class="mb-0" style="color:#525FE1;"></p>
            </div>
            <div class="mb-3">
              <label class="fw-bold">Original Message:</label>
              <div id="originalMessage" class="p-3" style="background:#f8f9fa;border-radius:8px;border-left:4px solid #525FE1;margin-top:4px;"></div>
            </div>
            <div class="mb-3">
              <label for="reply_message" class="form-label fw-bold">Your Reply:</label>
              <textarea name="reply_message" id="reply_message" class="form-control" rows="6" placeholder="Type your reply here..." required style="border-radius:8px;border:2px solid #e2e8f0;padding:12px;"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="reply_submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Send Reply</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> Copyright <strong><span>GIHEKE TSS</span></strong>. All Rights Reserved.
    <div class="credits">Developed by <a href="https://devoma.vercel.app" target="_blank">Omar MBONABUCYA</a></div>
  </footer>

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

    // Reply modal data injection
    const replyModal = document.getElementById('replyModal');
    if (replyModal) {
      replyModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const email = button.getAttribute('data-email');
        const message = button.getAttribute('data-message');
        document.getElementById('reply_id').value = id;
        document.getElementById('replyTo').textContent = name + ' (' + email + ')';
        document.getElementById('originalMessage').textContent = message;
      });
    }
  })();
  </script>
  <style>
  .btn-reply-modern {
    background: linear-gradient(135deg, #525FE1, #7C3AED);
    color: #fff;
    border: none;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s;
    margin-right: 4px;
  }
  .btn-reply-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(82, 95, 225, 0.3);
    color: #fff;
  }
  </style>
</body>
</html>
<?php } ?>
