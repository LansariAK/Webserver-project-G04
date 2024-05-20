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
    $maintenanceDate = $_POST['maintenanceDate'];
    $maintenanceDescription = $_POST['maintenanceDescription'];

    // Prepare and execute SQL query
    $sql = "INSERT INTO maintenance (vehicle_id, maintenance_date, description, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $vehicleId, $maintenanceDate, $maintenanceDescription);

    if ($stmt->execute()) {
        echo "Maintenance scheduled successfully.";
    } else {
        echo "Error scheduling maintenance.";
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
    <title>Schedule Maintenance</title>
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
    <!-- Schedule Maintenance Section -->
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form-style">
            <h2>Schedule Maintenance</h2>
            <div class="input-group">
                <label for="vehicle-id">Vehicle ID:</label>
                <input type="text" id="vehicle-id" name="vehicleId" required>
            </div>
            <div class="input-group">
                <label for="maintenance-date">Maintenance Date:</label>
                <input type="date" id="maintenance-date" name="maintenanceDate" required>
            </div>
            <div class="input-group">
                <label for="maintenance-description">Description:</label>
                <textarea id="maintenance-description" name="maintenanceDescription" required></textarea>
            </div>
            <div class="input-group">
                <button type="submit">Submit</button>
            </div>
        </form>
    </div>
</body>

</html>