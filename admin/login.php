<?php
session_start();
include('includes/connection.php');

if(isset($_POST['login'])) {
    $Email = $_POST['Email'];
    $Password = $_POST['Password'];
    
$sql = mysqli_query($conn, "SELECT * FROM tbl_admins WHERE (Email='$Email' && Password='$Password')");
$row = $sql ? mysqli_fetch_array($sql) : false;

if(isset($row) && $row > 0) {
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_Email'] = $row['Email'];
        $_SESSION['admin_FirstName'] = $row['FirstName'];
        $_SESSION['admin_LastName'] = $row['LastName'];
        $_SESSION['admin_Phone'] = $row['Phone'];
        $_SESSION['admin_ImageUrl'] = $row['ImageUrl'];
        $_SESSION['admin_Password'] = $row['Password'];
        
        session_write_close();
        echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
    } else {
        $error = "Invalid email or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - GIHEKE TSS</title>
    <link href="../img/giheke logo.webp" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="../assets/haip-theme.css" rel="stylesheet">
</head>
<body class="auth-haip">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="../img/giheke logo.webp" alt="GIHEKE Logo">
            <h2>Admin Login</h2>
            <p>Sign in to access the administration panel</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert-modern alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 20px; padding: 12px 16px; border-radius: var(--radius-sm); background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="post" novalidate>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="adminEmail" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Email Address</label>
                <input type="email" id="adminEmail" name="Email" class="form-control-haip" placeholder="admin@giheketss.rw" required autocomplete="email">
            </div>
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="adminPassword" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Password</label>
                <input type="password" id="adminPassword" name="Password" class="form-control-haip" placeholder="Enter your password" required autocomplete="current-password">
            </div>
            
            <button type="submit" name="login" class="btn-haip btn-haip-primary" style="width: 100%; padding: 14px; font-size: 1rem;">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>
            
            <div class="auth-links" style="margin-top: 20px; text-align: center;">
                <a href="reset-password.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">Forgot Password?</a>
                <span style="margin: 0 12px; color: #ccc;">|</span>
                <a href="../" style="color: var(--text-secondary); text-decoration: none; font-weight: 500;"><i class="bi bi-arrow-left"></i> Back to Home</a>
            </div>
        </form>
    </div>
</body>
</html>
