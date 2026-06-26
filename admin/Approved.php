<?php

session_start();
require_once 'includes/connection.php';

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_Email'])) {
    header('location: login.php');
    exit;
}

if (!isset($_GET['approveid']) || !ctype_digit($_GET['approveid'])) {
    header('location: studentApplication.php?error=Invalid+request');
    exit;
}

$id = (int) $_GET['approveid'];

$status = "approved";
$updateStmt = mysqli_prepare($conn, "UPDATE tbl_apply_student SET status = ? WHERE id = ?");
mysqli_stmt_bind_param($updateStmt, "si", $status, $id);
$updateResult = mysqli_stmt_execute($updateStmt);

$selectStmt = mysqli_prepare($conn, "SELECT FirstName, LastName, Email, Contact FROM tbl_apply_student WHERE id = ?");
mysqli_stmt_bind_param($selectStmt, "i", $id);
mysqli_stmt_execute($selectStmt);
$result = mysqli_stmt_get_result($selectStmt);
$call = mysqli_fetch_assoc($result);

if (!$call) {
    header('location: studentApplication.php?error=Record+not+found');
    exit;
}

$name = $call['FirstName'] . ' ' . $call['LastName'];

$linkStmt = mysqli_prepare($conn, "SELECT DocUrl FROM tbl_parent_doc WHERE id = 1");
mysqli_stmt_execute($linkStmt);
$linkResult = mysqli_stmt_get_result($linkStmt);
$linkRow = mysqli_fetch_assoc($linkResult);
$document = $linkRow ? $linkRow['DocUrl'] : '';

$siteBaseUrl  = 'http://localhost/Giheke';

require_once __DIR__ . '/../includes/smtp-config.php';

try {
    $mail = getMailer();
    $mail->addAddress($call['Email'], $name);
    $mail->Subject = 'Giheke TSS School - Application Approved';
    $mail->Body = '<h3>Dear ' . htmlspecialchars($name) . ',</h3>'
        . '<h4>You are successfully approved for school application at GIHEKE TSS.</h4>'
        . '<p>For more information contact us. Congratulations!</p>';
    if (!empty($document)) {
        $docUrl = $siteBaseUrl . '/admin/Parent Doc/' . urlencode($document);
        $mail->Body .= '<p><a href="' . $docUrl . '">Click here to download the Parent Document (Babyeyi)</a></p>';
    }
    $mail->send();
} catch (Exception $e) {
    error_log('Email send failed: ' . $e->getMessage());
}

header('location: studentApplication.php?msg=Student+Approved+Successfully');
exit;
