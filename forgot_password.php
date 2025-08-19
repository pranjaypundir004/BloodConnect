<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('includes/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'includes/PHPMailer/src/Exception.php';
require_once 'includes/PHPMailer/src/PHPMailer.php';
require_once 'includes/PHPMailer/src/SMTP.php';

if(isset($_POST['submit'])){
    $email = $_POST['email'];

    // Check if email exists in tblblooddonors or tblbloodrequester
 $check_query = "SELECT emailid FROM tblblooddonars WHERE emailid=:email 
                UNION 
                SELECT emailid FROM tblbloodrequester WHERE emailid=:email";
$stmt = $dbh->prepare($check_query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$result) {
        echo "<script>alert('Email not registered with Blood Connect'); window.location.href='forgot_password.php';</script>";
    } else {
        $otp = rand(100000, 999999);

        // Store OTP in session for verification
        $_SESSION['otp'] = $otp;
        $_SESSION['reset_email'] = $email;

        // PHPMailer configuration
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'bloodconnect98@gmail.com'; // Your email
            $mail->Password = 'ekrqdrcxeofjvitn'; // Your email password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email details
            $mail->setFrom('bloodconnect98@gmail.com', 'Blood Connect');
            $mail->addAddress($email);
            $mail->isHTML(true);
   $mail->Subject = "Password Reset Request - Blood Connect";
$mail->Body = "
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .otp {
            font-size: 20px;
            font-weight: bold;
            color: #d9534f;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            font-size: 14px;
            color: #777;
            text-align: center;
            margin-top: 20px;
        }
        .support {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>🔑 Password Reset Request</div>
        <p>Dear User,</p>
        <p>We have received a request to reset your password for your Blood Connect account.</p>
        <p>Please use the following One-Time Password (OTP) to proceed with resetting your password:</p>
        <div class='otp'>$otp</div>
        <p><strong>Note:</strong> This OTP is valid for a limited time and should not be shared with anyone for security reasons.</p>
        <p>If you did not request a password reset, please ignore this email and contact our support team immediately.</p>
        <div class='footer'>
            Thank you for using Blood Connect.<br>
            <a href='mailto:bloodconnect98@gmail.com' class='support'>Contact Support</a>
        </div>
    </div>
</body>
</html>";
$mail->isHTML(true);


            if($mail->send()) {
                echo "<script>alert('OTP sent to your email'); window.location.href='verify-otp.php';</script>";
            } else {
                echo "<script>alert('Failed to send OTP. Please try again.'); window.location.href='forgot-password.php';</script>";
            }
        } catch (Exception $e) {
            echo "<script>alert('Mailer Error: " . $mail->ErrorInfo . "'); window.location.href='forgot-password.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Blood Connect | Forgot Password</title>
    <!-- Meta tag Keywords -->
    <script>
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <!--// Meta tag Keywords -->

    <!-- Custom-Files -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Bootstrap-Core-CSS -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <!-- Style-CSS -->
    <link rel="stylesheet" href="css/fontawesome-all.css">
    <!-- Font-Awesome-Icons-CSS -->
    <!-- //Custom-Files -->

    <!-- Web-Fonts -->
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese"
        rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese"
        rel="stylesheet">
    <!-- //Web-Fonts -->

    <style>
        .login {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login h5 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .login .form-group {
            margin-bottom: 15px;
        }

        .login .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .login .form-control {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .login .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .login .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .login .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .login .account-w3ls {
            font-size: 14px;
            text-align: center;
            color: #333;
        }

        .login .account-w3ls a {
            color: #007bff;
            text-decoration: none;
        }

        .login .account-w3ls a:hover {
            text-decoration: underline;
        }

        .login small {
            color: red;
            display: block;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php');?>

    <!-- banner 2 -->
    <div class="inner-banner-w3ls">
        <div class="container">
        </div>
    </div>
    <!-- page details -->
    <div class="breadcrumb-agile">
        <div aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Forgot Password</li>
            </ol>
        </div>
    </div>
    <!-- //page details -->

    <!-- about -->
    <section class="about py-5">
        <div class="container py-xl-5 py-lg-3">
            <div class="login px-4 mx-auto mw-100">
                <h5 class="text-center mb-4">Forgot Password</h5>
                <form action="#" method="post" name="forgot-password">
                    <div class="form-group">
                        <label>Email ID</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter your email" required="">
                    </div>
                    <button type="submit" class="btn btn-primary submit mb-4" name="submit">Send OTP</button>
                </form>
            </div>
        </div>
    </section>
    <!-- //about -->

    <?php include('includes/footer.php');?>

    <!-- Js files -->
    <!-- JavaScript -->
    <script src="js/jquery-2.2.3.min.js"></script>
    <!-- Default-JavaScript-File -->

    <!-- banner slider -->
    <script src="js/responsiveslides.min.js"></script>
    <script>
        $(function () {
            $("#slider4").responsiveSlides({
                auto: true,
                pager: true,
                nav: true,
                speed: 1000,
                namespace: "callbacks",
                before: function () {
                    $('.events').append("<li>before event fired.</li>");
                },
                after: function () {
                    $('.events').append("<li>after event fired.</li>");
                }
            });
        });
    </script>
    <!-- //banner slider -->

    <!-- fixed navigation -->
    <script src="js/fixed-nav.js"></script>
    <!-- //fixed navigation -->

    <!-- smooth scrolling -->
    <script src="js/SmoothScroll.min.js"></script>
    <!-- move-top -->
    <script src="js/move-top.js"></script>
    <!-- easing -->
    <script src="js/easing.js"></script>
    <!--  necessary snippets for few javascript files -->
    <script src="js/medic.js"></script>

    <script src="js/bootstrap.js"></script>
    <!-- Necessary-JavaScript-File-For-Bootstrap -->
</body>
</html>