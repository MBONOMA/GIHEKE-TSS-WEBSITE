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


if(isset($_GET['deleteid'])){


    include 'includes/connection.php';

    $id = $_GET['deleteid'];

    $sql = "DELETE FROM `tbl_works` WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if($result == TRUE){

        header("location:manage-works.php?msg=School Work Deleted Successfully");
    }else{

        header("location:manage-works.php?error=Something Went Wrong!!! Try Again");
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

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
  <link rel="stylesheet" href="assets/css/quiz.css">

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
    <link rel="stylesheet" href="assets/css/form.css">

  <style type="text/css">
    .btnAdd {
      text-align: right;
      width: 83%;
      margin-bottom: 20px;
    }
  </style>
    </head>

<body>








          <!-- Info Box -->
          <div class="info_box">
      <div class="info-title"><span>Some Rules of this Quiz</span></div>
      <div class="info-list">
          
          <div class="info">1. Once you select your answer, it can't be undone.</div>
          <div class="info">2. You can't select any option once time goes off.</div>
          <div class="info">3. You can't exit from the Quiz while you're playing.</div>
          <div class="info">4. You'll get points on the basis of your correct answers.</div>
      </div>
      <div class="buttons">
          <button class="quit">Exit Quiz</button>
          <button class="restart">Continue</button>
      </div>
  </div>















   <!-- Quiz Box -->
   <div class="quiz_box">
    <header>
        <div class="title">Awesome Quiz Application</div>
        
        <div class="time_line"></div>
    </header>
    <section>
        <div class="que_text">
            <!-- Here I've inserted question from JavaScript -->
        </div>
        <div class="option_list">
            <!-- Here I've inserted options from JavaScript -->
        </div>
    </section>

    <!-- footer of Quiz Box -->
    <footer>
        <div class="total_que">
            <!-- Here I've inserted Question Count Number from JavaScript -->
        </div>
        <button class="next_btn">Next Question</button>
    </footer>
</div>

<!-- Result Box -->
<div class="result_box">
    <div class="icon">
        <i class="fas fa-crown"></i>
    </div>
    <div class="complete_text">You've completed the Quiz!</div>
    <div class="score_text">
        <!-- Here I've inserted Score Result from JavaScript -->
    </div>
    <div class="buttons">
        <button class="restart">Replay Quiz</button>
        <button class="quit">Quit Quiz</button>
    </div>
</div>





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
      <h1>Manage School Works </h1>
      <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Manage Works</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->




    <div style="margin-top:30px;" class="row">
                              <div class="col-12">
                                 <!---Success Message--->  
                                <?php  if($msg = $_GET["msg"]  OR $_GET["delete"]){ ?>
                                    <div class="alert-modern alert-success alert-dismissible fade show" role="alert">
                                            <i class="bi bi-check-circle me-1"></i>
                                            <strong>Well done!</strong> <?php echo htmlentities($msg);?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                 <?php } ?>
                                 <!---Error Message--->
                                 <?php if($error = $_GET["error"]){ ?>
                                    <div class="alert-modern alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-octagon me-1"></i>
                                        <strong>Oh snap!</strong> <?php echo htmlentities($error);?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                 <?php } ?>

                                 
                              </div>
   </div>







    <!-- Vertical Form -->
    <form class="row g-3 needs-validation" novalidate method="post" action="manage-works.php">




        <div class="col-6 position-relative">
            <label for="validationTooltip04" class="form-label">Select School Level</label>
            <select class="form-modern" id="validationTooltip04" required name="Level">
              <option selected disabled value="">Choose Level</option>
              <option value="Level 3">Level 3</option>
              <option value="Level 4">Level 4</option>
              <option value="Level 5">Level 5</option>
            </select>
            <div class="invalid-tooltip">
              Please select a valid state.
            </div>
            <div class="valid-tooltip">
                Looks good!
              </div> 
          </div>




        <div class="col-6 position-relative">
            <label for="validationTooltip04" class="form-label">Select Department</label>
            <select class="form-modern" id="validationTooltip04" required name="Department">
              <option selected disabled value="">Choose Department</option>
              <option value="Software Development">Software Development</option>
              <option value="Network and internet technology">Network and internet technology</option>
               <option value="Electronics &amp; Telecom">Electronics &amp; Telecom</option>
               <option value="Electrical Technology">Electrical Technology</option>
               <option value="Professional Accounting">Professional Accounting</option>
               <option value="Comp Systems &amp; Architecture">Comp Systems &amp; Architecture</option>
               <option value="Building Construction">Building Construction</option>
              

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
            <button class="btn-modern btn-modern-primary" name="submit" type="submit">Search Works</button>
          </div>

          
          
        </div>





            <?php

                   if(isset($_POST['submit'])){




                                
                      include 'includes/connection.php';
                      
                      
                      $Level = $_POST['Level'];
                      $Department = $_POST['Department'];


                      
                      


                      ?>





                <h3 style="margin-top:30px; text-align:center;"><?php echo $Level ." " . $Department ?></h3>
                        <div class="mb-3">
                          <button id="deleteSelected" class="btn-modern btn-modern-danger">Delete Selected</button>
                        </div>
                        <table class="table datatable table table-borderless datatable">
                        <thead>
                            <tr>
                            <th scope="col"><input type="checkbox" id="selectAll"></th>
                            <th scope="col">#</th>
                            <th scope="col">Module Name</th>
                            <th scope="col">Work Level</th>
                            <th scope="col">Work Department</th>
                           
                            <th colspan="2" scope="col">Operations</th>

                    </tr>
                  </thead>
                  <tbody>


                  

                  <?php

                            include 'includes/connection.php';
   
                            $Level = $_POST['Level'];
                            $Department = $_POST['Department'];
      
      
                            $sql = "SELECT * FROM `tbl_works` WHERE Level = '$Level' AND Department = '$Department'";
                            $result = mysqli_query($conn, $sql);
                            $count = 1;
                            while($call = mysqli_fetch_array($result))
                            {



                            


                    ?>



                <tr>
                      <td><input type="checkbox" class="select-item" value="<?php echo $call['id']; ?>"></td>
                      <th scope="row"><?php echo htmlentities($count);?></th>
                      <td><?php echo $call['ModuleName'] ?> </td>
                      <td><?php echo $call['Level'] ?> </td>
                      <td><?php echo $call['Department'] ?> </td>
                      <td><a href="update-works.php?updateid=<?php echo $call['id'] ?> "  class="btn-modern btn-modern-primary">Update Work</a></td>
                      <td><a href="manage-works.php?deleteid=<?php echo $call['id'] ?> " class="btn-modern btn-modern-danger">Remove Work</a></td>
                      
                        
                    </tr>
                    

                     <?php

                    $count++;
                    }
                    ?>


                  </tbody>
                </table>
                <!-- End Table with stripped rows -->









                    <?php


                    }
                    



                ?>




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
        if (confirm('Are you sure you want to delete ' + selected.length + ' selected work(s)?')) {
          window.location.href = 'deletecontents.php?deleteworksids=' + selected.join(',');
        }
      });
    }
  });
  </script>

    <!-- Inside this JavaScript file I've inserted Questions and Options only -->    <!-- Inside this JavaScript file I've inserted Questions and Options only -->
    <script>
        // creating an array and passing the number, questions, options, and answers

       
let questions = [
    {
    numb: 1,
    question: "<?php echo "sfsafasfasas"?>",  
    answer: "set of programs, documentation & configuration of data",
    options: [
      "set of programs, documentation & configuration of data",
      "set of programs",
      "documentation and configuration of data",
      "None of the mentioned"
    ]
  },
    {
    numb: 2,
    question: "What is Software Engineering?",
    answer: "Application of engineering principles to the design a software",
    options: [
      "Designing a software",
      "Testing a software",
      "Application of engineering principles to the design a software",
      "None of the above"
    ]
  },
    {
    numb: 3,
    question: "Who is the father of Software Engineering?",
    answer: "Watts S. Humphrey",
    options: [
      "Margaret Hamilton",
      "Watts S. Humphrey",
      "Alan Turing",
      "Boris Beizer"
    ]
  },
    {
    numb: 4,
    question: "What are the features of Software Code?",
    answer: "Modularity",
    options: [
      "Simplicity",
      "Accessibility",
      "Modularity",
      "All of the above"
    ]
  },
//     {
//     numb: 5,
//     question: "____________ is a software development activity that is not a part of software processes.",
//     answer: "Dependence",
//     options: [
//       "Validation",
//       "Specification",
//       "Development",
//       "Dependence"
//     ]
//   },
  // you can uncomment the below codes and make duplicate as more as you want to add question
  // but remember you need to give the numb value serialize like 1,2,3,5,6,7,8,9.....

  //   {
  //   numb: 6,
  //   question: "Your Question is Here",
  //   answer: "Correct answer of the question is here",
  //   options: [
  //     "Option 1",
  //     "option 2",
  //     "option 3",
  //     "option 4"
  //   ]
  // },
];
    </script>

    <!-- Inside this JavaScript file I've coded all Quiz Codes -->

    <script src="assets/js/script.js"></script>





</body>

</html><?php } ?>
