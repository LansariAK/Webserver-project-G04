<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $driver_name = $_POST["driverName"];
    $update_message = $_POST["updateMessage"];
    $issue_resolution = $_POST["issueResolution"];

    echo "Driver Name: " . $driver_name . "<br>";
    echo "Update Message: " . $update_message . "<br>";
    echo "Issue Resolution: " . $issue_resolution;
} else {
    header("Location: DriverManagementCommunication.html");
    exit();
}
?>