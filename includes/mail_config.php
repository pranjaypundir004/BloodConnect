<?php
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    require_once 'includes/PHPMailer/src/Exception.php';
    require_once 'includes/PHPMailer/src/PHPMailer.php';
    require_once 'includes/PHPMailer/src/SMTP.php';
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
