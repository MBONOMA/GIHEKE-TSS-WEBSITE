<?php
$current_page = basename($_SERVER['PHP_SELF']);
$active_home = ($current_page == 'index.php') ? 'active' : '';
$base_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/Giheke';
if (!isset($siteSettings)) {
    $siteSettings = [];
    if (!isset($conn) || !$conn) {
        include_once dirname(__FILE__) . '/connection.php';
    }
}
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<!-- READING PROGRESS BAR -->
<div class="reading-progress-global" id="readingProgressGlobal"></div>

<!-- PARALLAX BACKGROUND OVERLAY -->
<div class="parallax-overlay" id="parallaxOverlay"></div>

<style>
:root {
  --font-futuristic: 'Orbitron', 'Segoe UI', Tahoma, sans-serif;
  --font-modern: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
}
body {
  font-family: var(--font-modern);
}
.logo-haip .logo-text {
  font-family: var(--font-futuristic) !important;
  letter-spacing: 1.5px !important;
  text-transform: uppercase;
}
.logo-haip .logo-text span {
  font-family: var(--font-futuristic) !important;
  font-weight: 700 !important;
  letter-spacing: 1.5px !important;
}
.navbar-haip {
  transition: background 0.35s ease, box-shadow 0.35s ease, padding 0.35s ease !important;
}
.navbar-haip.navbar-shrink {
  background: rgba(255,255,255,0.92) !important;
  backdrop-filter: blur(14px) !important;
  -webkit-backdrop-filter: blur(14px) !important;
  box-shadow: 0 4px 30px rgba(0,0,0,0.08) !important;
}
</style>

<!-- TOP BAR -->
<div class="top-bar">
    <div class="container-haip">
        <div class="top-content">
            <div class="top-left">
                <div class="contact-info">
<?php
if (!isset($siteSettings)) {
    $siteSettings = [];
    if (!isset($conn) || !$conn) {
        include_once dirname(__FILE__) . '/connection.php';
    }
    if (isset($conn)) {
        $ssq = mysqli_query($conn, "SELECT setting_key, setting_value FROM tbl_site_settings");
        if ($ssq) {
            while ($ssr = mysqli_fetch_assoc($ssq)) {
                $siteSettings[$ssr['setting_key']] = $ssr['setting_value'];
            }
        }
    }
}
$header_phone = $siteSettings['site_phone'] ?? '+250 788 876 460';
$header_email = $siteSettings['site_email'] ?? 'giheketss@gmail.com';
?>
                    <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9]/', '', $header_phone)); ?>" class="contact-itemaa">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="icon" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"></path></svg> <?php echo htmlspecialchars($header_phone); ?>
                    </a>
                    <a href="mailto:<?php echo htmlspecialchars($header_email); ?>" class="contact-itemaa">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="icon" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg> <?php echo htmlspecialchars($header_email); ?>
                    </a>
                </div>
            </div>
            <div class="top-right">
                <div class="quick-links">
                    <a href="<?php echo $base_url; ?>/student-verification.php" class="quick-link">
                        <span class="quick-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M319.4 320.6L224 416l-95.4-95.4C57.1 323.7 0 382.2 0 454.4v9.6c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-9.6c0-72.2-57.1-130.7-128.6-133.8zM13.6 79.8l6.4 1.5v58.4c-7 4.2-12 11.5-12 20.3 0 8.4 4.6 15.4 11.1 19.7L3.5 242c-1.7 6.9 2.1 14 7.6 14h41.8c5.5 0 9.3-7.1 7.6-14l-15.6-62.3C51.4 175.4 56 168.4 56 160c0-8.8-5-16.1-12-20.3V87.1l66 15.9c-8.6 17.2-14 36.4-14 57 0 70.7 57.3 128 128 128s128-57.3 128-128c0-20.6-5.3-39.8-14-57l96.3-23.2c18.2-4.4 18.2-27.1 0-31.5l-190.4-46c-13-3.1-26.7-3.1-39.7 0L13.6 48.2c-18.1 4.4-18.1 27.2 0 31.6z"></path></svg></span>
                        <span class="quick-text">Student Portal</span>
                        <span class="badge badge-success">VERIFY</span>
                    </a>
                    <a href="<?php echo $base_url; ?>/elearning.php" class="quick-link">
                        <span class="quick-icon"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M448 360V24c0-13.3-10.7-24-24-24H96C43 0 0 43 0 96v320c0 53 43 96 96 96h328c13.3 0 24-10.7 24-24v-16c0-7.5-3.5-14.3-8.9-18.7-4.2-15.4-4.2-59.3 0-74.7 5.4-4.3 8.9-11.1 8.9-18.6zM128 134c0-3.3 2.7-6 6-6h212c3.3 0 6 2.7 6 6v20c0 3.3-2.7 6-6 6H134c-3.3 0-6-2.7-6-6v-20zm0 64c0-3.3 2.7-6 6-6h212c3.3 0 6 2.7 6 6v20c0 3.3-2.7 6-6 6H134c-3.3 0-6-2.7-6-6v-20zm253.4 250H96c-17.7 0-32-14.3-32-32 0-17.6 14.4-32 32-32h285.4c-1.9 17.1-1.9 46.9 0 64z"></path></svg></span>
                        <span class="quick-text">E-Learning</span>
                        <span class="badge badge-warning">NEW</span>
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar-haip" id="mainNavbar">
    <div class="container-haip">
        <a href="<?php echo $base_url; ?>/index.php" class="logo-haip">
            <img src="<?php echo $base_url; ?>/<?php echo htmlspecialchars($siteSettings['site_logo'] ?? 'img/giheke logo.webp'); ?>" alt="GIHEKE Logo">
            <div class="logo-text"><?php echo htmlspecialchars($siteSettings['site_name'] ?? 'GIHEKE'); ?><span> TSS</span></div>
        </a>
        <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
        <ul class="nav-menu" id="navLinks">
            <li class="<?php echo $active_home; ?>"><a href="<?php echo $base_url; ?>/index.php">Home</a></li>
            <li><a href="<?php echo $base_url; ?>/index.php#about">About Us</a></li>
            <li><a href="<?php echo $base_url; ?>/index.php#team">Trades</a></li>
            <li><a href="<?php echo $base_url; ?>/gallery.php">Gallery</a></li>
            <li><a href="<?php echo $base_url; ?>/blog/blog.php">News</a></li>
            <li><a href="<?php echo $base_url; ?>/elearning.php">E-Learning</a></li>
            <li><a href="<?php echo $base_url; ?>/index.php#contact">Contact Us</a></li>
            <li><a href="<?php echo $base_url; ?>/SchoolApplication.php" class="btn-haip btn-haip-primary" style="padding:8px 20px;font-size:0.85rem;">Apply</a></li>
        </ul>
    </div>
</nav>
<div class="mobile-overlay" id="mobileOverlay"></div>

<script>
(function() {
  var bar = document.getElementById('readingProgressGlobal');
  if (bar) {
    window.addEventListener('scroll', function() {
      var scrollTop = window.scrollY;
      var docHeight = document.documentElement.scrollHeight - window.innerHeight;
      if (docHeight > 0) {
        bar.style.width = (scrollTop / docHeight) * 100 + '%';
      }
    });
  }

  var parallaxSections = document.querySelectorAll('.parallax-section');
  if (parallaxSections.length) {
    window.addEventListener('scroll', function() {
      var scrollY = window.scrollY;
      parallaxSections.forEach(function(section) {
        var speed = parseFloat(section.getAttribute('data-parallax-speed')) || 0.15;
        var rect = section.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) {
          var y = rect.top * speed;
          var bg = section.querySelector('.parallax-bg');
          if (bg) {
            bg.style.transform = 'translateY(' + y + 'px)';
          } else {
            section.style.backgroundPositionY = (y * 0.5) + 'px';
          }
        }
      });
    });
  }
  var navbar = document.getElementById('mainNavbar');
  if (navbar) {
    function updateNavbar() {
      if (window.scrollY > 60) {
        navbar.classList.add('navbar-shrink');
      } else {
        navbar.classList.remove('navbar-shrink');
      }
    }
    window.addEventListener('scroll', updateNavbar, { passive: true });
    updateNavbar();
  }

  var hamburger = document.getElementById('hamburgerBtn');
  var navLinks = document.getElementById('navLinks');
  var mobileOverlay = document.getElementById('mobileOverlay');
  if (hamburger && navLinks) {
    function openMobile() {
      navLinks.classList.add('mobile-open');
      hamburger.classList.add('active');
      if (mobileOverlay) mobileOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    function closeMobile() {
      navLinks.classList.remove('mobile-open');
      hamburger.classList.remove('active');
      if (mobileOverlay) mobileOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }
    hamburger.addEventListener('click', function() {
      if (navLinks.classList.contains('mobile-open')) { closeMobile(); } else { openMobile(); }
    });
    if (mobileOverlay) {
      mobileOverlay.addEventListener('click', closeMobile);
    }
    navLinks.querySelectorAll('a').forEach(function(link) {
      link.addEventListener('click', function() {
        if (navLinks.classList.contains('mobile-open')) { closeMobile(); }
      });
    });
  }
})();
</script>
