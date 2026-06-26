<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once dirname(__FILE__) . '/connection.php';

function getNotificationCounts() {
    global $conn;
    $data = [];
    
    $msg_query = "SELECT COUNT(*) as cnt FROM `tbl_student_message` WHERE 1";
    $msg_run = mysqli_query($conn, $msg_query);
    $data['messages'] = mysqli_fetch_assoc($msg_run)['cnt'] ?? 0;
    
    $app_query = "SELECT COUNT(*) as cnt FROM `tbl_apply_student` WHERE Status = 'pending'";
    $app_run = mysqli_query($conn, $app_query);
    $data['applications'] = mysqli_fetch_assoc($app_run)['cnt'] ?? 0;
    
    $contact_query = "SELECT COUNT(*) as cnt FROM `tbl_contact_messages` WHERE 1";
    $contact_run = mysqli_query($conn, $contact_query);
    $data['contacts'] = mysqli_fetch_assoc($contact_run)['cnt'] ?? 0;


    $data['total'] = $data['messages'] + $data['applications'] + $data['contacts'];
    $data['total_messages'] = $data['messages'] + $data['contacts'];
    
    return $data;
}

$notifData = getNotificationCounts();
?>
<li class="nav-item" style="position:relative;">
  <a class="nav-link nav-profile d-flex align-items-center pe-0" href="studentMessage.php" style="color:#0F172A;font-weight:600;position:relative;">
    <i class="bi bi-envelope" style="font-size:1.3rem;"></i>
    <?php if($notifData['total_messages'] > 0): ?>
      <span class="notif-badge"><?php echo $notifData['total_messages']; ?></span>
    <?php endif; ?>
  </a>
</li>
<li class="nav-item dropdown" style="position:relative;">
  <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" style="color:#0F172A;font-weight:600;position:relative;">
    <i class="bi bi-bell" style="font-size:1.3rem;"></i>
    <?php if($notifData['total'] > 0): ?>
      <span class="notif-badge"><?php echo $notifData['total']; ?></span>
    <?php endif; ?>
  </a>
  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" style="border-radius:12px;border:1px solid #e8f0f5;box-shadow:0 8px 30px rgba(0,0,0,0.1);min-width:260px;padding:8px;">
    <li class="dropdown-header" style="padding:8px 14px;"><h6 style="font-weight:700;font-size:0.9rem;">Notifications</h6></li>
    <li><hr class="dropdown-divider" style="margin:4px 0;"></li>
    <li><a class="dropdown-item d-flex align-items-center" href="studentMessage.php" style="border-radius:8px;padding:10px 14px;font-size:0.85rem;">
      <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#eef2ff;color:#6366f1;margin-right:10px;flex-shrink:0;"><i class="bi bi-chat-dots"></i></span>
      <span><strong><?php echo $notifData['messages']; ?></strong> Student Messages</span>
    </a></li>
      
    <li><a class="dropdown-item d-flex align-items-center" href="studentApplication.php" style="border-radius:8px;padding:10px 14px;font-size:0.85rem;">
      <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#fef3c7;color:#d97706;margin-right:10px;flex-shrink:0;"><i class="bi bi-folder-check"></i></span>
      <span><strong><?php echo $notifData['applications']; ?></strong> Pending Applications</span>
    </a></li>
    <li><a class="dropdown-item d-flex align-items-center" href="contact-messages.php" style="border-radius:8px;padding:10px 14px;font-size:0.85rem;">
      <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:#f0fdfa;color:#14b8a6;margin-right:10px;flex-shrink:0;"><i class="bi bi-envelope"></i></span>
      <span><strong><?php echo $notifData['contacts']; ?></strong> Contact Messages</span>
    </a></li>
  </ul>
</li>
