<?php
include('includes/config.php'); // Ensure the database connection is included

if(isset($_POST['state_id'])) {
    $state_id = $_POST['state_id'];

    $sql = "SELECT * FROM cities WHERE state_id = :state_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':state_id', $state_id, PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
}
?>
