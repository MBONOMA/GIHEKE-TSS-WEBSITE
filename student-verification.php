<?php
session_start();
include('includes/connection.php');

if (isset($_POST['verify'])) {
    $StudentCode = strtoupper(trim($_POST['StudentCode']));
    $sql = mysqli_query($conn, "SELECT * FROM `tbl_students` WHERE (StudentCode='$StudentCode')");
    $num = mysqli_fetch_array($sql);
    if ($num > 0) {
        $_SESSION['id'] = $num['id'];
        $_SESSION['StudentCode'] = $num['StudentCode'];
        $_SESSION['FullName'] = $num['FullName'];
        $_SESSION['StudentLevel'] = $num['StudentLevel'];
        $_SESSION['StudentDepartment'] = $num['StudentDepartment'];
        $_SESSION['last_login_timestamp'] = time();
        $_SESSION['login'] = $StudentCode;
        echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
    } else {
        $error = "Invalid verification code. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Student Verification - GIHEKE TSS</title>
    <link href="img/giheke logo.webp" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/haip-theme.css">
</head>
<body class="auth-haip">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="img/giheke logo.webp" alt="GIHEKE Logo">
            <h2>Student Verification</h2>
            <p>Enter your student verification code to access your dashboard</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 20px; padding: 12px 16px; border-radius: var(--radius-sm); background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="post" novalidate>
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="studentCode" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Verification Code</label>
                <input type="text" id="studentCode" name="StudentCode" class="form-control-haip" placeholder="e.g., OMAR MBONABUCYA" required autocomplete="off" style="text-transform: uppercase;">
            </div>
            
            <button type="submit" name="verify" class="btn-haip btn-haip-primary" style="width: 100%; padding: 14px; font-size: 1rem;">
                <i class="bi bi-person-check"></i> Verify & Access Dashboard
            </button>
            
            <div class="auth-links" style="margin-top: 20px; text-align: center;">
                <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 500;"><i class="bi bi-arrow-left"></i> Back to Home</a>
            </div>
        </form>
    </div>
</body>
</html>
