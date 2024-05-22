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
$vehicleId = $conn->real_escape_string($_POST['vehicleId']);
$expenseType = $conn->real_escape_string($_POST['expenseType']);
$amount = $conn->real_escape_string($_POST['amount']);
$fuelType = $conn->real_escape_string($_POST['fuelType']);
$expenseDate = $conn->real_escape_string($_POST['expenseDate']);

// Construct the insert query
$insertQuery = "INSERT INTO fuelexpenses (vehicle_id, expense_type, amount, date, fuel_type) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("issss", $vehicleId, $expenseType, $amount, $expenseDate, $fuelType);

// Execute the query
if ($stmt->execute()) {
    header('Location: manage-fuelexpenses.php?success=1');
} else {
    header('Location: manage-fuelexpenses.php?error=1');
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>