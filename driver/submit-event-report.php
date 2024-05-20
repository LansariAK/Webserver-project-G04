<?php
// Start the session
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}

// Include the database connection
require_once '../db.php';

// Fetch the current user's ID based on email
$email = $_SESSION['email'];
$userQuery = "SELECT user_id FROM user WHERE email = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $user_id = $userRow['user_id'];
} else {
    // Redirect to login if user not found
    header('Location: ../login.php');
    exit();
}

// Retrieve and sanitize form data
$eventType = $conn->real_escape_string($_POST['eventType']);
$eventDescription = $conn->real_escape_string($_POST['eventDescription']);
$eventDate = $conn->real_escape_string($_POST['eventDate']);

// Construct the insert query
$insertQuery = "INSERT INTO eventreport (event_type, description, date, user_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("sssi", $eventType, $eventDescription, $eventDate, $user_id);

// Execute the query
if ($stmt->execute()) {
    header('Location: events-report.php?success=1');
} else {
    header('Location: events-report.php?error=1');
}

// Close the statement and connection
$stmt->close();
$conn->close();