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


if(isset($_POST['submit'])){







    $DocumentFile=$_FILES["Document"]["name"];
   // get the image extension
   $extension = substr($DocumentFile,strlen($DocumentFile)-4,strlen($DocumentFile));
   // allowed extensions
   $allowed_extensions = array(".pdf",".PDF");
   // Validation for allowed extensions .in_array() function searches an array for a specific value.
   if(!in_array($extension,$allowed_extensions))
   {
   $error = "Invalid format. Only pdf format allowed";
   }
   else
   {
   //rename the image file
   $DocumentUrl=md5($DocumentFile).$extension;
   // Code for move image into directory
   move_uploaded_file($_FILES["Document"]["tmp_name"],"Parent Doc/".$DocumentUrl);




          


    $sql= "UPDATE `tbl_parent_doc` SET DocUrl = '$DocumentUrl' WHERE id = '1'";

                $result = mysqli_query($conn, $sql);
                if($result){

                    $msg = "Parent Document Updated Successfully";
                }else{

                    $error = "Something Went Wrong";
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
    </head>

<body>

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
            $admin_query = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
            $admin_data = mysqli_fetch_array($admin_query);
            $admin_initials = strtoupper($admin_data['FirstName']);
          ?>
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" style="color:#0F172A; font-weight:600;">
            <img src="admin-img/<?php echo $admin_data['ImageUrl']; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block" style="margin-left:8px;">
              <?php echo substr($admin_initials, 0, 1) . ". " . htmlspecialchars($admin_data['LastName']); ?>
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile" style="border-radius:12px; border:1px solid #e8f0f5; box-shadow: 0 8px 30px rgba(0,0,0,0.1);">
            <li class="dropdown-header">
              <h6 style="font-weight:700;"><?php echo htmlspecialchars($admin_data['FirstName'] . " " . $admin_data['LastName']); ?></h6>
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

   
  <?php include('includes/sidebar.php'); ?>
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Update Parent Document</h1>
      <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Update Parent Document</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->







<!--adding gallery post starting-->





    
    <!-- Vertical Form -->
    <form class="row g-3 needs-validation" novalidate  enctype="multipart/form-data" method="post" action="parent-doc.php">









               
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

        
 

   

         <div  class="col-12 position-relative">
            <div class="col-sm-12">
               <div class="admin-section-card">
                  <h4 style="margin-bottom:16px;"><b>Existing Parent Document</b></h4>

                  <?php



                        $link_query = "SELECT * FROM `tbl_parent_doc` WHERE id = '1' ";
                        $query_run = mysqli_query($conn, $link_query);
                        $link = mysqli_fetch_array($query_run);






                    ?>
                     
                        <div class="col-2 btn-modern btn-modern-info mb-2">
                                <a class="nav-link " target="_blank" href="Parent Doc/<?php echo $link['DocUrl'] ?>">
                                <i class="bx bxs-arrow-from-right"></i>
                                <span>Open Doc</span>
                                </a>
                        </div>
               </div>
            </div>
         </div>


         <br><br>

        <div  class="col-12 position-relative">
            <div class="col-sm-12">
               <div class="admin-section-card">
                  <h4 style="margin-bottom:16px;"><b>New Feature  Document</b></h4> 
                  <input type="file" class="form-modern" id="postimage" name="Document"  required>
               </div>
            </div>
         </div>




        
        
        <div class="text-center">
          <div class="d-grid gap-2 mt-3">
            <button class="btn-modern btn-modern-primary" name="submit" type="submit">Add Document</button>
          </div>

          <div class="d-grid gap-2 mt-3">
            <button type="reset" class="btn-modern btn-modern-outline">Reset</button>
          </div>
          
        </div>
      </form><!-- Vertical Form -->











   

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

<?php } ?>
