<?php
// Start session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}

// Include the database connection
require_once '../db.php';

// Fetch the current user's position
$userPosition = $_SESSION['position'];

// Ensure the user is a Fleet_Admin
if ($userPosition != 'Fleet_Admin') {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $vehicleId = $_POST['vehicleId'];
    $vehicleType = $_POST['vehicleType'];
    $vehicleModel = $_POST['vehicleModel'];
    $vehicleYear = $_POST['vehicleYear'];
    $vehicleStatus = $_POST['vehicleStatus'];

    // Prepare and execute SQL query
    $sql = "UPDATE vehicle SET vehicle_type = ?, model = ?, year = ?, status = ?, assigned_driver_id = ? WHERE vehicle_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissi", $vehicleType, $vehicleModel, $vehicleYear, $vehicleStatus, $assignedDriverId, $vehicleId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Vehicle updated successfully.";
    } else {
        echo "Error updating vehicle.";
    }

    // Close statement
    $stmt->close();
}

// Fetch vehicle data if vehicle ID is provided
$vehicle = null;
if (isset($_GET['vehicle_id'])) {
    $vehicleId = $_GET['vehicle_id'];
    $vehicleQuery = "SELECT * FROM vehicle WHERE vehicle_id = ?";
    $stmt = $conn->prepare($vehicleQuery);
    $stmt->bind_param("i", $vehicleId);
    $stmt->execute();
    $vehicleResult = $stmt->get_result();

    if ($vehicleResult->num_rows > 0) {
        $vehicle = $vehicleResult->fetch_assoc();
    }

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
    <title>Update Vehicle</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/nav.css">

</head>
<body>
    <nav class="nav">
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
    <div class="container">
        <form action="UpdateVehicle.php" method="POST" class="form-style">
            <h2>Update Vehicle</h2>
            <?php if ($vehicle): ?>
                <input type="hidden" name="vehicleId" value="<?php echo htmlspecialchars($vehicle['vehicle_id']); ?>">
                <div class="input-group">
                    <label for="vehicle-type">Vehicle Type:</label>
                    <select id="vehicle-type" name="vehicleType">
                        <option value="car" <?php echo ($vehicle['vehicle_type'] == 'car') ? 'selected' : ''; ?>>Car</option>
                        <option value="truck" <?php echo ($vehicle['vehicle_type'] == 'truck') ? 'selected' : ''; ?>>Truck</option>
                        <option value="van" <?php echo ($vehicle['vehicle_type'] == 'van') ? 'selected' : ''; ?>>Van</option>
                        <option value="bus" <?php echo ($vehicle['vehicle_type'] == 'bus') ? 'selected' : ''; ?>>Bus</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="vehicle-model">Vehicle Model:</label>
                    <input type="text" id="vehicle-model" name="vehicleModel" value="<?php echo htmlspecialchars($vehicle['model']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="vehicle-year">Vehicle Year:</label>
                    <input type="number" id="vehicle-year" name="vehicleYear" value="<?php echo htmlspecialchars($vehicle['year']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="vehicle-status">Vehicle Status:</label>
                    <select id="vehicle-status" name="vehicleStatus">
                        <option value="active" <?php echo ($vehicle['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($vehicle['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <button type="submit">Update Vehicle</button>

            <?php else: ?>
                <p></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
