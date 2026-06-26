
<?php

if(isset($_POST['change_password'])){

  include 'includes/connection.php';

  $Email =  mysqli_real_escape_string($conn, $_POST['email']);
  $PasswordToken =  mysqli_real_escape_string($conn, $_POST['password_token']);
  $NewPassword =  mysqli_real_escape_string($conn, $_POST['NewPassword']);
  $CPassword =  mysqli_real_escape_string($conn, $_POST['CPassword']);



  if(!empty($PasswordToken))
  {

    if(!empty($Email) && !empty($NewPassword) && !empty($CPassword)){

      $check_token = "SELECT `reset_token_hash` FROM `tbl_admins` WHERE `reset_token_hash` = '$PasswordToken' LIMIT 1 ";

      // $check_token _run = mysqli_query($conn, $check_token);
      $query_run = mysqli_query($conn, $check_token);
      
      if(mysqli_num_rows($query_run) > 0){


        if($NewPassword == $CPassword){


          $Update_password = " UPDATE `tbl_admins` SET Password = '$NewPassword' WHERE `reset_token_hash` = '$PasswordToken' LIMIT 1";
          $run_update = mysqli_query($conn, $Update_password);

          if($run_update){

            $NewToken = md5(rand())."funda";
            $Update_to_new_token = " UPDATE `tbl_admins` SET `reset_token_hash`= '$NewToken' WHERE `reset_token_hash` = '$PasswordToken' LIMIT 1";
            $Update_to_new_token_run = mysqli_query($conn, $Update_to_new_token);



            echo
            "<script>alert('New Password Changed Successfully. You Can Login Now'); window.location.href='login.php'</script>";
            // header("Location:reset-password.php");
    

          }else{

            // header("Location:new-password.php?token=$PasswordToken&&email=$Email");
        echo
        "<script>alert('Something Went Wrong !!!')</script>";
        // header("Location:reset-password.php");


      }

        }else{

         
          echo
          "<script>alert('Password Must Match !!!'); window.location.href='new-password.php?token=$PasswordToken&&email=$Email'</script>";
          // header("Location:reset-password.php");


      }



      }else{

        
        echo
        "<script>alert('Invalid Token')</script>";
        


      }


    }else{

    
      echo
      "<script>alert('Email is Required); window.location.href='new-password.php?token=$PasswordToken&&email=$Email'</script>";
      // header("Location:reset-password.php");

    }

  }else{


     
    echo
    "<script>alert('No Password Token Available'); window.location.href='reset-password.php?token=$PasswordToken&&email=$Email'</script>";
    
  }

}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin Reset Password</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="../img/giheke logo.webp" rel="apple-touch-icon">

  <!-- Google Fonts -->
    
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
        
  <!-- Template Main CSS File -->
    <link href="../assets/css/backend.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Sep 18 2023 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

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

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <span class="d-none d-lg-block">Admin Reset Password</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">


                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <div class="card-modern-header"><h3>New Password For Your Account</h3></div>
                    <p class="text-center small">Enter Your New Password  To Reset Password</p>
                  </div>

                  <form method="post"  action="new-password.php" class="row g-3 needs-validation" novalidate>


                  
                  <input type="hidden" name="password_token" value="<?php if(isset($_GET['token'])){echo $_GET['token'];} ?>">

                  <input type="hidden" name="email"  value="<?php if(isset($_GET['email'])){echo $_GET['email'];} ?>"> 

                    <div class="col-12">
                      <label class="form-label">New Password</label>
                      <input type="password" name="NewPassword" class="form-modern" required>
                      <div class="invalid-feedback">Please Enter Your New Password</div>
                    </div>


                    <div class="col-12">
                      <label class="form-label">Confirm Password</label>
                      <input type="password" name="CPassword" class="form-modern" required>
                      <div class="invalid-feedback">Please Confirm Your Password</div>
                    </div>

                   
                    <div class="col-12">
                      <button name="change_password" class="btn-modern btn-modern-primary w-100" type="submit">Reset Password</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Remember Password <a href="login.php">Login Admin Account</a></p>
                    </div>
                  </form>

                </div>
              </div>

              <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                Developed by <a href="https://devoma.vercel.app" target="_blank">Omar MBONABUCYA</a>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
      <script src="assets/vendor/quill/quill.min.js"></script>
      
  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  
  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    /**
* Template Name: Gp
* Updated: Sep 18 2023 with Bootstrap v5.3.2
* Template URL: https://bootstrapmade.com/gp-free-multipurpose-html-bootstrap-template/
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/
(function() {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  /**
   * Easy on scroll event listener 
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)

  /**
   * Scrolls to an element with header offset
   */
  const scrollto = (el) => {
    let header = select('#header')
    let offset = header.offsetHeight

    let elementPos = select(el).offsetTop
    window.scrollTo({
      top: elementPos - offset,
      behavior: 'smooth'
    })
  }

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  let selectHeader = select('#header')
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled')
      } else {
        selectHeader.classList.remove('header-scrolled')
      }
    }
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
  }

  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Mobile nav toggle
   */
  on('click', '.mobile-nav-toggle', function(e) {
    select('#navbar').classList.toggle('navbar-mobile')
    this.classList.toggle('bi-list')
    this.classList.toggle('bi-x')
  })

  /**
   * Mobile nav dropdowns activate
   */
  on('click', '.navbar .dropdown > a', function(e) {
    if (select('#navbar').classList.contains('navbar-mobile')) {
      e.preventDefault()
      this.nextElementSibling.classList.toggle('dropdown-active')
    }
  }, true)

  /**
   * Scrool with ofset on links with a class name .scrollto
   */
  on('click', '.scrollto', function(e) {
    if (select(this.hash)) {
      e.preventDefault()

      let navbar = select('#navbar')
      if (navbar.classList.contains('navbar-mobile')) {
        navbar.classList.remove('navbar-mobile')
        let navbarToggle = select('.mobile-nav-toggle')
        navbarToggle.classList.toggle('bi-list')
        navbarToggle.classList.toggle('bi-x')
      }
      scrollto(this.hash)
    }
  }, true)

  /**
   * Scroll with ofset on page load with hash links in the url
   */
  window.addEventListener('load', () => {
    if (window.location.hash) {
      if (select(window.location.hash)) {
        scrollto(window.location.hash)
      }
    }
  });

  /**
   * Preloader
   */
  let preloader = select('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove()
    });
  }

  /**
   * Clients Slider
   */
  new Swiper('.clients-slider', {
    speed: 400,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    },
    breakpoints: {
      320: {
        slidesPerView: 2,
        spaceBetween: 40
      },
      480: {
        slidesPerView: 3,
        spaceBetween: 60
      },
      640: {
        slidesPerView: 4,
        spaceBetween: 80
      },
      992: {
        slidesPerView: 6,
        spaceBetween: 120
      }
    }
  });

  /**
   * Porfolio isotope and filter
   */
  window.addEventListener('load', () => {
    let portfolioContainer = select('.portfolio-container');
    if (portfolioContainer) {
      let portfolioIsotope = new Isotope(portfolioContainer, {
        itemSelector: '.portfolio-item'
      });

      let portfolioFilters = select('#portfolio-flters li', true);

      on('click', '#portfolio-flters li', function(e) {
        e.preventDefault();
        portfolioFilters.forEach(function(el) {
          el.classList.remove('filter-active');
        });
        this.classList.add('filter-active');

        portfolioIsotope.arrange({
          filter: this.getAttribute('data-filter')
        });
        portfolioIsotope.on('arrangeComplete', function() {
          AOS.refresh()
        });
      }, true);
    }

  });

  /**
   * Initiate portfolio lightbox 
   */
  const portfolioLightbox = GLightbox({
    selector: '.portfolio-lightbox'
  });

  /**
   * Portfolio details slider
   */
  new Swiper('.portfolio-details-slider', {
    speed: 400,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    }
  });

  /**
   * Testimonials slider
   */
  new Swiper('.testimonials-slider', {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    slidesPerView: 'auto',
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    }
  });

  /**
   * Animation on scroll
   */
  window.addEventListener('load', () => {
    AOS.init({
      duration: 1000,
      easing: "ease-in-out",
      once: true,
      mirror: false
    });
  });

  /**
   * Initiate Pure Counter 
   */
  new PureCounter();

})()
  </script>

</body>

</html>