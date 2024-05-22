<?php
// Start the session to get the user's email and determine their mission coordinator ID
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
$userQuery = "SELECT user_id, name, position FROM user WHERE email = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $user_id = $userRow['user_id'];
    $userName = $userRow['name'];
    $userPosition = $userRow['position'];
} else {
    // Redirect to login if the user is not found
    header('Location: ../login.php');
    exit();
}

// Ensure the user is a Mission Coordinator
if ($userPosition != 'MissionCoordinator') {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $mission_name = $_POST['mission_name'];
    $description = $_POST['description'];
    $status = "pending";  // Automatically set status to "pending"
    $progress = 0;  // Default progress is 0 for a new mission

    // Prepare an INSERT statement to add the new mission
    $insertQuery = "INSERT INTO mission (mission_name, description, status, progress) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sssi", $mission_name, $description, $status, $progress);

    if ($stmt->execute()) {
        echo "<p class='success'>Mission created successfully!</p>";
    } else {
        echo "<p class='error'>Error creating mission: " . $conn->error . "</p>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Mission</title>
    <link rel="stylesheet" href="../CSS/nav.css">
    <link rel="stylesheet" href="../CSS/CreateMission.css">
</head>

<body>
    <div class="nav">
        <nav>
            <ul>
                <li><a href="MissionCoordinatorDashboard.php">Home</a></li>
                <li><a href="CreateMission.php">Create Mission</a></li>
                <li><a href="AssignMissionDriver.php">Assign a New Mission to a Driver</a></li>
                <li><a href="DriverManagementCommunication.php">Communicate with a Driver</a></li>
                <li><a href="MissionTracker.php">Track a Mission</a></li>
                <li><a href="ViewAllMissions.php">View All Missions</a></li>
                <li class="logout"><a href="../disconnect.php">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="container">
        <h1>Create a New Mission</h1>
        <form action="CreateMission.php" method="post">
            <div class="input-group">
                <label for="mission_name">Mission Name:</label>
                <input type="text" id="mission_name" name="mission_name" required>
            </div>

            <div class="input-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" cols="50"></textarea>
            </div>

            <button type="submit" name="submit">Create Mission</button>
        </form>
    </div>
</body>

</html>