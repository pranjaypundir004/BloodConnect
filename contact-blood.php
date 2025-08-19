<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('includes/config.php'); 

require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';
require 'includes/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if user is logged in
if (!isset($_SESSION['bbdmsdid'])) {
    echo "<script>alert('Please log in to request blood.'); window.location='login.php';</script>";
    exit;
}

// Fetch user details from the database
$user_id = $_SESSION['bbdmsdid'];
$sql = "SELECT FullName, EmailId, MobileNumber FROM tblblooddonars WHERE id=:user_id";
$query = $dbh->prepare($sql);
$query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$query->execute();
$user = $query->fetch(PDO::FETCH_OBJ);

if (!$user) {
    $sql = "SELECT FullName, EmailId, MobileNumber FROM tblbloodrequester WHERE id=:user_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_OBJ);
}

$name = $user->FullName ?? '';
$email = $user->EmailId ?? '';
$contactno = $user->MobileNumber ?? '';

// Handle blood request form submission
if (isset($_POST['send'])) {
    $brf = $_POST['brf'];
    $message = $_POST['message'];
    $cid = $_GET['cid'] ?? null;
    $bid = $_GET['bid'] ?? null;

    // Determine if request is for a donor or blood bank
    if ($cid) {
        $sql = "INSERT INTO tblbloodrequirer (BloodDonarID, name, EmailId, ContactNumber, BloodRequirefor, Message)
                VALUES (:cid, :name, :email, :contactno, :brf, :message)";
    } elseif ($bid) {
        $sql = "INSERT INTO tblbloodrequirer (BloodBankID, name, EmailId, ContactNumber, BloodRequirefor, Message)
                VALUES (:bid, :name, :email, :contactno, :brf, :message)";
    } else {
        echo "<script>alert('Invalid request.');</script>";
        exit;
    }

    $query = $dbh->prepare($sql);
    if ($cid) $query->bindParam(':cid', $cid, PDO::PARAM_STR);
    if ($bid) $query->bindParam(':bid', $bid, PDO::PARAM_STR);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':contactno', $contactno, PDO::PARAM_STR);
    $query->bindParam(':brf', $brf, PDO::PARAM_STR);
    $query->bindParam(':message', $message, PDO::PARAM_STR);
    $query->execute();

    if ($dbh->lastInsertId()) {
        // Fetch recipient's email and name (Donor or Blood Bank)
        if ($cid) {
            $sql = "SELECT EmailId, FullName FROM tblblooddonars WHERE id=:cid";
        } else {
            $sql = "SELECT EmailId, bloodBankName AS FullName FROM tblbloodstock WHERE id=:bid";
        }

        $query = $dbh->prepare($sql);
        if ($cid) $query->bindParam(':cid', $cid, PDO::PARAM_INT);
        if ($bid) $query->bindParam(':bid', $bid, PDO::PARAM_INT);
        $query->execute();
        $recipient = $query->fetch(PDO::FETCH_OBJ);
        $recipientEmail = $recipient->EmailId ?? '';
        $recipientName = $recipient->FullName ?? 'Recipient';

        // Send Emails to both users
        if (!empty($recipientEmail) && filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            sendEmailToRecipient($recipientEmail, $recipientName, $name,$email, $contactno, $brf, $message);
        } else {
            error_log("Invalid recipient email: " . $recipientEmail);
        }

        sendEmailToRequester($email, $name, $brf, $message);

        echo '<script>alert("Request has been sent. We will contact you shortly.");</script>';
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
}

// Function to Send Email to the Recipient (Donor/Blood Bank)
function sendEmailToRecipient($recipientEmail, $recipientName, $senderName, $email, $senderContact, $bloodFor, $message) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bloodconnect98@gmail.com'; // Your Gmail
        $mail->Password = 'ekrqdrcxeofjvitn'; // Use App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender Information
        $mail->setFrom('bloodconnect98@gmail.com', 'Blood Connect');
        $mail->addReplyTo('bloodconnect98@gmail.com', 'Blood Connect Team');

        // Email to Recipient
        $mail->addAddress($recipientEmail);
        $mail->Subject = "Urgent Blood Request Received";
        $mail->isHTML(true);
        $mail->Body = "
<html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px;'>
        <p style='font-size: 18px;'><strong>Dear $recipientName,</strong></p>

        <p>We hope you are doing well. You have been identified as a potential donor for an <strong>urgent blood request</strong> received through <strong>Blood Connect</strong>. Your generosity can make a life-saving difference!</p>

        <h3 style='color: #d9534f;'>Requester's Details:</h3>
        <table style='border-collapse: collapse; width: 100%; max-width: 600px; font-size: 16px; border: 1px solid #ddd;'>
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd; background-color: #f8f8f8;'><strong>Name:</strong></td>
                <td style='padding: 10px; border: 1px solid #ddd;'>$senderName</td>
            </tr>
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd; background-color: #f8f8f8;'><strong>Contact:</strong></td>
                <td style='padding: 10px; border: 1px solid #ddd;'>$senderContact</td>
            </tr>
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd; background-color: #f8f8f8;'><strong>Email address:</strong></td>
                <td style='padding: 10px; border: 1px solid #ddd;'>$email</td>
            </tr>
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd; background-color: #f8f8f8;'><strong>Blood Required For:</strong></td>
                <td style='padding: 10px; border: 1px solid #ddd;'>$bloodFor</td>
            </tr>
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd; background-color: #f8f8f8;'><strong>Message:</strong></td>
                <td style='padding: 10px; border: 1px solid #ddd;'>$message</td>
            </tr>
        </table>

        <p style='font-size: 16px;'>If you are available to donate, please reach out to the requester directly using the provided contact details. Your willingness to help is truly appreciated!</p>

        <p style='margin-top: 20px; font-size: 14px;'>For any assistance, feel free to reach out to our support team.</p>

        <p style='margin-top: 20px; font-weight: bold;'>Best Regards,</p>
        <p><strong>Blood Connect Team</strong></p>
        <p>Email: <a href='mailto:bloodconnect98@gmail.com' style='color: #d9534f; text-decoration: none;'>bloodconnect98@gmail.com</a></p>
        <p>Website: <a href='https://www.bloodconnect.com' style='color: #d9534f; text-decoration: none;'>www.bloodconnect.com</a></p>
    </body>
</html>
";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email to recipient failed: " . $mail->ErrorInfo);
    }
}

// Function to Send Email to the Requester
function sendEmailToRequester($requesterEmail, $requesterName, $bloodFor, $message) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bloodconnect98@gmail.com'; // Your Gmail
        $mail->Password = 'ekrqdrcxeofjvitn'; // Use App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email to Requester
        $mail->setFrom('bloodconnect98@gmail.com', 'Blood Connect');
        $mail->addAddress($requesterEmail);
        $mail->Subject = "Blood Request Sent Successfully";
        $mail->isHTML(true);
        $mail->Body = "
<html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px;'>
        <p style='font-size: 18px;'><strong>Dear $requesterName,</strong></p>

        <p>Thank you for reaching out to <strong>Blood Connect</strong>. Your blood request has been successfully submitted, and we are working to connect you with a potential donor.</p>
       
        <p style='font-size: 16px;'>Our team or a suitable donor will reach out to you soon. If you have any urgent concerns, please do not hesitate to contact us.</p>

        <p style='margin-top: 20px; font-size: 14px;'>For any assistance, feel free to reach out to our support team.</p>

        <p style='margin-top: 20px; font-weight: bold;'>Best Regards,</p>
        <p><strong>Blood Connect Team</strong></p>
        <p>Email: <a href='mailto:bloodconnect98@gmail.com' style='color: #d9534f; text-decoration: none;'>support@bloodconnect.com</a></p>
        <p>Website: <a href='https://www.bloodconnect.com' style='color: #d9534f; text-decoration: none;'>www.bloodconnect.com</a></p>
    </body>
</html>
";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email to requester failed: " . $mail->ErrorInfo);
    }
}
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>Blood Connect | Request Blood</title>
    <!-- Meta tag Keywords -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Blood Connect, Blood Donation, Request Blood" />
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
                <li class="breadcrumb-item active" aria-current="page">Request Blood</li>
            </ol>
        </div>
    </div>
    <!-- //page details -->

    <div class="container py-5">
        <h3 class="text-center">Request Blood</h3>
        <form method="post">
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" class="form-control" name="fullname" value="<?php echo htmlentities($name); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" class="form-control" name="contactno" value="<?php echo htmlentities($contactno); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlentities($email); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Blood Require For</label>
                <select class="form-control" name="brf" required>
                    <option value="">Select</option>
                    <option value="Father">Father</option>
                    <option value="Mother">Mother</option>
                    <option value="Brother">Brother</option>
                    <option value="Sister">Sister</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea rows="4" class="form-control" name="message" placeholder="Enter your message" maxlength="999" required></textarea>
            </div>
            <div class="form-group">
                <input type="submit" value="Send Request" name="send" class="btn btn-primary">
            </div>
        </form>
    </div>

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

    <!-- //Js files -->

</body>
</html>