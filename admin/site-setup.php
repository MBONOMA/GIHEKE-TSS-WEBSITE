<?php
session_start();
include('includes/connection.php');
if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit;
}

// Prevent timeout for big setup
set_time_limit(120);

$results = [];

// ====== EXISTING TABLES (unchanged) ======
$existingTables = [
    "CREATE TABLE IF NOT EXISTS `tbl_site_settings` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `setting_key` VARCHAR(100) NOT NULL UNIQUE,
        `setting_value` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE IF NOT EXISTS `tbl_activity_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL DEFAULT 0,
        `user_type` VARCHAR(50) DEFAULT 'admin',
        `action` VARCHAR(100) NOT NULL,
        `description` TEXT,
        `ip_address` VARCHAR(45) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_user` (`user_id`, `user_type`),
        INDEX `idx_action` (`action`),
        INDEX `idx_created` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE IF NOT EXISTS `tbl_media` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `file_name` VARCHAR(255) NOT NULL,
        `file_path` VARCHAR(500) NOT NULL,
        `file_type` VARCHAR(50) DEFAULT NULL,
        `file_size` INT DEFAULT NULL,
        `alt_text` VARCHAR(255) DEFAULT NULL,
        `uploaded_by` INT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_type` (`file_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE IF NOT EXISTS `tbl_navigation` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `label` VARCHAR(100) NOT NULL,
        `url` VARCHAR(500) NOT NULL,
        `parent_id` INT DEFAULT NULL,
        `sort_order` INT DEFAULT 0,
        `target` VARCHAR(10) DEFAULT '_self',
        `icon` VARCHAR(50) DEFAULT NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_parent` (`parent_id`),
        INDEX `idx_active` (`is_active`),
        INDEX `idx_order` (`sort_order`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE IF NOT EXISTS `tbl_seo_meta` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `page_path` VARCHAR(500) NOT NULL UNIQUE,
        `meta_title` VARCHAR(255) DEFAULT NULL,
        `meta_description` TEXT,
        `meta_keywords` TEXT,
        `og_image` VARCHAR(500) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

foreach ($existingTables as $sql) {
    if (mysqli_query($conn, $sql)) {
        $results[] = "Base table ready.";
    } else {
        $results[] = "Error: " . mysqli_error($conn);
    }
}

// ====== NEW CONTENT TABLES ======
$newTables = [
    "CREATE TABLE IF NOT EXISTS `tbl_trades` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(200) NOT NULL,
        `description` TEXT,
        `duration` VARCHAR(50) DEFAULT '3 Years',
        `icon` VARCHAR(50) DEFAULT 'bi bi-tools',
        `image` VARCHAR(255) DEFAULT NULL,
        `sort_order` INT DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE IF NOT EXISTS `tbl_staff_members` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(200) NOT NULL,
        `role` VARCHAR(200) DEFAULT NULL,
        `phone` VARCHAR(50) DEFAULT NULL,
        `initials` VARCHAR(10) DEFAULT NULL,
        `image` VARCHAR(255) DEFAULT NULL,
        `sort_order` INT DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE IF NOT EXISTS `tbl_facilities` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(200) NOT NULL,
        `description` TEXT,
        `image` VARCHAR(255) DEFAULT NULL,
        `sort_order` INT DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE IF NOT EXISTS `tbl_features` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(200) NOT NULL,
        `description` TEXT,
        `icon` VARCHAR(50) DEFAULT 'bi bi-star',
        `sort_order` INT DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    "CREATE TABLE IF NOT EXISTS `tbl_core_values` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(200) NOT NULL,
        `description` TEXT,
        `icon` VARCHAR(50) DEFAULT 'bi bi-heart',
        `sort_order` INT DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

foreach ($newTables as $sql) {
    if (mysqli_query($conn, $sql)) {
        $results[] = "Content table ready.";
    } else {
        $results[] = "Error: " . mysqli_error($conn);
    }
}

// Add image column to tables that may not have it yet
$alterQueries = [
    "ALTER TABLE `tbl_features` ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) DEFAULT NULL AFTER `icon`",
    "ALTER TABLE `tbl_core_values` ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) DEFAULT NULL AFTER `icon`",
];
foreach ($alterQueries as $sql) {
    mysqli_query($conn, $sql);
    // Ignore errors (column may already exist)
}
$results[] = "Image columns checked.";

// ====== SEED HOMEPAGE TEXT SETTINGS ======
$homepageSettings = [
    ['hero_badge_prefix', 'Welcome to'],
    ['hero_badge_suffix', 'School'],
    ['hero_heading', 'Building Futures Through <span class="highlight">Technical Education</span> Excellence'],
    ['hero_subtitle', 'GIHEKE Technical Secondary School empowers students with practical skills, discipline, and innovation for a brighter tomorrow.'],
    ['hero_btn_primary', 'Apply Now'],
    ['hero_btn_secondary', 'Learn More'],
    ['hero_stat_1_label', 'Students Enrolled'],
    ['hero_stat_2_label', 'Expert Teachers'],
    ['hero_stat_3_label', 'Trade Programs'],
    ['hero_badge_1', '15+ Years Excellence'],
    ['hero_badge_2', '700+ Students'],
    ['about_label', 'About GIHEKE TSS'],
    ['about_subtitle', 'Empowering the next generation of technical leaders through hands-on education, discipline, and innovation in Rusizi District, Rwanda.'],
    ['vision_title', 'Our Vision'],
    ['vision_text', 'To provide high quality in technical skills by enhancing competent trainees.'],
    ['mission_title', 'Our Mission'],
    ['mission_text', 'To provide accessible, high-quality technical and vocational education that equips students with practical skills, critical thinking, and ethical values needed to excel in the modern workforce and contribute meaningfully to society.'],
    ['motto_title', 'Our Motto'],
    ['motto_text', 'We\'re our country\'s solutions providers.'],
    ['objective_title', 'Our Objective'],
    ['objective_text', 'Empowering practical skills for better future.'],
    ['slogan_title', 'Our Slogan'],
    ['slogan_text', 'From training to doing.'],
    ['values_heading', 'Our Core Values'],
    ['story_label', 'Our Story'],
    ['story_heading', 'Forging Paths Through Technical Education'],
    ['story_lead', 'Born in the heart of Giheke, our school rose from a vision to create world-class technical leaders.'],
    ['story_para_1', 'In 2017, a small group of educators and engineers gathered in Giheke Sector with one mission: to give every young person in Rusizi District a chance to learn by doing. What started as a handful of workshops in a modest compound has since grown into GIHEKE Technical Secondary School — a place where theory meets practice, and classrooms become workshops.'],
    ['story_quote', '"Education should not only open minds — it should build futures."'],
    ['story_para_2', 'Today, we serve over 700 students across seven technical trades. Our campuses are equipped with modern laboratories, computer labs, and workshops that mirror the demands of today&#39;s workforce.'],
    ['story_para_3', 'From our first intake of 120 students to today&#39;s thriving community, one value has never changed: every student leaves with a skill, a purpose, and the confidence to shape tomorrow.'],
    ['story_badge_num', '8+'],
    ['story_badge_text', 'Years of Excellence'],
    ['principal_label', 'Principal\'s Message'],
    ['principal_heading', 'Building Skills, Shaping Futures'],
    ['principal_msg_1', '"At GIHEKE Technical Secondary School, we believe that every student possesses unique talents waiting to be discovered. Our mission is to create an environment where those talents can flourish through practical, hands-on education that prepares students not for exams, but for life.'],
    ['principal_msg_2', 'Our dedicated team of trainers works tirelessly to ensure that each student receives the guidance, support, and resources they need to succeed. We are proud of our growing community of over 700 students and our alumni who are making a difference across Rwanda and beyond."'],
    ['principal_name', 'KANYANDEGE Joseph Desire'],
    ['principal_title', 'Principal, GIHEKE TSS'],
    ['staff_label', 'Our Team'],
    ['staff_title', 'Meet Our Staff'],
    ['staff_subtitle', 'Dedicated professionals committed to your success'],
    ['programs_label', 'Our Programs'],
    ['programs_title', 'Specialized Technical Trades'],
    ['programs_subtitle', 'Seven hands-on career programs built in partnership with industry'],
    ['programs_duration', '3 Years'],
    ['features_label', 'Why Choose Us'],
    ['features_title', 'What Makes GIHEKE Special'],
    ['features_subtitle', 'Unique learning combining theory with hands-on practice'],
    ['cta_heading', 'Apply for Admission at GIHEKE TSS'],
    ['cta_text', 'Take the first step toward a successful technical career. Applications are now open.'],
    ['news_label', 'Latest News'],
    ['news_title', 'School Updates'],
    ['news_subtitle', 'Stay informed about GIHEKE Technical Secondary School'],
    ['news_empty', 'No news articles yet. Check back soon for updates!'],
    ['contact_label', 'Get In Touch'],
    ['contact_title', 'Contact Us'],
    ['contact_subtitle', 'Reach out for inquiries, applications, or visits'],
    ['contact_info_heading', 'Contact Information'],
    ['contact_info_location', 'Location'],
    ['contact_info_email', 'Email'],
    ['contact_info_phone', 'Phone'],
    ['contact_form_heading', 'Send us a Message'],
    ['contact_form_name_placeholder', 'Your full name'],
    ['contact_form_email_placeholder', 'Your email'],
    ['contact_form_msg_placeholder', 'Your message...'],
    ['contact_form_btn', 'Send Message'],
];

// Only insert if keys don't already exist
foreach ($homepageSettings as $pair) {
    list($key, $value) = $pair;
    $check = mysqli_query($conn, "SELECT id FROM `tbl_site_settings` WHERE `setting_key` = '$key' LIMIT 1");
    if (mysqli_num_rows($check) == 0) {
        $v = mysqli_real_escape_string($conn, $value);
        mysqli_query($conn, "INSERT INTO `tbl_site_settings` (`setting_key`, `setting_value`) VALUES ('$key', '$v')");
    }
}
$results[] = "Homepage text settings seeded.";

// ====== SEED TRADES ======
$checkTrades = mysqli_query($conn, "SELECT COUNT(*) as c FROM `tbl_trades`");
$tradeCount = mysqli_fetch_assoc($checkTrades)['c'];
if ($tradeCount == 0) {
    $trades = [
        ['Comp Systems &amp; Architecture', 'Master motherboards, processors, memory, and system-level design. Build, configure, and maintain desktop and server systems with real-world hardware labs.', 'bi bi-cpu', 1],
        ['Software Development', 'Learn modern programming languages, web and mobile development, version control, databases, and software engineering best practices.', 'bi bi-code-slash', 2],
        ['Networking and Internet Technology', 'Design LAN/WAN networks, configure routers and switches, secure data, and manage cloud services and internet infrastructure.', 'bi bi-globe2', 3],
        ['Building Construction', 'Study architectural drawing, masonry, concrete works, site supervision, and sustainable building methods together with hands-on workshop projects.', 'bi bi-building', 4],
        ['Electronics and Telecommunication Services', 'Troubleshoot circuit boards, telecommunications equipment, radio systems, and digital electronics through advanced workshop training.', 'bi bi-motherboard', 5],
        ['Professional Accounting', 'Gain skills in bookkeeping, taxation, payroll, financial reporting, and accounting software used by modern businesses.', 'bi bi-calculator', 6],
        ['Electrical Technology', 'Explore residential and industrial wiring, electrical installations, power distribution, control panels, and energy-efficient systems.', 'bi bi-lightning', 7],
    ];
    foreach ($trades as $t) {
        $n = mysqli_real_escape_string($conn, $t[0]);
        $d = mysqli_real_escape_string($conn, $t[1]);
        $i = mysqli_real_escape_string($conn, $t[2]);
        $s = (int)$t[3];
        mysqli_query($conn, "INSERT INTO `tbl_trades` (`name`, `description`, `icon`, `sort_order`) VALUES ('$n', '$d', '$i', $s)");
    }
    $results[] = "Trades seeded.";
}

// ====== SEED STAFF ======
$checkStaff = mysqli_query($conn, "SELECT COUNT(*) as c FROM `tbl_staff_members`");
$staffCount = mysqli_fetch_assoc($checkStaff)['c'];
if ($staffCount == 0) {
    $staff = [
        ['KANYANDEGE Joseph Desire', 'Principal', '+250 788885418', 'KJ', 1],
        ['HABIMANA Syliver', 'Dean of Studies', '+250 783441664', 'HS', 2],
        ['MUKAMANA Marie', 'Dean of Discipline', '+250 783044000', 'MM', 3],
        ['NGENDAHIMANA Jean Pierre', 'Accountant', '+250 788908086', 'NP', 4],
        ['IRASUBIZA Olive', 'Secretary', '+250 789387751', 'IO', 5],
        ['NGENDAHIMANA Erneste', 'Patron', '+250 786081509', 'NE', 6],
        ['BENEGUSENGA Jacqueline', 'Matron', '+250 784732450', 'BJ', 7],
        ['IRIVUZUMUREMYI Nathanael', 'Coach', '+250 795931713', 'IN', 8],
    ];
    foreach ($staff as $s) {
        $n = mysqli_real_escape_string($conn, $s[0]);
        $r = mysqli_real_escape_string($conn, $s[1]);
        $p = mysqli_real_escape_string($conn, $s[2]);
        $i = mysqli_real_escape_string($conn, $s[3]);
        $o = (int)$s[4];
        mysqli_query($conn, "INSERT INTO `tbl_staff_members` (`name`, `role`, `phone`, `initials`, `sort_order`) VALUES ('$n', '$r', '$p', '$i', $o)");
    }
    $results[] = "Staff seeded.";
}

// ====== SEED FACILITIES ======
$checkFacilities = mysqli_query($conn, "SELECT COUNT(*) as c FROM `tbl_facilities`");
$facCount = mysqli_fetch_assoc($checkFacilities)['c'];
if ($facCount == 0) {
    $facilities = [
        ['Computer Lab', 'Modern computers for software development and networking practice', 1],
        ['Electrical Workshop', 'Fully equipped for wiring, installations, and control panel training', 2],
        ['Construction Workshop', 'Hands-on training in masonry, concrete, and building techniques', 3],
        ['Resource Library', 'Extensive collection of technical books and learning materials', 4],
        ['Electronics Lab', 'Advanced equipment for circuit design and telecommunications', 5],
        ['Sports & Recreation', 'Outdoor spaces for physical education and team activities', 6],
    ];
    foreach ($facilities as $f) {
        $n = mysqli_real_escape_string($conn, $f[0]);
        $d = mysqli_real_escape_string($conn, $f[1]);
        $s = (int)$f[2];
        mysqli_query($conn, "INSERT INTO `tbl_facilities` (`name`, `description`, `sort_order`) VALUES ('$n', '$d', $s)");
    }
    $results[] = "Facilities seeded.";
}

// ====== SEED FEATURES ======
$checkFeatures = mysqli_query($conn, "SELECT COUNT(*) as c FROM `tbl_features`");
$featCount = mysqli_fetch_assoc($checkFeatures)['c'];
if ($featCount == 0) {
    $features = [
        ['Hands-On Training', 'Practical workshops and real-world projects.', 'bi bi-tools', 1],
        ['Expert Teachers', 'Qualified instructors with industry experience.', 'bi bi-people', 2],
        ['Modern Facilities', 'Well-equipped labs and workshops.', 'bi bi-building', 3],
        ['Career Ready', 'Practical skills for various industries.', 'bi bi-briefcase', 4],
        ['Strong Values', 'Discipline, integrity and respect.', 'bi bi-shield-check', 5],
        ['Community Focus', 'Serving Rusizi families with quality education.', 'bi bi-heart', 6],
    ];
    foreach ($features as $f) {
        $t = mysqli_real_escape_string($conn, $f[0]);
        $d = mysqli_real_escape_string($conn, $f[1]);
        $i = mysqli_real_escape_string($conn, $f[2]);
        $s = (int)$f[3];
        mysqli_query($conn, "INSERT INTO `tbl_features` (`title`, `description`, `icon`, `sort_order`) VALUES ('$t', '$d', '$i', $s)");
    }
    $results[] = "Features seeded.";
}

// ====== SEED CORE VALUES ======
$checkValues = mysqli_query($conn, "SELECT COUNT(*) as c FROM `tbl_core_values`");
$valCount = mysqli_fetch_assoc($checkValues)['c'];
if ($valCount == 0) {
    $values = [
        ['We choose To stay together', 'Unity and collaboration are the foundation of our school community.', 'bi bi-people-fill', 1],
        ['We choose To be accountable', 'Responsibility and integrity guide every action we take.', 'bi bi-shield-fill', 2],
        ['We choose To think big', 'Innovation and ambition drive us to achieve excellence.', 'bi bi-rocket-takeoff', 3],
    ];
    foreach ($values as $v) {
        $t = mysqli_real_escape_string($conn, $v[0]);
        $d = mysqli_real_escape_string($conn, $v[1]);
        $i = mysqli_real_escape_string($conn, $v[2]);
        $s = (int)$v[3];
        mysqli_query($conn, "INSERT INTO `tbl_core_values` (`title`, `description`, `icon`, `sort_order`) VALUES ('$t', '$d', '$i', $s)");
    }
    $results[] = "Core values seeded.";
}

// Also update existing basic settings with the correct values
$basicDefaults = [
    'site_name' => 'GIHEKE TSS',
    'site_tagline' => 'Technical Secondary School',
    'site_email' => 'giheketss@gmail.com',
    'site_phone' => '+250 788 885 418',
    'site_address' => 'Rusizi District, Giheke Sector',
    'social_facebook' => '#',
    'social_twitter' => '#',
    'social_instagram' => '#',
    'social_linkedin' => '#',
    'site_logo' => 'assets/img/logo.png',
    'hero_bg_image' => '',
];
foreach ($basicDefaults as $key => $value) {
    $v = mysqli_real_escape_string($conn, $value);
    mysqli_query($conn, "INSERT INTO `tbl_site_settings` (`setting_key`, `setting_value`) VALUES ('$key', '$v') ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)");
}
$results[] = "Basic settings updated.";

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Site Management - Full Setup</title>
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/admin-2027-theme.css" rel="stylesheet">
  <style>
    body { font-family: system-ui, sans-serif; background: #f8f9fc; padding: 40px; }
    .card { max-width: 720px; margin: 0 auto; background: #fff; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); padding: 36px; }
    h1 { font-size: 1.5rem; color: #3D47C9; margin-bottom: 6px; }
    p { color: #888; font-size: 0.92rem; margin-bottom: 20px; }
    .result { padding: 10px 14px; border-radius: 10px; margin-bottom: 8px; font-size: 0.88rem; display: flex; align-items: center; gap: 8px; }
    .result i { font-size: 1.1rem; }
    .success { background: #e8f5e9; color: #2e7d32; }
    .error { background: #fce4ec; color: #c62828; }
    .btn-group { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 12px 28px; border-radius: 12px; text-decoration: none; font-weight: 700; transition: all 0.2s; }
    .btn-primary { background: #525FE1; color: #fff; }
    .btn-primary:hover { background: #3D47C9; transform: translateY(-2px); }
    .btn-outline { border: 2px solid #525FE1; color: #525FE1; background: transparent; }
    .btn-outline:hover { background: #525FE1; color: #fff; }
  </style>
</head>
<body>
  <div class="card">
    <h1><i class="bi bi-database-check"></i> Complete Setup</h1>
    <p>All Site Management tables and default content have been configured.</p>
    <?php foreach ($results as $r): ?>
      <div class="result <?php echo strpos($r, 'Error') === false ? 'success' : 'error'; ?>">
        <i class="bi <?php echo strpos($r, 'Error') === false ? 'bi-check-circle' : 'bi-exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($r); ?>
      </div>
    <?php endforeach; ?>
    <div class="btn-group">
      <a href="site-settings.php" class="btn btn-primary"><i class="bi bi-gear"></i> Open Site Settings</a>
      <a href="content-manager.php" class="btn btn-outline"><i class="bi bi-grid-3x3-gap"></i> Content Manager</a>
    </div>
  </div>
</body>
</html>
