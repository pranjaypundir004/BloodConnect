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

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); 

    $sql = "SELECT id FROM tblblooddonars WHERE EmailId = :email AND Password = :password 
            UNION ALL 
            SELECT id FROM tblbloodrequester WHERE EmailId = :email AND Password = :password";

    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result) { 
        $_SESSION['bbdmsdid'] = $result->id;
        $_SESSION['login'] = $email; 

        // Get User IP Address
        $ip = $_SERVER['REMOTE_ADDR'];

        // Send Login Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'bloodconnect98@gmail.com'; // Your email
            $mail->Password   = 'ekrqdrcxeofjvitn'; // Your email password (or App Password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('your-email@gmail.com', 'Blood Connect');
            $mail->addAddress($email);

            // Email Content
 date_default_timezone_set('Asia/Kolkata'); // Set timezone to IST
$loginTime = date('Y-m-d H:i:s'); // Get the current time in IST

$mail->isHTML(true);
$mail->Subject = 'Login Notification - Blood Connect';
$mail->Body = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Login Notification</title>
</head>
<body>
    <table width='100%' border='0' cellspacing='0' cellpadding='0' style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
        <tr>
            <td align='center'>
                <table width='600' border='0' cellspacing='0' cellpadding='0' bgcolor='#ffffff' style='border-radius: 8px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); padding: 20px;'>
                    <tr>
                        <td align='center' style='font-size: 22px; font-weight: bold; color: #D32F2F;'>Login Notification</td>
                    </tr>
                    <tr>
                        <td align='left' style='font-size: 16px; color: #555; padding: 20px; line-height: 24px;'>
                            <p>Dear User,</p>
                            <p>Your <span style='color:#D32F2F; font-weight: bold;'>Blood Connect</span> account was accessed:</p>
                            <p><b>Login Time:</b> $loginTime (IST)</p>
                            <p><b>IP Address:</b> $ip</p>
                            <p>If this wasn’t you, please <a href='https://www.bloodconnect.com/reset-password' style='color: #D32F2F; text-decoration: none;'>reset your password</a> immediately.</p>
                            <p>Stay safe,</p>
                            <p><strong style='color:#D32F2F;'>Blood Connect Team</strong></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>";

$mail->AltBody = "Dear User,\n\nYour Blood Connect account was accessed on:\n\nLogin Time: " . date('Y-m-d H:i:s') . "\nIP Address: $ip\n\nIf this wasn't you, please reset your password immediately: https://www.bloodconnect.com/reset-password\n\nBest regards,\nBlood Connect Team";

$mail->send();

        } catch (Exception $e) {
            echo "<script>alert('Mail Error: {$mail->ErrorInfo}');</script>";
        }

        echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
        exit();
    } else {
        echo "<script>alert('Invalid Email or Password');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Blood Connect | Login</title>
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

        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #555;
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
                <li class="breadcrumb-item active" aria-current="page">Login</li>
            </ol>
        </div>
    </div>
    <!-- //page details -->
<!-- about -->
<section class="about py-5">
    <div class="container py-xl-5 py-lg-3">
        <div class="login px-4 mx-auto mw-100">
            <h5 class="text-center mb-4">Login Now</h5>
            <form action="#" method="post" name="login">
                <div class="form-group">
                    <label>Email ID</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required="">
                </div>
                <div class="form-group password-toggle">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required="">
                    <span class="toggle-password" onclick="togglePasswordVisibility()"><i class="fas fa-eye"></i></span>
                </div>
                <button type="submit" class="btn btn-primary submit mb-4" name="login">Login</button>
                <p class="account-w3ls text-center pb-4" style="color:#000">
                    Don't have an account?
                    <a href="sign-up.php">Create one now</a>
                </p>
                <p class="account-w3ls text-center pb-4" style="color:#000">
                    <a href="forgot_password.php">Forgot Password?</a>
                </p>
            </form>
        </div>
    </div>
</section>
<!-- //about -->
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

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>
</body>

</html>