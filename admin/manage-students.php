<?php
session_start();
include('includes/connection.php');
error_reporting(E_ERROR | E_PARSE);
if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
}

if(isset($_GET['deleteid'])) {
    $id = intval($_GET['deleteid']);
    mysqli_query($conn, "DELETE FROM tbl_students WHERE id = '$id'");
    mysqli_query($conn, "DELETE FROM tbl_stdaccounts WHERE StudentCode NOT IN (SELECT StudentCode FROM tbl_students)");
    header("location:manage-students.php?msg=Student Deleted Successfully");
}

// Get filter params
$filterLevel = $_GET['level'] ?? '';
$filterDept = $_GET['dept'] ?? '';
$search = $_GET['search'] ?? '';

$where = [];
if ($filterLevel) $where[] = "StudentLevel = '" . mysqli_real_escape_string($conn, $filterLevel) . "'";
if ($filterDept) $where[] = "StudentDepartment = '" . mysqli_real_escape_string($conn, $filterDept) . "'";
if ($search) $where[] = "(FullName LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' OR StudentCode LIKE '%" . mysqli_real_escape_string($conn, $search) . "%')";
$whereClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
$sql = "SELECT * FROM tbl_students $whereClause ORDER BY StudentLevel, StudentDepartment, FullName ASC";
$result = mysqli_query($conn, $sql);
$totalStudents = mysqli_num_rows($result);

$depts = ['Software Development', 'Network and internet technology', 'Comp Systems & Architecture', 'Electrical Technology', 'Electronics & Telecom', 'Professional Accounting', 'Building Construction'];
$levels = ['Level 3', 'Level 4', 'Level 5'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Manage Students - GIHEKE TSS</title>
    <link href="../img/giheke logo.webp" rel="icon">
    <link href="../img/giheke logo.webp" rel="apple-touch-icon">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
    <link href="assets/css/giheke-toast.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
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
                    $aid = $_SESSION['admin_id'];
                    $admin_res = mysqli_query($conn, "SELECT * FROM tbl_admins WHERE id = '$aid'");
                    $admin = mysqli_fetch_array($admin_res);
                    $Char = strtoupper($admin['FirstName'] ?? 'A');
                    ?>
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" style="color:#0F172A;font-weight:600;">
                        <img src="admin-img/<?php echo $admin['ImageUrl'] ?? 'default.png'; ?>" alt="Profile" class="rounded-circle" style="width:36px;height:36px;border-radius:50%;border:2px solid #525FE1;">
                        <span class="d-none d-md-block" style="margin-left:8px;"><?php echo substr($Char, 0,1) . ". " . ($admin['LastName'] ?? 'Admin'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="border-radius:12px;border:1px solid #e8f0f5;box-shadow:0 8px 30px rgba(0,0,0,0.1);">
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
            <h1><i class="bi bi-mortarboard me-2" style="color:#525FE1;"></i>Manage Students</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Manage Students</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="admin-content">
                <?php if(isset($_GET['msg'])): ?>
                    <div class="alert-modern alert-success">
                        <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                    </div>
                <?php endif; ?>

                <div class="admin-section-card">
                    <div class="admin-section-header">
                        <h3><i class="bi bi-funnel me-2" style="color:#525FE1;"></i>Filter Students by Level &amp; Trade</h3>
                    </div>
                    <div class="p-4">
                        <form class="row g-3" method="get">
                            <div class="col-md-3">
                                <label class="form-label">Level</label>
                                <select class="form-modern" name="level">
                                    <option value="">All Levels</option>
                                    <?php foreach ($levels as $lv): ?>
                                        <option value="<?php echo $lv; ?>" <?php echo $filterLevel == $lv ? 'selected' : ''; ?>><?php echo $lv; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Trade / Department</label>
                                <select class="form-modern" name="dept">
                                    <option value="">All Trades</option>
                                    <?php foreach ($depts as $dp): ?>
                                        <option value="<?php echo $dp; ?>" <?php echo $filterDept == $dp ? 'selected' : ''; ?>><?php echo $dp; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search (name or code)</label>
                                <input type="text" class="form-modern" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn-modern btn-modern-primary w-100"><i class="bi bi-search"></i> Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="admin-section-card">
                    <div class="admin-section-header">
                        <h3><i class="bi bi-people me-2" style="color:#525FE1;"></i>Registered Students</h3>
                        <span style="color:#64748B;font-size:0.9rem;"><?php echo $totalStudents; ?> student(s) found</span>
                    </div>
                    <div class="p-3">
                        <div class="mb-3 d-flex gap-2">
                            <button id="deleteSelected" class="btn-modern btn-modern-danger">Delete Selected</button>
                        </div>
                        <div class="table-responsive-custom">
                            <table id="example" class="table-modern" style="width:100%">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>#</th>
                                        <th>Student Code</th>
                                        <th>Full Names</th>
                                        <th>Level</th>
                                        <th>Trade / Department</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $count = 1; while($student = mysqli_fetch_array($result)): ?>
                                    <tr>
                                        <td><input type="checkbox" class="select-item" value="<?php echo $student['id']; ?>"></td>
                                        <td><?php echo $count++; ?></td>
                                        <td><?php echo htmlspecialchars($student['StudentCode']); ?></td>
                                        <td><?php echo htmlspecialchars(ucwords(strtolower($student['FullName']))); ?></td>
                                        <td><?php echo htmlspecialchars($student['StudentLevel']); ?></td>
                                        <td><?php echo htmlspecialchars($student['StudentDepartment']); ?></td>
                                        <td>
                                            <a href="update-students.php?Updateid=<?php echo $student['id']; ?>" class="btn-action btn-edit-modern btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <a href="?deleteid=<?php echo $student['id']; ?>" onclick="return confirm('Delete this student?')" class="btn-action btn-delete-modern btn-sm" title="Delete"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
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
    <script src="assets/js/giheke-toast.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script>
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
        $(document).ready(function() {
            $('#example').DataTable({ dom: 'Bfrtip', buttons: ['copy', 'excel', 'pdf', 'print'] });
        });
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
                if (confirm('Are you sure you want to delete ' + selected.length + ' selected student(s)?')) {
                    window.location.href = 'deletecontents.php?deletestudentids=' + selected.join(',');
                }
            });
        }
    });
    </script>
</body>
</html>