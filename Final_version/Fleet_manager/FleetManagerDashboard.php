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
    echo "<a href='index.php'><<-- Go back to the login.</a>";
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

// Calculate active and inactive vehicles
$activeVehiclesQuery = "SELECT COUNT(*) AS activeVehicles FROM vehicle WHERE status = 'active'";
$stmt = $conn->prepare($activeVehiclesQuery);
$stmt->execute();
$activeVehiclesResult = $stmt->get_result();
$activeVehiclesRow = $activeVehiclesResult->fetch_assoc();
$activeVehicles = $activeVehiclesRow['activeVehicles'];

$inactiveVehicles = $totalVehicles - $activeVehicles;

// Fetch total number of pending maintenance tasks
$pendingMaintenanceQuery = "SELECT COUNT(*) AS pendingMaintenance FROM maintenance WHERE status = 'pending'";
$stmt = $conn->prepare($pendingMaintenanceQuery);
$stmt->execute();
$pendingMaintenanceResult = $stmt->get_result();
$pendingMaintenanceRow = $pendingMaintenanceResult->fetch_assoc();
$pendingMaintenance = $pendingMaintenanceRow['pendingMaintenance'];

// Fetch total number of event reports
$totalEventReportsQuery = "SELECT COUNT(*) AS totalEventReports FROM eventreport";
$stmt = $conn->prepare($totalEventReportsQuery);
$stmt->execute();
$totalEventReportsResult = $stmt->get_result();
$totalEventReportsRow = $totalEventReportsResult->fetch_assoc();
$totalEventReports = $totalEventReportsRow['totalEventReports'];



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
        <li><a href="Users.php">Manage Users</a></li>
        <li><a href="Drivers.php">Manage Drivers</a></li>
        <li><a href="Vehicules.php">Manage Vehicles</a></li>
        <li><a href="ManageEventReports.php">Manage Event Reports</a></li>
        <li><a href="ManageMaintenance.php">Manage Maintenance</a></li>
        <li><a href="ScheduleMaintenance.php">Schedule Maintenance</a></li>
        <li class="logout"><a href="/disconnect.php">Logout</a></li>
    </ul>
</nav>
<fieldset>
    <legend class="tilelegend"><?php echo htmlspecialchars($fleetManagerName); ?>'s Dashboard</legend>
    <div class="container">
        <div class="tile">
            <h2>Fleet Manager Dashboard</h2>
            <a href="Users.php" class="sub-tile">- Manage Users</a>
            <a href="Drivers.php" class="sub-tile">- Manage Drivers</a>
            <a href="Vehicules.php" class="sub-tile">- Manage Vehicles</a>
            <a href="ManageEventReports.php" class="sub-tile">- Manage Event Reports</a>
            <a href="ManageMaintenance.php" class="sub-tile">- Manage Maintenance</a>
            <a href="ScheduleMaintenance.php" class="sub-tile">- Schedule Maintenance</a>
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
        <div class="tile">
            <h2>Event Reports</h2>
            <a href="#" class="sub-tile">- Total Event Reports: <?php echo $totalEventReports; ?></a>
        </div>
       
    </div>
</fieldset>
</body>

</html>
