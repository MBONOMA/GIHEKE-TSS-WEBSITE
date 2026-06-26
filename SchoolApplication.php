<?php
$errors = [];
$success = false;

if(isset($_POST['submit'])){

  include 'admin/includes/connection.php';
  $status_check = "SELECT * FROM `tbl_aply_status` WHERE id = 1";
  $check = mysqli_query($conn,$status_check);
  $status_call = mysqli_fetch_assoc($check);

  if($status_call['Status'] == 'approved'){

    $FirstName = trim($_POST['FirstName'] ?? '');
    $LastName = trim($_POST['LastName'] ?? '');
    $Email = trim($_POST['Email'] ?? '');
    $Contact = trim($_POST['Contact'] ?? '');
    $SchoolName = trim($_POST['SchoolName'] ?? '');
    $PreviousLevel = trim($_POST['PreviousLevel'] ?? '');
    $PreviousDepartment = trim($_POST['PreviousDepartment'] ?? '');
    $NewLevel = trim($_POST['NewLevel'] ?? '');
    $NewDepartment = trim($_POST['NewDepartment'] ?? '');
    $Message = trim($_POST['Message'] ?? '');

    if(empty($FirstName)){
      $errors[] = 'First Name is required';
    } elseif(!preg_match("/^[a-zA-Z]+$/", $FirstName)){
      $errors[] = 'Invalid First Name format';
    }

    if(empty($LastName)){
      $errors[] = 'Last Name is required';
    } elseif(!preg_match("/^[a-zA-Z]+$/", $LastName)){
      $errors[] = 'Invalid Last Name format';
    }

    if(empty($Email)){
      $errors[] = 'Email is required';
    } elseif(!filter_var($Email, FILTER_VALIDATE_EMAIL)){
      $errors[] = 'Invalid Email address';
    }

    if(empty($Contact)){
      $errors[] = 'Phone Contact is required';
    } elseif(!preg_match("/^[0-9]+$/", $Contact)){
      $errors[] = 'Invalid Phone Contact format';
    } elseif(strlen($Contact) != 10){
      $errors[] = 'Phone must contain exactly 10 digits';
    }

    if(empty($SchoolName)){
      $errors[] = 'Previous School Name is required';
    }

    if(empty($PreviousLevel)){
      $errors[] = 'Previous Level is required';
    }

    if(empty($PreviousDepartment)){
      $errors[] = 'Previous Department is required';
    }

    if(empty($NewLevel)){
      $errors[] = 'New Level is required';
    }

    if(empty($NewDepartment)){
      $errors[] = 'New Department is required';
    }

    if(empty($Message)){
      $errors[] = 'Your message is required';
    } elseif(strlen($Message) < 10 || strlen($Message) > 520){
      $errors[] = 'Message must be between 10 and 520 characters';
    }

    // File upload validation
    $SchoolReport = $_FILES["SchoolReport"]["name"] ?? '';
    if(empty($SchoolReport)){
      $errors[] = 'School Report file is required';
    } else {
      $extension = strtolower(pathinfo($SchoolReport, PATHINFO_EXTENSION));
      $allowed_extensions = array("pdf");
      if(!in_array($extension, $allowed_extensions)){
        $errors[] = 'Invalid format. Only PDF files are allowed';
      } elseif($_FILES["SchoolReport"]["error"] !== UPLOAD_ERR_OK){
        $errors[] = 'File upload failed. Please try again';
      } elseif($_FILES["SchoolReport"]["size"] > 5242880){
        $errors[] = 'File is too large. Maximum size is 5MB';
      }
    }

if(empty($errors)){
      $ReportUrl = md5(time().$SchoolReport).'.'.$extension;
      $uploadDir = 'Student Report/';
      if(!is_dir($uploadDir)){
        mkdir($uploadDir, 0755, true);
      }
      $uploadSuccess = move_uploaded_file($_FILES["SchoolReport"]["tmp_name"], $uploadDir.$ReportUrl);
      if(!$uploadSuccess){
        $errors[] = 'File upload failed on server. Please try again.';
      }
    }

    if(empty($errors)){
      $status = "pending";
      $sql = "INSERT INTO `tbl_apply_student`(FirstName, LastName, Email, Contact, SchoolName, SchoolReport, PreviousTrade, PreviousLevel, SchoolTrade, SchoolLevel, status, Message)
        VALUES('$FirstName', '$LastName', '$Email', '$Contact', '$SchoolName', '$ReportUrl', '$PreviousDepartment','$PreviousLevel','$NewDepartment','$NewLevel','$status','$Message')";
      $query_run = mysqli_query($conn, $sql);
      if($query_run){
        require_once __DIR__ . '/includes/smtp-config.php';
        try {
          $mail = getMailer();
          $mail->addAddress($Email, $FirstName . ' ' . $LastName);
          $mail->Subject = 'Application Received - GIHEKE TSS';
          $mail->Body = '<h3>Dear ' . htmlspecialchars($FirstName . ' ' . $LastName) . ',</h3>'
            . '<p>Thank you for applying to GIHEKE Technical Secondary School.</p>'
            . '<p>Your application has been received successfully and is currently under review.</p>'
            . '<p>We will contact you at ' . htmlspecialchars($Email) . ' once a decision has been made.</p>'
            . '<p>For any inquiries, please contact us at giheketss@gmail.com or call +250 788 876 460.</p>'
            . '<br><p>Best regards,<br>GIHEKE TSS Administration</p>';
          $mail->send();
        } catch (Exception $e) {
          error_log('Confirmation email failed: ' . $e->getMessage());
        }
        $success = true;
        echo "<script>sessionStorage.setItem('appSuccess','1'); window.location.href='SchoolApplication.php';</script>";
      } else {
        $errors[] = 'Database error: '.mysqli_error($conn);
      }
    }
  } else {
    $errors[] = 'Applications are currently closed. Please apply during the start of school terms.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>School Application - GIHEKE TSS</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <link href="assets/haip-theme.css" rel="stylesheet">
    <link href="admin/assets/css/giheke-toast.css" rel="stylesheet">
</head>
<body>

<!-- ANNOUNCEMENT BAR -->
<div class="announcement-bar">
    <div class="announcement-inner">
        <?php
        include 'admin/includes/connection.php';
        $announce_query = "SELECT Announcement FROM `tbl_announcement` LIMIT 1";
        $announce_run = mysqli_query($conn, $announce_query);
        $announce = mysqli_fetch_assoc($announce_run);
        ?>
        <span class="announcement-label">Announcement</span>
        <div class="announcement-text">
            <span class="announcement-scroll"><?php echo htmlspecialchars($announce['Announcement']); ?></span>
        </div>
    </div>
</div>

<?php include 'includes/haip-header.php'; ?>

<main class="application-page" style="padding: 80px 0; background: var(--bg-light);">
    <div class="container-haip">
        <div class="application-header" style="text-align: center; margin-bottom: 40px;">
            <span class="section-label">Admissions</span>
            <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--primary-dark); margin-top: 12px;">Application Form</h1>
            <p style="color: #666; margin-top: 8px;">Apply for admission at GIHEKE Technical Secondary School</p>
        </div>

        <!-- Progress Indicator -->
        <div class="step-indicator" style="display: flex; justify-content: center; gap: 20px; margin-bottom: 40px; flex-wrap: wrap;">
            <div class="step-circle active" data-step="1">
                <span class="step-number">1</span>
                <span class="step-label">Student Info</span>
            </div>
            <div class="step-connector"></div>
            <div class="step-circle" data-step="2">
                <span class="step-number">2</span>
                <span class="step-label">Previous School</span>
            </div>
            <div class="step-connector"></div>
            <div class="step-circle" data-step="3">
                <span class="step-number">3</span>
                <span class="step-label">School Choice</span>
            </div>
        </div>

        <?php if(!empty($errors)): ?>
        <div class="alert-haip alert-haip-danger" style="max-width: 800px; margin: 0 auto 24px;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach($errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if($success): ?>
        <div class="alert-haip alert-haip-success" style="max-width: 800px; margin: 0 auto 24px;">
            <i class="bi bi-check-circle-fill"></i>
            Application submitted successfully!
        </div>
        <?php endif; ?>

        <!-- Application Form Card -->
        <div class="card-haip" style="max-width: 800px; margin: 0 auto;">
            <div class="card-haip-body" style="padding: 40px;">
                <form action="SchoolApplication.php" method="post" novalidate enctype="multipart/form-data" id="applicationForm">
                    
                    <!-- Step 1: Student Information -->
                    <div class="form-step active" data-step="1">
                        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 8px;">Student Information</h2>
                        <p style="color: #666; margin-bottom: 24px;">Enter your personal details</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="firstName" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">First Name</label>
                                    <input type="text" id="firstName" name="FirstName" class="form-control-haip" placeholder="e.g., Omar" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="lastName" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Last Name</label>
                                    <input type="text" id="lastName" name="LastName" class="form-control-haip" placeholder="e.g., MBONABUCYA" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="email" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Email</label>
                                    <input type="email" id="email" name="Email" class="form-control-haip" placeholder="youremail@gmail.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="contact" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Contact Number</label>
                                    <input type="tel" id="contact" name="Contact" class="form-control-haip" placeholder="e.g., 0785120223" required>
                                </div>
                            </div>
                        </div>

                        <div class="step-actions" style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                            <button type="button" class="btn-haip btn-haip-outline next-step" data-next="2">Next <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Step 2: Previous School Info -->
                    <div class="form-step" data-step="2" style="display: none;">
                        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 8px;">Previous School Information</h2>
                        <p style="color: #666; margin-bottom: 24px;">Tell us about your previous school</p>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="schoolName" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">School Name</label>
                            <input type="text" id="schoolName" name="SchoolName" class="form-control-haip" placeholder="e.g., Giheke TSS" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="schoolReport" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">School Report (PDF only)</label>
                            <input type="file" id="schoolReport" name="SchoolReport" class="form-control-haip" accept=".pdf" required>
                            <small style="color: #999; font-size: 0.85rem;">Upload your school report in PDF format</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="previousLevel" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Previous Level</label>
                                    <select id="previousLevel" name="PreviousLevel" class="form-control-haip" required>
                                        <option value="">Select Your Level</option>
                                        <option value="Level 3">Level 3</option>
                                        <option value="Level 4">Level 4</option>
                                        <option value="Level 5">Level 5</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="previousDepartment" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Previous Department/Trade</label>
                                    <select id="previousDepartment" name="PreviousDepartment" class="form-control-haip" required>
                                        <option value="">Select Your Trade</option>
                                        <option value="Software Development">Software Development</option>
                                        <option value="Network and internet technology">Network and internet technology</option>
                                        <option value="Computer System Architecture">Computer System Architecture</option>
                                        <option value="Electrical Technology">Electrical Technology</option>
                                        <option value="Electronics Services">Electronics Services</option>
                                        <option value="Building Construction">Building Construction</option>
                                        <option value="Professional Accounting">Professional Accounting</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="step-actions" style="display: flex; justify-content: space-between; gap: 12px; margin-top: 24px;">
                            <button type="button" class="btn-haip btn-haip-outline prev-step" data-prev="1"><i class="bi bi-arrow-left"></i> Back</button>
                            <button type="button" class="btn-haip btn-haip-primary next-step" data-next="3">Next <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Step 3: School Choice -->
                    <div class="form-step" data-step="3" style="display: none;">
                        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 8px;">Your School Choice</h2>
                        <p style="color: #666; margin-bottom: 24px;">Select the level and trade you want to study</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="newLevel" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Level</label>
                                    <select id="newLevel" name="NewLevel" class="form-control-haip" required>
                                        <option value="">Select Your Level</option>
                                        <option value="Level 3">Level 3</option>
                                        <option value="Level 4">Level 4</option>
                                        <option value="Level 5">Level 5</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label for="newDepartment" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Department/Trade</label>
                                    <select id="newDepartment" name="NewDepartment" class="form-control-haip" required>
                                        <option value="">Select Your Trade</option>
                                        <option value="Software Development">Software Development</option>
                                        <option value="Network and internet technology">Network and internet technology</option>
                                        <option value="Computer System Architecture">Computer System Architecture</option>
                                        <option value="Electrical Technology">Electrical Technology</option>
                                        <option value="Electronics Services">Electronics Services</option>
                                        <option value="Building Construction">Building Construction</option>
                                        <option value="Professional Accounting">Professional Accounting</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="message" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Reason for Applying</label>
                            <textarea id="message" name="Message" class="form-control-haip" rows="5" placeholder="Tell us why you want to join GIHEKE TSS..." required></textarea>
                        </div>

                        <div class="step-actions" style="display: flex; justify-content: space-between; gap: 12px; margin-top: 24px;">
                            <button type="button" class="btn-haip btn-haip-outline prev-step" data-prev="2"><i class="bi bi-arrow-left"></i> Back</button>
                            <button type="submit" name="submit" class="btn-haip btn-haip-submit">Submit Application <i class="bi bi-send"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div style="text-align: center; margin-top: 24px; color: #666; font-size: 0.9rem;">
            <i class="bi bi-shield-lock-fill" style="margin-right: 6px; color: var(--success-green);"></i>
            Your information is secure and will only be used for admission purposes.
        </div>
    </div>
</main>

<?php include 'includes/haip-footer.php'; ?>

<div id="preloader"></div>
<button class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></button>

<script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/js/main.js"></script>
<script src="admin/assets/js/giheke-toast.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (sessionStorage.getItem('appSuccess') === '1') {
        sessionStorage.removeItem('appSuccess');
        if (window.GihekeToast) {
            GihekeToast.showModal({title:'Application Submitted',message:'Your application has been submitted successfully! You will receive a confirmation email.',type:'success',buttonText:'OK'});
        }
    }
    const steps = document.querySelectorAll('.form-step');
    const stepCircles = document.querySelectorAll('.step-circle');
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    let currentStep = 1;

    function showStep(step) {
        steps.forEach(s => {
            s.style.display = s.dataset.step == step ? 'block' : 'none';
        });
        stepCircles.forEach(c => {
            c.classList.toggle('active', parseInt(c.dataset.step) <= step);
        });
        currentStep = step;
    }

    nextButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const next = parseInt(btn.dataset.next);
            if (validateStep(currentStep)) {
                showStep(next);
            }
        });
    });

    prevButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const prev = parseInt(btn.dataset.prev);
            showStep(prev);
        });
    });

    function validateStep(step) {
        const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
        let valid = true;
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.style.borderColor = 'var(--danger-red)';
                valid = false;
            } else {
                input.style.borderColor = '';
            }
        });
        return valid;
    }

    // Step indicator styles
    const indicatorStyle = document.createElement('style');
    indicatorStyle.textContent = `
        .step-indicator { position: relative; }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 15%;
            right: 15%;
            height: 2px;
            background: #e0e0e0;
            z-index: 1;
        }
        .step-circle {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
            max-width: 140px;
        }
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #999;
            transition: all 0.3s;
        }
        .step-circle.active .step-number {
            background: var(--gradient-purple);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(74, 10, 110, 0.15);
        }
        .step-label {
            font-size: 0.8rem;
            color: #999;
            text-align: center;
            font-weight: 500;
        }
        .step-circle.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }
        .step-connector { display: none; }
        @media (max-width: 768px) {
            .step-indicator::before { display: none; }
            .step-circle { max-width: 100px; }
        }
    `;
    document.head.appendChild(indicatorStyle);

    const appResponsive = document.createElement('style');
    appResponsive.textContent = `
        @media (max-width: 768px) {
            .application-page { padding: 40px 0 !important; }
            .application-header h1 { font-size: 1.5rem !important; }
            .card-haip-body { padding: 24px 16px !important; }
            .card-haip-body h2 { font-size: 1.2rem !important; }
            .step-actions { flex-direction: column !important; gap: 10px !important; }
            .step-actions .btn-haip { width: 100%; justify-content: center; }
        }
        @media (max-width: 480px) {
            .application-header h1 { font-size: 1.2rem !important; }
            .card-haip-body { padding: 16px 12px !important; }
            .card-haip-body h2 { font-size: 1rem !important; }
            .step-number { width: 32px; height: 32px; font-size: 0.8rem; }
            .step-label { font-size: 0.65rem; }
            .step-indicator { gap: 8px; }
            .step-circle { max-width: 80px !important; }
            .form-group { margin-bottom: 14px !important; }
        }
    `;
    document.head.appendChild(appResponsive);
    window.addEventListener('resize', function() {});
});

// button visibility fallback
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.prev-step, .next-step').forEach(function(btn) {
        btn.style.display = 'inline-flex';
        btn.style.visibility = 'visible';
        btn.style.opacity = '1';
    });
});
</script>
</body>
</html>
