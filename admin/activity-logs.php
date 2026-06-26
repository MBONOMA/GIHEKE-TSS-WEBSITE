<?php
session_start();
include('includes/connection.php');
error_reporting(0);
if (!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
    exit;
}

$id = $_SESSION['admin_id'];
$FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
$First = mysqli_fetch_array($FirstChar);
$Char = strtoupper($First['FirstName']);

// Filter by action type
$actionFilter = isset($_GET['action']) ? mysqli_real_escape_string($conn, $_GET['action']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where = '';
if ($actionFilter) {
    $where .= " AND l.`action` = '$actionFilter'";
}
if ($search) {
    $where .= " AND (l.`description` LIKE '%$search%' OR l.`ip_address` LIKE '%$search%')";
}

$logQuery = mysqli_query($conn, "SELECT l.*, a.FirstName, a.LastName 
                                  FROM `tbl_activity_logs` l 
                                  LEFT JOIN `tbl_admins` a ON l.user_id = a.id
                                  WHERE 1=1 $where 
                                  ORDER BY l.`created_at` DESC 
                                  LIMIT 200");

// Get distinct action types for filter
$actionsQuery = mysqli_query($conn, "SELECT DISTINCT `action` FROM `tbl_activity_logs` ORDER BY `action` ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Activity Logs</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
    <style>
      .logs-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); padding: 24px; border: 1px solid rgba(0,0,0,0.04); }
      .filter-bar { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 20px; }
      .filter-bar select { padding: 10px 16px; border: 2px solid #e8e8e8; border-radius: 12px; font-size: 0.85rem; background: #fafafa; min-width: 160px; }
      .filter-bar select:focus { border-color: #525FE1; outline: none; }
      .search-advanced { position: relative; flex: 1; min-width: 220px; }
      .search-advanced input { width: 100%; padding: 10px 44px 10px 42px; border: 2px solid #e8e8e8; border-radius: 12px; font-size: 0.85rem; background: #fafafa; transition: all 0.2s; }
      .search-advanced input:focus { border-color: #525FE1; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(82,95,225,0.08); }
      .search-advanced .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
      .search-advanced .clear-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 24px; height: 24px; border-radius: 50%; border: none; background: #e2e8f0; color: #475569; cursor: pointer; display: none; align-items: center; justify-content: center; font-size: 12px; }
      .search-advanced .clear-btn:hover { background: #cbd5e1; }
      .search-advanced.has-value .clear-btn { display: inline-flex; }
      .search-dropdown { display: none; position: absolute; top: calc(100% + 6px); left: 0; right: 0; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); z-index: 1060; max-height: 300px; overflow-y: auto; }
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
      <div class="pagetitle" style="margin-bottom:24px;">
        <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">Activity Logs</h1>
        <nav><ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item active" style="color:#888;">Activity Logs</li>
        </ol></nav>
      </div>

      <div class="logs-card">
        <form method="get" class="filter-bar">
          <select name="action">
            <option value="">All Actions</option>
            <?php while ($a = mysqli_fetch_assoc($actionsQuery)): ?>
              <option value="<?php echo htmlspecialchars($a['action']); ?>" <?php echo $actionFilter===$a['action']?'selected':''; ?>><?php echo htmlspecialchars(ucwords(str_replace('_',' ',$a['action']))); ?></option>
            <?php endwhile; ?>
          </select>
          <div class="search-advanced" id="searchAdvancedLogs" style="flex:1;min-width:220px;">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="logsSearchInput" name="search" placeholder="Search logs..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="button" class="clear-btn" id="logsSearchClear"><i class="bi bi-x"></i></button>
            <div class="search-dropdown" id="logsSearchDropdown">
              <div class="sd-section">
                <div class="sd-label"><i class="bi bi-clock-history"></i> Recent Searches</div>
                <div id="logsRecentList"><p style="font-size:12px;color:#94a3b8;">No recent searches</p></div>
              </div>
              <div class="sd-section">
                <div class="sd-label"><i class="bi bi-arrow-up-circle"></i> Trending</div>
                <div id="logsTrendingList"></div>
              </div>
            </div>
          </div>
          <button type="submit" class="btn-filter"><i class="bi bi-funnel"></i> Filter</button>
          <?php if ($actionFilter || $search): ?>
            <a href="activity-logs.php" class="btn-filter" style="background:#888;text-decoration:none;">Clear</a>
          <?php endif; ?>
        </form>

        <?php if (mysqli_num_rows($logQuery) > 0): ?>
          <?php while ($log = mysqli_fetch_assoc($logQuery)): 
            $actionType = $log['action'];
            if (strpos($actionType, 'add') === 0 || strpos($actionType, 'create') === 0 || strpos($actionType, 'upload') === 0) $iconClass = 'create';
            elseif (strpos($actionType, 'update') === 0 || strpos($actionType, 'edit') === 0) $iconClass = 'update';
            elseif (strpos($actionType, 'delete') === 0 || strpos($actionType, 'remove') === 0) $iconClass = 'delete';
            elseif (strpos($actionType, 'upload') === 0) $iconClass = 'upload';
            else $iconClass = 'default';
            $userName = ($log['FirstName'] ?? '') ? $log['FirstName'] . ' ' . $log['LastName'] : 'System';
          ?>
          <div class="log-entry">
            <div class="log-icon <?php echo $iconClass; ?>">
              <?php if ($iconClass === 'create'): ?><i class="bi bi-plus-circle"></i>
              <?php elseif ($iconClass === 'update'): ?><i class="bi bi-pencil"></i>
              <?php elseif ($iconClass === 'delete'): ?><i class="bi bi-trash"></i>
              <?php elseif ($iconClass === 'upload'): ?><i class="bi bi-upload"></i>
              <?php else: ?><i class="bi bi-record-circle"></i>
              <?php endif; ?>
            </div>
            <div class="log-content">
              <div class="action"><?php echo htmlspecialchars(ucwords(str_replace('_',' ',$actionType))); ?></div>
              <div class="desc"><?php echo htmlspecialchars($log['description'] ?? ''); ?></div>
              <div class="meta">
                <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($userName); ?></span>
                <span><i class="bi bi-clock"></i> <?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?></span>
                <span><i class="bi bi-globe"></i> <?php echo $log['ip_address']; ?></span>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="bi bi-journal-text"></i>
            <p>No activity logs yet. Logs will appear as you manage the site.</p>
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

  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('logsSearchInput');
    const searchClear = document.getElementById('logsSearchClear');
    const searchDropdown = document.getElementById('logsSearchDropdown');
    const searchWrapper = document.getElementById('searchAdvancedLogs');
    const recentList = document.getElementById('logsRecentList');
    const trendingList = document.getElementById('logsTrendingList');
    let debounceTimer = null;

    const trending = ['Create', 'Update', 'Delete', 'Upload', 'System'];
    let recent = [];
    try { const stored = localStorage.getItem('logsRecentSearches'); if (stored) recent = JSON.parse(stored).slice(0, 5); } catch {}

    function saveRecent(q) {
      recent = [q, ...recent.filter(r => r !== q)].slice(0, 5);
      localStorage.setItem('logsRecentSearches', JSON.stringify(recent));
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
