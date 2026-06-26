<?php
   session_start();
   include('includes/connection.php');
   error_reporting(0);
   if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email']))
     {
   header('location:login.php');
   }
   else{
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
    <link href="../assets/css/backend.css" rel="stylesheet">
<link href="assets/css/admin-2027-theme.css" rel="stylesheet">
<link href="assets/css/giheke-toast.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
      background: #f0f4f8;
    }

    #header {
      background: #fff;
      border-bottom: 1px solid #E2E8F0;
      padding: 0 28px;
      height: 64px;
      transition: all 0.3s;
      z-index: 1000;
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    #header .logo {
      font-size: 1.2rem;
      font-weight: 800;
      color: #0F172A;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    #header .logo img {
      height: 38px;
      border-radius: 8px;
    }

    #header .logo span {
      color: #525FE1;
    }

    .toggle-sidebar-btn {
      color: #0F172A;
      font-size: 1.4rem;
      cursor: pointer;
      transition: 0.3s;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
    }

    .toggle-sidebar-btn:hover {
      background: rgba(82,95,225,0.1);
      color: #525FE1;
    }

    .header-nav .nav-profile img {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: 2px solid #525FE1;
    }

    .header-nav .nav-profile span {
      font-weight: 600;
      color: #0F172A;
      font-size: 0.9rem;
    }

    .header-nav .nav-profile {
      color: #0F172A;
    }

    #sidebar {
      background: #0F172A;
      min-height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      width: 270px;
      z-index: 999;
      transition: all 0.3s;
      padding-top: 64px;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: rgba(82,95,225,0.3) transparent;
    }

    #sidebar::-webkit-scrollbar { width: 5px; }
    #sidebar::-webkit-scrollbar-thumb { background: rgba(82,95,225,0.3); border-radius: 10px; }

    #sidebar.toggled {
      width: 80px;
    }

    #sidebar.toggled .brand-text,
    #sidebar.toggled .sidebar-nav .nav-content,
    #sidebar.toggled .sidebar-nav .nav-link span:not(.nav-icon),
    #sidebar.toggled .section-label {
      display: none;
    }

    #sidebar.toggled .sidebar-nav .nav-link {
      justify-content: center;
      padding: 14px;
    }

    #sidebar.toggled .sidebar-nav .nav-link i {
      margin-right: 0;
      font-size: 1.3rem;
    }

    .sidebar-brand {
      padding: 20px 18px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      position: fixed;
      top: 0;
      left: 0;
      width: 270px;
      height: 64px;
      z-index: 1001;
      background: #0F172A;
      transition: width 0.3s;
    }

    #sidebar.toggled .sidebar-brand {
      width: 80px;
    }

    .sidebar-brand img {
      width: 38px;
      height: 38px;
      border-radius: 8px;
      flex-shrink: 0;
    }

    .sidebar-brand .brand-text {
      color: #fff;
      font-weight: 800;
      font-size: 1.05rem;
      white-space: nowrap;
    }

    .sidebar-brand .brand-text small {
      display: block;
      font-weight: 400;
      font-size: 0.7rem;
      color: #525FE1;
      letter-spacing: 1.5px;
      text-transform: uppercase;
    }

    .sidebar-nav {
      padding: 14px 10px;
      list-style: none;
      margin: 0;
    }

    .sidebar-nav .nav-item {
      margin-bottom: 2px;
    }

     .sidebar-nav .nav-link {
       display: flex;
       align-items: center;
       gap: 12px;
       padding: 11px 14px;
       color: rgba(255,255,255,0.75);
       border-radius: 10px;
       font-weight: 500;
       font-size: 0.88rem;
       transition: 0.3s;
       white-space: nowrap;
       text-decoration: none;
     }

     .sidebar-nav .nav-link:hover,
     .sidebar-nav .nav-link.active {
       background: rgba(99,102,241,0.22);
       color: #fff;
     }

     .sidebar-nav .nav-link i {
       font-size: 1.1rem;
       flex-shrink: 0;
       width: 22px;
       text-align: center;
       color: rgba(255,255,255,0.7);
     }

     .sidebar-nav .nav-link:hover i,
     .sidebar-nav .nav-link.active i {
       color: #818cf8;
     }

     .sidebar-nav .nav-content {
       list-style: none;
       padding-left: 44px;
       margin-top: 4px;
     }

     .sidebar-nav .nav-content li a {
       display: flex;
       align-items: center;
       gap: 10px;
       padding: 7px 10px;
       color: rgba(255,255,255,0.6);
       font-size: 0.83rem;
       font-weight: 400;
       text-decoration: none;
       transition: 0.3s;
       border-radius: 8px;
     }

     .sidebar-nav .nav-content li a:hover {
       color: #a5b4fc;
       background: rgba(99,102,241,0.12);
       padding-left: 14px;
     }

     .sidebar-nav .nav-content li a i {
       font-size: 0.55rem;
       color: rgba(255,255,255,0.45);
     }

     .sidebar-nav .nav-content li a:hover i {
       color: #818cf8;
     }

    .sidebar-section-label {
      display: block;
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: rgba(255,255,255,0.35);
      padding: 18px 14px 6px;
      font-weight: 700;
    }

    .sidebar-nav .btn-sidebar-action {
      width: 100%;
      padding: 9px 14px;
      margin-bottom: 8px;
      border: none;
      background: rgba(255,255,255,0.05);
      color: rgba(255,255,255,0.7);
      border-radius: 10px;
      font-weight: 500;
      font-size: 0.88rem;
      text-align: left;
      transition: 0.3s;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sidebar-nav .btn-sidebar-action.danger {
      background: rgba(220,38,38,0.2);
      color: #f87171;
      margin-top: 12px;
    }

    .sidebar-nav .btn-sidebar-action:hover {
      background: rgba(82,95,225,0.2);
      color: #6B72E8;
    }

    .sidebar-nav .btn-sidebar-action.danger:hover {
      background: rgba(220,38,38,0.35);
      color: #ef4444;
    }

    #main {
      margin-left: 270px;
      transition: margin-left 0.3s;
      padding-top: 64px;
      min-height: 100vh;
    }

    #main.full-width {
      margin-left: 80px;
    }

    .pagetitle {
      padding: 22px 28px 16px;
      background: #fff;
      border-bottom: 1px solid #E2E8F0;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .pagetitle h1 {
      font-size: 1.3rem;
      font-weight: 800;
      color: #0F172A;
      margin: 0;
    }

    .breadcrumb {
      background: none;
      padding: 0;
      margin: 0;
      font-size: 0.82rem;
    }

    .breadcrumb-item a {
      color: #525FE1;
      text-decoration: none;
    }

    .breadcrumb-item.active {
      color: #64748B;
    }

    .admin-content {
      padding: 24px 28px;
    }

    .dashboard-cards-modern {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-bottom: 28px;
    }

    .dash-card-modern {
      background: #fff;
      border-radius: 14px;
      padding: 22px 20px;
      border: 1px solid #E2E8F0;
      display: flex;
      align-items: center;
      gap: 16px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.03);
      transition: 0.3s;
      text-decoration: none;
      color: inherit;
    }

    .dash-card-modern:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(15,23,42,0.1);
      border-color: #525FE1;
    }

    .dash-card-icon-modern {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      flex-shrink: 0;
    }

    .dash-card-icon-modern.blue { background: rgba(82,95,225,0.12); color: #525FE1; }
    .dash-card-icon-modern.green { background: rgba(22,163,74,0.12); color: #16A34A; }
    .dash-card-icon-modern.orange { background: rgba(245,158,11,0.18); color: #F59E0B; }
    .dash-card-icon-modern.purple { background: rgba(99,102,241,0.12); color: #6366f1; }
    .dash-card-icon-modern.lime { background: rgba(132,204,22,0.2); color: #65a30d; }
    .dash-card-icon-modern.red { background: rgba(220,38,38,0.12); color: #DC2626; }
    .dash-card-icon-modern.dark { background: rgba(15,23,42,0.1); color: #0F172A; }
    .dash-card-icon-modern.teal { background: rgba(20,184,166,0.12); color: #14b8a6; }

    .dash-card-info h4 {
      font-size: 1.5rem;
      font-weight: 800;
      color: #0F172A;
      margin: 0;
      line-height: 1.2;
    }

    .dash-card-info p {
      font-size: 0.82rem;
      color: #64748B;
      margin: 2px 0 0;
      font-weight: 500;
    }

    .admin-section-card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid #E2E8F0;
      box-shadow: 0 1px 4px rgba(0,0,0,0.03);
      margin-bottom: 24px;
      overflow: hidden;
    }

    .admin-section-header {
      padding: 18px 22px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #E2E8F0;
      flex-wrap: wrap;
      gap: 10px;
    }

    .admin-section-header h3 {
      margin: 0;
      font-size: 1rem;
      font-weight: 700;
      color: #0F172A;
    }

    .table-responsive-custom {
      overflow-x: auto;
    }

    .table-modern {
      width: 100%;
      border-collapse: collapse;
      margin: 0;
    }

    .table-modern thead {
      background: #F1F5F9;
    }

    .table-modern thead th {
      padding: 13px 18px;
      text-align: left;
      font-size: 0.78rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      color: #64748B;
      border-bottom: 1px solid #E2E8F0;
      white-space: nowrap;
    }

    .table-modern tbody td {
      padding: 13px 18px;
      font-size: 0.88rem;
      border-bottom: 1px solid #E2E8F0;
      vertical-align: middle;
      color: #0F172A;
    }

    .table-modern tbody tr {
      transition: 0.3s;
    }

    .table-modern tbody tr:hover {
      background: #F8FAFC;
    }

    .badge-modern {
      display: inline-block;
      padding: 5px 12px;
      border-radius: 50px;
      font-size: 0.76rem;
      font-weight: 600;
      letter-spacing: 0.2px;
    }

    .badge-approved { background: rgba(22,163,74,0.12); color: #16A34A; }
    .badge-pending { background: rgba(245,158,11,0.2); color: #F59E0B; }
    .badge-rejected { background: rgba(220,38,38,0.12); color: #DC2626; }
    .badge-info { background: rgba(82,95,225,0.12); color: #525FE1; }

    .btn-action {
      padding: 6px 13px;
      font-size: 0.78rem;
      font-weight: 600;
      border-radius: 7px;
      border: none;
      cursor: pointer;
      transition: 0.3s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      margin: 0 2px;
    }

    .btn-view-modern { background: rgba(22,163,74,0.1); color: #16A34A; }
    .btn-view-modern:hover { background: #16A34A; color: #fff; }
    .btn-edit-modern { background: rgba(82,95,225,0.1); color: #525FE1; }
    .btn-edit-modern:hover { background: #525FE1; color: #fff; }
    .btn-delete-modern { background: rgba(220,38,38,0.1); color: #DC2626; }
    .btn-delete-modern:hover { background: #DC2626; color: #fff; }

    .admin-footer {
      text-align: center;
      padding: 18px;
      color: #64748B;
      font-size: 0.82rem;
      border-top: 1px solid #E2E8F0;
      margin-top: 20px;
    }

    .admin-footer a {
      color: #525FE1;
      font-weight: 600;
      text-decoration: none;
    }

    .admin-footer a:hover {
      color: #0F172A;
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
      .dashboard-cards-modern { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 768px) {
      #sidebar { width: 80px; }
      #sidebar .brand-text,
      #sidebar .sidebar-nav .nav-link span:not(.nav-icon),
      #sidebar .nav-content { display: none; }
      #sidebar .sidebar-nav .nav-link { justify-content: center; padding: 14px; }
      #sidebar .sidebar-nav .btn-sidebar-action { justify-content: center; padding: 14px; }
      .sidebar-brand { width: 80px; justify-content: center; }
      .sidebar-brand .brand-text { display: none; }
      #main { margin-left: 80px; }
      .dashboard-cards-modern { grid-template-columns: 1fr; }
      .pagetitle { flex-direction: column; align-items: flex-start; gap: 8px; }
    }
  </style>
    <link href="../assets/css/admin-panel.css" rel="stylesheet">
</head>
<body>

  <!-- ======= HEADER ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between" style="width:100%;">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block" style="font-weight:800; color:#0F172A; font-size:1.1rem;">GIHEKE <span style="color:#525FE1;">Admin</span></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn" id="sidebarToggle"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center" style="list-style:none;margin:0;padding:0;gap:6px;">
        <?php include('includes/notifications.php'); ?>
        <li class="nav-item dropdown pe-3">
          <?php
          $id = $_SESSION['admin_id'];
          $FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
          $First = mysqli_fetch_array($FirstChar);
          $Char = strtoupper($First['FirstName']);
          ?>
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" style="color:#0F172A; font-weight:600;">
            <img src="admin-img/<?php echo $First['ImageUrl'] ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block" style="margin-left:8px;">
              <?php echo substr($Char, 0,1) .". ".$First['LastName']; ?>
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" style="border-radius:12px; border:1px solid #e8f0f5; box-shadow: 0 8px 30px rgba(0,0,0,0.1);">
            <li class="dropdown-header">
              <h6 style="font-weight:700;"><?php echo $First['FirstName']." ". $First['LastName'] ?></h6>
              <span style="color:#525FE1;">School Admin</span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="user-profile.php"><i class="bi bi-person"></i><span>My Profile</span></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="user-profile.php"><i class="bi bi-person-lines-fill"></i><span>Edit Profile</span></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="changePassword.php"><i class="bi bi-shuffle"></i><span>Change Password</span></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="logout.php"><i class="bi bi-box-arrow-right"></i><span>Sign Out</span></a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <!-- ======= MODERN SIDEBAR ======= -->
  <aside id="sidebar" class="sidebar">
    <div class="sidebar-brand" id="sidebarBrand">
      <img src="assets/img/logo.png" alt="Logo">
      <div class="brand-text">
        GIHEKE
        <small>Admin Panel</small>
      </div>
    </div>

    <ul class="sidebar-nav" id="sidebarNav">
      <li class="nav-item">
        <a class="nav-link active" href="index.php">
          <i class="bi bi-grid-fill"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li><span class="sidebar-section-label">School Management</span></li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#attendance" data-bs-toggle="collapse" href="#">
          <i class="bi bi-folder2-open"></i>
          <span>School Category</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="attendance" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="add-category.php"><i class="bi bi-arrow-bar-right"></i><span>Add Category</span></a></li>
          <li><a href="manage-category.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Category</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#trainers" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people"></i>
          <span>School Trainers</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="trainers" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="add-trainers.php"><i class="bi bi-arrow-bar-right"></i><span>Add Trainer</span></a></li>
          <li><a href="manage-trainers.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Trainers</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#gallery" data-bs-toggle="collapse" href="#">
          <i class="bi bi-images"></i>
          <span>School Gallery</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="gallery" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="add-gallerypost.php"><i class="bi bi-arrow-bar-right"></i><span>Add Image</span></a></li>
          <li><a href="manage-gallerypost.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Gallery</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#blog" data-bs-toggle="collapse" href="#">
          <i class="bi bi-newspaper"></i>
          <span>School Blog</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="blog" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="add-blogpost.php"><i class="bi bi-arrow-bar-right"></i><span>Add News</span></a></li>
          <li><a href="manage-blogpost.php"><i class="bi bi-arrow-bar-right"></i><span>Manage News</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#messages" data-bs-toggle="collapse" href="#">
          <i class="bi bi-chat-dots"></i>
          <span>Student Messages</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="messages" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="studentMessage.php"><i class="bi bi-arrow-bar-right"></i><span>View Messages</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#parent" data-bs-toggle="collapse" href="#">
          <i class="bi bi-file-earmark-text"></i>
          <span>Parent Document</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="parent" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="parent-doc.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Document</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#announce" data-bs-toggle="collapse" href="#">
          <i class="bi bi-megaphone"></i>
          <span>Announcements</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="announce" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="announce.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Announcement</span></a></li>
        </ul>
      </li>

      <li><span class="sidebar-section-label">Academics</span></li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#applications" data-bs-toggle="collapse" href="#">
          <i class="bi bi-folder-check"></i>
          <span>Applications</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="applications" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="apply_status.php"><i class="bi bi-arrow-bar-right"></i><span>Set Application Status</span></a></li>
          <li><a href="studentApplication.php"><i class="bi bi-arrow-bar-right"></i><span>Applied Students</span></a></li>
          <li><a href="approved-students.php"><i class="bi bi-arrow-bar-right"></i><span>Approved</span></a></li>
          <li><a href="rejected-students.php"><i class="bi bi-arrow-bar-right"></i><span>Rejected</span></a></li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#students" data-bs-toggle="collapse" href="#">
          <i class="bi bi-mortarboard"></i>
          <span>Students</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="students" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li><a href="add-student.php"><i class="bi bi-arrow-bar-right"></i><span>Add Student</span></a></li>
          <li><a href="manage-students.php"><i class="bi bi-arrow-bar-right"></i><span>Manage Students</span></a></li>
        </ul>
      </li>



      <li><span class="sidebar-section-label">System</span></li>

      <li class="nav-item">
        <a class="nav-link" href="schoolPassword.php">
          <i class="bi bi-key"></i>
          <span>Application Password</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="changePassword.php">
          <i class="bi bi-shield-lock"></i>
          <span>Change Password</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link btn-sidebar-action danger" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </aside>

  <!-- ======= MAIN CONTENT ======= -->
  <main id="main" class="main">
    <div class="pagetitle">
      <h1><i class="bi bi-speedometer2 me-2" style="color:#525FE1;"></i>Dashboard Overview</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="admin-content">

        <!-- MODERN DASHBOARD CARDS -->
        <div class="dashboard-cards-modern">
          <a href="manage-gallerypost.php" class="dash-card-modern" data-aos="fade-up">
            <div class="dash-card-icon-modern blue"><i class="bi bi-card-image"></i></div>
            <div class="dash-card-info">
              <h4>Gallery</h4>
              <p>Manage Images</p>
            </div>
          </a>
          <a href="manage-blogpost.php" class="dash-card-modern" data-aos="fade-up" data-aos-delay="50">
            <div class="dash-card-icon-modern purple"><i class="bi bi-newspaper"></i></div>
            <div class="dash-card-info">
              <h4>Blog</h4>
              <p>School News</p>
            </div>
          </a>
          <a href="books.php" class="dash-card-modern" data-aos="fade-up" data-aos-delay="100">
            <div class="dash-card-icon-modern green"><i class="bi bi-book"></i></div>
            <div class="dash-card-info">
              <h4>Books</h4>
              <p>Books & Past Papers</p>
            </div>
          </a>

          <a href="studentMessage.php" class="dash-card-modern" data-aos="fade-up" data-aos-delay="200">
            <div class="dash-card-icon-modern teal"><i class="bi bi-chat-dots"></i></div>
            <div class="dash-card-info">
              <h4>Messages</h4>
              <p>Student Inbox</p>
            </div>
          </a>
          <a href="apply_status.php" class="dash-card-modern" data-aos="fade-up" data-aos-delay="250">
            <div class="dash-card-icon-modern lime"><i class="bi bi-folder-check"></i></div>
            <div class="dash-card-info">
              <h4>Applications</h4>
              <p>Admission Status</p>
            </div>
          </a>
          <a href="manage-students.php" class="dash-card-modern" data-aos="fade-up" data-aos-delay="300">
            <div class="dash-card-icon-modern dark"><i class="bi bi-people"></i></div>
            <div class="dash-card-info">
              <h4>Students</h4>
              <p>All Records</p>
            </div>
          </a>
          <a href="manage-trainers.php" class="dash-card-modern" data-aos="fade-up" data-aos-delay="350">
            <div class="dash-card-icon-modern blue"><i class="bi bi-person-badge"></i></div>
            <div class="dash-card-info">
              <h4>Trainers</h4>
              <p>Teaching Staff</p>
            </div>
          </a>
        </div>

        <!-- MODERN ACTIVITY TABLE PLACEHOLDER -->
        <div class="admin-section-card" data-aos="fade-up" data-aos-delay="100">
          <div class="admin-section-header">
            <h3><i class="bi bi-activity me-2" style="color:#525FE1;"></i>Recent Activity</h3>
            <span class="badge-modern badge-info">Live</span>
          </div>
          <div class="table-responsive-custom">
            <table class="table-modern">
              <thead>
                <tr>
                  <th>Activity</th>
                  <th>Admin</th>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>New student application received</td>
                  <td><strong>Admin</strong></td>
                  <td>Just now</td>
                  <td><span class="badge-modern badge-pending">Pending</span></td>
                </tr>
                <tr>
                  <td>Gallery image uploaded</td>
                  <td><strong>Admin</strong></td>
                  <td>2 hours ago</td>
                  <td><span class="badge-modern badge-approved">Completed</span></td>
                </tr>
                <tr>
                  <td>New blog post published</td>
                  <td><strong>Admin</strong></td>
                  <td>Yesterday</td>
                  <td><span class="badge-modern badge-approved">Completed</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </section>
  </main>

  <!-- ======= FOOTER ======= -->
  <footer class="admin-footer">
    <div class="copyright">
      &copy; <?php echo date('Y'); ?> Copyright <strong><span>GIHEKE TSS</span></strong>. All Rights Reserved.
    </div>
    <div class="credits">
      Developed by <a href="https://devoma.vercel.app" target="_blank">Omar MBONABUCYA</a>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center" id="backToTopAdmin">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
  (function() {
    'use strict';

    // Modern sidebar toggle
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const toggleBtn = document.getElementById('sidebarToggle');

    toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('toggled');
      if (sidebar.classList.contains('toggled')) {
        main.classList.add('full-width');
      } else {
        main.classList.remove('full-width');
      }
    });

    // Back to top
    const backToTop = document.getElementById('backToTopAdmin');
    if (backToTop) {
      window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
          backToTop.style.opacity = '1';
          backToTop.style.visibility = 'visible';
        } else {
          backToTop.style.opacity = '0';
          backToTop.style.visibility = 'hidden';
        }
      });
      backToTop.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }

  })();
  </script>
  <script src="assets/js/giheke-toast.js"></script>

</body>
</html>
<?php } ?>
