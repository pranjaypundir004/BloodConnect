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
if (isset($_POST['reset'])) {
    $new_password = md5($_POST['password']);
    $confirm_password = md5($_POST['confirm_password']);
    $email = $_SESSION['reset_email'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match. Please try again.";
    } else {
        // Update password in tblblooddonars
        $sql_donor = "UPDATE tblblooddonars SET Password=:password WHERE EmailId=:email";
        $query_donor = $dbh->prepare($sql_donor);
        $query_donor->bindParam(':password', $new_password, PDO::PARAM_STR);
        $query_donor->bindParam(':email', $email, PDO::PARAM_STR);

        // Update password in tblbloodrequester
        $sql_requester = "UPDATE tblbloodrequester SET Password=:password WHERE EmailId=:email";
        $query_requester = $dbh->prepare($sql_requester);
        $query_requester->bindParam(':password', $new_password, PDO::PARAM_STR);
        $query_requester->bindParam(':email', $email, PDO::PARAM_STR);

        // Execute the updates
        $donor_updated = $query_donor->execute();
        $requester_updated = $query_requester->execute();

        if ($donor_updated || $requester_updated) {
            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // SMTP Configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'bloodconnect98@gmail.com'; // Your email
            $mail->Password = 'ekrqdrcxeofjvitn'; // Replace with your email password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Email Content
                $mail->setFrom('no-reply@bloodconnect.com', 'Blood Connect'); // Sender
                $mail->addAddress($email); // Recipient
                $mail->isHTML(true);
              $mail->Subject = 'Password Change Confirmation - Blood Connect';
$mail->Body = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Password Change Confirmation</title>
</head>
<body>
    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
        <tr>
            <td align='center' bgcolor='#ffffff'>
                <table width='600' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td align='center' style='font-size: 24px; font-weight: bold; color: #333333; padding: 20px 0;'>Password Change Confirmation</td>
                    </tr>
                    <tr>
                        <td align='left' style='font-size: 16px; color: #555555; padding: 20px; line-height: 24px;'>
                            <p>Hello,</p>
                            <p>This is to confirm that your password for your Blood Connect account has been successfully changed.</p>
                            <p>If you did not request this change, please contact our support team immediately at <a href='mailto:bloodconnect98@gmail.com'>bloodconnect98@gmail.com</a>.</p>
                            <p>Thank you for using Blood Connect.</p>
                            <p>Best regards,</p>
                            <p><strong>Blood Connect Team</strong></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>";

$mail->AltBody = "Hello,\n\nThis is to confirm that your password for your Blood Connect account has been successfully changed.\n\nIf you did not request this change, please contact our support team immediately at bloodconnect98@gmail.com.\n\nThank you for using Blood Connect.\n\nBest regards,\nBlood Connect Team";
                $mail->send(); // Send the email

                $_SESSION['success'] = "Password reset successfully. A confirmation email has been sent.";
                session_destroy();
                header("Location: login.php");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = "Password reset successful, but email could not be sent. Error: " . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['error'] = "An error occurred while resetting the password. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Blood Connect | Reset Password</title>
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
                <li class="breadcrumb-item active" aria-current="page">Reset Password</li>
            </ol>
        </div>
    </div>
    <!-- //page details -->

    <!-- about -->
    <section class="about py-5">
        <div class="container py-xl-5 py-lg-3">
            <div class="login px-4 mx-auto mw-100">
                <h5 class="text-center mb-4">Reset Password</h5>
                <?php if(isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>

                <?php if(isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php } ?>

                <form action="" method="post" name="reset-password" id="reset-password-form">
                    <div class="form-group">
                        <label>New Password</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" name="password" id="password" required oninput="validatePassword()">
                            <span id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                        <small id="passwordStrength">
                            Password must contain:
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                <li id="lengthCheck">❌ At least 8 characters</li>
                                <li id="uppercaseCheck">❌ 1 uppercase letter</li>
                                <li id="lowercaseCheck">❌ 1 lowercase letter</li>
                                <li id="digitCheck">❌ 1 digit</li>
                                <li id="specialCharCheck">❌ 1 special character</li>
                            </ul>
                        </small>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required oninput="validatePassword()">
                            <span id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                        <small id="passwordMatch" style="color: red; display: none;">Passwords do not match</small>
                    </div>
                    <button type="submit" class="btn btn-primary submit mb-4" name="reset" id="reset-btn">Reset Password</button>
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

    <script>
        function validatePassword() {
            const password = document.getElementById("password").value;
            const confirm_password = document.getElementById("confirm_password").value;
            const resetBtn = document.getElementById("reset-btn");
            const passwordMatch = document.getElementById("passwordMatch");

            // Password strength conditions
            const lengthCheck = password.length >= 8;
            const uppercaseCheck = /[A-Z]/.test(password);
            const lowercaseCheck = /[a-z]/.test(password);
            const digitCheck = /[0-9]/.test(password);
            const specialCharCheck = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            const passwordMatchCheck = password === confirm_password;

            // Update UI for each condition
            updateRequirement("lengthCheck", lengthCheck);
            updateRequirement("uppercaseCheck", uppercaseCheck);
            updateRequirement("lowercaseCheck", lowercaseCheck);
            updateRequirement("digitCheck", digitCheck);
            updateRequirement("specialCharCheck", specialCharCheck);

            // Show password match warning
            if (!passwordMatchCheck) {
                passwordMatch.style.display = "block";
            } else {
                passwordMatch.style.display = "none";
            }

            // Enable/Disable "Reset Password" button
            resetBtn.disabled = !(lengthCheck && uppercaseCheck && lowercaseCheck && digitCheck && specialCharCheck && passwordMatchCheck);
        }

        // Update UI for password requirements
        function updateRequirement(id, isValid) {
            const element = document.getElementById(id);
            if (isValid) {
                element.innerHTML = "✅ " + element.innerText.slice(2);
                element.style.color = "green";
            } else {
                element.innerHTML = "❌ " + element.innerText.slice(2);
                element.style.color = "red";
            }
        }

        // Toggle password visibility for "New Password"
        document.getElementById("togglePassword").addEventListener("click", function () {
            const passwordField = document.getElementById("password");
            const icon = this.querySelector("i");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });

        // Toggle password visibility for "Confirm Password"
        document.getElementById("toggleConfirmPassword").addEventListener("click", function () {
            const confirmPasswordField = document.getElementById("confirm_password");
            const icon = this.querySelector("i");
            if (confirmPasswordField.type === "password") {
                confirmPasswordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                confirmPasswordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });

        // Form submission warning
        document.getElementById("reset-password-form").addEventListener("submit", function (event) {
            if (document.getElementById("reset-btn").disabled) {
                event.preventDefault();
                alert("Please fulfill all the requirements before submitting.");
            }
        });
    </script>
</body>
</html>