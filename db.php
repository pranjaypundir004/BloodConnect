<?php
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Leave blank if no password is set
$dbname = "bbdms"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
