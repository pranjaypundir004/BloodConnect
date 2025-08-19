<?php
include('includes/config.php');

if (isset($_POST['state_id'])) {
    $state_id = $_POST['state_id'];

    $sql = "SELECT * FROM cities WHERE state_id = :state_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':state_id', $state_id, PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    echo '<option value="">Select City</option>';
    foreach ($results as $row) {
        echo "<option value='$row->id'>$row->city_name</option>";
    }
}
?>
