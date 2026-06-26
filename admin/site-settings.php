<?php
session_start();
include('includes/connection.php');
include('includes/activity-log.php');
error_reporting(0);
if (!isset($_SESSION['admin_id']) AND !isset($_SESSION['admin_Email'])) {
    header('location:login.php');
    exit;
}

$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'tbl_site_settings'");
if (mysqli_num_rows($tableCheck) == 0) {
    echo "<!DOCTYPE html><html><head><link href='assets/vendor/bootstrap-icons/bootstrap-icons.css' rel='stylesheet'><link href='assets/css/admin-2027-theme.css' rel='stylesheet'><style>body{font-family:system-ui;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f8f9fc;}.card{max-width:500px;background:#fff;border-radius:20px;padding:40px;text-align:center;box-shadow:0 8px 30px rgba(0,0,0,0.06);}h1{font-size:1.3rem;color:#3D47C9;margin-bottom:12px;}p{color:#666;font-size:0.92rem;margin-bottom:20px;line-height:1.6;}.btn{display:inline-block;padding:12px 28px;border-radius:12px;background:#525FE1;color:#fff;text-decoration:none;font-weight:700;transition:all 0.2s;}.btn:hover{background:#3D47C9;transform:translateY(-2px);}code{background:#f1f5f9;padding:2px 8px;border-radius:4px;font-size:0.85rem;}</style></head><body><div class='card'><i class='bi bi-database' style='font-size:2.5rem;color:#525FE1;display:block;margin-bottom:16px;'></i><h1>Database Table Not Found</h1><p>The <code>tbl_site_settings</code> table doesn't exist yet. Click below to create all tables.</p><a href='site-setup.php' class='btn'>Run Setup Script</a></div></body></html>";
    exit;
}

$id = $_SESSION['admin_id'];
$FirstChar = mysqli_query($conn, "SELECT * FROM `tbl_admins` WHERE id = '$id'");
$First = mysqli_fetch_array($FirstChar);
$Char = strtoupper($First['FirstName']);

if (!is_dir('../assets/uploads/')) { mkdir('../assets/uploads/', 0777, true); }

function uploadSiteImage($file, $existing = null) {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) return $existing;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp','svg'];
    if (!in_array($ext, $allowed)) return false;
    $name = 'site_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = dirname(__FILE__) . '/../assets/uploads/' . $name;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        if ($existing && file_exists(dirname(__FILE__) . '/../' . $existing)) unlink(dirname(__FILE__) . '/../' . $existing);
        return 'assets/uploads/' . $name;
    }
    return false;
}

// Handle settings save (with image upload)
if (isset($_POST['save_settings'])) {
    $keys = ['site_name','site_tagline','site_email','site_phone','site_address',
             'social_facebook','social_twitter','social_instagram','social_linkedin',
             'footer_copyright','maintenance_mode'];
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $val = mysqli_real_escape_string($conn, $_POST[$key]);
            mysqli_query($conn, "INSERT INTO `tbl_site_settings` (`setting_key`, `setting_value`) VALUES ('$key', '$val') ON DUPLICATE KEY UPDATE `setting_value` = '$val'");
        }
    }
    // Handle logo upload
    $currentLogo = $settings['site_logo'] ?? null;
    $newLogo = uploadSiteImage($_FILES['site_logo'], $currentLogo);
    if ($newLogo && $newLogo !== $currentLogo) {
        $v = mysqli_real_escape_string($conn, $newLogo);
        mysqli_query($conn, "INSERT INTO `tbl_site_settings` (`setting_key`, `setting_value`) VALUES ('site_logo', '$v') ON DUPLICATE KEY UPDATE `setting_value` = '$v'");
    }
    logActivity($conn, 'update_settings', 'Updated general site settings');
    echo "<script>GihekeToast.showModal('success','Settings Saved','General settings updated.')</script>";
}

// Handle homepage text save
if (isset($_POST['save_homepage'])) {
    $keys = [
        'hero_badge_prefix','hero_badge_suffix','hero_heading','hero_subtitle',
        'hero_btn_primary','hero_btn_secondary','hero_stat_1_label','hero_stat_2_label','hero_stat_3_label',
        'hero_badge_1','hero_badge_2',
        'about_label','about_subtitle',
        'vision_title','vision_text','mission_title','mission_text','motto_title','motto_text',
        'objective_title','objective_text','slogan_title','slogan_text','values_heading',
        'story_label','story_heading','story_lead','story_para_1','story_quote','story_para_2','story_para_3',
        'story_badge_num','story_badge_text',
        'principal_label','principal_heading','principal_msg_1','principal_msg_2','principal_name','principal_title',
        'staff_label','staff_title','staff_subtitle',
        'programs_label','programs_title','programs_subtitle','programs_duration',
        'features_label','features_title','features_subtitle',
        'cta_heading','cta_text',
        'news_label','news_title','news_subtitle','news_empty',
        'contact_label','contact_title','contact_subtitle',
        'contact_info_heading','contact_info_location','contact_info_email','contact_info_phone',
        'contact_form_heading','contact_form_name_placeholder','contact_form_email_placeholder',
        'contact_form_msg_placeholder','contact_form_btn',
    ];
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $val = mysqli_real_escape_string($conn, $_POST[$key]);
            mysqli_query($conn, "INSERT INTO `tbl_site_settings` (`setting_key`, `setting_value`) VALUES ('$key', '$val') ON DUPLICATE KEY UPDATE `setting_value` = '$val'");
        }
    }
    // Handle hero background image upload
    $currentHeroBg = $settings['hero_bg_image'] ?? null;
    $newHeroBg = uploadSiteImage($_FILES['hero_bg_image'], $currentHeroBg);
    if ($newHeroBg && $newHeroBg !== $currentHeroBg) {
        $v = mysqli_real_escape_string($conn, $newHeroBg);
        mysqli_query($conn, "INSERT INTO `tbl_site_settings` (`setting_key`, `setting_value`) VALUES ('hero_bg_image', '$v') ON DUPLICATE KEY UPDATE `setting_value` = '$v'");
    }
    logActivity($conn, 'update_settings', 'Updated homepage text content');
    echo "<script>GihekeToast.showModal('success','Content Saved','Homepage content updated.')</script>";
}

// Fetch all settings
$settings = [];
$q = mysqli_query($conn, "SELECT `setting_key`, `setting_value` FROM `tbl_site_settings`");
while ($r = mysqli_fetch_assoc($q)) {
    $settings[$r['setting_key']] = $r['setting_value'];
}

$activeTab = $_GET['tab'] ?? 'general';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Site Settings</title>
  <link href="../img/giheke logo.webp" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <link href="assets/css/giheke-toast.css" rel="stylesheet">
  <style>
    .settings-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); padding: 32px; border: 1px solid rgba(0,0,0,0.04); margin-top: 20px; }
    .settings-card:first-of-type { margin-top: 0; }
    .settings-card h3 { font-size: 1.1rem; font-weight: 700; color: #3D47C9; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0; }
    .form-label-custom { font-weight: 600; font-size: 0.85rem; color: #555; margin-bottom: 6px; display: block; }
    .form-control-custom { width: 100%; padding: 10px 14px; border: 2px solid #e8e8e8; border-radius: 10px; font-size: 0.92rem; transition: all 0.2s; background: #fafafa; }
    .form-control-custom:focus { border-color: #525FE1; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(74,10,110,0.08); }
    textarea.form-control-custom { min-height: 80px; resize: vertical; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
    .form-grid-2 .full-w, .form-grid-3 .full-w { grid-column: 1 / -1; }
    .btn-save { padding: 12px 32px; border-radius: 12px; font-weight: 700; font-size: 0.92rem; border: none; background: linear-gradient(135deg,#525FE1,#3D47C9); color: #fff; transition: all 0.3s; }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(82,95,225,0.3); }
    .btn-outline-primary { border: 2px solid #525FE1; color: #525FE1; background: transparent; padding: 10px 24px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
    .btn-outline-primary:hover { background: #525FE1; color: #fff; }
    .ss-tabs { display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: 24px; background: #fff; padding: 8px; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.04); }
    .ss-tab { padding: 10px 20px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; color: #666; text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
    .ss-tab:hover { background: #f1f5f9; color: #3D47C9; }
    .ss-tab.active { background: #525FE1; color: #fff; box-shadow: 0 4px 12px rgba(82,95,225,0.25); }
    .section-help { font-size: 0.82rem; color: #94a3b8; margin-top: 4px; }
    .inline-link { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 10px; background: #f1f5f9; color: #525FE1; font-weight: 600; font-size: 0.85rem; text-decoration: none; transition: all 0.2s; margin-bottom: 8px; }
    .inline-link:hover { background: #eef2ff; }
    @media (max-width: 768px) { .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; } .ss-tabs { overflow-x: auto; flex-wrap: nowrap; } }
  </style>
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center"><img src="assets/img/logo.png" alt=""><span class="d-none d-lg-block">Administration</span></a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <?php include('includes/notifications.php'); ?>
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="admin-img/<?php echo $First['ImageUrl'] ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo substr($Char,0,1) .". ".$First['LastName']; ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header"><h6><?php echo $First['FirstName']." ".$First['LastName']; ?></h6><span>School Admin</span></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="user-profile.php"><i class="bi bi-person"></i><span>My Profile</span></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center" href="logout.php"><i class="bi bi-box-arrow-right"></i><span>Sign Out</span></a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <?php include('includes/sidebar.php'); ?>
  <main id="main" class="main">
    <div style="padding:24px;">
      <div class="pagetitle" style="margin-bottom:16px;">
        <h1 style="font-size:1.6rem;font-weight:800;color:#3D47C9;">Site Settings</h1>
        <nav><ol class="breadcrumb" style="background:transparent;padding:0;margin:0;">
          <li class="breadcrumb-item"><a href="index.php" style="color:#525FE1;">Home</a></li>
          <li class="breadcrumb-item active" style="color:#888;">Site Settings</li>
        </ol></nav>
      </div>

      <!-- Tabs -->
      <div class="ss-tabs">
        <a href="?tab=general" class="ss-tab <?php echo $activeTab=='general'?'active':''; ?>"><i class="bi bi-gear"></i> General</a>
        <a href="?tab=hero" class="ss-tab <?php echo $activeTab=='hero'?'active':''; ?>"><i class="bi bi-megaphone"></i> Hero</a>
        <a href="?tab=about" class="ss-tab <?php echo $activeTab=='about'?'active':''; ?>"><i class="bi bi-info-circle"></i> About</a>
        <a href="?tab=programs" class="ss-tab <?php echo $activeTab=='programs'?'active':''; ?>"><i class="bi bi-tools"></i> Programs</a>
        <a href="?tab=features" class="ss-tab <?php echo $activeTab=='features'?'active':''; ?>"><i class="bi bi-star"></i> Features</a>
        <a href="?tab=staff" class="ss-tab <?php echo $activeTab=='staff'?'active':''; ?>"><i class="bi bi-people"></i> Staff</a>
        <a href="?tab=cta" class="ss-tab <?php echo $activeTab=='cta'?'active':''; ?>"><i class="bi bi-chat"></i> CTA & Contact</a>
      </div>

<?php if ($activeTab == 'general'): ?>
      <!-- ========== GENERAL ========== -->
      <form method="post" enctype="multipart/form-data">
        <div class="settings-card">
          <h3>General Information</h3>
          <div class="form-grid-2">
            <div>
              <label class="form-label-custom">Site Name</label>
              <input type="text" name="site_name" class="form-control-custom" value="<?php echo htmlspecialchars($settings['site_name']??'GIHEKE TSS'); ?>">
            </div>
            <div>
              <label class="form-label-custom">Maintenance Mode</label>
              <select name="maintenance_mode" class="form-control-custom">
                <option value="0" <?php echo ($settings['maintenance_mode']??'0')=='0'?'selected':''; ?>>Disabled</option>
                <option value="1" <?php echo ($settings['maintenance_mode']??'0')=='1'?'selected':''; ?>>Enabled</option>
              </select>
            </div>
            <div class="full-w">
              <label class="form-label-custom">Tagline</label>
              <textarea name="site_tagline" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['site_tagline']??''); ?></textarea>
            </div>
            <div class="full-w">
              <label class="form-label-custom">School Logo</label>
              <input type="file" name="site_logo" class="form-control-custom" accept="image/*">
              <?php if (!empty($settings['site_logo'])): ?>
                <div style="margin-top:8px;display:flex;align-items:center;gap:10px;">
                  <img src="../<?php echo htmlspecialchars($settings['site_logo']); ?>" style="height:48px;border-radius:8px;border:2px solid #f0f0f0;">
                  <span style="font-size:0.82rem;color:#888;">Current logo</span>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="settings-card">
          <h3>Contact Information</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Email</label><input type="email" name="site_email" class="form-control-custom" value="<?php echo htmlspecialchars($settings['site_email']??''); ?>"></div>
            <div><label class="form-label-custom">Phone</label><input type="text" name="site_phone" class="form-control-custom" value="<?php echo htmlspecialchars($settings['site_phone']??''); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Address</label><input type="text" name="site_address" class="form-control-custom" value="<?php echo htmlspecialchars($settings['site_address']??''); ?>"></div>
          </div>
        </div>
        <div class="settings-card">
          <h3>Social Media Links</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Facebook</label><input type="url" name="social_facebook" class="form-control-custom" value="<?php echo htmlspecialchars($settings['social_facebook']??''); ?>"></div>
            <div><label class="form-label-custom">Twitter</label><input type="url" name="social_twitter" class="form-control-custom" value="<?php echo htmlspecialchars($settings['social_twitter']??''); ?>"></div>
            <div><label class="form-label-custom">Instagram</label><input type="url" name="social_instagram" class="form-control-custom" value="<?php echo htmlspecialchars($settings['social_instagram']??''); ?>"></div>
            <div><label class="form-label-custom">LinkedIn</label><input type="url" name="social_linkedin" class="form-control-custom" value="<?php echo htmlspecialchars($settings['social_linkedin']??''); ?>"></div>
          </div>
        </div>
        <div class="settings-card">
          <h3>Footer</h3>
          <div class="form-grid-2">
            <div class="full-w"><label class="form-label-custom">Copyright Text</label><input type="text" name="footer_copyright" class="form-control-custom" value="<?php echo htmlspecialchars($settings['footer_copyright']??''); ?>"></div>
          </div>
        </div>
        <div style="margin-top:24px;text-align:right;">
          <button type="submit" name="save_settings" class="btn-save"><i class="bi bi-check2-circle"></i> Save General Settings</button>
        </div>
      </form>

<?php elseif ($activeTab == 'hero'): ?>
      <!-- ========== HERO ========== -->
      <form method="post" enctype="multipart/form-data">
        <div class="settings-card">
          <h3>Hero Section</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Badge Prefix</label><input type="text" name="hero_badge_prefix" class="form-control-custom" value="<?php echo htmlspecialchars($settings['hero_badge_prefix']??'Welcome to'); ?>"></div>
            <div><label class="form-label-custom">Badge Suffix</label><input type="text" name="hero_badge_suffix" class="form-control-custom" value="<?php echo htmlspecialchars($settings['hero_badge_suffix']??'School'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Heading (HTML allowed)</label><textarea name="hero_heading" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['hero_heading']??'Building Futures Through <span class="highlight">Technical Education</span> Excellence'); ?></textarea></div>
            <div class="full-w"><label class="form-label-custom">Subtitle</label><textarea name="hero_subtitle" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['hero_subtitle']??''); ?></textarea></div>
            <div><label class="form-label-custom">Primary Button Text</label><input type="text" name="hero_btn_primary" class="form-control-custom" value="<?php echo htmlspecialchars($settings['hero_btn_primary']??'Apply Now'); ?>"></div>
            <div><label class="form-label-custom">Secondary Button Text</label><input type="text" name="hero_btn_secondary" class="form-control-custom" value="<?php echo htmlspecialchars($settings['hero_btn_secondary']??'Learn More'); ?>"></div>
          </div>
        </div>
        <div class="settings-card">
          <h3>Hero Stats & Badges</h3>
          <div class="form-grid-3">
            <div><label class="form-label-custom">Stat 1 Label</label><input type="text" name="hero_stat_1_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['hero_stat_1_label']??'Students Enrolled'); ?>"></div>
            <div><label class="form-label-custom">Stat 2 Label</label><input type="text" name="hero_stat_2_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['hero_stat_2_label']??'Expert Teachers'); ?>"></div>
            <div><label class="form-label-custom">Stat 3 Label</label><input type="text" name="hero_stat_3_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['hero_stat_3_label']??'Trade Programs'); ?>"></div>
            <div><label class="form-label-custom">Badge 1</label><input type="text" name="hero_badge_1" class="form-control-custom" value="<?php echo htmlspecialchars($settings['hero_badge_1']??'15+ Years Excellence'); ?>"></div>
            <div><label class="form-label-custom">Badge 2</label><input type="text" name="hero_badge_2" class="form-control-custom" value="<?php echo htmlspecialchars($settings['hero_badge_2']??'700+ Students'); ?>"></div>
            <div class="full-w">
              <label class="form-label-custom">Hero Background Image</label>
              <input type="file" name="hero_bg_image" class="form-control-custom" accept="image/*">
              <?php if (!empty($settings['hero_bg_image'])): ?>
                <div style="margin-top:8px;display:flex;align-items:center;gap:10px;">
                  <img src="../<?php echo htmlspecialchars($settings['hero_bg_image']); ?>" style="height:60px;border-radius:8px;border:2px solid #f0f0f0;object-fit:cover;">
                  <span style="font-size:0.82rem;color:#888;">Current background</span>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div style="margin-top:24px;text-align:right;">
          <button type="submit" name="save_homepage" class="btn-save"><i class="bi bi-check2-circle"></i> Save Hero Content</button>
        </div>
      </form>

<?php elseif ($activeTab == 'about'): ?>
      <!-- ========== ABOUT ========== -->
      <form method="post">
        <div class="settings-card">
          <h3>About Section Header</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Label</label><input type="text" name="about_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['about_label']??'About GIHEKE TSS'); ?>"></div>
            <div><label class="form-label-custom">Values Heading</label><input type="text" name="values_heading" class="form-control-custom" value="<?php echo htmlspecialchars($settings['values_heading']??'Our Core Values'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Subtitle</label><textarea name="about_subtitle" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['about_subtitle']??''); ?></textarea></div>
          </div>
        </div>
        <div class="settings-card">
          <h3>Vision, Mission, Motto, Objective, Slogan</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Vision Title</label><input type="text" name="vision_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['vision_title']??'Our Vision'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Vision Text</label><textarea name="vision_text" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['vision_text']??''); ?></textarea></div>
            <div><label class="form-label-custom">Mission Title</label><input type="text" name="mission_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['mission_title']??'Our Mission'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Mission Text</label><textarea name="mission_text" class="form-control-custom" rows="3"><?php echo htmlspecialchars($settings['mission_text']??''); ?></textarea></div>
            <div><label class="form-label-custom">Motto Title</label><input type="text" name="motto_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['motto_title']??'Our Motto'); ?>"></div>
            <div><label class="form-label-custom">Motto Text</label><input type="text" name="motto_text" class="form-control-custom" value="<?php echo htmlspecialchars($settings['motto_text']??''); ?>"></div>
            <div><label class="form-label-custom">Objective Title</label><input type="text" name="objective_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['objective_title']??'Our Objective'); ?>"></div>
            <div><label class="form-label-custom">Objective Text</label><input type="text" name="objective_text" class="form-control-custom" value="<?php echo htmlspecialchars($settings['objective_text']??''); ?>"></div>
            <div><label class="form-label-custom">Slogan Title</label><input type="text" name="slogan_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['slogan_title']??'Our Slogan'); ?>"></div>
            <div><label class="form-label-custom">Slogan Text</label><input type="text" name="slogan_text" class="form-control-custom" value="<?php echo htmlspecialchars($settings['slogan_text']??''); ?>"></div>
          </div>
        </div>
        <div class="settings-card">
          <h3>Our Story</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Story Label</label><input type="text" name="story_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['story_label']??'Our Story'); ?>"></div>
            <div><label class="form-label-custom">Story Heading</label><input type="text" name="story_heading" class="form-control-custom" value="<?php echo htmlspecialchars($settings['story_heading']??'Forging Paths Through Technical Education'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Lead</label><textarea name="story_lead" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['story_lead']??''); ?></textarea></div>
            <div class="full-w"><label class="form-label-custom">Paragraph 1</label><textarea name="story_para_1" class="form-control-custom" rows="3"><?php echo htmlspecialchars($settings['story_para_1']??''); ?></textarea></div>
            <div class="full-w"><label class="form-label-custom">Quote</label><textarea name="story_quote" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['story_quote']??''); ?></textarea></div>
            <div class="full-w"><label class="form-label-custom">Paragraph 2</label><textarea name="story_para_2" class="form-control-custom" rows="3"><?php echo htmlspecialchars($settings['story_para_2']??''); ?></textarea></div>
            <div class="full-w"><label class="form-label-custom">Paragraph 3</label><textarea name="story_para_3" class="form-control-custom" rows="3"><?php echo htmlspecialchars($settings['story_para_3']??''); ?></textarea></div>
            <div><label class="form-label-custom">Badge Number</label><input type="text" name="story_badge_num" class="form-control-custom" value="<?php echo htmlspecialchars($settings['story_badge_num']??'8+'); ?>"></div>
            <div><label class="form-label-custom">Badge Text</label><input type="text" name="story_badge_text" class="form-control-custom" value="<?php echo htmlspecialchars($settings['story_badge_text']??'Years of Excellence'); ?>"></div>
          </div>
        </div>
        <div class="settings-card">
          <h3>Principal's Message</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Label</label><input type="text" name="principal_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['principal_label']??"Principal's Message"); ?>"></div>
            <div><label class="form-label-custom">Heading</label><input type="text" name="principal_heading" class="form-control-custom" value="<?php echo htmlspecialchars($settings['principal_heading']??'Building Skills, Shaping Futures'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Message 1</label><textarea name="principal_msg_1" class="form-control-custom" rows="3"><?php echo htmlspecialchars($settings['principal_msg_1']??''); ?></textarea></div>
            <div class="full-w"><label class="form-label-custom">Message 2</label><textarea name="principal_msg_2" class="form-control-custom" rows="3"><?php echo htmlspecialchars($settings['principal_msg_2']??''); ?></textarea></div>
            <div><label class="form-label-custom">Principal Name</label><input type="text" name="principal_name" class="form-control-custom" value="<?php echo htmlspecialchars($settings['principal_name']??'KANYANDEGE Joseph Desire'); ?>"></div>
            <div><label class="form-label-custom">Principal Title</label><input type="text" name="principal_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['principal_title']??'Principal, GIHEKE TSS'); ?>"></div>
          </div>
        </div>
        <div style="margin-top:24px;text-align:right;">
          <button type="submit" name="save_homepage" class="btn-save"><i class="bi bi-check2-circle"></i> Save About Content</button>
        </div>
      </form>

<?php elseif ($activeTab == 'programs'): ?>
      <!-- ========== PROGRAMS ========== -->
      <form method="post">
        <div class="settings-card">
          <h3>Programs Section Text</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Label</label><input type="text" name="programs_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['programs_label']??'Our Programs'); ?>"></div>
            <div><label class="form-label-custom">Default Duration</label><input type="text" name="programs_duration" class="form-control-custom" value="<?php echo htmlspecialchars($settings['programs_duration']??'3 Years'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Title</label><input type="text" name="programs_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['programs_title']??'Specialized Technical Trades'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Subtitle</label><textarea name="programs_subtitle" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['programs_subtitle']??''); ?></textarea></div>
          </div>
          <div style="margin-top:16px;padding-top:16px;border-top:2px solid #f0f0f0;">
            <p style="font-size:0.85rem;color:#888;margin-bottom:12px;"><i class="bi bi-info-circle"></i> Manage the actual trade program listings:</p>
            <a href="manage-trades.php" class="inline-link"><i class="bi bi-tools"></i> Manage Trade Programs</a>
          </div>
        </div>
        <div style="margin-top:24px;text-align:right;">
          <button type="submit" name="save_homepage" class="btn-save"><i class="bi bi-check2-circle"></i> Save Programs Text</button>
        </div>
      </form>

<?php elseif ($activeTab == 'features'): ?>
      <!-- ========== FEATURES ========== -->
      <form method="post">
        <div class="settings-card">
          <h3>Features Section Text</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Label</label><input type="text" name="features_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['features_label']??'Why Choose Us'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Title</label><input type="text" name="features_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['features_title']??'What Makes GIHEKE Special'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Subtitle</label><textarea name="features_subtitle" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['features_subtitle']??''); ?></textarea></div>
          </div>
          <div style="margin-top:16px;padding-top:16px;border-top:2px solid #f0f0f0;">
            <p style="font-size:0.85rem;color:#888;margin-bottom:12px;"><i class="bi bi-info-circle"></i> Manage individual feature cards:</p>
            <a href="manage-features.php" class="inline-link"><i class="bi bi-star"></i> Manage Features</a>
            <a href="manage-values.php" class="inline-link" style="margin-left:8px;"><i class="bi bi-heart"></i> Manage Core Values</a>
          </div>
        </div>
        <div style="margin-top:24px;text-align:right;">
          <button type="submit" name="save_homepage" class="btn-save"><i class="bi bi-check2-circle"></i> Save Features Text</button>
        </div>
      </form>

<?php elseif ($activeTab == 'staff'): ?>
      <!-- ========== STAFF ========== -->
      <form method="post">
        <div class="settings-card">
          <h3>Staff Section Text</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Label</label><input type="text" name="staff_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['staff_label']??'Our Team'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Title</label><input type="text" name="staff_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['staff_title']??'Meet Our Staff'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Subtitle</label><textarea name="staff_subtitle" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['staff_subtitle']??''); ?></textarea></div>
          </div>
          <div style="margin-top:16px;padding-top:16px;border-top:2px solid #f0f0f0;">
            <p style="font-size:0.85rem;color:#888;margin-bottom:12px;"><i class="bi bi-info-circle"></i> Manage individual staff members and facilities:</p>
            <a href="manage-staff.php" class="inline-link"><i class="bi bi-people"></i> Manage Staff Members</a>
            <a href="manage-facilities.php" class="inline-link" style="margin-left:8px;"><i class="bi bi-building"></i> Manage Facilities</a>
          </div>
        </div>
        <div style="margin-top:24px;text-align:right;">
          <button type="submit" name="save_homepage" class="btn-save"><i class="bi bi-check2-circle"></i> Save Staff Section Text</button>
        </div>
      </form>

<?php elseif ($activeTab == 'cta'): ?>
      <!-- ========== CTA & CONTACT ========== -->
      <form method="post">
        <div class="settings-card">
          <h3>Call-to-Action Section</h3>
          <div class="form-grid-2">
            <div class="full-w"><label class="form-label-custom">CTA Heading</label><input type="text" name="cta_heading" class="form-control-custom" value="<?php echo htmlspecialchars($settings['cta_heading']??'Apply for Admission at GIHEKE TSS'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">CTA Text</label><textarea name="cta_text" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['cta_text']??''); ?></textarea></div>
          </div>
        </div>
        <div class="settings-card">
          <h3>News Section</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Label</label><input type="text" name="news_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['news_label']??'Latest News'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Title</label><input type="text" name="news_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['news_title']??'School Updates'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Subtitle</label><textarea name="news_subtitle" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['news_subtitle']??''); ?></textarea></div>
            <div class="full-w"><label class="form-label-custom">Empty State Message</label><input type="text" name="news_empty" class="form-control-custom" value="<?php echo htmlspecialchars($settings['news_empty']??'No news articles yet. Check back soon for updates!'); ?>"></div>
          </div>
        </div>
        <div class="settings-card">
          <h3>Contact Section</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Label</label><input type="text" name="contact_label" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_label']??'Get In Touch'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Title</label><input type="text" name="contact_title" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_title']??'Contact Us'); ?>"></div>
            <div class="full-w"><label class="form-label-custom">Subtitle</label><textarea name="contact_subtitle" class="form-control-custom" rows="2"><?php echo htmlspecialchars($settings['contact_subtitle']??''); ?></textarea></div>
          </div>
        </div>
        <div class="settings-card">
          <h3>Contact Info Labels</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Info Block Heading</label><input type="text" name="contact_info_heading" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_info_heading']??'Contact Information'); ?>"></div>
            <div><label class="form-label-custom">Location Label</label><input type="text" name="contact_info_location" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_info_location']??'Location'); ?>"></div>
            <div><label class="form-label-custom">Email Label</label><input type="text" name="contact_info_email" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_info_email']??'Email'); ?>"></div>
            <div><label class="form-label-custom">Phone Label</label><input type="text" name="contact_info_phone" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_info_phone']??'Phone'); ?>"></div>
          </div>
        </div>
        <div class="settings-card">
          <h3>Contact Form Labels</h3>
          <div class="form-grid-2">
            <div><label class="form-label-custom">Form Heading</label><input type="text" name="contact_form_heading" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_form_heading']??'Send us a Message'); ?>"></div>
            <div><label class="form-label-custom">Name Placeholder</label><input type="text" name="contact_form_name_placeholder" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_form_name_placeholder']??'Your full name'); ?>"></div>
            <div><label class="form-label-custom">Email Placeholder</label><input type="text" name="contact_form_email_placeholder" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_form_email_placeholder']??'Your email'); ?>"></div>
            <div><label class="form-label-custom">Message Placeholder</label><input type="text" name="contact_form_msg_placeholder" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_form_msg_placeholder']??'Your message...'); ?>"></div>
            <div><label class="form-label-custom">Submit Button Text</label><input type="text" name="contact_form_btn" class="form-control-custom" value="<?php echo htmlspecialchars($settings['contact_form_btn']??'Send Message'); ?>"></div>
          </div>
        </div>
        <div style="margin-top:24px;text-align:right;">
          <button type="submit" name="save_homepage" class="btn-save"><i class="bi bi-check2-circle"></i> Save CTA & Contact Content</button>
        </div>
      </form>

<?php endif; ?>
    </div>
  </main>

  <footer class="admin-footer">
    &copy; <?php echo date('Y'); ?> Copyright <strong><span>GIHEKE TSS</span></strong>. All Rights Reserved.
    <div class="credits">Developed by <a href="https://devoma.vercel.app" target="_blank">Omar MBONABUCYA</a></div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center" id="backToTopAdmin"><i class="bi bi-arrow-up-short"></i></a>

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
<?php $conn->close(); ?>
