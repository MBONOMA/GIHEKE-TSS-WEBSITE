<?php
  session_start();
  include('includes/connection.php');
  error_reporting(E_ERROR | E_PARSE);
  if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Dashboard - GIHEKE TSS</title>

  <link href="../img/giheke logo.webp" rel="icon">
  <link href="../img/giheke logo.webp" rel="apple-touch-icon">

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">

</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between" style="width:100%;">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="" style="height:38px;border-radius:8px;">
        <span class="d-none d-lg-block" style="font-weight:800;color:#0F172A;font-size:1.1rem;">GIHEKE <span style="color:#525FE1;">Admin</span></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn" id="sidebarToggle"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center" style="list-style:none;margin:0;padding:0;gap:8px;">
        <?php include('includes/notifications.php'); ?>
        <li class="nav-item dropdown pe-3">
          <?php
            include 'includes/connection.php';
            $id = $_SESSION['admin_id'];
            $admin_res = mysqli_query($conn, "SELECT * FROM tbl_admins WHERE id = '$id'");
            $admin = mysqli_fetch_array($admin_res);
            $Char = strtoupper($admin['FirstName'] ?? 'A');
          ?>
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" style="color:#0F172A;font-weight:600;">
            <img src="admin-img/<?php echo $admin['ImageUrl'] ?? 'default.png'; ?>" alt="Profile" class="rounded-circle" style="width:36px;height:36px;border-radius:50%;border:2px solid #525FE1;">
            <span class="d-none d-md-block" style="margin-left:8px;"><?php echo substr($Char, 0,1) . ". " . ($admin['LastName'] ?? 'Admin'); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" style="border-radius:12px;border:1px solid #e8f0f5;box-shadow:0 8px 30px rgba(0,0,0,0.1);">
            <li class="dropdown-header"><h6 style="font-weight:700;"><?php echo ($admin['FirstName'] ?? '') . " " . ($admin['LastName'] ?? ''); ?></h6><span style="color:#525FE1;">School Admin</span></li>
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
      <h1><i class="bi bi-newspaper me-2" style="color:#525FE1;"></i>Manage News Posts</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Manage News</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="admin-content">
        <?php if(isset($_GET['msg']) || isset($_GET['delete'])): ?>
          <div class="alert-modern alert-success">
            <i class="bi bi-check-circle"></i> <?php echo htmlentities($_GET['msg'] ?? $_GET['delete']); ?>
          </div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
          <div class="alert-modern alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?php echo htmlentities($_GET['error']); ?>
          </div>
        <?php endif; ?>

        <div class="admin-section-card">
          <div class="admin-section-header">
            <h3><i class="bi bi-list-ul me-2" style="color:#525FE1;"></i>News Posts List</h3>
          </div>
          <div class="mb-3">
            <button id="deleteSelected" class="btn-modern btn-modern-danger">Delete Selected</button>
          </div>
          <div class="table-responsive-custom">
            <table class="table-modern">
              <thead>
                <tr>
                  <th><input type="checkbox" id="selectAll"></th>
                  <th>#</th>
                  <th>News Title</th>
                  <th>Category</th>
                  <th>Description</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  include 'includes/connection.php';
                  $sql = "SELECT tblposts.id AS postid, tblposts.PostTitle AS title,
                          tblposts.PostingDate AS CreationDate,
                          tbl_school_category.CategoryName AS category,
                          tbl_school_category.CategoryDescription as categoryDescription
                          FROM tblposts LEFT JOIN tbl_school_category ON tbl_school_category.id = tblposts.CategoryId
                          ORDER BY tblposts.PostingDate DESC";
                  $result = mysqli_query($conn, $sql);
                  $count = 1;
                  while($row = mysqli_fetch_array($result)) {
                ?>
                  <tr>
                    <td><input type="checkbox" class="select-item" value="<?php echo $row['postid']; ?>"></td>
                    <td><?php echo $count++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['categoryDescription']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($row['CreationDate'])); ?></td>
                    <td>
                      <a href="update-blogpost.php?updateid=<?php echo $row['postid']; ?>" class="btn-action btn-edit-modern"><i class="bi bi-pencil"></i> Edit</a>
                      <a href="deletecontents.php?deleteblogid=<?php echo $row['postid']; ?>" onclick="return confirm('Delete this news post?')" class="btn-action btn-delete-modern"><i class="bi bi-trash"></i> Delete</a>
                    </td>
                  </tr>
                <?php } ?>
                <?php if(mysqli_num_rows($result) == 0): ?>
                  <tr><td colspan="6" class="text-center" style="padding:40px;color:#64748B;">No news posts found</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> Copyright <strong><span>GIHEKE TSS</span></strong>. All Rights Reserved.
    <div class="credits">Developed by <a href="https://devoma.vercel.app" target="_blank">Omar MBONABUCYA</a></div>
  </footer>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const selectItems = document.querySelectorAll('.select-item');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    if (selectAll) {
      selectAll.addEventListener('change', function() {
        selectItems.forEach(cb => cb.checked = this.checked);
      });
    }
    if (deleteSelectedBtn) {
      deleteSelectedBtn.addEventListener('click', function() {
        const selected = [];
        selectItems.forEach(cb => { if (cb.checked) selected.push(cb.value); });
        if (selected.length === 0) { alert('Please select items to delete'); return; }
        if (confirm('Are you sure you want to delete ' + selected.length + ' selected news post(s)?')) {
          window.location.href = 'deletecontents.php?deleteblogids=' + selected.join(',');
        }
      });
    }
  });
  </script>  <script src="assets/js/giheke-toast.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const selectItems = document.querySelectorAll('.select-item');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    if (selectAll) {
      selectAll.addEventListener('change', function() {
        selectItems.forEach(cb => cb.checked = this.checked);
      });
    }
    if (deleteSelectedBtn) {
      deleteSelectedBtn.addEventListener('click', function() {
        const selected = [];
        selectItems.forEach(cb => { if (cb.checked) selected.push(cb.value); });
        if (selected.length === 0) { alert('Please select items to delete'); return; }
        if (confirm('Are you sure you want to delete ' + selected.length + ' selected news post(s)?')) {
          window.location.href = 'deletecontents.php?deleteblogids=' + selected.join(',');
        }
      });
    }
  });
  </script>  <script>
    (function() {
      const sidebar = document.getElementById('sidebar');
      const main = document.getElementById('main');
      const toggleBtn = document.getElementById('sidebarToggle');
      if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
          sidebar.classList.toggle('toggled');
          if (sidebar.classList.contains('toggled')) {
            main.classList.add('full-width');
          } else {
            main.classList.remove('full-width');
          }
        });
      }
    })();
  </script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const selectItems = document.querySelectorAll('.select-item');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    if (selectAll) {
      selectAll.addEventListener('change', function() {
        selectItems.forEach(cb => cb.checked = this.checked);
      });
    }
    if (deleteSelectedBtn) {
      deleteSelectedBtn.addEventListener('click', function() {
        const selected = [];
        selectItems.forEach(cb => { if (cb.checked) selected.push(cb.value); });
        if (selected.length === 0) { alert('Please select items to delete'); return; }
        if (confirm('Are you sure you want to delete ' + selected.length + ' selected news post(s)?')) {
          window.location.href = 'deletecontents.php?deleteblogids=' + selected.join(',');
        }
      });
    }
  });
  </script></body>
</html>