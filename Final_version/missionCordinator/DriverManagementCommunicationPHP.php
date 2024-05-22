<?php
require_once '../db.php'; // Database connection

// Start the session to get the user's email and verify their role
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch the current user's position based on email
$email = $_SESSION['email'];
$userQuery = "SELECT position FROM user WHERE email = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $userPosition = $userRow['position'];

    // Ensure the user is a Mission Coordinator
    if ($userPosition != 'MissionCoordinator') {
        echo "Access Denied: Your account does not have access to this page.";
        exit();
    }
} else {
    // Redirect to login if no such user is found
    header('Location: ../login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['sendMessage']) && !empty($_POST['driver']) && !empty($_POST['message'])) {
        $selected_driver = $_POST["driver"];
        $message = $_POST["message"];

        // Insert the message into the `communication` table
        $insertMessageQuery = "INSERT INTO communication (driver_id, message, timestamp) VALUES (?, ?, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($insertMessageQuery);
        $stmt->bind_param("is", $selected_driver, $message);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<p>Message sent successfully to the driver.</p>";
        } else {
            echo "<p>Error sending message: " . $conn->error . "</p>";
        }
        $stmt->close();
    }
} else {
    header("Location: DriverManagementCommunication.php");
    exit();
}
?>