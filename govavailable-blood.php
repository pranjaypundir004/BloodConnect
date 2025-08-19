 <?php
error_reporting(0);
include('includes/config.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Blood Connect | Available Blood</title>

    <!-- Meta Tag Keywords -->
    <script>
        addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>

    <!-- Custom Files -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/fontawesome-all.css">

    <!-- Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700|Roboto+Condensed:400,700" rel="stylesheet">
</head>

<body>
    <!-- Include Header -->
    <?php include('includes/header.php'); ?>

     <!-- Page Banner -->
    <div class="inner-banner-w3ls">
        <div class="container">
            <h2 class="text-white text-center py-4"></h2>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb-agile">
        <div aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Available Blood</li>
            </ol>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-danger text-white text-center">
                        <h3>Available Blood Stock</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>Blood Bank</th>
                                    <th>Blood Type</th>
                                    <th>Quantity</th>
                                    <th>Request</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sql = "SELECT * FROM tbgovlbloodstock ORDER BY bloodBankName ASC";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);

                                if ($query->rowCount() > 0) {
                                    foreach ($results as $result) { ?>
                                        <tr>
                                            <td><?php echo htmlentities($result->bloodBankName); ?></td>
                                            <td><?php echo htmlentities($result->bloodType); ?></td>
                                            <td><?php echo htmlentities($result->quantity) . " Units"; ?></td>
                                            <td>
                                                <a href="request-blood.php?bank=<?php echo urlencode($result->bloodBankName); ?>&type=<?php echo urlencode($result->bloodType); ?>" 
                                                   class="btn btn-success btn-sm">Request</a>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="4" class="text-danger">No Blood Stock Available</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include('includes/footer.php'); ?>

</body>
</html>
