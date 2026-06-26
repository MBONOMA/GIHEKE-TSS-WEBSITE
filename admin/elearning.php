<?php
session_start();
include('includes/connection.php');
error_reporting(E_ERROR | E_PARSE);
if (!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
    exit;
}

$id = $_SESSION['admin_id'];
$admin_row = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'"));
$Char = strtoupper($admin_row['FirstName']);
$apiBase = 'http://localhost:4000/api/v1';

$materialsJson = @file_get_contents($apiBase . '/elearning/materials');
$materials = [];
if ($materialsJson) {
    $data = json_decode($materialsJson, true);
    $materials = $data['data'] ?? $data ?? [];
    if (!is_array($materials)) $materials = [];
}

$programsJson = @file_get_contents($apiBase . '/programs');
$programs = [];
if ($programsJson) {
    $pdata = json_decode($programsJson, true);
    $programs = $pdata['data'] ?? $pdata ?? [];
    if (!is_array($programs)) $programs = [];
}

if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['material_id'])) {
    $mid = $_POST['material_id'];
    $opts = [
        'http' => [
            'method' => 'DELETE',
            'header' => 'Content-Type: application/json'
        ]
    ];
    @file_get_contents($apiBase . '/elearning/materials/' . $mid, false, stream_context_create($opts));
    header('location: elearning.php?msg=Material deleted successfully');
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $year = $_POST['year'] ?? '';
    $isPublic = isset($_POST['is_public']) ? 1 : 0;
    $programId = $_POST['program_id'] ?? '';

    $payload = [
        'title' => $title,
        'description' => $description,
        'category' => $category,
        'subject' => $subject,
        'year' => $year ? (int)$year : null,
        'isPublic' => $isPublic,
        'fileType' => 'document',
    ];
    if ($programId) $payload['programId'] = $programId;

    $ch = curl_init($apiBase . '/elearning/materials');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_USERPWD, '');
    $resp = curl_exec($ch);
    curl_close($ch);
    header('location: elearning.php?msg=Material created successfully');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>E-Learning Materials - GIHEKE TSS</title>
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
    .section-title { font-size: 1.1rem; font-weight: 800; color: #3D47C9; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #f0f0f0; display: flex; align-items: center; gap: 10px; }
    .btn-action { padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.78rem; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s; }
    .btn-add { background: #525FE1; color: #fff; }
    .btn-add:hover { background: #434cd6; color: #fff; }
    .btn-edit { background: #eef2ff; color: #525FE1; }
    .btn-edit:hover { background: #525FE1; color: #fff; }
    .btn-delete { background: #fce4ec; color: #c62828; }
    .btn-delete:hover { background: #c62828; color: #fff; }
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
        <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">E-Learning Materials</h1>
        <nav><ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item active" style="color:#888;">E-Learning</li>
        </ol></nav>
      </div>

      <?php if (isset($_GET['msg'])): ?>
      <div class="alert-modern alert-success alert-dismissible fade show" role="alert" style="margin-bottom:20px;">
        <i class="bi bi-check-circle me-1"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
      <div class="alert-modern alert-danger alert-dismissible fade show" role="alert" style="margin-bottom:20px;">
        <i class="bi bi-exclamation-octagon me-1"></i> <?php echo htmlspecialchars($_GET['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>

      <div class="detail-card">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div class="section-title" style="border:none;margin:0;padding:0;"><i class="bi bi-book"></i> All Materials</div>
          <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add Material</button>
        </div>

        <?php if (empty($materials)): ?>
        <div class="empty-state text-center py-5">
          <i class="bi bi-inbox" style="font-size:3rem;color:#d0d5dd;"></i>
          <p class="text-muted mt-2">No learning materials found.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="table table-modern">
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Category</th>
              <th>Subject</th>
              <th>Year</th>
              <th>Downloads</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($materials as $i => $m): ?>
            <tr>
              <td style="color:#888;font-weight:600;"><?php echo $i+1; ?></td>
              <td><strong><?php echo htmlspecialchars($m['title'] ?? ''); ?></strong></td>
              <td><?php echo htmlspecialchars($m['category'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($m['subject'] ?? '-'); ?></td>
              <td><?php echo htmlspecialchars($m['year'] ?? '-'); ?></td>
              <td><?php echo $m['downloads'] ?? 0; ?></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="../<?php echo htmlspecialchars($m['fileUrl'] ?? '#'); ?>" target="_blank" class="btn-action btn-edit"><i class="bi bi-eye"></i> View</a>
                  <form method="post" style="display:inline;" onsubmit="return confirm('Delete this material?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="material_id" value="<?php echo htmlspecialchars($m['id']); ?>">
                    <button type="submit" class="btn-action btn-delete"><i class="bi bi-trash"></i> Delete</button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
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

  <div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius:16px;">
        <div class="modal-header" style="border-bottom:1px solid #e2e8f0;">
          <h5 class="modal-title" style="font-weight:700;"><i class="bi bi-upload text-primary me-2"></i>Upload Learning Material</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <input type="hidden" name="action" value="create">
            <div class="mb-3">
              <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" required placeholder="e.g., Grade 10 Mathematics Notes">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Brief description..."></textarea>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                <input type="text" name="category" class="form-control" required placeholder="e.g., Notes, Past Papers, Textbook">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="e.g., Mathematics">
              </div>
            </div>
            <div class="row g-3 mt-1">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Program</label>
                <select name="program_id" class="form-select">
                  <option value="">Select Program</option>
                  <?php foreach ($programs as $p): ?>
                  <option value="<?php echo htmlspecialchars($p['id']); ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Year</label>
                <input type="number" name="year" class="form-control" value="<?php echo date('Y'); ?>">
              </div>
            </div>
            <div class="mb-3 mt-3">
              <label class="form-label fw-semibold">File (PDF/Document/Video)</label>
              <input type="file" name="file" class="form-control">
              <small class="text-muted">Note: File upload requires backend endpoint configured for file handling.</small>
            </div>
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="is_public" id="isPublicCheck">
              <label class="form-check-label" for="isPublicCheck">Public (visible to all users)</label>
            </div>
          </div>
          <div class="modal-footer" style="border-top:1px solid #e2e8f0;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-add"><i class="bi bi-cloud-upload"></i> Upload</button>
          </div>
        </form>
      </div>
    </div>
  </div>

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
