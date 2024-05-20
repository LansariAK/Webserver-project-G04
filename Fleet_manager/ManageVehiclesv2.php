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
    $vehicleType = $_POST['vehicleType'];
    $vehicleModel = $_POST['vehicleModel'];
    $vehicleYear = $_POST['vehicleYear'];

    // Prepare and execute SQL query
    $sql = "INSERT INTO vehicle (vehicle_type, model, year) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $vehicleType, $vehicleModel, $vehicleYear);

    if ($stmt->execute()) {
        echo "Vehicle added successfully.";
    } else {
        echo "Error adding vehicle.";
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
    <title>Manage Vehicles</title>
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
    <!-- Manage Vehicles Section -->
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form-style">
            <h2>Manage Vehicles</h2>
            <div class="input-group">
                <label for="vehicle-type">Vehicle Type:</label>
                <select id="vehicle-type" name="vehicleType">
                    <option value="car">Car</option>
                    <option value="truck">Truck</option>
                    <option value="van">Van</option>
                    <option value="bus">Bus</option>
                </select>
            </div>
            <div class="input-group">
                <label for="vehicle-model">Vehicle Model:</label>
                <input type="text" id="vehicle-model" name="vehicleModel" required>
            </div>
            <div class="input-group">
                <label for="vehicle-year">Vehicle Year:</label>
                <input type="number" id="vehicle-year" name="vehicleYear" min="1900" max="2099" step="1" required>
            </div>
            <div class="input-group">
                <button type="submit">Add Vehicle</button>
            </div>
        </form>
    </div>
</body>

</html>