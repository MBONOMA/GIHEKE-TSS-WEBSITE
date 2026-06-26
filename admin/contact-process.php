<?php
header('Content-Type: application/json');
include('includes/connection.php');

$response = ['success' => false, 'message' => ''];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if(empty($name)) {
        $response['message'] = 'Name is required';
    } elseif(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Valid email is required';
    } elseif(empty($message)) {
        $response['message'] = 'Message is required';
    } else {
        $name = mysqli_real_escape_string($conn, $name);
        $email = mysqli_real_escape_string($conn, $email);
        $message = mysqli_real_escape_string($conn, $message);

        $dupCheck = mysqli_query($conn, "SELECT id FROM tbl_contact_messages WHERE Email = '$email' AND FullName = '$name' AND Message = '$message' AND CreatedAt >= NOW() - INTERVAL 1 HOUR");
        if (mysqli_num_rows($dupCheck) > 0) {
            $response['message'] = 'A similar message was already sent recently. Please wait before sending again.';
        } else {
            $sql = "INSERT INTO `tbl_contact_messages` (FullName, Email, Message) VALUES ('$name', '$email', '$message')";
            if(mysqli_query($conn, $sql)) {
                $response['success'] = true;
                $response['message'] = 'Your message has been sent successfully!';
            } else {
                $response['message'] = 'Database error. Please try again.';
            }
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>
