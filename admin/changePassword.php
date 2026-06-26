<?php
    
    session_start();
    include('includes/connection.php');
    error_reporting(0);

$siteSettings = [];
$siteSettingsQuery = mysqli_query($conn, "SELECT setting_key, setting_value FROM tbl_site_settings");
if ($siteSettingsQuery) {
  while ($row = mysqli_fetch_assoc($siteSettingsQuery)) {
    $siteSettings[$row['setting_key']] = $row['setting_value'];
  }
}
$schoolLogo = $siteSettings['site_logo'] ?? 'assets/img/logo.png';

    if(!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email']))
      { 
    header('location:login.php');
    }
    else{
error_reporting(E_ERROR | E_PARSE);
if(isset($_POST['submit'])){

        include 'includes/connection.php';


       $Password = $_POST['Password'];
       $CPassword = $_POST['CPassword'];
        

        if($Password == $CPassword){

                        
                    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS tbl_student_password (id INT AUTO_INCREMENT PRIMARY KEY, Password VARCHAR(255) NOT NULL, CreationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
                    mysqli_query($conn, "INSERT INTO tbl_student_password (id, Password) VALUES (1, '$Password') ON DUPLICATE KEY UPDATE Password = '$Password', CreationDate = CURRENT_TIMESTAMP");

                    $result = mysqli_query($conn, "UPDATE tbl_student_password SET Password = '$Password', CreationDate = CURRENT_TIMESTAMP WHERE id = 1");

                    if($result){

                        $msg = "School Password Updated Successfully";

                    }else{

                        $error = "Something Went Wrong";
                    }
        }else{

            $error = "Password Must Match Please !!!";

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

 <!-- ======= Header ======= -->
 <header id="header" class="header fixed-top d-flex align-items-center">

<div class="d-flex align-items-center justify-content-between">
  <a href="index.php" class="logo d-flex align-items-center">
    <img src="../<?php echo htmlspecialchars($schoolLogo); ?>" alt="School logo" style="height:42px;width:42px;object-fit:cover;border-radius:10px;">
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
      <h1>Change School Password</h1>
      <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Change Pasword</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->









    <!-- Vertical Form -->
    <form class="row g-3 needs-validation" novalidate action = "changePassword.php" method="post">



            
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
          
          <label for="" class="form-label">New Password </label>
          
           <input type="password" class="form-modern"  name="Password" required placeholder="Enter New Student Password">
          <div class="valid-tooltip">
            Looks good!
          </div>    
          <div class="invalid-tooltip">
            Your file is empty
          </div>  
        
        </div>


        

        <div class="col-12 position-relative">
          
          <label for="" class="form-label">Confirm Password</label>
          
           <input type="password" class="form-modern"  name="CPassword" required placeholder="Confirm Student Password">
          <div class="valid-tooltip">
            Looks good!
          </div>    
          <div class="invalid-tooltip">
            Your file is empty
          </div>  
        
        </div>



     


        
        
        <div class="text-center">
          <div class="d-grid gap-2 mt-3">
            <button class="btn-modern btn-modern-primary" name="submit" onclick="validation()" type="submit">Change Password</button>
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








</body>

</html>

<?php } ?>
