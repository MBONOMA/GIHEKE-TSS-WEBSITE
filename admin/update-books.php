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

if(isset($_POST['update'])){

    $id = $_GET['Updateid'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $video_url = mysqli_real_escape_string($conn, trim($_POST['video_url'] ?? ''));

    $file_type = 'pdf';
    $file_uploaded = false;

    if(!empty($_FILES["postimage"]["name"])){
        $imgfile = $_FILES["postimage"]["name"];
        $extension = strtolower(pathinfo($imgfile, PATHINFO_EXTENSION));

        if($extension != 'pdf'){
            $error = "Invalid format. Only PDF files are allowed";
        } else {
            $file_size = $_FILES["postimage"]["size"];
            $max_size = 262144000;
            if ($file_size > $max_size) {
                $error = "File too large. Maximum size is 250MB.";
            } else {
                $filename = md5($imgfile . time()) . '.' . $extension;
                move_uploaded_file($_FILES["postimage"]["tmp_name"], "uploads/books/" . $filename);
                $file_path = 'admin/uploads/books/' . $filename;
                $file_uploaded = true;
            }
        }
    }

    if(empty($error)) {
        if(!empty($video_url)) {
            $file_type = 'video';
        }

        if($file_uploaded) {
            $sql = "UPDATE `tbl_books` SET `title`='$title', `category`='$category', `level`='$level', `department`='$department', `description`='$description', `file_path`='$file_path', `file_type`='$file_type', `file_size`='$file_size', `video_url`='$video_url' WHERE `tbl_books`.`id`='$id'";
        } else {
            $sql = "UPDATE `tbl_books` SET `title`='$title', `category`='$category', `level`='$level', `department`='$department', `description`='$description', `file_type`='$file_type', `video_url`='$video_url' WHERE `tbl_books`.`id`='$id'";
        }

        $result = mysqli_query($conn, $sql);
        if($result){
            header('location:manage-books.php?msg=Content updated successfully');
        } else {
            header('location:manage-books.php?error=Something went wrong');
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
      <h1>Update Books</h1>
      <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Update Books</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->







    <!-- Vertical Form -->
    <form class="row g-3 needs-validation" novalidate enctype="multipart/form-data"  method="post">


    
            
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

        <?php

                include 'includes/connection.php';                    

                  $id = $_GET['Updateid'];

                  $sql = "SELECT * FROM `tbl_books` WHERE id = '$id'";
                  $result = mysqli_query($conn, $sql);
                  $call = mysqli_fetch_assoc ($result);
        ?>
          
          <div class="col-12 position-relative">
          
          <label for="" class="form-label">Book Title</label>
          
           <input type="text" class="form-modern" value="<?php echo $call['title'];?>"  id="floatingPassword" required placeholder="Enter Book Title" name="title">
          <div class="valid-tooltip">
            Looks good!
          </div>    
          <div class="invalid-tooltip">
            Your file is empty
          </div>  
        
        </div>

        <div class="col-12 position-relative">
            <label for="validationTooltip04" class="form-label">Category</label>
            <select class="form-modern" id="updateCategory" required name="category">
              <option disabled value="">Choose Category</option>
              <option value="Book" <?php if($call['category'] == 'Book') echo 'selected'; ?>>Book</option>
              <option value="Past Paper" <?php if($call['category'] == 'Past Paper') echo 'selected'; ?>>Past Paper</option>
              <option value="Video Tutorial" <?php if($call['category'] == 'Video Tutorial') echo 'selected'; ?>>Video Tutorial</option>
            </select>
            <div class="invalid-tooltip">
              Please select a valid category.
            </div>
            <div class="valid-tooltip">
                Looks good!
            </div>
        </div>

        <div class="col-12 position-relative">
            <label for="validationTooltip04" class="form-label">Select Level</label>
            <select class="form-modern" id="validationTooltip04" required name="level">
              <option disabled value="">Choose Level</option>
              <option value="L3" <?php if($call['level'] == 'L3') echo 'selected'; ?>>L3</option>
              <option value="L4" <?php if($call['level'] == 'L4') echo 'selected'; ?>>L4</option>
              <option value="L5" <?php if($call['level'] == 'L5') echo 'selected'; ?>>L5</option>
            </select>
            <div class="invalid-tooltip">
              Please select a valid state.
            </div>
            <div class="valid-tooltip">
                Looks good!
            </div>
        </div>

        <div class="col-12 position-relative">
            <label for="validationTooltip04" class="form-label">Select Trade</label>
            <select class="form-modern" id="validationTooltip04" required name="department">
              <option disabled value="">Choose Trade</option>
              <?php
              $trades = mysqli_query($conn, "SELECT name FROM tbl_trades WHERE is_active=1 ORDER BY sort_order ASC");
              while($tr = mysqli_fetch_assoc($trades)):
                $sel = ($call['department'] == $tr['name']) ? 'selected' : '';
              ?>
              <option value="<?php echo htmlspecialchars($tr['name']); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($tr['name']); ?></option>
              <?php endwhile; ?>
            </select>
            <div class="invalid-tooltip">
              Please select a valid state.
            </div>
            <div class="valid-tooltip">
                Looks good!
            </div>
        </div>

          <div class="col-12 position-relative">
            <label class="form-label">Description</label>
            <textarea class="form-modern" name="description" rows="3" placeholder="Brief description"><?php echo htmlspecialchars($call['description'] ?? ''); ?></textarea>
          </div>

          <div id="updateVideoUrlGroup" class="col-12 position-relative" style="margin-top:15px;<?php echo ($call['category'] ?? '') !== 'Video Tutorial' ? 'display:none;' : ''; ?>">
            <label class="form-label">YouTube URL (for Video Tutorials)</label>
            <input type="url" class="form-modern" name="video_url" placeholder="https://youtube.com/watch?v=..." value="<?php echo htmlspecialchars($call['video_url'] ?? ''); ?>" style="padding:8px;">
            <small class="text-muted">Paste the YouTube video link instead of uploading a file</small>
          </div>

          <div style="margin-top:25px;" class="col-12 position-relative">
            <div class="col-sm-12">
               <div class="admin-section-card">
                  <h4 style="margin-bottom:16px;"><b>Upload New PDF (leave empty to keep current)</b></h4>
                  <p style="font-size:0.8rem;color:#888;margin-bottom:10px;">Max 250MB. Only PDF format.</p>
                  <input type="file" class="form-modern" id="postimage" name="postimage" accept=".pdf">
               </div>
            </div>
            <div class="invalid-tooltip">Please select a valid state.</div>
            <div class="valid-tooltip">Looks good!</div>
         </div>




        
        
        <div class="text-center">
          <div class="d-grid gap-2 mt-3">
            <button class="btn-modern btn-modern-primary" name="update" type="submit">Update Book</button>
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
      


  



  <script>


// function validation(){


        //         var FirstName = document.getElementById("FirstName").value
        //         // var LastName = document.getElementById("LastName").value
        //         // var Email = document.getElementById("Email").value
        //         // var Contact = document.getElementById("Contact").value



        //          //Text verification (if input contains only text)
        // const textVerify = (text) => {
        // const regex = /^[a-zA-Z]{3,}$/;
        // return regex.test(text);
        // };




        // //Email verification
        // const emailVerify = (input) => {
        // const regex = /^[a-z0-9_]+@[a-z]{3,}\.[a-z\.]{3,}$/;
        // return regex.test(input);
        // };




        // //Phone number verification
        //     const phoneVerify = (number) => {
        //     const regex = /^[0-9]{10}$/;
        //     return regex.test(number);
        //     };








            


// if(FirstName == "")
// {    
//     alert("FirstName is Required")
// }

// else if (textVerify(FirstName)== false) {
//                 //If verification returns true
//               message="First Name must contain Text";
//             }
// else if(FirstName.length >=20){

// alert("First Name is Too Long")
// }

// else if(LastName == ""){
// alert("Last Name is Required")
// }else if (textVerify(LastName)== false) {
//                 //If verification returns true
//                alert("Invalid Last Name")
//             }
//  else if(LastName.length >=20){

//     alert("Last Name is Too Long")
//     }
//  else if(Email == ""){
//     alert('Email Address is Required ')
//  }else  if (emailVerify(Email)== false) {
//                 //If verification returns true
//                alert("Invalid Email Address")
//             }
//    else  if(Contact == "") {
//     alert('Contact Field is Required')
//    }else  if (phoneVerify(Contact)== false) {
//                 //If verification returns true
//                alert("Invalid Contacts")
//             }

// else{

// Form1.style.left = "-500px";
// Form2.style.left = "40px";

// progress.style.width = "210px";
// }
                




// }







  



                                    
       

         

                                    




 


    
                                
                    


</script>

<script>
(function() {
  var catSelect = document.getElementById('updateCategory');
  var videoUrlGroup = document.getElementById('updateVideoUrlGroup');
  if (catSelect && videoUrlGroup) {
    function toggle() {
      videoUrlGroup.style.display = catSelect.value === 'Video Tutorial' ? 'block' : 'none';
    }
    catSelect.addEventListener('change', toggle);
  }
})();
</script>

</body>

</html><?php } ?>
