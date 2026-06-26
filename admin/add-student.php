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

if(isset($_POST['submit'])){

    include 'includes/connection.php';

    // Ensure tbl_stdaccounts table exists
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `tbl_stdaccounts` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `StudentCode` varchar(20) NOT NULL,
      `FullName` varchar(255) NOT NULL,
      `Password` varchar(255) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `StudentCode` (`StudentCode`)
    )");

    $FirstName = $_POST['FirstName'];
    $LastName = $_POST['LastName'];
    $Level = $_POST['Level'];
    $Department = $_POST['Department'];
    $studentCode = trim($_POST['StudentCode']);

    if (empty($studentCode)) {
        $error = "Student Code is required";
        goto after_insert;
    }

    // Check if code already exists
    $checkCode = mysqli_query($conn, "SELECT id FROM tbl_students WHERE StudentCode = '$studentCode'");
    if (mysqli_num_rows($checkCode) > 0) {
        $error = "Student Code '$studentCode' already exists";
        goto after_insert;
    }

    // Extract level and department info
    $CodeLevel = substr($Level, 0, 2);
    $NameLevel = substr($Level, 2);
    $CodeDepartment = substr($Department, 0, 3);
    $NameDepartment = substr($Department, 3);

    // Insert into tbl_students
    $insert_sql = "INSERT INTO tbl_students
    (
        StudentCode,
        LevelCode,
        DepartmentCode,
        StudentLevel,
        StudentDepartment,
        FullName
    )
    VALUES
    (
        '$studentCode',
        '$CodeLevel',
        '$CodeDepartment',
        '$NameLevel',
        '$NameDepartment',
        CONCAT('$FirstName', ' ', '$LastName')
    )";
    $result = mysqli_query($conn, $insert_sql);
    if ($result) {
        // Insert into tbl_stdaccounts for login
        $fullName = $FirstName . ' ' . $LastName;
        $defaultPassword = password_hash('student123', PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO tbl_stdaccounts (StudentCode, FullName, Password) VALUES ('$studentCode', '$fullName', '$defaultPassword')");

        $msg = "Student Added Successfully";
    } else {
        $error = mysqli_error($conn);
    }
  }

  after_insert:
  
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
<!-- Template Main CSS File -->
    </head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between" style="width:100%;">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block" style="font-weight:800;color:#0F172A;font-size:1.1rem;">GIHEKE <span style="color:#525FE1;">Admin</span></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn" id="sidebarToggle"></i>
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

    <div class="pagetitle">
      <h1>Add Student</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Add Student</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="admin-content">
        <div class="admin-section-card">
          <div class="admin-section-header">
            <h3><i class="bi bi-plus-circle me-2" style="color:#525FE1;"></i>Add Student</h3>
          </div>
          <div class="p-3">
            <form class="row g-3 needs-validation" novalidate action="add-student.php" method="post">



    
            
    <div class="row">
                              <div class="col-12 " >
                                 <!---Success Message--->  
                                 <?php if($msg){ ?>
                                    <div class="alert-modern alert-success alert-dismissible fade show" role="alert">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <strong>Well done!</strong> <?php echo htmlentities($msg);?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                 <?php } ?>
                                 <!---Error Message--->
                                 <?php if($error){ ?>
                                    <div class="alert-modern alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-octagon me-1"></i>
                                        <strong>Oh snap!</strong> <?php echo htmlentities($error);?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                 <?php } ?>
                              </div>
   </div>






        <div class="col-12 position-relative">
          <label class="form-label">Student Code (for login)</label>
          <input type="text" class="form-modern" required placeholder="e.g. SDMS2630S001" name="StudentCode" maxlength="20" style="text-transform:uppercase;">
          <div style="color:#64748B;font-size:0.78rem;margin-top:4px;">Enter a unique SDMS code the student will use to log in</div>
        </div>

        <div class="col-12 position-relative">
          
          <label for="" class="form-label">First Name</label>
          
           <input type="text" class="form-modern"  id="floatingPassword" required placeholder="Enter Your First Name" name="FirstName">
          <div class="valid-tooltip">
            Looks good!
          </div>    
          <div class="invalid-tooltip">
            Your file is empty
          </div>  
        
        </div>


        <div class="col-12 position-relative">
          
          <label for="validationTooltip01" class="form-label">Last Name</label>
          
           <input type="text" class="form-modern"  id="validationTooltip01" required placeholder="Enter Your Last Name" name = "LastName">
          <div class="valid-tooltip">
            Looks good!
          </div>    
          <div class="invalid-tooltip">
            Your file is empty
          </div>  
        
        </div>







        <div class="col-12 position-relative">
            <label for="validationTooltip04" class="form-label">Select School Level</label>
            <select class="form-modern" id="validationTooltip04" required  name="Level">
              <option name="Level" selected disabled value="">Choose Level</option>
              <option name="Level" value="30Level 3">Level 3</option>
              <option name="Level" value="40Level 4">Level 4</option>
              <option name="Level" value="50Level 5">Level 5</option>
            </select>
            <div class="invalid-tooltip">
              Please select a valid state.
            </div>
            <div class="valid-tooltip">
                Looks good!
              </div> 
          </div>







          <div class="col-12 position-relative">
            <label for="validationTooltip04" class="form-label">Select Department</label>
            <select class="form-modern" id="validationTooltip04" required name="Department">
              <option selected disabled value="">Choose Department</option>
              <option value="100Software Development">Software Development</option>
              <option value="200Network and internet technology">Network and internet technology</option>
               <option value="300Electronics &amp; Telecom">Electronics &amp; Telecom</option>
               <option value="400Electrical Technology">Electrical Technology</option>
               <option value="500Professional Accounting">Professional Accounting</option>
               <option value="600Comp Systems &amp; Architecture">Comp Systems &amp; Architecture</option>
               <option value="700Building Construction">Building Construction</option>
              

            </select>
            <div class="invalid-tooltip">
              Please select a valid state.
            </div>
            <div class="valid-tooltip">
                Looks good!
              </div> 
          </div>





        
        
        <div class="text-center">
          <div class="d-grid gap-2 mt-3">
            <button class="btn-modern btn-modern-primary" name="submit" type="submit">Add Students</button>
          </div>

          <div class="d-grid gap-2 mt-3">
            <button type="reset" class="btn-modern btn-modern-outline">Reset</button>
          </div>
          
        </div>
      </form><!-- Vertical Form -->
          </div>
        </div>
      </div>
    </section>
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