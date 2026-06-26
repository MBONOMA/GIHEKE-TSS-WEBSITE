<?php
  session_start();
  include('includes/connection.php');
  error_reporting(0);
  if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email']))
    { 
  header('location:login.php');
  }
  else{

      
if(isset($_POST['update']))
{

  
           $id =  $_SESSION['admin_id'];



           $FirstName =  $_POST['FirstName'];
           $LastName = $_POST['LastName'];
           $Email = $_POST['Email'];
           $Phone = $_POST['Phone'];


           $sql = "UPDATE `tbl_admins` SET FirstName = '$FirstName', LastName = '$LastName', Email = '$Email', Phone = '$Phone' WHERE id='$id'";

                   $result = mysqli_query($conn, $sql);


                    if($result){
                       echo "<script>if(window.GihekeToast){GihekeToast.showModal({title:'Success',message:'Profile Info Updated Successfully',type:'success',buttonText:'OK'});}else{alert('Profile Info Updated Successfully');}</script>";
                    }else{
                       echo "<script>if(window.GihekeToast){GihekeToast.showModal({title:'Error',message:'Something Went Wrong',type:'error',buttonText:'OK'});}else{alert('Something Went Wrong');}</script>";
                    }



}

if (isset($_POST['changePassword'])){

    $OldPassword = $_POST['OldPassword'];
    $NewPassword = $_POST['NewPassword'];
    $CPassword = $_POST['CPassword'];

    $id = $_SESSION['admin_id'];

    $sql =  "SELECT Password FROM `tbl_admins` WHERE id='$id'";
    $query_run = mysqli_query($conn, $sql);
    $call = mysqli_fetch_array($query_run);

    if($OldPassword == $call['Password']){

        if($NewPassword == $CPassword){

                $psw_sql = "UPDATE `tbl_admins` SET Password = '$CPassword' WHERE id = '$id'";
                $query_run = mysqli_query($conn , $psw_sql);

                if($query_run){
                  echo "<script>if(window.GihekeToast){GihekeToast.showModal({title:'Success',message:'Password Changed Successfully',type:'success',buttonText:'OK'});}else{alert('Password Changed Successfully');}</script>";
                }else{
                  echo "<script>if(window.GihekeToast){GihekeToast.showModal({title:'Error',message:'Something Went Wrong',type:'error',buttonText:'OK'});}else{alert('Something Went Wrong');}</script>";
                }



        }else{

          echo
          "<script>if(window.GihekeToast){GihekeToast.showModal({title:'Error',message:'Password Must Match',type:'error',buttonText:'OK'});}else{alert('Password Must Match');}</script>";

        }


    }else{

      echo
      "<script>if(window.GihekeToast){GihekeToast.showModal({title:'Error',message:'Incorrect Password',type:'error',buttonText:'OK'});}else{alert('Incorrect Password');}</script>";
    }

}


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

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
         
  <style>
    .profile-wrapper {
      padding: 24px;
      transition: padding 0.3s ease;
    }
    .profile-wrapper.full-width {
      padding-left: 24px;
    }
    .btn-modern.btn-modern-outline {
      padding: 10px 24px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.88rem;
      border: 2px solid #525FE1;
      background: transparent;
      color: #525FE1;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-block;
    }
    .btn-modern.btn-modern-outline:hover {
      background: #525FE1;
      color: #fff;
    }
    .btn-modern.btn-modern-danger.btn-sm {
      padding: 6px 14px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.8rem;
      border: none;
      background: #ef4444;
      color: #fff;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-block;
    }
    .btn-modern.btn-modern-danger.btn-sm:hover {
      background: #dc2626;
    }
    .btn-modern.btn-modern-primary.btn-sm {
      padding: 6px 14px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.8rem;
      border: none;
      background: #525FE1;
      color: #fff;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-block;
    }
    .btn-modern.btn-modern-primary.btn-sm:hover {
      background: #3D47C9;
    }
    .profile-grid-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.08);
      padding: 40px;
      border: 1px solid rgba(0,0,0,0.05);
    }
    .profile-grid {
      display: grid;
      grid-template-columns: 300px 1fr;
      gap: 40px;
      align-items: start;
    }
    .profile-avatar-section {
      text-align: center;
      border-right: 1px solid #f0f0f0;
      padding-right: 40px;
    }
    .profile-avatar-section img {
      width: 140px;
      height: 140px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid var(--primary-glow, rgba(74,10,110,0.2));
      margin-bottom: 16px;
    }
    .profile-avatar-section h2 {
      font-size: 1.4rem;
      font-weight: 700;
      color: #3D47C9;
      margin-bottom: 4px;
    }
    .profile-avatar-section h3 {
      font-size: 0.95rem;
      color: #888;
      font-weight: 500;
    }
    .profile-details-section .tab-content-inner {
      padding-top: 8px;
    }
    .profile-details-section .nav-tabs-custom {
      display: flex;
      gap: 4px;
      border-bottom: 2px solid #f0f0f0;
      padding-bottom: 12px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }
    .profile-details-section .nav-tabs-custom .tab-btn {
      padding: 10px 22px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.85rem;
      cursor: pointer;
      border: none;
      background: transparent;
      color: #666;
      transition: all 0.2s;
    }
    .profile-details-section .nav-tabs-custom .tab-btn:hover {
      background: #f5f0f8;
      color: #525FE1;
    }
    .profile-details-section .nav-tabs-custom .tab-btn.active {
      background: #525FE1;
      color: #fff;
      box-shadow: 0 4px 12px rgba(74,10,110,0.25);
    }
    .detail-row {
      display: flex;
      padding: 12px 0;
      border-bottom: 1px solid #f5f5f5;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-label {
      width: 140px;
      font-weight: 600;
      color: #888;
      font-size: 0.88rem;
      flex-shrink: 0;
    }
    .detail-value {
      flex: 1;
      color: #333;
      font-weight: 500;
    }
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    .form-grid .full-width { grid-column: 1 / -1; }
    .form-grid label {
      display: block;
      font-weight: 600;
      font-size: 0.85rem;
      color: #555;
      margin-bottom: 6px;
    }
    .form-grid .form-modern {
      width: 100%;
      padding: 10px 14px;
      border: 2px solid #e8e8e8;
      border-radius: 10px;
      font-size: 0.92rem;
      transition: all 0.2s;
      background: #fafafa;
    }
    .form-grid .form-modern:focus {
      border-color: #525FE1;
      outline: none;
      background: #fff;
      box-shadow: 0 0 0 4px rgba(74,10,110,0.08);
    }
    .profile-social-links {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 16px;
    }
    .profile-social-links a {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 0.9rem;
      transition: all 0.2s;
      background: #525FE1;
      text-decoration: none;
    }
    .profile-social-links a:hover { transform: translateY(-2px); }
    @media (max-width: 992px) {
      .profile-wrapper { padding: 16px; }
      .profile-grid { grid-template-columns: 1fr; gap: 24px; }
      .profile-avatar-section { border-right: none; padding-right: 0; border-bottom: 1px solid #f0f0f0; padding-bottom: 24px; }
      .form-grid { grid-template-columns: 1fr; }
    }
  </style>
  <!-- Template Main CSS File -->
    </head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Administration</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->


    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
      <?php include('includes/notifications.php'); ?>
      <li class="nav-item dropdown pe-3">

      <?php 

        $id = $_SESSION['admin_id'];

          $FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
          $First = mysqli_fetch_array($FirstChar);
          
          $Char = strtoupper($First['FirstName']);

         

          // $sql = "SELECT * FROM `tbl_admins` WHERE id = '$id'";
          // $result = mysqli_query($conn, $sql);
          // $call = mysqli_fetch_array($result);

          
          // echo $call['LastName'];


        ?>

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="admin-img/<?php echo $First['ImageUrl'] ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">
             
                <?php  echo substr($Char, 0,1) .". ".$First['LastName']; ?>

            </span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $First['FirstName']." ". $First['LastName']  ?></h6>
              <span>School Admin</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="user-profile.php">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="user-profile.php">
                <i class="bi bi-person-lines-fill"></i>
                <span>Edit Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="user-profile.php">
                <i class="bi bi-shuffle"></i>
                <span>Change Password</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

    
  <?php include('includes/sidebar.php'); ?>
<main id="main" class="main">
  <div class="profile-wrapper" id="profileWrapper">
    <div class="pagetitle" style="margin-bottom:20px;">
      <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">Profile</h1>
      <nav>
        <ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item active" style="color:#888;">Profile</li>
        </ol>
      </nav>
    </div>

    <div class="profile-grid-card">
      <div class="profile-grid">
        <!-- Avatar / Left Section -->
        <div class="profile-avatar-section">
          <img src="admin-img/<?php echo $First['ImageUrl'] ?>" alt="Profile">
          <h2><?php echo $First['FirstName']." ". $First['LastName']  ?></h2>
          <h3>School Admin</h3>
          <div class="profile-social-links">
            <a href="#"><i class="bi bi-twitter"></i></a>
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <!-- Details / Right Section -->
        <div class="profile-details-section">
          <div class="nav-tabs-custom">
            <button class="tab-btn active" data-tab="overview">Overview</button>
            <button class="tab-btn" data-tab="edit">Edit Profile</button>
            <button class="tab-btn" data-tab="password">Change Password</button>
          </div>

          <!-- Overview Tab -->
          <div class="tab-content-inner tab-pane-custom active" id="tab-overview">
            <h3 style="font-size:1.1rem;font-weight:700;color:#3D47C9;margin-bottom:6px;">About</h3>
            <p style="color:#666;font-size:0.92rem;margin-bottom:20px;line-height:1.6;">School Administrator is responsible for whole Operations to add, modify, remove, delete contents and view different actions in School System As Seen Above.</p>
            <h3 style="font-size:1.1rem;font-weight:700;color:#3D47C9;margin-bottom:16px;">Profile Details</h3>
            <div class="detail-row">
              <div class="detail-label">First Name</div>
              <div class="detail-value"><?php echo $First['FirstName'] ?></div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Last Name</div>
              <div class="detail-value"><?php echo $First['LastName'] ?></div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Title</div>
              <div class="detail-value">School Administrator</div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Email</div>
              <div class="detail-value"><?php echo $First['Email'] ?></div>
            </div>
            <div class="detail-row">
              <div class="detail-label">Phone</div>
              <div class="detail-value"><?php echo $First['Phone'] ?></div>
            </div>
          </div>

          <!-- Edit Profile Tab -->
          <div class="tab-content-inner tab-pane-custom" id="tab-edit" style="display:none;">
            <form method="post" action="user-profile.php" enctype="multipart/form-data">
              <div class="form-grid">
                <div class="full-width" style="display:flex;align-items:center;gap:16px;margin-bottom:8px;">
                  <img src="admin-img/<?php echo $First['ImageUrl'] ?>" alt="Profile" style="width:64px;height:64px;border-radius:50%;object-fit:cover;">
                  <div style="display:flex;gap:8px;">
                    <a href="edit-profile-img.php" class="btn-modern btn-modern-primary btn-sm"><i class="bi bi-upload"></i> Upload</a>
                    <a href="remove-img.php" onclick="return confirm('Remove profile image?');" class="btn-modern btn-modern-danger btn-sm"><i class="bi bi-trash"></i> Remove</a>
                  </div>
                </div>
                <div>
                  <label>First Name</label>
                  <input name="FirstName" type="text" class="form-modern" value="<?php echo $First['FirstName'] ?>">
                </div>
                <div>
                  <label>Last Name</label>
                  <input name="LastName" type="text" class="form-modern" value="<?php echo $First['LastName'] ?>">
                </div>
                <div>
                  <label>Email</label>
                  <input name="Email" type="email" class="form-modern" value="<?php echo $First['Email'] ?>">
                </div>
                <div>
                  <label>Phone</label>
                  <input name="Phone" type="text" class="form-modern" value="<?php echo $First['Phone'] ?>">
                </div>
              </div>
              <div style="margin-top:24px;text-align:right;">
                <button name="update" type="submit" class="btn-modern btn-modern-primary">Save Changes</button>
              </div>
            </form>
          </div>

          <!-- Change Password Tab -->
          <div class="tab-content-inner tab-pane-custom" id="tab-password" style="display:none;">
            <form method="post" action="user-profile.php">
              <div class="form-grid">
                <div class="full-width">
                  <label>Current Password</label>
                  <input name="OldPassword" type="password" class="form-modern" placeholder="Enter current password" required>
                </div>
                <div>
                  <label>New Password</label>
                  <input name="NewPassword" type="password" class="form-modern" placeholder="Enter new password" required>
                </div>
                <div>
                  <label>Re-enter New Password</label>
                  <input name="CPassword" type="password" class="form-modern" placeholder="Confirm new password" required>
                </div>
              </div>
              <div style="margin-top:24px;text-align:right;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="location.href='reset-password.php'" class="btn-modern btn-modern-outline">Forgot Password</button>
                <button name="changePassword" type="submit" class="btn-modern btn-modern-primary">Change Password</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </main><!-- End #main -->

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
    'use strict';
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const wrapper = document.getElementById('profileWrapper');
    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('toggled');
        main.classList.toggle('full-width', sidebar.classList.contains('toggled'));
        if (wrapper) wrapper.classList.toggle('full-width', sidebar.classList.contains('toggled'));
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

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
        this.classList.add('active');
        document.querySelectorAll('.tab-pane-custom').forEach(function(p) { p.style.display = 'none'; });
        var target = document.getElementById('tab-' + this.getAttribute('data-tab'));
        if (target) target.style.display = 'block';
      });
    });
  })();
  </script>
      



</body>

</html>

<?php } ?>