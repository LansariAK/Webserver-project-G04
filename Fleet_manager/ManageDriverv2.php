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
    $driverName = $_POST['driverName'];
    $driverEmail = $_POST['driverEmail'];
    $driverPassword = password_hash($_POST['driverPassword'], PASSWORD_DEFAULT);

    // Prepare and execute SQL query
    $sql = "INSERT INTO user (name, email, password_hash, position) VALUES (?, ?, ?, 'Driver')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $driverName, $driverEmail, $driverPassword);

    if ($stmt->execute()) {
        echo "Driver added successfully.";
    } else {
        echo "Error adding driver.";
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
    <title>Manage Drivers</title>
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
    <!-- Manage Driver Section -->
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form-style">
            <h2>Manage Drivers</h2>
            <div class="input-group">
                <label for="driver-name">Driver Name:</label>
                <input type="text" id="driver-name" name="driverName" required>
            </div>
            <div class="input-group">
                <label for="driver-email">Driver Email:</label>
                <input type="email" id="driver-email" name="driverEmail" required>
            </div>
            <div class="input-group">
                <label for="driver-password">Driver Password:</label>
                <input type="password" id="driver-password" name="driverPassword" required>
            </div>
            <div class="input-group">
                <button type="submit">Add Driver</button>
            </div>
        </form>
    </div>
</body>

</html>