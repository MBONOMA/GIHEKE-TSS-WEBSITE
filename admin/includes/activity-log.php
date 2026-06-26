<?php
function logActivity($conn, $action, $description, $userId = null, $userType = 'admin') {
    if ($userId === null) {
        $userId = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0;
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $action = mysqli_real_escape_string($conn, $action);
    $description = mysqli_real_escape_string($conn, $description);
    $userType = mysqli_real_escape_string($conn, $userType);
    $ip = mysqli_real_escape_string($conn, $ip);

    $sql = "INSERT INTO `tbl_activity_logs` (`user_id`, `user_type`, `action`, `description`, `ip_address`) 
            VALUES ('$userId', '$userType', '$action', '$description', '$ip')";
    return mysqli_query($conn, $sql);
}
