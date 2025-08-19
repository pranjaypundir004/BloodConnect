<?php 
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['bbdmsdid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['update'])) {
        $uid = $_SESSION['bbdmsdid'];
        $name = trim($_POST['fullname']);
        $mno = trim($_POST['mobileno']);
        $age = trim($_POST['age']);
        $gender = $_POST['gender'];
        $bloodgroup = $_POST['bloodgroup'];
        $address = trim($_POST['address']);
        $message = trim($_POST['message']);

        // Validations
        $errors = [];

        // Name Validation
        if (!preg_match("/^[A-Za-z ]{3,}$/", $name)) {
            $errors[] = "Full Name must contain only letters and spaces (at least 3 characters).";
        }

        // Mobile Number Validation (10 digits)
        if (!preg_match("/^[0-9]{10}$/", $mno)) {
            $errors[] = "Mobile Number must be exactly 10 digits.";
        }

        // Age Validation (Only Numbers)
        if (!preg_match("/^[0-9]{1,3}$/", $age) || $age < 18 || $age > 65) {
            $errors[] = "Age must be a valid number between 18 and 65.";
        }

        // If there are no errors, proceed with update
        if (empty($errors)) {
            try {
                $sql1 = "UPDATE tblblooddonars SET FullName=:name, MobileNumber=:mno, Age=:age, Gender=:gender, BloodGroup=:bloodgroup, Address=:address, Message=:message WHERE id=:uid";
                $sql2 = "UPDATE tblbloodrequester SET FullName=:name, MobileNumber=:mno, Age=:age, Gender=:gender, BloodGroup=:bloodgroup, Address=:address, Message=:message WHERE id=:uid";

                $query1 = $dbh->prepare($sql1);
                $query2 = $dbh->prepare($sql2);

                $params = [
                    ':name' => $name,
                    ':mno' => $mno,
                    ':age' => $age,
                    ':gender' => $gender,
                    ':bloodgroup' => $bloodgroup,
                    ':address' => $address,
                    ':message' => $message,
                    ':uid' => $uid
                ];

                $query1->execute($params);
                $query2->execute($params);

                echo '<script>alert("Profile has been updated successfully.");</script>';
            } catch (Exception $e) {
                echo '<script>alert("Error updating profile: ' . $e->getMessage() . '");</script>';
            }
        } else {
            foreach ($errors as $error) {
                echo "<script>alert('$error');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
	<title>Blood Connect !! Donor Profile</title>
	
	<script>
        document.addEventListener("DOMContentLoaded", function () {
            // Full Name Validation
            document.getElementById("fullname_profile").addEventListener("input", function () {
                validateTextInput(this, /^[A-Za-z ]{3,}$/, "❌ Only letters and spaces allowed (Min 3 characters).");
            });

            // Mobile Number Validation
            document.getElementById("mobileno_profile").addEventListener("input", function () {
                validateTextInput(this, /^[0-9]{10}$/, "❌ Mobile Number must be exactly 10 digits.");
            });

            // Age Validation
            document.getElementById("age_profile").addEventListener("input", function () {
                let age = this.value;
                if (!/^\d{1,3}$/.test(age) || age < 18 || age > 65) {
                    showError(this, "❌ Age must be between 18 and 65.");
                } else {
                    clearError(this);
                }
            });

            function validateTextInput(inputElement, regex, errorMessage) {
                if (!regex.test(inputElement.value)) {
                    showError(inputElement, errorMessage);
                } else {
                    clearError(inputElement);
                }
            }

            function showError(inputElement, message) {
                let errorSpan = inputElement.nextElementSibling;
                if (!errorSpan || errorSpan.tagName !== "SPAN") {
                    errorSpan = document.createElement("SPAN");
                    errorSpan.style.color = "red";
                    inputElement.parentNode.insertBefore(errorSpan, inputElement.nextSibling);
                }
                errorSpan.textContent = message;
            }

            function clearError(inputElement) {
                let errorSpan = inputElement.nextElementSibling;
                if (errorSpan && errorSpan.tagName === "SPAN") {
                    errorSpan.textContent = "";
                }
            }
        });
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
				<li class="breadcrumb-item active" aria-current="page">Donor Profile</li>
			</ol>
		</div>
	</div>
	<!-- //page details -->

	<!-- contact -->
	<div class="appointment py-5">
		<div class="py-xl-5 py-lg-3">
			<div class="w3ls-titles text-center mb-5">
				<h3 class="title">Donor Profile</h3>
				<span>
					<i class="fas fa-user-md"></i>
				</span>
			</div>
			<div class="d-flex">
				<div class="appoint-img">

				</div>
				<div class="contact-right-w3l appoint-form">
					<h5 class="title-w3 text-center mb-5">Detail of Your profile</h5>
					<form action="#" method="post">
						<?php
$uid=$_SESSION['bbdmsdid'];
$sql="SELECT * from  tblblooddonars where id=:uid
UNION
SELECT * from  tblbloodrequester where id=:uid";

$query = $dbh -> prepare($sql);
$query->bindParam(':uid',$uid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $row)
{               ?>
						<div class="form-group">
							<label for="recipient-name" class="col-form-label">Full Name</label>
							<input type="text" class="form-control" name="fullname" id="fullname_profile" value="<?php  echo $row->FullName;?>">
						</div>
						<script>
							function validateAfterTyping(fullname, errorElement) {
    var onlyLettersAndSpacesRegex = /^[A-Za-z ]+$/; // Only letters and spaces allowed
    var minLengthRegex = /^[A-Za-z ]{3,}$/; // At least 3 characters

    if (fullname.trim() === "") {
        errorElement.textContent = ""; // No error when empty
    } else if (!onlyLettersAndSpacesRegex.test(fullname)) {
        errorElement.textContent = "❌ Only letters and spaces are allowed.";
        errorElement.style.color = "red";
    } else if (!minLengthRegex.test(fullname)) {
        errorElement.textContent = "❌ Name must be at least 3 characters.";
        errorElement.style.color = "red";
    } else {
        errorElement.textContent = "";
        errorElement.style.color = "green";
    }
}
</script>
						<div class="form-group">
							<label for="recipient-phone" class="col-form-label">Mobile Number</label>
							<input type="text" class="form-control" name="mobileno" id="mobileno_profile" required="true" maxlength="10" pattern="[0-9]+" value="<?php  echo $row->MobileNumber;?>">
						</div>
						<div class="form-group">
							<label for="recipient-phone" class="col-form-label">Email Id <span style="color:red; font-size:10px;">(Can't be Change)</span></label>
							<input type="email" name="emailid" class="form-control" value="<?php  echo $row->EmailId;?>" readonly>
						</div>
						<div class="form-group">
							<label for="recipient-phone" class="col-form-label">Age</label>
							<input type="text" class="form-control" name="age" id="age_profile" required="" value="<?php  echo $row->Age;?>">
						</div>
						<div class="form-group">
							<label for="datepicker" class="col-form-label">Gender</label>
							<select required="" class="form-control" name="gender">
								<option value="<?php  echo $row->Gender;?>"><?php  echo $row->Gender;?></option>
<option value="Male">Male</option>
<option value="Female">Female</option>
							</select>
						</div>
						<div class="form-group">
    <label for="datepicker" class="col-form-label">Blood Group <span style="color:red; font-size:10px;">(Can't be Changed)</span></label>
    <input type="text" class="form-control" name="bloodgroup" value="<?php echo $row->BloodGroup; ?>" readonly>
</div>

						<div class="form-group">
							<label for="datepicker" class="col-form-label">Address</label>
							<input type="text" class="form-control" name="address" id="address" required="true" value="<?php  echo $row->Address;?>">
						</div>
						<div class="form-group">
							<label for="datepicker" class="col-form-label">Message</label>
							<textarea class="form-control" name="message" ><?php  echo $row->Message;?></textarea>
						</div>
						
						<?php $cnt=$cnt+1;}} ?>
						<input type="submit" value="Update" name="update" class="btn_apt">
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