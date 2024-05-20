<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start the session to get the user's email and determine their driver ID
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['email']) || $_SESSION['position'] !== 'Driver') {
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
    // Redirect to login if user not found
    header('../login.php');
    exit();
}

// Ensure the user is a Driver
if ($userPosition != 'Driver') {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}
$driverName = $userName;

// Fetch number of active missions for the driver (as vehicle_id isn't in missions directly)
$activeMissionsQuery = "SELECT COUNT(*) AS activeMissions 
FROM mission 
WHERE  status = 'in_progress'";

$stmt = $conn->prepare($activeMissionsQuery);

$stmt->execute();
$activeMissionsResult = $stmt->get_result();
$activeMissionsRow = $activeMissionsResult->fetch_assoc();
$activeMissions = $activeMissionsRow['activeMissions'];


// Fetch total number of missions
$totalMissionsQuery = "SELECT COUNT(*) AS TotalMissions FROM mission";

$stmt = $conn->prepare($totalMissionsQuery);
$stmt->execute();
$totalMissionsResult = $stmt->get_result();
$totalMissionsRow = $totalMissionsResult->fetch_assoc();
$totalMissions = $totalMissionsRow['TotalMissions'];


// Now, total vehicles are independent and should be counted separately
$totalVehiclesQuery = "SELECT COUNT(*) AS totalVehicles FROM vehicle";
$stmt = $conn->prepare($totalVehiclesQuery);
$stmt->execute();
$totalVehiclesResult = $stmt->get_result();
$totalVehiclesRow = $totalVehiclesResult->fetch_assoc();
$totalVehicles = $totalVehiclesRow['totalVehicles'];

$inactiveVehicles = $totalVehicles - $activeMissions;
$activeVehicles = $activeMissions;


// Fetch number of pending missions for the driver
$pendingMissionsQuery = "SELECT COUNT(*) AS pendingMissions 
FROM mission 
WHERE driver_id = ? AND status = 'assigned'";

$stmt = $conn->prepare($pendingMissionsQuery);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$pendingMissionsResult = $stmt->get_result();
$pendingMissionsRow = $pendingMissionsResult->fetch_assoc();
$pendingMissions = $pendingMissionsRow['pendingMissions'];
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/dashboards.css">
    <link rel="stylesheet" href="../CSS/nav.css">

    <title>Driver Dashboard</title>
</head>

<body>
    <nav>
        <ul>
            <li><a href="driver-dashboard.php">Home</a></li>
            <li><a href="driver-update.php">Update Profile</a></li>
            <li><a href="missions.php">Missions</a></li>

            <li><a href="events-report.php">Events Report</a></li>
            <li class="logout"><a href="../disconnect.php">Logout</a></li>
        </ul>
    </nav>

    <fieldset>
        <legend class="tilelegend"><?php echo htmlspecialchars($driverName); ?></legend>
        <div class="container">
            <div class="tile">
                <h2>Driver Dashboard</h2>
                <a href="driver-update.php" class="sub-tile">- Update Your Information</a>
                <a href="missions.php" class="sub-tile">- View Mission Assignments</a>
                <a href="events-report.php" class="sub-tile">- Report an Event</a>
            </div>
            <div class="tile">
                <h2>Fleet Status</h2>
                <a href="#" class="sub-tile">- Total Vehicles: <?php echo $totalVehicles; ?></a>
                <a href="#" class="sub-tile">- Active: <?php echo $activeVehicles; ?></a>
                <a href="#" class="sub-tile">- Inactive: <?php echo $inactiveVehicles; ?></a>
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