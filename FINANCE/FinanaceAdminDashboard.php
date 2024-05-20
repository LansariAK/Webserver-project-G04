<?php

// Start the session to get the user's email and determine their finance admin ID
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

// Ensure the user is a Finance Admin
if ($userPosition != 'FinanceAdmin') {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}
$adminName = $userName;

// Fetch the number of approved and pending expense reports

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

// Fetch total vehicles, active vehicles, and inactive vehicles
$totalVehiclesQuery = "SELECT COUNT(*) AS totalVehicles FROM vehicle";
$activeVehiclesQuery = "SELECT COUNT(*) AS activeVehicles FROM mission WHERE status = 'in_progress'";

$stmt = $conn->prepare($totalVehiclesQuery);
$stmt->execute();
$totalVehiclesResult = $stmt->get_result();
$totalVehiclesRow = $totalVehiclesResult->fetch_assoc();
$totalVehicles = $totalVehiclesRow['totalVehicles'];

$stmt = $conn->prepare($activeVehiclesQuery);
$stmt->execute();
$activeVehiclesResult = $stmt->get_result();
$activeVehiclesRow = $activeVehiclesResult->fetch_assoc();
$activeVehicles = $activeVehiclesRow['activeVehicles'];

$inactiveVehicles = $totalVehicles - $activeVehicles;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/dashboards.css">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Finance Administrator Dashboard</title>
</head>

<body>
    <nav>
        <ul>
            <li><a href="FinanaceAdminDashboard.php">Home</a></li>
            <li><a href="BudgetAllocation.html">Allocate a Budget to a Mission</a></li>
            <li><a href="VehExpensesManag.html">Manage Vehicle Expenses</a></li>
            <li><a href="FinancialReport.html">Generate a Report</a></li>
            <li class="logout"><a href="/disconnect.php">Logout</a></li>
        </ul>
    </nav>
    <fieldset>
        <legend class="tilelegend"><?php echo htmlspecialchars($adminName); ?></legend>
        <div class="container">
            <div class="tile">
                <h2>Finance Administrator Dashboard</h2>
                <a href="BudgetAllocation.html" class="sub-tile">- Allocate a Budget to a Mission</a>
                <a href="VehExpensesManag.html" class="sub-tile">- Manage Vehicle Expenses</a>
                <a href="FinancialReport.html" class="sub-tile">- Generate a Report</a>
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
            <!-- <div class="tile">
                <h2>Expense Report Status</h2>
                <a href="#" class="sub-tile">- Approved Reports: <//?php echo $approvedReports; ?></a>
                <a href="#" class="sub-tile">- Pending Reports: <//?php echo $pendingReports; ?></a>
                <a href="#" class="sub-tile">- Total Expenses: $<//?php echo number_format($totalExpense, 2); ?></a>
            </div> -->
        </div>
    </fieldset>
</body>

</html>