<?php
session_start();
include('includes/connection.php');
error_reporting(0);
if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email']))
{
    header('location:login.php');
}
else{
error_reporting(E_ERROR | E_PARSE);

if(isset($_GET['deleteid'])){
    $id = $_GET['deleteid'];
    $sql = "DELETE FROM `tbl_books` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    if($result == TRUE){
        header("location:manage-books.php?msg=School Book Deleted Successfully");
    }else{
        header("location:manage-books.php?error=Something Went Wrong!!! Try Again");
    }
}

$all_books = mysqli_query($conn, "SELECT * FROM tbl_books ORDER BY created_at DESC");
$levels = mysqli_query($conn, "SELECT DISTINCT level FROM tbl_books WHERE 1 ORDER BY level");
$departments = mysqli_query($conn, "SELECT DISTINCT department FROM tbl_books WHERE 1 ORDER BY department");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Dashboard</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="../img/giheke logo.webp" rel="apple-touch-icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <style>
    .filter-bar { display: flex; gap: 14px; flex-wrap: wrap; align-items: center; margin-bottom: 24px; background: #fff; border-radius: 16px; padding: 18px 22px; border: 1px solid #e2e8f0; }
    .filter-bar label { font-weight: 700; color: #0f172a; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }
    .filter-bar select { padding: 8px 12px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.85rem; font-weight: 600; background: #f8fafc; outline: none; }
    .filter-bar select:focus { border-color: #4f46e5; }
    .action-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .table th { white-space: nowrap; }
    @media (max-width: 768px) { .filter-bar { flex-direction: column; align-items: stretch; } }
  </style>
</head>
<body>

<header id="header" class="header fixed-top d-flex align-items-center">
<div class="d-flex align-items-center justify-content-between">
  <a href="index.php" class="logo d-flex align-items-center">
    <img src="assets/img/logo.png" alt="">
    <span class="d-none d-lg-block">Administration</span>
  </a>
  <i class="bi bi-list toggle-sidebar-btn"></i>
</div>
<nav class="header-nav ms-auto">
  <ul class="d-flex align-items-center">
  <?php include('includes/notifications.php'); ?>
  <li class="nav-item dropdown pe-3">
  <?php
    $id = $_SESSION['admin_id'];
    $FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
    $First = mysqli_fetch_array($FirstChar);
    $Char = strtoupper($First['FirstName']);
  ?>
      <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
        <img src="admin-img/<?php echo $First['ImageUrl'] ?>" alt="Profile" class="rounded-circle">
        <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo substr($Char, 0,1) .". ".$First['LastName']; ?></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
        <li class="dropdown-header">
          <h6><?php echo $First['FirstName']." ". $First['LastName']  ?></h6>
          <span>School Admin</span>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item d-flex align-items-center" href="user-profile.php"><i class="bi bi-person"></i><span>My Profile</span></a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item d-flex align-items-center" href="user-profile.php"><i class="bi bi-person-lines-fill"></i><span>Edit Profile</span></a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item d-flex align-items-center" href="user-profile.php"><i class="bi bi-shuffle"></i><span>Change Password</span></a></li>
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
  <h1>Manage Books & Past Papers</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Manage Books</li>
    </ol>
  </nav>
</div>

<div class="row">
  <div class="col-12">
    <?php if(isset($_GET["msg"])){ ?>
      <div class="alert-modern alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i><strong>Well done!</strong> <?php echo htmlentities($_GET["msg"]);?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php } ?>
    <?php if(isset($_GET["error"])){ ?>
      <div class="alert-modern alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-octagon me-1"></i><strong>Oh snap!</strong> <?php echo htmlentities($_GET["error"]);?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php } ?>
  </div>
</div>

<div class="filter-bar">
  <label><i class="bi bi-funnel"></i> Filters</label>
  <select id="filterLevel">
    <option value="all">All Levels</option>
    <?php while($l = mysqli_fetch_assoc($levels)): ?>
    <option value="<?php echo htmlspecialchars($l['level']); ?>"><?php echo htmlspecialchars($l['level']); ?></option>
    <?php endwhile; ?>
  </select>
  <select id="filterDepartment">
    <option value="all">All Departments</option>
    <?php while($d = mysqli_fetch_assoc($departments)): ?>
    <option value="<?php echo htmlspecialchars($d['department']); ?>"><?php echo htmlspecialchars($d['department']); ?></option>
    <?php endwhile; ?>
  </select>
  <select id="filterCategory">
    <option value="all">All Types</option>
    <option value="Book">Books</option>
    <option value="Past Paper">Past Papers</option>
    <option value="Video Tutorial">Video Tutorials</option>
  </select>
  <input type="text" id="filterSearch" placeholder="Search by title..." style="padding:8px 12px;border:2px solid #e2e8f0;border-radius:10px;font-size:0.85rem;outline:none;flex:1;min-width:180px;">
</div>

<div class="action-bar">
  <div>
    <a href="add-book.php" class="btn-modern btn-modern-primary"><i class="bi bi-plus-lg"></i> Add New Book</a>
  </div>
  <div>
    <button id="deleteSelected" class="btn-modern btn-modern-danger"><i class="bi bi-trash"></i> Delete Selected</button>
  </div>
</div>

<div class="table-responsive">
  <table class="table table-borderless datatable" id="booksTable">
    <thead>
      <tr>
        <th><input type="checkbox" id="selectAll"></th>
        <th>#</th>
        <th>Title</th>
        <th>Category</th>
        <th>Type</th>
        <th>Level</th>
        <th>Department</th>
        <th>Size</th>
        <th>Link</th>
        <th>Added</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if(mysqli_num_rows($all_books) > 0):
        $count = 1;
        while($book = mysqli_fetch_assoc($all_books)):
          $isVideo = ($book['file_type'] == 'video');
      ?>
      <tr data-level="<?php echo htmlspecialchars($book['level']); ?>" data-department="<?php echo htmlspecialchars($book['department']); ?>" data-category="<?php echo htmlspecialchars($book['category']); ?>">
        <td><input type="checkbox" class="select-item" value="<?php echo $book['id']; ?>"></td>
        <td><?php echo $count++; ?></td>
        <td><?php echo htmlspecialchars($book['title']); ?></td>
        <td><span class="badge <?php echo ($book['category'] == 'Book') ? 'bg-success' : (($book['category'] == 'Video Tutorial') ? 'bg-danger' : 'bg-warning text-dark'); ?>"><?php echo htmlspecialchars($book['category']); ?></span></td>
        <td><?php if ($isVideo): ?><i class="bi bi-play-circle text-danger"></i> Video<?php else: ?><i class="bi bi-file-pdf text-danger"></i> PDF<?php endif; ?></td>
        <td><?php echo htmlspecialchars($book['level']); ?></td>
        <td><?php echo htmlspecialchars($book['department']); ?></td>
        <td style="font-size:0.8rem;color:#64748b;"><?php echo !empty($book['file_size']) ? round($book['file_size'] / 1048576, 1) . ' MB' : '-'; ?></td>
        <td><?php if (!empty($book['video_url'])): ?><a href="<?php echo htmlspecialchars($book['video_url']); ?>" target="_blank" title="YouTube link"><i class="bi bi-youtube text-danger" style="font-size:1.2rem;"></i></a><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
        <td style="font-size:0.82rem;color:#64748b;"><?php echo date('d M Y', strtotime($book['created_at'])); ?></td>
        <td>
          <div style="display:flex;gap:6px;flex-wrap:nowrap;">
            <a href="admin/uploads/books/<?php echo rawurlencode(basename($book['file_path'])); ?>" target="_blank" class="btn-modern btn-modern-outline" style="padding:6px 12px;font-size:0.78rem;"><i class="bi bi-eye"></i></a>
            <a href="update-books.php?Updateid=<?php echo $book['id']; ?>" class="btn-modern btn-modern-primary" style="padding:6px 12px;font-size:0.78rem;"><i class="bi bi-pencil"></i></a>
            <a href="manage-books.php?deleteid=<?php echo $book['id']; ?>" class="btn-modern btn-modern-danger" style="padding:6px 12px;font-size:0.78rem;" onclick="return confirm('Delete this content?')"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endwhile;
      else: ?>
      <tr><td colspan="11" style="text-align:center;padding:40px;color:#94a3b8;">No content found. <a href="add-book.php">Add your first content</a></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</main>

<footer class="admin-footer">
  &copy; <?php echo date('Y'); ?> Copyright <strong><span>GIHEKE TSS</span></strong>. All Rights Reserved.
  <div class="credits">Developed by <a href="https://devoma.vercel.app" target="_blank">Omar MBONABUCYA</a></div>
</footer>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center" id="backToTopAdmin">
  <i class="bi bi-arrow-up-short"></i>
</a>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/giheke-toast.js"></script>
<script>
(function() {
  var sidebar = document.getElementById('sidebar');
  var main = document.getElementById('main');
  var toggleBtn = document.getElementById('sidebarToggle');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('toggled');
      main.classList.toggle('full-width', sidebar.classList.contains('toggled'));
    });
  }
  var backToTop = document.getElementById('backToTopAdmin');
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

  function filterTable() {
    var level = document.getElementById('filterLevel').value;
    var dept = document.getElementById('filterDepartment').value;
    var cat = document.getElementById('filterCategory').value;
    var search = document.getElementById('filterSearch').value.toLowerCase().trim();
    var rows = document.querySelectorAll('#booksTable tbody tr');
    rows.forEach(function(row) {
      if (row.cells.length < 7) return;
      var l = level === 'all' || row.dataset.level === level;
      var d = dept === 'all' || row.dataset.department === dept;
      var c = cat === 'all' || row.dataset.category === cat;
      var title = row.cells[2] ? row.cells[2].textContent.toLowerCase() : '';
      var s = !search || title.includes(search);
      row.style.display = (l && d && c && s) ? '' : 'none';
    });
  }

  document.getElementById('filterLevel').addEventListener('change', filterTable);
  document.getElementById('filterDepartment').addEventListener('change', filterTable);
  document.getElementById('filterCategory').addEventListener('change', filterTable);
  document.getElementById('filterSearch').addEventListener('keyup', filterTable);

  var selectAll = document.getElementById('selectAll');
  var selectItems = document.querySelectorAll('.select-item');
  var deleteSelectedBtn = document.getElementById('deleteSelected');
  if (selectAll) {
    selectAll.addEventListener('change', function() {
      selectItems.forEach(function(cb) {
        var row = cb.closest('tr');
        cb.checked = this.checked && row && row.style.display !== 'none';
      }.bind(this));
    });
  }
  if (deleteSelectedBtn) {
    deleteSelectedBtn.addEventListener('click', function() {
      var selected = [];
      selectItems.forEach(function(cb) {
        var row = cb.closest('tr');
        if (cb.checked && row && row.style.display !== 'none') selected.push(cb.value);
      });
      if (selected.length === 0) { alert('Please select items to delete'); return; }
      if (confirm('Are you sure you want to delete ' + selected.length + ' selected book(s)?')) {
        window.location.href = 'deletecontents.php?deletebookids=' + selected.join(',');
      }
    });
  }
})();
</script>
</body>
</html>
<?php } ?>
