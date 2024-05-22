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
    $maintenanceDate = $_POST['maintenanceDate'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Insert maintenance data
    $sql = "INSERT INTO maintenance (vehicle_id, maintenance_date, description, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $vehicleId, $maintenanceDate, $description, $status);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $successMessage = "Maintenance scheduled successfully.";
    } else {
        $errorMessage = "Error scheduling maintenance.";
    }

    // Close statement
    $stmt->close();
    // Close connection
    $conn->close();
}
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

    <!-- Schedule Maintenance Section -->
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form-style">
            <h2>Schedule Maintenance</h2>
            <?php if (isset($successMessage)) { ?>
                <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php } elseif (isset($errorMessage)) { ?>
                <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php } ?>
            <div class="input-group">
                <label for="vehicle-id">Vehicle ID:</label>
                <input type="number" id="vehicle-id" name="vehicleId" required>
            </div>
            <div class="input-group">
                <label for="maintenance-date">Maintenance Date:</label>
                <input type="date" id="maintenance-date" name="maintenanceDate" required>
            </div>
            <div class="input-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div class="input-group">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="input-group">
                <button type="submit">Schedule Maintenance</button>
            </div>
        </form>
    </div>
</body>
</html>
