<?php
session_start();
$otp = rand(10000, 99999); // Generate 5-digit OTP
$_SESSION['otp'] = $otp; // Store OTP in session
$email = ""; // Replace with user's email

$subject = "Your OTP Code";
$message = "Your OTP for verification is: $otp";
$headers = "From: hemantpundir637@gmail.com";

if(mail($email, $subject, $message, $headers)) {
    echo "OTP sent successfully!";
} else {
    echo "Failed to send OTP.";
}
?>
