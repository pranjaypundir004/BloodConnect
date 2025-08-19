<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $state_id = $_POST['state'];
    $city_id = $_POST['city'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $dbh->prepare("INSERT INTO users (fullname, email, state_id, city_id, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$fullname, $email, $state_id, $city_id, $password]);

    if ($stmt) {
        echo "Registration successful!";
    } else {
        echo "Error in registration.";
    }
}
?>
