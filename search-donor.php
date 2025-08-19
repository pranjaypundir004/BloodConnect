<?php
include('includes/config.php');
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Blood Connect | Search Blood Donor</title>
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
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
    <!-- //Web-Fonts -->

    <style>
        /* Styling the individual donor cards */
        .donor-card {
            width: 100%;
            max-width: 350px;
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            background-color: #fff; 
            overflow: hidden; 
            height: 100%;
        }

        /* Adjust image size inside the donor card */
        .donor-card img {
            width: 100%; 
            max-height: 250px; 
            object-fit: cover; 
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .donor-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
        }

        /* Responsive handling of cards for smaller screens */
        @media (max-width: 767px) {
            .donor-card {
                max-width: 100%;
            }
        }

        .search-form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .search-form .form-group {
            margin-bottom: 15px;
        }

        .search-form .form-group label {
            font-weight: bold;
            color: #333;
        }

        .search-form .form-control {
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
            font-size: 14px;
        }

        .search-form .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto 0;
            width: 100%;
        }

        .search-form .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
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
    <!-- //banner 2 -->

    <!-- page details -->
    <div class="breadcrumb-agile">
        <div aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Blood Donor List</li>
            </ol>
        </div>
    </div>
    <!-- //page details -->

    <!-- contact -->
    <section class="agileits-contact py-5">
        <div class="container py-xl-5 py-lg-3">
            <div class="w3ls-titles text-center mb-5">
                <h3 class="title">Search Blood Donor</h3>
                <span><i class="fas fa-user-md"></i></span>
            </div>
            <div class="search-form">
                <form name="donar" method="post">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="form-group">
                                <label>Blood Group<span style="color:red">*</span></label>
                                <select name="bloodgroup" class="form-control" required>
                                    <?php
                                    $sql = "SELECT * from tblbloodgroup";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    $cnt = 1;
                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) {  
                                    ?>
                                        <option value="<?php echo htmlentities($result->BloodGroup);?>">
                                            <?php echo htmlentities($result->BloodGroup);?>
                                        </option>
                                    <?php }} ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
    <div class="form-group">
        <label>Gender</label>
        <select name="gender" class="form-control">
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            
        </select>
    </div>
</div>


                       
                      <div class="col-lg-4 mb-4">
    <div class="form-group">
<label>State</label>
<select name="state" id="state" class="form-control">
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
    <select name="location" id="city" class="form-control"> <!-- Changed name from "address" to "location" -->
        <option value="">Select City</option>
    </select>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#state').on('change', function() { // Detect state selection
        var stateId = $(this).val();
        if (stateId) {
            $.ajax({
                url: "fetch_cities.php", // File to fetch cities
                method: "POST",
                data: { state_id: stateId },
                dataType: "json",
                success: function(data) {
                    $('#city').html('<option value="">Select City</option>'); // Reset city dropdown
                    $.each(data, function(index, value) {
                        $('#city').append('<option value="' + value.name + '">' + value.name + '</option>'); // Append options
                    });
                },
                error: function(xhr, status, error) {
                    console.log("Error: " + error); // Log errors for debugging
                }
            });
        } else {
            $('#city').html('<option value="">Select City</option>'); // Reset if no state selected
        }
    });
});
</script>



                        <div class="col-lg-4 mb-4">
                            <div class="form-group">
                                <button type="submit" name="sub" class="btn btn-primary" style="cursor:pointer">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="agileits-contact py-5">
        <div class="container py-xl-5 py-lg-3">
            <?php
            if (isset($_POST['sub'])) {
    $status = 1;
    $bloodgroup = $_POST['bloodgroup'];
    $location = $_POST['location'];
    $gender = $_POST['gender'];

    // Start building the SQL query dynamically
    $sql = "SELECT * FROM tblblooddonars WHERE status=:status AND BloodGroup=:bloodgroup";
    
    if (!empty($location)) {
        $sql .= " AND Address=:location"; // Include location filter if provided
    }

    if (!empty($gender)) {
        $sql .= " AND Gender=:gender"; // Include gender filter if selected
    }

    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':bloodgroup', $bloodgroup, PDO::PARAM_STR);
    
    if (!empty($location)) {
        $query->bindParam(':location', $location, PDO::PARAM_STR);
    }

    if (!empty($gender)) {
        $query->bindParam(':gender', $gender, PDO::PARAM_STR);
    }

    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    
    if ($query->rowCount() > 0) { ?>
        <div class="w3ls-titles text-center mb-5">
            <h3 class="title">Search Results</h3>
            <span><i class="fas fa-user-md"></i></span>
        </div>
        <div class="donor-container">
            <?php foreach ($results as $result) { ?>
                <div class="col-md-4">
                    <div class="donor-card">
                        <div class="price-top">
                            <a href="single.html">
                                <img src="images/blood-donor.jpg" alt="Blood Donor" class="img-fluid" />
                            </a>
                            <h3><?php echo htmlentities($result->FullName); ?></h3>
                        </div>
                        <div class="price-bottom p-4">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Gender</th>
                                        <td><?php echo htmlentities($result->Gender); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Blood Group</td>
                                        <td><?php echo htmlentities($result->BloodGroup); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Mobile No.</td>
                                        <td><?php echo htmlentities($result->MobileNumber); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Email ID</td>
                                        <td><?php echo htmlentities($result->EmailId); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Age</td>
                                        <td><?php echo htmlentities($result->Age); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Address</td>
                                        <td><?php echo htmlentities($result->Address); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Message</td>
                                        <td><?php echo htmlentities($result->Message); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <a class="btn btn-primary" href="contact-blood.php?cid=<?php echo $result->id; ?>">Request</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else {
        echo "<div class='alert alert-warning text-center'>No Record Found</div>";
    }
}

            ?>
        </div>
    </section>
    <!-- //contact -->

    <?php include('includes/footer.php');?>

    <!-- Js files -->
    <!-- JavaScript -->
    <script src="js/jquery-2.2.3.min.js"></script>
    <!-- Default-JavaScript-File -->
    <script src="js/bootstrap.js"></script>
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

    <!-- Necessary-JavaScript-File-For-Bootstrap -->
</body>
</html>