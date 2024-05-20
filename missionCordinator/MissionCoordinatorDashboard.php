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
$coordinatorName = $userName;

// Fetch total missions, active missions, and pending missions
$totalMissionsQuery = "SELECT COUNT(*) AS totalMissions FROM mission";
$activeMissionsQuery = "SELECT COUNT(*) AS activeMissions FROM mission WHERE status = 'in_progress'";
$pendingMissionsQuery = "SELECT COUNT(*) AS pendingMissions FROM mission WHERE status = 'assigned'";

$stmt = $conn->prepare($totalMissionsQuery);
$stmt->execute();
$totalMissionsResult = $stmt->get_result();
$totalMissionsRow = $totalMissionsResult->fetch_assoc();
$totalMissions = $totalMissionsRow['totalMissions'];

$stmt = $conn->prepare($activeMissionsQuery);
$stmt->execute();
$activeMissionsResult = $stmt->get_result();
$activeMissionsRow = $activeMissionsResult->fetch_assoc();
$activeMissions = $activeMissionsRow['activeMissions'];

$stmt = $conn->prepare($pendingMissionsQuery);
$stmt->execute();
$pendingMissionsResult = $stmt->get_result();
$pendingMissionsRow = $pendingMissionsResult->fetch_assoc();
$pendingMissions = $pendingMissionsRow['pendingMissions'];

// Fetch total drivers using the driver table
$totalDriversQuery = "SELECT COUNT(*) AS totalDrivers FROM driver";
$stmt = $conn->prepare($totalDriversQuery);
$stmt->execute();
$totalDriversResult = $stmt->get_result();
$totalDriversRow = $totalDriversResult->fetch_assoc();
$totalDrivers = $totalDriversRow['totalDrivers'];

// Fetch total vehicles from the vehicle table
$totalVehiclesQuery = "SELECT COUNT(*) AS totalVehicles FROM vehicle";
$stmt = $conn->prepare($totalVehiclesQuery);
$stmt->execute();
$totalVehiclesResult = $stmt->get_result();
$totalVehiclesRow = $totalVehiclesResult->fetch_assoc();
$totalVehicles = $totalVehiclesRow['totalVehicles'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mission Coordinator Dashboard</title>
    <link rel="stylesheet" href="../CSS/dashboards.css">
    <link rel="stylesheet" href="../CSS/nav.css">
</head>

<body>
    <nav>
        <ul>
            <li><a href="MissionCoordinatorDashboard.php">Home</a></li>
            <li><a href="CreateMission.php">Create Mission</a></li>
            <li><a href="AssignMissionDriver.php">Assign a New Mission to a Driver</a></li>
            <li><a href="DriverManagementCommunication.php">Communicate with a Driver</a></li>
            <li><a href="MissionTracker.php">Track a Mission</a></li>
            <li class="logout"><a href="../disconnect.php">Logout</a></li>
        </ul>
    </nav>
    <fieldset>
        <legend class="tilelegend"><?php echo htmlspecialchars($coordinatorName); ?></legend>
        <div class="container">
            <div class="tile">
                <h2>Mission Coordinator Dashboard</h2>
                <li><a href="MissionCoordinatorDashboard.php">Home</a></li>
                <li><a href="CreateMission.php">Create Mission</a></li>
                <li><a href="AssignMissionDriver.php">Assign a New Mission to a Driver</a></li>
                <li><a href="DriverManagementCommunication.php">Communicate with a Driver</a></li>
                <li><a href="MissionTracker.php">Track a Mission</a></li>
                <div class="tile">
                    <h2>Fleet Status</h2>
                    <a href="#" class="sub-tile">- Total Drivers: <?php echo $totalDrivers; ?></a>
                    <a href="#" class="sub-tile">- Total Vehicles: <?php echo $totalVehicles; ?></a>
                </div>
                <div class="tile">
                    <h2>Mission Status</h2>
                    <a href="#" class="sub-tile">- Total Missions: <?php echo $totalMissions; ?></a>
                    <a href="#" class="sub-tile">- Active: <?php echo $activeMissions; ?></a>
                    <a href="#" class="sub-tile">- Pending: <?php echo $pendingMissions; ?></a>
                </div>
            </div>
    </fieldset>
</body>

</html>