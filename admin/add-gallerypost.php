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
   

if(isset($_POST['submit']))
   {

    $GalleryCategory = $_POST['GalleryCategory'];
    $mediaType = $_POST['MediaType'] ?? 'image';

    if($mediaType === 'video'){
        $mediafile=$_FILES["postmedia"]["name"];
        $extension = strtolower(pathinfo($mediafile, PATHINFO_EXTENSION));
        $allowed_extensions = array("mp4","webm","avi","mov","wmv","flv","mkv","3gp");
        if(empty($mediafile)){
            $error = "Please select a video file";
        } elseif(!in_array($extension,$allowed_extensions)){
            $error = "Invalid video format. Allowed: mp4, webm, avi, mov, wmv, flv, mkv, 3gp";
        } else {
            $medianewfile=md5(time().$mediafile).'.'.$extension;
            move_uploaded_file($_FILES["postmedia"]["tmp_name"],"Gallery Images/".$medianewfile);
            $sql = "INSERT INTO `tbl_gallery_post` (id, CategoryNameId, ImageUrl, MediaType)
                     VALUES('','$GalleryCategory', '$medianewfile', 'video')";
            $result = mysqli_query($conn, $sql);
            if($result){ $msg ="Video added Successfully"; }
            else { $error = "Something Went Wrong Please Try Again"; }
        }
    } else {
        $imgfile=$_FILES["postimage"]["name"];
        $extension = strtolower(pathinfo($imgfile, PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg","jpeg","png","gif","webp");
        if(empty($imgfile)){
            $error = "Please select an image file";
        } elseif(!in_array($extension,$allowed_extensions)){
            $error = "Invalid format. Only jpg/jpeg/png/gif/webp allowed";
        } else {
            $imgnewfile=md5(time().$imgfile).'.'.$extension;
            move_uploaded_file($_FILES["postimage"]["tmp_name"],"Gallery Images/".$imgnewfile);
            $sql = "INSERT INTO `tbl_gallery_post` (id, CategoryNameId, ImageUrl, MediaType)
                     VALUES('','$GalleryCategory', '$imgnewfile', 'image')";
            $result = mysqli_query($conn, $sql);
            if($result){ $msg ="Gallery Image added Successfully"; }
            else { $error = "Something Went Wrong Please Try Again"; }
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
      <h1>Add Gallery Post</h1>
      <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Add Gallery Post</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="admin-content">
        <div class="admin-section-card">
          <div class="admin-section-header">
            <h3><i class="bi bi-plus-circle me-2" style="color:#525FE1;"></i>Add Gallery Image</h3>
          </div>
          <div class="p-3">
            <form class="row g-3 needs-validation" novalidate  enctype="multipart/form-data" method="post" action="add-gallerypost.php">









               
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
            <label for="validationTooltip04" class="form-label">Select Gallery Category</label>
            <select class="form-modern" id="validationTooltip04" required  name="GalleryCategory">
              <option selected disabled value="">Choose Category</option>

              <?php

                    include 'includes/connection.php';

                    $sql = "SELECT * FROM `tbl_school_category`";
                    $result = mysqli_query($conn, $sql);
                    while($call=mysqli_fetch_assoc($result)){


                    


                ?>
              <option  value="<?php echo $call['id'] ?>"><?php echo $call['CategoryName'] ?></option>

              <?php

                    }



                    ?>

              
            </select>
            <div class="invalid-tooltip">
              Please select a valid state.
            </div>
            <div class="valid-tooltip">
                Looks good!
              </div> 
          </div>




    <div class="col-12 position-relative">
            <label for="mediaType" class="form-label">Media Type</label>
            <select class="form-modern" id="mediaType" required name="MediaType" onchange="toggleMediaInput()">
              <option selected disabled value="">Choose Type</option>
              <option value="image">Image (JPG, PNG, GIF, WebP)</option>
              <option value="video">Video (MP4, WebM, AVI, MOV, WMV, FLV, MKV, 3GP)</option>
            </select>
          </div>

          <div class="col-12 position-relative" id="imageInput">
            <label for="postimage" class="form-label">Upload Image</label>
            <input type="file" class="form-modern" id="postimage" name="postimage" accept=".jpg,.jpeg,.png,.gif,.webp">
          </div>

          <div class="col-12 position-relative d-none" id="videoInput">
            <label for="postmedia" class="form-label">Upload Video (Max 200MB)</label>
            <input type="file" class="form-modern" id="postmedia" name="postmedia" accept=".mp4,.webm,.avi,.mov,.wmv,.flv,.mkv,.3gp">
            <small class="text-muted">Max size: 200MB. Supported: MP4, WebM, AVI, MOV, WMV, FLV, MKV, 3GP</small>
          </div>




        
        
        <div class="text-center">
          <div class="d-grid gap-2 mt-3">
            <button class="btn-modern btn-modern-primary" name="submit" type="submit">Add Gallery Image</button>
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

function toggleMediaInput(){
  var type = document.getElementById('mediaType')?.value;
  var imgInput = document.getElementById('imageInput');
  var vidInput = document.getElementById('videoInput');
  var imgFile = document.getElementById('postimage');
  var vidFile = document.getElementById('postmedia');
  if(type === 'video'){
    imgInput.classList.add('d-none');
    vidInput.classList.remove('d-none');
    imgFile.required = false;
    vidFile.required = true;
  } else {
    imgInput.classList.remove('d-none');
    vidInput.classList.add('d-none');
    imgFile.required = true;
    vidFile.required = false;
  }
}
</script>
</body>
</html>
<?php } ?>