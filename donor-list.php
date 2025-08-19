<?php
error_reporting(0);
include('includes/config.php');
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Blood Connect | Blood Donor List</title>
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
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/fontawesome-all.css">
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
    </div>

    <!-- Page Details -->
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
            
	<!-- //banner bottom -->
	<!-- blog -->
	<div class="blog-w3ls py-5" id="blog">
		<div class="container py-xl-5 py-lg-3">
			<div class="w3ls-titles text-center mb-5">
				<h3 class="title text-white">Some of the Donor</h3>
				<span>
					<i class="fas fa-user-md text-white"></i>
				</span>
			</div>
			<div class="row package-grids mt-5">
				<?php 
$status=1;
$sql = "SELECT * from tblblooddonars where status=:status order by rand() limit 6";
$query = $dbh -> prepare($sql);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{ ?>
				<div class="col-md-4 pricing" style="margin-top:2%;">
					<div class="price-top">
						<img src="images/blood-donor.jpg" alt="" class="img-fluid" />
						<h3><?php echo htmlentities($result->FullName);?></h3>
					</div>
					<div class="price-bottom p-4">
						<h4 class="text-dark mb-3">Gender: <?php echo htmlentities($result->Gender);?></h4>
						<p class="card-text"><b>Blood Group :</b> <?php echo htmlentities($result->BloodGroup);?></p>
						<a class="btn btn-primary" style="color:#fff" href="contact-blood.php?cid=<?php echo $result->id;?>">Request</a>
					</div>
				</div><?php }} ?>
			</div>
		</div>
	</div>
	<!-- //blog -->

    <!-- Blood Banks Info with Available Blood Types Button -->
    <div class="w3ls-titles text-center mb-5 mt-5">
        <h3 class="title">Some Blood Banks in Dehradun</h3>
        <span>
            <i class="fas fa-hospital"></i>
        </span>
        <p class="mt-2">Find the nearest blood bank!</p>
    </div>

    <div class="row">
        <!-- Blood Bank 1 -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">IMA Blood Bank of Dehradun</h5>
                    <p class="card-text">Location: Dehradun, India</p><h2>
                    <p class="card-text">Contact: 0135-2742222</p>
                    <a href="imaavailable-blood.php?bank=IMA+Blood+Bank+of+Uttarakhand" class="btn btn-info">Available Blood</a>
                    <a href="https://www.google.com/maps/search/IMA+Blood+Bank+of+Uttarakhand%2C+Dehradun%2C+India" class="btn btn-primary" target="_blank">View on Google Maps</a>
                </div>
            </div>
        </div>

        <!-- Blood Bank 2 -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Government Blood Bank</h5>
                    <p class="card-text">Location: Dehradun, India</p><h2>
                    <p class="card-text">Contact: 0135-2726378</p>
                    <a href="govavailable-blood.php" class="btn btn-info">Available Blood</a>
                    <a href="https://www.google.com/maps/search/Govt.+Blood+Bank%2C+Dehradun%2C+India" class="btn btn-primary" target="_blank">View on Google Maps</a>
                </div>
            </div>
        </div>

        <!-- Blood Bank 3 -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Max Blood Bank</h5>
                    <p class="card-text">Location: Dehradun, India</p><h2>
                    <p class="card-text">Contact: 0135-2771203</p>
                    <a href="available-blood.php?bank=Max+Blood+Bank" class="btn btn-info">Available Blood</a>
                    <a href="https://www.google.com/maps/search/Max+Blood+Bank%2C+Dehradun%2C+India" class="btn btn-primary" target="_blank">View on Google Maps</a>
                </div>
            </div>
        </div>
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
