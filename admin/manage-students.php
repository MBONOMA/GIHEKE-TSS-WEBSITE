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
    <style>
      .search-advanced { position: relative; }
      .search-advanced input { width: 100%; padding: 10px 16px 10px 42px; border: 2px solid #e8e8e8; border-radius: 12px; font-size: 0.9rem; background: #fafafa; transition: all 0.2s; }
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
      .no-results-box { text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px solid #e5e7eb; margin-top: 16px; color: #94a3b8; }
      .no-results-box i { font-size: 40px; display: block; margin-bottom: 12px; color: #cbd5e1; }
      .no-results-box h4 { color: #475569; font-weight: 600; margin-bottom: 6px; }
      .no-results-box p { font-size: 13px; max-width: 360px; margin: 0 auto 16px; color: #64748b; }
      @media (max-width: 768px) {
        .search-advanced { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: #fff; z-index: 1060; padding: 12px; display: flex; flex-direction: column; }
        .search-advanced .search-bar-mobile-top { display: flex !important; align-items: center; gap: 8px; }
        .search-advanced input { font-size: 16px; padding: 12px 44px 12px 42px; border-radius: 10px; }
        .search-advanced .search-dropdown { position: static; border-radius: 12px; margin-top: 10px; max-height: calc(100vh - 120px); }
        .search-mobile-close { display: inline-flex !important; }
      }
    </style>
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
                                <div class="search-advanced" id="searchAdvancedStudent">
                                    <i class="bi bi-search search-icon"></i>
                                    <input type="text" id="studentSearchInput" class="form-modern" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button type="button" class="clear-btn" id="studentSearchClear"><i class="bi bi-x"></i></button>
                                    <div class="search-dropdown" id="studentSearchDropdown">
                                        <div class="sd-section">
                                            <div class="sd-label"><i class="bi bi-clock-history"></i> Recent Searches</div>
                                            <div id="studentRecentList"><p style="font-size:12px;color:#94a3b8;">No recent searches</p></div>
                                        </div>
                                        <div class="sd-section">
                                            <div class="sd-label"><i class="bi bi-arrow-up-circle"></i> Trending</div>
                                            <div id="studentTrendingList"></div>
                                        </div>
                                    </div>
                                </div>
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
        const searchInput = document.getElementById('studentSearchInput');
        const searchClear = document.getElementById('studentSearchClear');
        const searchDropdown = document.getElementById('studentSearchDropdown');
        const searchWrapper = document.getElementById('searchAdvancedStudent');
        const recentList = document.getElementById('studentRecentList');
        const trendingList = document.getElementById('studentTrendingList');
        let debounceTimer = null;

        const trending = ['Grade 10', 'Level 5', 'Software Dev', 'Networking', 'Accounting'];
        let recent = [];
        try { const stored = localStorage.getItem('studentRecentSearches'); if (stored) recent = JSON.parse(stored).slice(0, 5); } catch {}

        function saveRecent(q) {
            recent = [q, ...recent.filter(r => r !== q)].slice(0, 5);
            localStorage.setItem('studentRecentSearches', JSON.stringify(recent));
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
                if (searchWrapper) {
                    searchWrapper.classList.toggle('has-value', val.length > 0);
                }
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    this.form.submit();
                }, 300);
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