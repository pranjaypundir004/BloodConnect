<?php
session_start();
error_reporting(0);
include('includes/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'includes/PHPMailer/src/Exception.php';
require_once 'includes/PHPMailer/src/PHPMailer.php';
require_once 'includes/PHPMailer/src/SMTP.php';

// Function to send OTP
function sendOTP($email) {
    $otp = rand(100000, 999999); // Generate 6-digit OTP
    $_SESSION['otp'] = $otp; // Store OTP in session
    $_SESSION['email'] = $email; // Store email in session

    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server (Use your SMTP host)
        $mail->SMTPAuth = true;
        $mail->Username = 'bloodconnect98@gmail.com'; // Your email
        $mail->Password = 'ekrqdrcxeofjvitn'; // Your email password (use App Password if using Gmail)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Content
        $mail->setFrom('bloodconnect98@gmail.com', 'Blood Connect');
        $mail->addAddress($email);
        $mail->Subject = "Your OTP for Blood Connect Registration";
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 8px;'>
            <h2 style='color: #d9534f; text-align: center;'>Blood Connect - OTP Verification</h2>
            <p>Dear User,</p>

            <p>Thank you for registering with <b>Blood Connect</b>, a platform dedicated to connecting blood donors with those in need. To complete your registration, please use the following One-Time Password (OTP):</p>

            <h2 style='text-align: center; color: #d9534f; background-color: #fff3cd; padding: 10px; border-radius: 5px; display: inline-block;'>
                🔢 <b>$otp</b>
            </h2>

            <p><b>Note:</b> This OTP is valid for the next 10 minutes. Please do not share it with anyone for security reasons.</p>

            <h3>About Blood Connect</h3>
            <p>Blood Connect is a community-driven initiative that helps patients find blood donors quickly and efficiently. By signing up, you become a part of a life-saving network, making a real difference in people’s lives.</p>

            <h3>Why Join Blood Connect?</h3>
            <ul>
                <li>✅ <b>Find & Donate Blood Easily</b> – Seamlessly search for donors or register as a donor.</li>
                <li>✅ <b>Trusted & Secure</b> – Your data is safe with us, and we ensure reliable donor connections.</li>
                <li>✅ <b>Save Lives</b> – Every drop counts, and your contribution can make a huge impact.</li>
            </ul>

            <p>If you did not request this OTP, please ignore this email.</p>

            <p>For any assistance, feel free to contact us at <b>[Bloodconnect98@gmail.com]</b>.</p>

            <p style='text-align: center; font-size: 16px; color: #555;'><b>Best regards,<br>Blood Connect Team</b></p>
        </div>
        ";
        $mail->isHTML(true);

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

// Handle OTP Request
if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $mobileno = $_POST['mobileno'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $bloodgroup = $_POST['bloodgroup'];
    $address = $_POST['address'];
    // Check if email already exists in the database
    $checkEmailQuery = $dbh->prepare("SELECT EmailId FROM tblblooddonars WHERE EmailId = :email");
        $checkEmailQuery = $dbh->prepare("SELECT EmailId FROM tblbloodrequester WHERE EmailId = :email");
    $checkEmailQuery->bindParam(':email', $email, PDO::PARAM_STR);
    $checkEmailQuery->execute();

    if ($checkEmailQuery->rowCount() > 0) {
        echo "<script>alert('❌ Email already registered. Please log in or register with a new email.'); window.location.href='login.php';</script>";
        exit();
    }

    $_SESSION['user_details'] = [
        'fullname' => $fullname,
        'mobileno' => $mobileno,
        'email' => $email,
        'password' => $password,
        'role' => $role,
        'gender' => $gender,
        'age' => $age,
        'bloodgroup' => $bloodgroup,
        'address' => $address
    ];

    sendOTP($email);
}

// OTP Verification Logic
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
                $sql = "INSERT INTO tblblooddonars (FullName, MobileNumber, EmailId, Password) 
                        VALUES (:fullname, :mobile, :email, :password)";
            } elseif ($role == "Blood Requester") {
                // Insert into tblbloodrequester
                $sql = "INSERT INTO tblbloodrequester (FullName, MobileNumber, EmailId, Password) 
                        VALUES (:fullname, :mobile, :email, :password)";
            } else {
                echo "<script>alert('❌ Invalid role selected.');</script>";
                exit();
            }

            $query = $dbh->prepare($sql);
            $query->bindParam(':fullname', $user['fullname'], PDO::PARAM_STR);
            $query->bindParam(':mobile', $user['mobileno'], PDO::PARAM_STR);
            $query->bindParam(':email', $user['email'], PDO::PARAM_STR);
            $query->bindParam(':password', $user['password'], PDO::PARAM_STR);
            $query->bindParam(':gender', $user['gender'], PDO::PARAM_STR);
            $query->bindParam(':age', $user['age'], PDO::PARAM_STR);
            $query->bindParam(':bloodgroup', $user['bloodgroup'], PDO::PARAM_STR);
            $query->bindParam(':address', $user['address'], PDO::PARAM_STR);
            if ($query->execute()) {
                // Clear session data after successful registration
                unset($_SESSION['otp']);
                unset($_SESSION['user_details']);
                echo "<script>alert('✅ OTP Verified & Registration Successful!'); window.location.href='login.php';</script>";
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
<html lang="zxx">
<head>
<title>Blood Connect | Sign Up</title>
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
        max-width: 600px;
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
            <li class="breadcrumb-item active" aria-current="page">Sign Up</li>
        </ol>
    </div>
</div>
<!-- //page details -->

<!-- about -->
<section class="about py-5">
    <div class="container py-xl-5 py-lg-3">
        <div class="login px-4 mx-auto mw-100">
            <h5 class="text-center mb-4">Register Now</h5>
            <form action="#" method="post" name="signup" onsubmit="return validateForm()">

<label class="mb-2"><b>Your Name</b></label>
<input type="text" class="form-control" name="fullname" id="fullname"
       placeholder="Full Name" oninput="validateFullName()" onkeydown="blockInvalidKeys(event)">
<small id="fullnameError" style="color: red;"></small>

<script>
    let timeout = null; // For debouncing validation

    function validateFullName() {
        var fullname = document.getElementById("fullname").value;
        var error = document.getElementById("fullnameError");

        // If first character is a digit, show immediate warning
        if (/^\d/.test(fullname)) {
            error.textContent = "❌ Name must start with a letter.";
            error.style.color = "red";
            return; // Stop further validation
        }

        // If user is still typing and only entering letters, don't show any warning
        if (/^[A-Za-z ]*$/.test(fullname)) {
            error.textContent = ""; // No warning while entering valid letters
        }

        // Clear previous timeout to delay validation
        clearTimeout(timeout);
        timeout = setTimeout(() => validateAfterTyping(fullname, error), 500);
    }

    function validateAfterTyping(fullname, error) {
        var onlyLettersAndSpacesRegex = /^[A-Za-z ]+$/;  // Only letters and spaces allowed
        var minLengthRegex = /^[A-Za-z ]{3,}$/;  // At least 3 letters

        if (fullname.trim() === "") {
            error.textContent = "";  // No error when empty
        } else if (!onlyLettersAndSpacesRegex.test(fullname)) {
            error.textContent = "❌ Only letters and spaces are allowed.";
            error.style.color = "red";
        } else if (!minLengthRegex.test(fullname)) {
            error.textContent = "❌ Name must be at least 3 letters.";
            error.style.color = "red";
        } else {
            error.textContent = "";
            error.style.color = "green";
        }
    }

    function blockInvalidKeys(event) {
        var error = document.getElementById("fullnameError").textContent;

        // Prevent Tab or Enter key when the name is invalid
        if ((event.key === "Tab" || event.key === "Enter") && error.includes("❌")) {
            event.preventDefault();
            alert("❌ Please enter a valid name before proceeding!");
        }
    }
</script>
                <div class="form-group">
                    <label class="mb-2">Mobile Number</label>
                    <input type="text" class="form-control" name="mobileno" id="mobileno" required 
                        placeholder="Enter 10-digit Mobile Number" minlength="10" maxlength="10" 
                        pattern="\d{10}" oninput="validateMobile()" onblur="validateMobile()">
                    <small id="mobileError" style="color: red; display: none;">Mobile number must be exactly 10 digits. </small>
                </div>
                <script>
                    function validateMobile() {
                        let mobileInput = document.getElementById("mobileno");
                        let mobileError = document.getElementById("mobileError");
                        let mobileValue = mobileInput.value;

                        if (mobileValue.length === 10 && /^\d{10}$/.test(mobileValue)) {
                            mobileError.style.display = "none";  // Hide error when valid
                        } else {
                            mobileError.style.display = "block"; // Show error when invalid
                        }
                    }
                </script>
                <div class="form-group">
                    <label>Enter Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your valid Email" required="">
                </div>
                <div class="form-group">
    <label class="mb-2">Age</label>
    <input type="number" class="form-control" name="age" id="age" placeholder="Age" required min="18" oninput="validateAge()">
    <small id="ageError" style="color: red; display: none;">Age must be 18 or above.</small>
</div>

<script>
    function validateAge() {
        let ageInput = document.getElementById("age");
        let ageError = document.getElementById("ageError");
        let age = parseInt(ageInput.value, 10);

        if (age < 18 || isNaN(age)) {
            ageError.style.display = "block";
        } else {
            ageError.style.display = "none";
        }
    }
</script>

                <div class="form-group">
                    <label class="mb-2">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="form-group">
        <label class="mb-2">Role</label>
        <select name="role" class="form-control" required>
            <option value="">Select Role</option>
            <option value="Blood Donor">Blood Donor</option>
            <option value="Blood Requester">Blood Requester</option>
        </select>
    </div>
                <div class="form-group">
                    <label class="mb-2">Blood Group</label>
                    <select name="bloodgroup" class="form-control" required>
                        <?php 
                        $sql = "SELECT * from  tblbloodgroup ";
                        $query = $dbh -> prepare($sql);
                        $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                        $cnt=1;
                        if($query->rowCount() > 0)
                        {
                            foreach($results as $result)
                            {               ?>  
                                <option value="<?php echo htmlentities($result->BloodGroup);?>"><?php echo htmlentities($result->BloodGroup);?></option>
                                <?php }} ?>
                    </select>
                </div>
<div class="form-group">
<label class="mb-2">State</label>
<select name="state" id="state" class="form-control" required>
    <option value="">Select State</option>
    <?php 
   
    $sql = "SELECT * FROM states";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $result) { ?>  
            <option value="<?php echo htmlentities($result->state_id); ?>">
                <?php echo htmlentities($result->name); ?>
            </option>
    <?php }} ?>
</select>
</div>

<div class="form-group">
<label class="mb-2">City</label>
<select name="address" id="city" class="form-control" required>
    <option value="">Select City</option>
</select>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#state').on('change', function() { // Correcting ID reference
        var stateId = $(this).val();
        if (stateId) {
            $.ajax({
                url: "fetch_cities.php", // Fetch cities dynamically
                method: "POST",
                data: { state_id: stateId },
                dataType: "json",
                success: function(data) {
                    $('#city').html('<option value="">Select City</option>'); // Fix ID reference
                    $.each(data, function(key, value) {
                        $('#city').append('<option value="' + value.name + '">' + value.name + '</option>'); // Save city name correctly
                    });
                }
            });
        } else {
            $('#city').html('<option value="">Select City</option>'); // Reset if no state selected
        }
    });
});
</script>



                <div class="form-group">
                    <label>Message</label>
                    <textarea class="form-control" name="message" placeholder="Enter any messeage for us!"> </textarea> 
                </div>
<div class="form-group">
    <label>Password</label>
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

<script>
    function validatePassword() {
        const password = document.getElementById("password").value;
        const sendOtpBtn = document.querySelector("button[name='send_otp']");

        // Check conditions
        const lengthCheck = password.length >= 8;
        const uppercaseCheck = /[A-Z]/.test(password);
        const lowercaseCheck = /[a-z]/.test(password);
        const digitCheck = /[0-9]/.test(password);
        const specialCharCheck = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        // Update UI for each condition
        updateRequirement("lengthCheck", lengthCheck);
        updateRequirement("uppercaseCheck", uppercaseCheck);
        updateRequirement("lowercaseCheck", lowercaseCheck);
        updateRequirement("digitCheck", digitCheck);
        updateRequirement("specialCharCheck", specialCharCheck);

        // Enable/Disable "Send OTP" button
        sendOtpBtn.disabled = !(lengthCheck && uppercaseCheck && lowercaseCheck && digitCheck && specialCharCheck);
    }

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

    // Toggle password visibility
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
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/smooth-scroll/16.1.3/smooth-scroll.polyfills.min.js"></script>
<script>
    var scroll = new SmoothScroll('a[href*="#"]', {
        speed: 800,
        speedAsDuration: true,
        passive: true  // Prevents passive event errors
    });
</script>
<script>
    document.addEventListener("wheel", function(event) {
        event.stopPropagation(); // Stops the error
    }, { passive: false });
</script>

<button type="submit" name="send_otp" class="btn btn-primary btn-block send-otp-btn" disabled>
    Send OTP <i class="fas fa-paper-plane"></i>
</button>
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
<!-- Necessary-JavaScript-File-For