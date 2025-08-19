<?php
session_start();
include('includes/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'includes/PHPMailer/src/Exception.php';
require_once 'includes/PHPMailer/src/PHPMailer.php';
require_once 'includes/PHPMailer/src/SMTP.php';

// Function to Generate and Send OTP
function generateAndSendOTP($email) {
    $otp = rand(100000, 999999); // Generate a new 6-digit OTP
    $_SESSION['otp'] = $otp; // Store OTP in session

    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bloodconnect98@gmail.com'; // Replace with your email
        $mail->Password = 'ekrqdrcxeofjvitn';   // Replace with your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Set Email Headers
        $mail->setFrom('bloodconnect98@gmail.com', 'Blood Connect');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code for Blood Connect Registration';

        // Get the user's name from session
        $userName = isset($_SESSION['user_details']['fullname']) ? $_SESSION['user_details']['fullname'] : 'User';

        // Email Body
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 8px;'>
                <h2 style='color: #d9534f; text-align: center;'>Blood Connect - OTP Verification</h2>
                <p>Dear <b>$userName</b>,</p>
                <p>Your One-Time Password (OTP) for verification is:</p>
                <h2 style='text-align: center; color: #d9534f; background-color: #fff3cd; padding: 10px; border-radius: 5px; display: inline-block;'>
                    🔢 <b>$otp</b>
                </h2>
                <p><b>Note:</b> This OTP is valid for 10 minutes. Please do not share it with anyone.</p>
                <p>Best regards, <br><b>Blood Connect Team</b></p>
            </div>
        ";

        // Send Email
        if ($mail->send()) {
            echo "<script>
                alert('✅ OTP has been sent to your email. Please check your inbox.');
                window.location.href = 'verify_otp.php';
            </script>";
        } else {
            echo "<script>alert('❌ OTP could not be sent. Please try again.');</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('❌ Mailer Error: {$mail->ErrorInfo}');</script>";
    }
}

// Function to Send Registration Success Email
function sendRegistrationSuccessEmail($email, $name) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bloodconnect98@gmail.com';
        $mail->Password = 'ekrqdrcxeofjvitn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Set Email Headers
        $mail->setFrom('bloodconnect98@gmail.com', 'Blood Connect');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Registration Successful - Welcome to Blood Connect!';

        // Email Body
        $mail->Body = "
            <h2>Welcome, $name! 🎉</h2>
            <p>We are thrilled to have you as a part of <b>Blood Connect</b>. Thank you for signing up and joining our mission to help those in need.</p>
            <p>Your account has been successfully created, and you can now log in to start your journey as a donor.</p>
            <h3>What’s next?</h3>
            <ul>
                <li>✔️ Find & Donate Blood</li>
                <li>✔️ Help Save Lives</li>
                <li>✔️ Stay Updated with Requests</li>
            </ul>
            <p>Login here: <a href='http://yourwebsite.com/login.php'>Click to Login</a></p>
            <p>Thanks & Regards,<br><b>Blood Connect Team</b></p>
        ";

        // Send Email
        $mail->send();
    } catch (Exception $e) {
        echo "<script>alert('❌ Registration Success Mail Error: {$mail->ErrorInfo}');</script>";
    }
}

// Handle Resend OTP
if (isset($_POST['resend_otp'])) {
    if (isset($_SESSION['user_details'])) {
        $email = $_SESSION['user_details']['email'];
        generateAndSendOTP($email);
    } else {
        echo "<script>alert('❌ User session expired! Please register again.'); window.location.href='sign_up.php';</script>";
        exit();
    }
}

// Handle OTP Verification
if (isset($_POST['verify_otp'])) {
    $enteredOtp = $_POST['otp'];

    if (!isset($_SESSION['otp'])) {
        echo "<script>alert('❌ OTP not found! Please request a new OTP.');</script>";
        exit();
    }

    if ($enteredOtp == $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true;

        if (isset($_SESSION['user_details'])) {
            $user = $_SESSION['user_details'];
            $role = $user['role']; // Get the role from session

            if ($role == "Blood Donor") {
                // Insert into tblblooddonars
                $sql = "INSERT INTO tblblooddonars (FullName, MobileNumber, EmailId, Age, Gender, BloodGroup, Address, Message, status, Password) 
                        VALUES (:fullname, :mobile, :email, :age, :gender, :bloodgroup, :address, :message, 1, :password)";
            } elseif ($role == "Blood Requester") {
                // Insert into tblbloodrequester
                $sql = "INSERT INTO tblbloodrequester (FullName, MobileNumber, EmailId, Age, Gender, BloodGroup, Address, Message, status, Password) 
                        VALUES (:fullname, :mobile, :email, :age, :gender, :bloodgroup, :address, :message, 1, :password)";
            } else {
                echo "<script>alert('❌ Invalid role selected.');</script>";
                exit();
            }

            $query = $dbh->prepare($sql);
            $query->bindParam(':fullname', $user['fullname'], PDO::PARAM_STR);
            $query->bindParam(':mobile', $user['mobileno'], PDO::PARAM_STR);
            $query->bindParam(':email', $user['email'], PDO::PARAM_STR);
            $query->bindParam(':age', $user['age'], PDO::PARAM_STR);
            $query->bindParam(':gender', $user['gender'], PDO::PARAM_STR);
            $query->bindParam(':bloodgroup', $user['bloodgroup'], PDO::PARAM_STR);
            $query->bindParam(':address', $user['address'], PDO::PARAM_STR);
            $query->bindParam(':message', $user['message'], PDO::PARAM_STR);
            $query->bindParam(':password', $user['password'], PDO::PARAM_STR);

            if ($query->execute()) {
                // Send Registration Success Email
                sendRegistrationSuccessEmail($user['email'], $user['fullname']);

                // Clear user details after successful registration
                unset($_SESSION['user_details']);
                unset($_SESSION['otp']);

                echo "<script>alert('✅ OTP Verified & Registration Successful! Email Confirmation Sent.'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('❌ Database insertion failed! Please try again.');</script>";
            }
        } else {
            echo "<script>alert('❌ No user data found! Please try again.'); window.location.href='sign_up.php';</script>";
        }
    } else {
        echo "<script>alert('❌ Invalid OTP! Try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <style>
        .otp-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .otp-container h2 {
            margin-bottom: 20px;
        }
        .otp-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .otp-container button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .otp-container button:hover {
            background-color: #0056b3;
        }
        .resend-btn {
            background-color: #28a745;
            margin-top: 10px;
        }
        .resend-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="otp-container">
    <h2>Enter OTP for Verification</h2>
    <form method="POST">
        <input type="number" name="otp" placeholder="Enter OTP" required>
        <button type="submit" name="verify_otp">Verify OTP</button>
    </form>

    <form method="POST">
        <button type="submit" name="resend_otp" class="resend-btn">Resend OTP</button>
    </form>
</div>

</body>
</html>