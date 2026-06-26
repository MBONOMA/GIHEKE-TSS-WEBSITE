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

include 'includes/connection.php';



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
    





  100% {
    transform: rotate(360deg);
  }
}

/*--------------------------------------------------------------
# Disable aos animation delay on mobile devices
--------------------------------------------------------------*/
@media screen and (max-width: 768px) {
  [data-aos-delay] {
    transition-delay: 0 !important;
  }
}
  </style>
  <!-- Template Main CSS File -->
    <style>
    





  100% {
    transform: rotate(360deg);
  }
}

/*--------------------------------------------------------------
# Disable aos animation delay on mobile devices
--------------------------------------------------------------*/
@media screen and (max-width: 768px) {
  [data-aos-delay] {
    transition-delay: 0 !important;
  }
}
  </style>
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

    <div class="pagetitle">
      <h1>Rejected Students</h1>
      <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Rejected Students</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->



    <!--those are codes for table-->



    <section class="section">
        <div class="row">
          <div class="col-lg-12">
  
            <div class="card-modern">
              <div class="card-body">
                <div class="card-modern-header"><h3>Management of Rejected Students</h3></div>


                <div class="row">
                              <div class="col-12">
                                 <!---Success Message--->  
                                 <?php if($msg = $_GET['msg']  OR $_GET['delete']){ ?>
                                    <div class="alert-modern alert-success alert-dismissible fade show" role="alert">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <strong>Well done!</strong> <?php echo htmlentities($msg);?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                 <?php } ?>
                                 <!---Error Message--->
                                 <?php if($error = $_GET['error']){ ?>
                                    <div class="alert-modern alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-octagon me-1"></i>
                                        <strong>Oh snap!</strong> <?php echo htmlentities($error);?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                 <?php } ?>

                                 
                              </div>
   </div>





              
  
                <!-- Table with stripped rows -->
                <table class="table datatable">
                  <thead>
                  <tr>
                      <th scope="col">#</th>
                      <th scope="col">First Name</th>
                      <th scope="col">Last Name</th>
                      <th scope="col">Email</th>
                      <th scope="col">Contact</th>


                      <th scope="col">Previous School Name</th>
                     
                      <th scope="col"> Previous School Trade</th>
                      <th scope="col"> Previous School Level </th>
                      

                      <th scope="col">Chosen Trade</th>
                      <th>Chosen Level</th>
                      <th scope="col"> Student Message </th>

                      <th  colspan="2" scope="col" >Status</th>


                      
                    
                     
                     
                      
                    </tr>
                  </thead>
                  <tbody>


                   

                  <?php

                            include 'includes/connection.php';

                            $sql = "SELECT * FROM `tbl_apply_student` WHERE Status ='rejected'";
                            $result = mysqli_query($conn, $sql);
                            $count = 1;
                            while($call = mysqli_fetch_array($result))
                            {



                            


                    ?>

                  <tr>


                      <td scope="col"><?php echo htmlentities($count);?></t>
                      <td scope="col"><?php echo $call['FirstName'] ?> </td>
                      <td scope="col"><?php echo $call['LastName'] ?> </td>
                      <td scope="col"><?php echo $call['Email'] ?> </td>
                      <td scope="col"><?php echo $call['Contact'] ?> </td>


                      <td scope="col"><?php echo $call['SchoolName'] ?> </td>
                      
                      <td scope="col"><?php echo $call['PreviousTrade'] ?> </td>
                      <td scope="col"><?php echo $call['PreviousLevel'] ?>  </td>

                      <td scope="col"><?php echo $call['SchoolTrade'] ?> </td>
                      <td scope="col"><?php echo $call['SchoolLevel'] ?> </td>
                      <td scope="col"><?php echo $call['Message'] ?> </td>

                      <td scope="col"><a class="btn-modern btn-modern-outline" href="../Student Report/<?php echo $call['SchoolReport'] ?>" target="_blank">View Report</a></td>
                     

                      <td cscope="col">
                        
                        <a class="btn-modern btn-modern-danger"><i class="bi bi-exclamation-triangle"></i>Rejected</a>
                      </td>

                    




                  </tr>


                  <?php

                    $count++;
                    }
                    ?>

                                        
                  </tbody>
                </table>
                <!-- End Table with stripped rows -->
  
              </div>
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

</html><?php } ?>
