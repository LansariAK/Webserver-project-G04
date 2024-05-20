<?php
// db.php - Database connection setup

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fleet";

// Create and check the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>