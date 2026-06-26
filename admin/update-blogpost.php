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
if(isset($_POST['update']))
{

$BlogTitle = $_POST['BlogTitle'];
$BlogCategory = $_POST['BlogCategory'];
$PostDescription = $_POST['PostDescription'];



$lastuptdby= "GIHEKE TSS SCHOOL";

$arr = explode(" ",$BlogTitle);
$url=implode("-",$arr);
$status=1;
$postid=intval($_GET['updateid']);
$query=mysqli_query($conn,"update tblposts set PostTitle='$BlogTitle',CategoryId='$BlogCategory',PostDetails='$PostDescription',
                            PostUrl='$url',Is_Active='$status',lastUpdatedBy='$lastuptdby' where id='$postid'");
if($query)
{

  header("location:manage-blogpost.php?msg=Blog Post Updated Successfully");
// $msg =" Blog Post Updated Successfully";

}

else{

  header("location:manage-blogpost.php?error=Something went wrong . Please try again.");

// $error="Something went wrong . Please try again.";    
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
      <h1>Update Blog Post</h1>
      <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Update Blog Post</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->







<!--adding gallery post starting-->





    
    <!-- Vertical Form -->
    <form class="row g-3 needs-validation" novalidate enctype="multipart/form-data"  method= "post">

         

        
    <div style="margin-top:30px;" class="row">
                              <div class="col-12">
                                 <!---Success Message--->  
                                <?php  if($msg OR $msg = $_GET["msg"]  OR $_GET["delete"]){ ?>
                                    <div class="alert-modern alert-success alert-dismissible fade show" role="alert">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <strong>Well done!</strong> <?php echo htmlentities($msg);?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                 <?php } ?>
                                 <!---Error Message--->
                                 <?php if( $error OR$error = $_GET["error"]){ ?>
                                    <div class="alert-modern alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-octagon me-1"></i>
                                        <strong>Oh snap!</strong> <?php echo htmlentities($error);?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                 <?php } ?>

                                 
                              </div>
   </div>
      


          

          




                <?php

                    include 'includes/connection.php';


                    $id = $_GET['updateid'];
   
                    $sql = "SELECT * FROM `tblposts` WHERE id = '$id'";
        
                    $result = mysqli_query($conn, $sql);
                    $result = mysqli_query($conn, $sql);
                    $call = mysqli_fetch_array($result);


                ?>








        <div class="col-12 position-relative">
          
          <label for="" class="form-label">Blog Title Name</label>
          
           <input type="text" class="form-modern" value="<?php echo $call['PostTitle'] ?>"   name ="BlogTitle"  required placeholder="Enter Blog Title Name">
          <div class="valid-tooltip">
            Looks good!
          </div>    
          <div class="invalid-tooltip">
            Your file is empty
          </div>  
        
        </div>



    

       

        
        <div class="col-12 position-relative">
            <label for="validationTooltip04" class="form-label">Select Category Blog</label>
            <select class="form-modern" id="validationTooltip04" name="BlogCategory" required>

            <?php

                      $cat_query = "SELECT tbl_school_category.CategoryName AS Category
                              FROM tblposts LEFT JOIN tbl_school_category ON 
                          tbl_school_category.id = tblposts.CategoryId WHERE tblposts.id = '$id'";


                                    $query_run = mysqli_query($conn, $cat_query);
                                    while($cat = mysqli_fetch_array($query_run)){

                                  

            ?>

              <option selected disabled value=""><?php echo $cat['Category'] ?></option>


              <?php


    
}

?>

              <?php

                    include 'includes/connection.php';

                    $sql = "SELECT * FROM `tbl_school_category`";
                    $result = mysqli_query($conn, $sql);

                    while($row = mysqli_fetch_array($result)){


                 
                    


                ?>
                
              <option value = "<?php echo $row['id']  ?>"><?php  echo $row['CategoryName'] ?></option>

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
          
            <label for="" class="form-label">Blog Post Details</label>
            
            
            <div class="col-lg-12">

                <div class="card-modern">
                  <div class="card-body">
                    <div class="card-modern-header"><h3>Post Details...</h3></div>
      
                    <!-- TinyMCE Editor -->
                    <!-- class="tinymce-editor"  -->
                    <textarea class="tinymce-editor"  name="PostDescription" id="" cols="30" rows="10" data-name="PostDescription">

                        <?php echo $call['PostDetails'] ?>
                      
                    </textarea>
                    
      
                  </div>
                </div>
            
            
           
            <div class="valid-tooltip">
              Looks good!
            </div>    
            <div class="invalid-tooltip">
              Your file is empty
            </div>  
          
          </div>





          






          <div class="row">
                                 <div class="col-sm-12">
                                    <div class="admin-section-card">
                                       <h4 style="margin-bottom:16px;"><b>Post Image</b></h4>
                                       
                                      
                                       <img src="Blog Images/<?php echo $call['PostImage'] ?>" alt="" width="400">
                                      
                                       <br />
                                       <a href="change-image.php?pid=<?php echo $call['id'] ?>">Update Image</a>
                                    </div>
                                 </div>
        </div>





        
        
        <div class="text-center">
          <div class="d-grid gap-2 mt-3">
            <button class="btn-modern btn-modern-primary" name="update" type="submit">Update Blog </button>
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
      <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script>
  tinymce.init({
    selector: 'textarea.tinymce-editor',
    plugins: 'advlist autolink lists link image charmap preview anchor pagebreak code',
    toolbar_mode: 'floating',
    setup: function(editor) {
      editor.on('change', function() {
        editor.save();
      });
    }
  });
  document.querySelector('form').addEventListener('submit', function() {
    tinymce.triggerSave();
  });
  </script>
  

 

</body>
</html><?php } ?>
