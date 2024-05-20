<?php
// Start the session to get the user's email and determine their user ID and position
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}

// Include the database connection
require_once '../db.php';

// Fetch the current user's ID and position based on email
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
    header('Location: ../login.php');
    exit();
}

// Ensure the user is a Fleet Admin
if ($userPosition != 'Fleet_Admin') {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}
$fleetManagerName = $userName;

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

// Fetch total number of pending maintenance tasks
$pendingMaintenanceQuery = "SELECT COUNT(*) AS pendingMaintenance FROM maintenance WHERE status = 'pending'";
$stmt = $conn->prepare($pendingMaintenanceQuery);
$stmt->execute();
$pendingMaintenanceResult = $stmt->get_result();
$pendingMaintenanceRow = $pendingMaintenanceResult->fetch_assoc();
$pendingMaintenance = $pendingMaintenanceRow['pendingMaintenance'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fleet Manager Dashboard</title>
    <link rel="stylesheet" href="/CSS/dashboards.css">
    <link rel="stylesheet" href="../CSS/nav.css">

</head>

<body>
    <nav>
        <ul>
            <li><a href="FleetManagerDashboard.php">Home</a></li>
            <li><a href="ManageDriverv2.php">Manage Driver</a></li>
            <li><a href="ManageVehiclesv2.php">Manage Vehicles</a></li>
            <li><a href="ScheduleMaintenancev2.php">Schedule Maintenance</a></li>
            <li><a href="FuelExpensesMonitoringv2.php">Fuel Expenses Monitoring</a></li>
            <li><a href="VehicleReportGenerationv2.php">Vehicle Report Generation</a></li>
            <li class="logout"><a href="../disconnect.php">Logout</a></li>
        </ul>
    </nav>
    <fieldset>
        <legend class="tilelegend"> <?php echo htmlspecialchars($fleetManagerName); ?>'s Dashboard</legend>
        <div class="container">
            <div class="tile">
                <h2>Fleet Manager Dashboard</h2>
                <a href="ManageVehiclesv2.php" class="sub-tile">- Manage Vehicles</a>
                <a href="ManageDriverv2.php" class="sub-tile">- Manage Driver</a>
                <a href="ScheduleMaintenancev2.php" class="sub-tile">- Schedule Maintenance</a>
                <a href="FuelExpensesMonitoringv2.php" class="sub-tile">- Fuel Expenses Monitoring</a>
                <a href="VehicleReportGenerationv2.php" class="sub-tile">- Vehicle Report Generation</a>
            </div>
            <div class="tile">
                <h2>Fleet Status</h2>
                <a href="#" class="sub-tile">- Total Vehicles: <?php echo $totalVehicles; ?></a>
                <a href="#" class="sub-tile">- Active: <?php echo $activeVehicles; ?></a>
                <a href="#" class="sub-tile">- Inactive: <?php echo $inactiveVehicles; ?></a>
            </div>
            <div class="tile">
                <h2>Maintenance Status</h2>
                <a href="#" class="sub-tile">- Pending Maintenance: <?php echo $pendingMaintenance; ?></a>
            </div>
        </div>
    </fieldset>
</body>

</html>