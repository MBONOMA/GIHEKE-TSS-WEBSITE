<?php
// Centralized SMTP Configuration for GIHEKE TSS
// Uses PHPMailer to send emails via Gmail SMTP

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$autoloadPaths = [
    __DIR__ . '/../admin/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../admin/PHPMailer/src/Exception.php',
];

$phpmailerFound = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        if (strpos($path, 'autoload.php') !== false) {
            require_once $path;
            $phpmailerFound = true;
            break;
        } elseif (strpos($path, 'Exception.php') !== false) {
            $baseDir = dirname(dirname($path));
            require_once $baseDir . '/Exception.php';
            require_once $baseDir . '/PHPMailer.php';
            require_once $baseDir . '/SMTP.php';
            $phpmailerFound = true;
            break;
        }
    }
}

function getMailer() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->Username = 'giheketss@gmail.com';
    $mail->Password = 'waebfhjaywlyyfoh';
    $mail->setFrom('giheketss@gmail.com', 'GIHEKE TSS');
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    return $mail;
}
?>
