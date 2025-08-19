<?php 
session_start();
include('includes/config.php');

$error = "";
$msg = "";

if(strlen($_SESSION['alogin']) == 0) {    
    header('location:index.php');
} else {
    if(isset($_REQUEST['del'])) {
        $did = intval($_GET['del']);
        $sql = "DELETE FROM tblblooddonars WHERE id=:did";
        $query = $dbh->prepare($sql);
        $query->bindParam(':did', $did, PDO::PARAM_INT);
        $query->execute();
        $msg = "Record deleted successfully";
    }

    // Fetch filter values
    $filter_bloodgroup = $_POST['bloodgroup'] ?? "";
    $filter_gender = $_POST['gender'] ?? "";
    $filter_location = $_POST['location'] ?? "";
    $filter_search = $_POST['search'] ?? "";

    // Build query with filters
    $sql = "SELECT * FROM tblblooddonars WHERE 1=1";

    if (!empty($filter_bloodgroup)) {
        $sql .= " AND BloodGroup=:bloodgroup";
    }
    if (!empty($filter_gender)) {
        $sql .= " AND Gender=:gender";
    }
    if (!empty($filter_location)) {
        $sql .= " AND Address LIKE :location";
    }
    if (!empty($filter_search)) {
        $sql .= " AND (FullName LIKE :search OR EmailId LIKE :search)";
    }

    $query = $dbh->prepare($sql);

    if (!empty($filter_bloodgroup)) {
        $query->bindParam(':bloodgroup', $filter_bloodgroup, PDO::PARAM_STR);
    }
    if (!empty($filter_gender)) {
        $query->bindParam(':gender', $filter_gender, PDO::PARAM_STR);
    }
    if (!empty($filter_location)) {
        $locationParam = "%" . $filter_location . "%";
        $query->bindParam(':location', $locationParam, PDO::PARAM_STR);
    }
    if (!empty($filter_search)) {
        $searchParam = "%" . $filter_search . "%";
        $query->bindParam(':search', $searchParam, PDO::PARAM_STR);
    }

    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Blood Connect | Donors List</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <h2 class="page-title">Donors List</h2>
                <a href="download-records.php" class="btn btn-info">Download Donors List</a>

                <div class="panel-body">
                    <?php if (!empty($error)) { ?>
                        <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?></div>
                    <?php } else if (!empty($msg)) { ?>
                        <div class="succWrap"><strong>SUCCESS:</strong> <?php echo htmlentities($msg); ?></div>
                    <?php } ?>

                    <div class="panel panel-default">
                        <div class="panel-heading">Requesters Info</div>
                        <div class="panel-body">
                            <form method="POST" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select name="bloodgroup" class="form-control">
                                            <option value="">Select Blood Group</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="gender" class="form-control">
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="location" class="form-control" placeholder="Enter Location">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control" placeholder="Search by Name or Email">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">Filter</button>
                                <a href="requester-list.php" class="btn btn-danger mt-2">Clear</a>
                            </form>

                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Mobile No</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <th>Age</th>
                                        <th>Blood Group</th>
                                        <th>Location</th>
                                        <th>Message</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $cnt = 1;
                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) { ?>
                                            <tr>
                                                <td><?php echo htmlentities($cnt); ?></td>
                                                <td><?php echo htmlentities($result->FullName ?? ''); ?></td>
                                                <td><?php echo htmlentities($result->MobileNumber ?? ''); ?></td>
                                                <td><?php echo htmlentities($result->EmailId ?? ''); ?></td>
                                                <td><?php echo htmlentities($result->Gender ?? ''); ?></td>
                                                <td><?php echo htmlentities($result->Age ?? ''); ?></td>
                                                <td><?php echo htmlentities($result->BloodGroup ?? ''); ?></td>
                                                <td><?php echo htmlentities($result->Address ?? ''); ?></td>
                                                <td><?php echo htmlentities($result->Message ?? ''); ?></td>
                                                <td>
                                                    <a href="requester-list.php?del=<?php echo htmlentities($result->id); ?>"
                                                       onclick="return confirm('Do you really want to delete this record?')"
                                                       class="btn btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                        <?php $cnt++; }
                                    } else { ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-danger font-weight-bold">
                                                No data found with this filter.
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>
