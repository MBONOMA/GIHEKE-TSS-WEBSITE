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



// if($_GET['action']='del')
// {
// $postid=intval($_GET['pid']);
// $query=mysqli_query($conn,"delete from tbl_gallery_post where id='$postid'");
// if($query)
// {
//   header('location:manage-category.php?msg=School Category Deleted Successfully');
// }
// else{

// header('location:manage-category.php?error=Something went wrong . Please try again !!!');  
// } 
// }


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
      <h1>Manage School Gallery Post</h1>
      <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Manage Gallery Post</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->



    <!--those are codes for table-->



    <section class="section">
        <div class="row">
          <div class="col-lg-12">
  
            <div class="card-modern">
              <div class="card-body">
                <div class="card-modern-header"><h3>Management of School Gallery Post</h3></div>



       
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
           
               
  
                <div class="mb-3">
                  <button id="deleteSelected" class="btn-modern btn-modern-danger">Delete Selected</button>
                </div>
                <!-- Table with stripped rows -->
                <table class="table datatable table table-borderless datatable">
                  <thead>
                  <tr>
                      <th scope="col"><input type="checkbox" id="selectAll"></th>
                      <th scope="col">#</th>
                      <th scope="col">Image</th>
                      <th scope="col">Image Category</th>
                      <th scope="col">Category Description</th>     
                      <th scope="col">Creation Date</th>
                      <th colspan="2" scope="col">Operations</th>

                    </tr>
                  </thead>
                  <tbody>


                  

                  <?php

                            include 'includes/connection.php';

                            
                            $sql = "SELECT tbl_gallery_post.id AS postid,
                            tbl_gallery_post.CreationDate AS CreationDate,
                            tbl_gallery_post.ImageUrl AS ImageUrl,
                            tbl_school_category.CategoryName AS category,
                            
                            tbl_school_category.CategoryDescription as categoryDescription
                            FROM tbl_gallery_post LEFT JOIN tbl_school_category ON 
                            tbl_school_category.id = tbl_gallery_post.CategoryNameId";

                            $result = mysqli_query($conn, $sql);
                            $count = 1;
                            while($call = mysqli_fetch_array($result))
                            {



                            


                    ?>
                   <tr>
                      <td><input type="checkbox" class="select-item" value="<?php echo $call['postid']; ?>"></td>
                      <th scope="row"><?php echo htmlentities($count);?></th>
                      <td> <img src="Gallery Images/<?php echo $call['ImageUrl'] ?>" width="100" height="100" alt="Profile" class="rounded-circle"></td>
                      <td><?php echo $call['category'] ?></td>
                      <td><?php echo $call['categoryDescription'] ?></td>
                      <td><?php echo $call['CreationDate'] ?></td>
                      <td><a href="update-gallerypost.php?updateid=<?php echo  $call['postid'] ?>"   class="btn-modern btn-modern-primary">Update</a></td>
                      <td><a  href="deletecontents.php?deletegallerypostid=<?php echo $call['postid'] ?>" onclick="return confirm('Do You Want to Delete Gallery Post ?')"  class="btn-modern btn-modern-danger">Delete</a></td>
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
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const selectItems = document.querySelectorAll('.select-item');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    if (selectAll) {
      selectAll.addEventListener('change', function() {
        selectItems.forEach(cb => cb.checked = this.checked);
      });
    }
    if (deleteSelectedBtn) {
      deleteSelectedBtn.addEventListener('click', function() {
        const selected = [];
        selectItems.forEach(cb => { if (cb.checked) selected.push(cb.value); });
        if (selected.length === 0) { alert('Please select items to delete'); return; }
        if (confirm('Are you sure you want to delete ' + selected.length + ' selected gallery post(s)?')) {
          window.location.href = 'deletecontents.php?deletegallerypostids=' + selected.join(',');
        }
      });
    }
  });
  </script></body>

</html><?php } ?>
