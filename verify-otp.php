<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

if(isset($_POST['verify'])){
    $entered_otp = $_POST['otp'];

    if($entered_otp == $_SESSION['otp']){
        echo "<script>alert('OTP Verified! Set a new password.'); document.location ='reset-password.php';</script>";
    } else {
        echo "<script>alert('Invalid OTP');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Blood Connect | Verify OTP</title>
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
                <li class="breadcrumb-item active" aria-current="page">Verify OTP</li>
            </ol>
        </div>
    </div>
    <!-- //page details -->

    <!-- about -->
    <section class="about py-5">
        <div class="container py-xl-5 py-lg-3">
            <div class="login px-4 mx-auto mw-100">
                <h5 class="text-center mb-4">Verify OTP</h5>
                <form action="" method="post" name="verify-otp">
                    <div class="form-group">
                        <label>Enter OTP</label>
                        <input type="text" class="form-control" name="otp" placeholder="Enter the OTP" required="">
                    </div>
                    <button type="submit" class="btn btn-primary submit mb-4" name="verify">Verify OTP</button>
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