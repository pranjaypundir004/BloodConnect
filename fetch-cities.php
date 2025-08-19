<?php
include('db_connection.php'); // Ensure database connection

if (isset($_POST['state_id'])) {
    $state_id = $_POST['state_id'];
    $sql = "SELECT * FROM cities WHERE state_id = :state_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':state_id', $state_id, PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($results);
}
?>
