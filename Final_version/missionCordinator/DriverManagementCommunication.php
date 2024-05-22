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

// Process message sending
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sendMessage'])) {
    $selected_driver = $_POST["driver"];
    $message = $_POST["message"];

    // Insert the message into the `communication` table
    $insertMessageQuery = "INSERT INTO communication (driver_id, message, timestamp) VALUES (?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($insertMessageQuery);
    $stmt->bind_param("is", $selected_driver, $message);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p class='success'>Message sent successfully to the driver.</p>";
    } else {
        echo "<p class='error'>Error sending message: " . $conn->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/CreateMission.css">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Driver Management Communication</title>
</head>

<body>
    <nav class="nav">
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

    <div class="container">
        <h1>Driver Management Communication</h1>
        <form method="post" action="DriverManagementCommunication.php">
            <div class="input-group">
                <label for="driver">Select Driver:</label>
                <select id="driver" name="driver">
                    <?php
                    // Fetch available drivers
                    $driverQuery = "SELECT driver_id, name FROM driver JOIN user ON driver.user_id = user.user_id WHERE driver_status = 'active'";
                    $driverResult = $conn->query($driverQuery);

                    if ($driverResult->num_rows > 0) {
                        while ($row = $driverResult->fetch_assoc()) {
                            echo "<option value='" . $row['driver_id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No available drivers</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="input-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" placeholder="Type your message here..." rows="4"></textarea>
            </div>
            <button type="submit" name="sendMessage">Send Message</button>
        </form>
    </div>
</body>

</html>