<?php
session_start();
include('includes/connection.php');

// Ensure tbl_stdaccounts table exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `tbl_stdaccounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `StudentCode` varchar(20) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `StudentCode` (`StudentCode`)
)");

if (isset($_POST['login'])) {
    $StudentCode = $_POST['StudentCode'];
    $Password = $_POST['Password'];

    $sql = mysqli_query($conn, "SELECT * FROM tbl_stdaccounts WHERE StudentCode='$StudentCode'");
    $row = mysqli_fetch_array($sql);

    if ($row && password_verify($Password, $row['Password'])) {
        // Also get student details from tbl_students
        $studentSql = mysqli_query($conn, "SELECT * FROM tbl_students WHERE StudentCode='$StudentCode'");
        $student = mysqli_fetch_array($studentSql);

        $_SESSION['id'] = $student['id'];
        $_SESSION['StudentCode'] = $row['StudentCode'];
        $_SESSION['FullName'] = $row['FullName'];
        $_SESSION['StudentLevel'] = $student['StudentLevel'];
        $_SESSION['StudentDepartment'] = $student['StudentDepartment'];
        $_SESSION['last_login_timestamp'] = time();
        $_SESSION['login'] = $StudentCode;

        echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
    } else {
        $error = "Invalid SDMS Code or Password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Login - GIHEKE TSS</title>
    <link href="img/giheke logo.webp" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="assets/haip-theme.css" rel="stylesheet">
</head>
<body class="auth-haip">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="img/giheke logo.webp" alt="GIHEKE Logo">
            <h2>Student Portal</h2>
            <p>Enter your SDMS Code and password to access your dashboard</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert-modern alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 20px; padding: 12px 16px; border-radius: var(--radius-sm); background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" novalidate>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="studentCode" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">SDMS Code</label>
                <input type="text" id="studentCode" name="StudentCode" class="form-control-haip" placeholder="Enter your 12-digit SDMS code" required autocomplete="off" maxlength="12">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="studentPassword" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Password</label>
                <input type="password" id="studentPassword" name="Password" class="form-control-haip" placeholder="Enter your password" required autocomplete="current-password">
            </div>

            <button type="submit" name="login" class="btn-haip btn-haip-primary" style="width: 100%; padding: 14px; font-size: 1rem;">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>

            <div class="auth-links" style="margin-top: 20px; text-align: center;">
                <a href="index.php" style="color: var(--text-secondary); text-decoration: none; font-weight: 500;"><i class="bi bi-arrow-left"></i> Back to Home</a>
            </div>
        </form>
    </div>
</body>
</html>
