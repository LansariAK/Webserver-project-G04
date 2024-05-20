<?php
// Start session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['email']) || $_SESSION['position'] !== 'Fleet_Admin') {
    header('Location: ../login.php');
    exit();
}

// Include the database connection
require_once '../db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $vehicleId = $_POST['vehicleId'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $maintenanceStatus = $_POST['maintenanceStatus'];
    $fuelType = $_POST['fuelType'];

    // Prepare and execute SQL query
    $sql = "INSERT INTO vehiclereport (vehicle_id, start_date, end_date, maintenance_status, fuel_type) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $vehicleId, $startDate, $endDate, $maintenanceStatus, $fuelType);

    if ($stmt->execute()) {
        echo "Vehicle report generated successfully.";
    } else {
        echo "Error generating vehicle report.";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Report Generation</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/nav.css">
</head>

<body>
    <nav class="nav">
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
    <!-- Vehicle Report Generation Form -->
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form-style">
            <h2>Vehicle Report Generation</h2>
            <div class="input-group">
                <label for="vehicle-id">Vehicle ID:</label>
                <input type="text" id="vehicle-id" name="vehicleId" required>
            </div>
            <div class="input-group">
                <label for="start-date">Start Date:</label>
                <input type="date" id="start-date" name="startDate" required>
            </div>
            <div class="input-group">
                <label for="end-date">End Date:</label>
                <input type="date" id="end-date" name="endDate" required>
            </div>
            <div class="input-group">
                <label for="maintenance-status">Maintenance Status:</label>
                <select id="maintenance-status" name="maintenanceStatus">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="all">All</option>
                </select>
            </div>
            <div class="input-group">
                <label for="fuel-type">Fuel Type:</label>
                <select id="fuel-type" name="fuelType">
                    <option value="diesel">Diesel</option>
                    <option value="gasoline">Gasoline</option>
                    <option value="electric">Electric</option>
                    <option value="all">All</option>
                </select>
            </div>
            <div class="input-group">
                <button type="submit">Generate Report</button>
            </div>
        </form>
    </div>
</body>

</html>