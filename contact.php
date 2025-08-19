<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

// Debugging: Check session data
if (!isset($_SESSION['userid'])) {
    echo "<script>console.log('User is not logged in');</script>";
} else {
    echo "<script>console.log('User ID: " . $_SESSION['userid'] . "');</script>";
}

$userName = "";
$userEmail = "";
$userPhone = "";

if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];

    // Check if user exists in donors table
    $sql = "SELECT name, EmailId, ContactNumber FROM tblblooddonars WHERE id = :userid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':userid', $userid, PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // If not found in donors table, check in requesters table
    if (!$user) {
        $sql = "SELECT name, EmailId, ContactNumber FROM tblbloodrequester WHERE id = :userid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':userid', $userid, PDO::PARAM_INT);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);
    }

    // Debugging: Check if user data is retrieved
    if ($user) {
        echo "<script>console.log('User Data Found: " . json_encode($user) . "');</script>";
        $userName = $user['name'];
        $userEmail = $user['EmailId'];
        $userPhone = $user['ContactNumber'];
    } else {
        echo "<script>console.log('No user data found for this ID');</script>";
    }
}

// Handle form submission
if (isset($_POST['send'])) {
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $contactno = $_POST['contactno'];
    $message = $_POST['message'];
    $bloodGroup = $_POST['bloodGroup'];

    // Insert into database
    $sql = "INSERT INTO tblcontactusquery(name, EmailId, ContactNumber, BloodGroup, Message) VALUES(:name, :email, :contactno, :bloodGroup, :message)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':contactno', $contactno, PDO::PARAM_STR);
    $query->bindParam(':bloodGroup', $bloodGroup, PDO::PARAM_STR);
    $query->bindParam(':message', $message, PDO::PARAM_STR);
    $query->execute();

    $lastInsertId = $dbh->lastInsertId();

    if ($lastInsertId) {
        // Send email notification
        $mail = new PHPMailer(true);

        try {
            // SMTP Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bloodconnect98@gmail.com'; 
            $mail->Password = 'ekrqdrcxeofjvitn';  // Replace with correct app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email content
            $mail->setFrom('bloodconnect98@gmail.com', 'Blood Connect'); // ✅ This should match SMTP username
            $mail->addReplyTo($email, $name);
            $mail->addAddress('bloodconnect98@gmail.com'); // Admin Email
            $mail->isHTML(true);
            $mail->Subject = "New Contact Query from $name";
            $mail->Body = "
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        background-color: #f9f9f9;
                    }
                    h3 {
                        color: #2c3e50;
                        border-bottom: 2px solid #3498db;
                        padding-bottom: 5px;
                    }
                    p {
                        margin: 10px 0;
                    }
                    .footer {
                        margin-top: 20px;
                        font-size: 12px;
                        color: #777;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h3>New Contact Inquiry</h3>
                    <p><strong>Name:</strong> $name</p>
                    <p><strong>Email:</strong> <a href='mailto:$email'>$email</a></p>
                    <p><strong>Contact Number:</strong> $contactno</p>
                    <p><strong>Blood Group:</strong> $bloodGroup</p>
                    <p><strong>Message:</strong></p>
                    <p>$message</p>
                </div>
            </body>
            </html>
            ";

            // Send email
            $mail->send();
            echo '<script>alert("Query Sent. We will contact you shortly.");</script>';
        } catch (Exception $e) {
            echo "<script>alert('Mail could not be sent. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Blood Connect | Contact Us </title>
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
</head>

<body>
    <?php include('includes/header.php');?>

    <!-- banner 2 -->
    <div class="inner-banner-w3ls">
        <div class="container">
        </div>
        <!-- //banner 2 -->
    </div>
    <!-- page details -->
    <div class="breadcrumb-agile">
        <div aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
            </ol>
        </div>
    </div>
    <!-- //page details -->

    <!-- contact -->
    <div class="agileits-contact py-5">
        <div class="py-xl-5 py-lg-3">
            <div class="w3ls-titles text-center mb-5">
                <h3 class="title">Contact Us</h3>
                <span>
                    <i class="fas fa-user-md"></i>
                </span>
                <p class="mt-2">Donate blood, save lives. Join the Blood Connect community today!</p>
            </div>
            <div class="d-flex">
                <div class="col-lg-5 w3_agileits-contact-left">
                </div>
                <div class="col-lg-7 contact-right-w3l">
                    <h5 class="title-w3 text-center mb-5">Get In Touch</h5>
                    <form id="contactForm" method="post">
						<h3 class="form-group">Enter your details(Carefully)</h3>
                        <!-- Full Name -->
                        <div class="form-group">
                            <input type="text" class="form-control" id="fullname" name="fullname"
                                placeholder="Full Name" oninput="validateFullName()">
                            <small id="fullnameError" style="color: red;"></small>
                        </div>

                        <!-- Contact Number -->
                        <div class="form-group">
                            <input type="text" class="form-control" id="phone" name="contactno" maxlength="10"
                                required placeholder="Please enter your phone number." oninput="validatePhone()">
                            <small id="phoneError" style="color: red;"></small>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <input type="email" class="form-control" id="email" name="email"
                                required placeholder="Please enter your email address." oninput="validateEmail()">
                            <small id="emailError" style="color: red;"></small>
                        </div>

                        <!-- Blood Group -->
                        <div class="form-group">
                            <select class="form-control" name="bloodGroup" id="bloodGroup" required>
                                <option value="" disabled selected>-- Select Which blood you need --</option>
                                <?php
                                if (!$dbh) {
                                    die("Database connection failed!");
                                }

                                $sql = "SELECT * FROM tblbloodgroup";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $bloodGroups = $query->fetchAll(PDO::FETCH_ASSOC);

                                if (empty($bloodGroups)) {
                                    echo "<option disabled>No Blood Groups Found</option>";
                                } else {
                                    foreach ($bloodGroups as $bg) {
                                        echo '<option value="' . htmlspecialchars($bg["BloodGroup"]) . '">' . htmlspecialchars($bg["BloodGroup"]) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <small id="bloodGroupError" style="color: red;"></small>
                        </div>

                        <!-- Message -->
                        <div class="form-group">
                            <textarea rows="10" cols="100" class="form-control" id="message" name="message"
                                required placeholder="Please enter your message" maxlength="999" style="resize:none" oninput="validateMessage()"></textarea>
                            <small id="messageError" style="color: red;"></small>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <input type="submit" id="submitBtn" value="Send Message" name="send">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- //contact -->

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

    <script>
        function validateFullName() {
            var fullname = document.getElementById("fullname").value.trim();
            var error = document.getElementById("fullnameError");

            if (/^\d/.test(fullname)) {
                error.textContent = "❌ Name must start with a letter.";
            } else if (!/^[A-Za-z ]+$/.test(fullname)) {
                error.textContent = "❌ Only letters and spaces are allowed.";
            } else if (fullname.length < 3) {
                error.textContent = "❌ Name must be at least 3 letters.";
            } else {
                error.textContent = "";
            }
        }

        function validatePhone() {
            var phone = document.getElementById("phone").value.trim();
            var error = document.getElementById("phoneError");

            if (!/^\d{10}$/.test(phone)) {
                error.textContent = "❌ Phone number must be exactly 10 digits.";
            } else {
                error.textContent = "";
            }
        }

        function validateEmail() {
            var email = document.getElementById("email").value.trim();
            var error = document.getElementById("emailError");

            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                error.textContent = "❌ Enter a valid email address.";
            } else {
                error.textContent = "";
            }
        }

        function validateMessage() {
            var message = document.getElementById("message").value.trim();
            var error = document.getElementById("messageError");

            if (message.length < 10) {
                error.textContent = "❌ Message must be at least 10 characters.";
            } else {
                error.textContent = "";
            }
        }

        document.getElementById("contactForm").addEventListener("submit", function(event) {
            validateFullName();
            validatePhone();
            validateEmail();
            validateMessage();

            var fullnameError = document.getElementById("fullnameError").textContent;
            var phoneError = document.getElementById("phoneError").textContent;
            var emailError = document.getElementById("emailError").textContent;
            var messageError = document.getElementById("messageError").textContent;

            if (fullnameError || phoneError || emailError || messageError) {
                event.preventDefault();
                alert("❌ Please correct the errors before submitting.");
            }
        });
    </script>
</body>
</html>