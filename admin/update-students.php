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

if(isset($_POST['update'])){


        include 'includes/connection.php';
        


        $id = $_GET['Updateid'];
        $FullName = $_POST['FullName'];
        
        
        $Level = $_POST['Level'];
        $Department = $_POST['Department'];
       

        $query = "UPDATE `tbl_students` SET `FullName` = '$FullName', `StudentLevel` = '$Level', `StudentDepartment` = '$Department' WHERE `tbl_students`.`id` = '$id'";
        $query_run = mysqli_query($conn, $query);

        if($query_run){

          
          header("location:manage-students.php?msg= Student Updated Successfully");
        }else{

          header("location:manage-students.php?error= Something Went Wrong");
        }

            
        
  }
  
  

  
}




     


            

{
            

            





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
     
      <h1>Update Student  of <?php echo $_GET['Level']." ".$_GET['Department']?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Update Student</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->









    <!-- Vertical Form -->
    <form class="row g-3 needs-validation" novalidate action="" method="post">



    
            
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






                                  
                                  
                        <?php

                          include 'includes/connection.php';


                          $id = $_GET['Updateid'];
                          


                          $sql = "SELECT * FROM `tbl_students` WHERE id = '$id'";
                          $result = mysqli_query($conn, $sql);
                          
                          $call = mysqli_fetch_array($result);
                          





                          ?>





        <div class="col-12 position-relative">
          
          <label for="validationTooltip01" class="form-label">Last Name</label>
          
           <input type="text" class="form-modern" value="<?php echo $call['FullName'] ?>"  id="validationTooltip01" required placeholder="Enter Your Last Name" name = "FullName">
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
              <option name="Level" selected disabled value="<?php echo $call['StudentLevel'] ?>"><?php echo $call['StudentLevel'] ?></option>
              <option name="Level" value="L3">Level 3</option>
              <option name="Level" value="L4">Level 4</option>
              <option name="Level" value="L5">Level 5</option>
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
              <option selected disabled value="<?php echo $call['StudentDepartment'] ?>"><?php echo $call['StudentDepartment'] ?></option>
              <option value="SOD">Software Development</option>
              <option value="NIT">Network and internet technology</option>
              <option value="ELS">Electronics &amp; Telecom</option>
              <option value="ELC">Electrical Technology</option>
              <option value="ACC">Professional Accounting</option>
              <option value="CSA">Comp Systems &amp; Architecture</option>
              <option value="BUC">Building Construction</option>
              

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
            <button class="btn-modern btn-modern-primary" name="update" type="submit">Update Students</button>
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

</html><?php } ?>
