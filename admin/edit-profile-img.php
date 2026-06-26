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


// For adding post  
   

error_reporting(E_ERROR | E_PARSE);



include 'includes/connection.php';
if(isset($_POST['update']))
{   
    
    
    $allowed_extensions = array(".jpg",".JPG","jpeg", "JPEG","PNG",".png",".gif");

$imgfile=$_FILES["postimage"]["name"];
// get the image extension
$extension = substr($imgfile,strlen($imgfile)-4,strlen($imgfile));
// allowed extensions
$allowed_extensions = array(".jpg",".JPG","jpeg", "JPEG","PNG",".png",".gif");
// Validation for allowed extensions .in_array() function searches an array for a specific value.
if(empty($imgfile)){

  $error = "No Image Profile Selected !!!";

}else
if(!in_array($extension,$allowed_extensions))
{
    $error = "Invalid format. Only jpg / jpeg/ png /gif format allowed";
}
 else{


//rename the image file
$imgnewfile=md5($imgfile).$extension;
// Code for move image into directory
move_uploaded_file($_FILES["postimage"]["tmp_name"],"admin-img/".$imgnewfile);


$GalleryCategory = $_POST['GalleryCategory'];
$postid= $id = $_SESSION['admin_id'];;
$query=mysqli_query($conn,"update tbl_admins set ImageUrl='$imgnewfile' where id='$postid'");
if($query)
{
    // header('location:update-blogpost.php?'&msg=Blog Image Updated Successfully');
    header("location:user-profile.php");
}
else{


  // header('location:update-blogpost.php?updateid='.$postid.'&error=Something went wrong . Please try again.');
  $error = die(mysqli_error($conn));
//   header("location:manage-gallerypost.php?error=Something went wrong . Please try again.");

// header("location:manage-gallerypost.php?error=".$error);
    
    

} 
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
      <li class="nav-item dropdown pe-3">

      <?php 

        $id = $id = $_SESSION['admin_id'];;

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
      <h1>Change Profile</h1>
      <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Update Profile Post</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->







<!--adding gallery post starting-->





    




                <form name="edit-profile-img.php" novalidate  enctype="multipart/form-data" method="post">





               
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

        

   <div class="row">
            <div class="col-md-10 col-md-offset-1">
               <div class="p-6">    
                  <div class="">
    

                          <?php

                                  include 'includes/connection.php';

                                  $id = $id = $_SESSION['admin_id'];;

                                  $sql = "SELECT `ImageUrl` FROM `tbl_admins`  WHERE id='$id'";

                                  $query_run = mysqli_query($conn, $sql);
                                  $row = mysqli_fetch_array($query_run);



                                  
                          
                          ?>






          <br>
      <div class="row">
      <div class="col-sm-12">
      <div class="admin-section-card">
      <h4 style="margin-bottom:16px;"><b>Current Post Image</b></h4>
      <img src="admin-img/<?php echo $row['ImageUrl'] ?>" width="300"/>
      <br /><br>
      </div>
      </div>
      </div>
    


      <div class="row">
      <div class="col-sm-12">
      <div class="admin-section-card">
      <h4 style="margin-bottom:16px;"><b>New Feature Image</b></h4>
      <input type="file" class="form-modern" id="postimage" name="postimage"  required>
      </div>
      </div>
      </div>
      <br>
      <div class="text-center">
          <div class="d-grid gap-2 mt-3">
            <button class="btn-modern btn-modern-primary" name="update" type="submit">Update Profile  Image </button>
          </div>

         
          
        </div>
        

      </form>
      </div>
      </div> <!-- end p-20 -->
      </div> <!-- end col -->
      </div>
      <!-- end row -->
   </div>
   <!-- container -->
</div>
<!-- content -->






















   

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
