<?php
// db.php - Database connection setup

$servername = "sql203.infinityfree.com";
$username = "if0_36048559";
$password = "U4PfTcvvAtYetE";
$dbname = "if0_36048559_fleet_management";

// Create and check the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>