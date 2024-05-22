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
    $vehicleType = $_POST['vehicleType'];
    $vehicleModel = $_POST['vehicleModel'];
    $vehicleYear = $_POST['vehicleYear'];

    // Prepare and execute SQL query
    $sql = "INSERT INTO vehicle (vehicle_type, model, year) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $vehicleType, $vehicleModel, $vehicleYear);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
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
    <div class="nav">
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
    </div>
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
