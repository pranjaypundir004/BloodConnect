<?php 
session_start();
error_reporting(0);
include('includes/config.php');

// Regenerate session ID for security
session_regenerate_id(true);

if (strlen($_SESSION['bbdmsdid']) == 0) {
    header('location:logout.php');
    exit();
}

if (isset($_POST['change'])) {
    $uid = $_SESSION['bbdmsdid'];
    $cpassword = md5($_POST['currentpassword']); // Hash the current password
    $newpassword = $_POST['newpassword'];
    $confirmpassword = $_POST['confirmpassword'];

    // First, check in tblblooddonars
    $sql = "SELECT Password FROM tblblooddonars WHERE id=:uid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    // If not found in tblblooddonars, check tblbloodrequester
    if (!$result) {
        $sql = "SELECT Password FROM tblbloodrequester WHERE id=:uid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':uid', $uid, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        $userType = "requester";
    } else {
        $userType = "donor";
    }

    if (!$result) {
        echo '<script>alert("User not found.")</script>';
    } elseif ($cpassword !== $result->Password) {
        echo '<script>alert("Your current password is incorrect.")</script>';
    } elseif ($newpassword !== $confirmpassword) {
        echo '<script>alert("New Password and Confirm Password do not match.")</script>';
    } elseif (md5($newpassword) === $result->Password) {
        echo '<script>alert("New password cannot be the same as the last password.")</script>';
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $newpassword)) {
        echo '<script>alert("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.")</script>';
    } else {
        $newpassword = md5($newpassword); // Hash the new password

        // Update password in the correct table
        if ($userType == "donor") {
            $con = "UPDATE tblblooddonars SET Password=:newpassword WHERE id=:uid";
        } else {
            $con = "UPDATE tblbloodrequester SET Password=:newpassword WHERE id=:uid";
        }

        $chngpwd1 = $dbh->prepare($con);
        $chngpwd1->bindParam(':uid', $uid, PDO::PARAM_STR);
        $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
        $chngpwd1->execute();

        echo '<script>alert("Your password has been successfully changed.")</script>';
    }
}

        
    

?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Blood Connect!! Change Password</title>
    <script>
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <script type="text/javascript">
        function checkpass() {
            const newPassword = document.getElementById('newpassword').value;
            const confirmPassword = document.getElementById('confirmpassword').value;

            if (newPassword !== confirmPassword) {
                alert('New Password and Confirm Password field does not match');
                document.getElementById('confirmpassword').focus();
                return false;
            }
            return true;
        }

        function togglePasswordVisibility(inputId, eyeIconId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeIconId);
            if (input.type === "password") {
                input.type = "text";
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        function validatePassword() {
            const password = document.getElementById('newpassword').value;
            const warning = document.getElementById('password-warning');
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (regex.test(password)) {
                warning.style.display = 'none';
            } else {
                warning.style.display = 'block';
            }
        }

        function validateConfirmPassword() {
            const newPassword = document.getElementById('newpassword').value;
            const confirmPassword = document.getElementById('confirmpassword').value;
            const confirmWarning = document.getElementById('confirmpassword-warning');

            if (newPassword !== confirmPassword) {
                confirmWarning.style.display = 'block';
            } else {
                confirmWarning.style.display = 'none';
            }
        }

        function validateNewPasswordNotSameAsLast() {
            const newPassword = document.getElementById('newpassword').value;
            const lastPasswordWarning = document.getElementById('lastpassword-warning');

            // Fetch the last password from the server (you can use AJAX for this)
            // For now, we assume the last password is stored in a hidden field
            const lastPassword = document.getElementById('lastpassword').value;

            if (newPassword === lastPassword) {
                lastPasswordWarning.style.display = 'block';
            } else {
                lastPasswordWarning.style.display = 'none';
            }
        }
    </script>

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
        <div class="container"></div>
        <!-- //banner 2 -->
    </div>
    <!-- page details -->
    <div class="breadcrumb-agile">
        <div aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Change Password</li>
            </ol>
        </div>
    </div>
    <!-- //page details -->

    <!-- contact -->
    <div class="appointment py-5">
        <div class="py-xl-5 py-lg-3">
            <div class="w3ls-titles text-center mb-5">
                <h3 class="title">Change Password</h3>
                <span>
                    <i class="fas fa-user-md"></i>
                </span>
            </div>
            <div class="d-flex">
                <div class="appoint-img"></div>
                <div class="contact-right-w3l appoint-form">
                    <h5 class="title-w3 text-center mb-5">Reset your password if needed</h5>
                    <form action="#" method="post" onsubmit="return checkpass();" name="changepassword">
                        <!-- Hidden field to store the last password (for demo purposes) -->
                        <input type="hidden" id="lastpassword" value="<?php echo $result->Password; ?>">

                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="currentpassword" id="currentpassword" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePasswordVisibility('currentpassword', 'currentpassword-eye')">
                                        <i id="currentpassword-eye" class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="recipient-phone" class="col-form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" name="newpassword" id="newpassword" class="form-control" required oninput="validatePassword(); validateNewPasswordNotSameAsLast();">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePasswordVisibility('newpassword', 'newpassword-eye')">
                                        <i id="newpassword-eye" class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <small id="password-warning" style="color: red; display: none;">
                                Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.
                            </small>
                            <small id="lastpassword-warning" style="color: red; display: none;">
                                New password cannot be the same as the last password.
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="recipient-phone" class="col-form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" required oninput="validateConfirmPassword()">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePasswordVisibility('confirmpassword', 'confirmpassword-eye')">
                                        <i id="confirmpassword-eye" class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <small id="confirmpassword-warning" style="color: red; display: none;">
                                Confirm Password does not match New Password.
                            </small>
                        </div>
                        <input type="submit" value="Update" name="change" class="btn_apt">
                    </form>
                </div>
                <div class="clerafix"></div>
            </div>
        </div>
    </div>
    <!-- //contact -->

    <?php include('includes/footer.php');?>
    <!-- Js files -->
    <!-- JavaScript -->
    <script src="js/jquery-2.2.3.min.js"></script>
    <!-- Default-JavaScript-File -->

    <!--start-date-piker-->
    <link rel="stylesheet" href="css/jquery-ui.css" />
    <script src="js/jquery-ui.js"></script>
    <script>
        $(function () {
            $("#datepicker,#datepicker1").datepicker();
        });
    </script>
    <!-- //End-date-piker -->

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