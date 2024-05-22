<?php
// Start session
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}

// Include the database connection
require_once '../db.php';

// Fetch the current user's position
$userPosition = $_SESSION['position'];

// Ensure the user is authorized to view and delete maintenance records
$authorizedPositions = ['MissionCoordinator', 'Fleet_Admin'];
if (!in_array($userPosition, $authorizedPositions)) {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}

// Handle maintenance deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['maintenance_id'])) {
    $maintenanceId = $_POST['maintenance_id'];

    // Delete maintenance record from database
    $deleteQuery = "DELETE FROM maintenance WHERE maintenance_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $maintenanceId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $successMessage = "Maintenance record deleted successfully.";
    } else {
        $errorMessage = "Error deleting maintenance record.";
    }

    // Close statement
    $stmt->close();
}

// Fetch maintenance records
$maintenanceQuery = "SELECT maintenance_id, vehicle_id, maintenance_date, description, status FROM maintenance";
$maintenanceResult = $conn->query($maintenanceQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Manage Maintenance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #f4f4f4;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: white;
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-button {
            background-color: #f44336;
            color: white;
        }

        .delete-button:hover {
            background-color: #e53935;
        }

    </style>
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

    <div class="container">
        <h2>Manage Maintenance</h2>
        <?php
        if (isset($successMessage)) {
            echo "<div class='message success'>{$successMessage}</div>";
        } elseif (isset($errorMessage)) {
            echo "<div class='message error'>{$errorMessage}</div>";
        }
        ?>
        <table>
            <thead>
                <tr>
                    <th>Maintenance ID</th>
                    <th>Vehicle ID</th>
                    <th>Maintenance Date</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($maintenanceResult->num_rows > 0) {
                    while ($row = $maintenanceResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['maintenance_id']}</td>
                                <td>{$row['vehicle_id']}</td>
                                <td>{$row['maintenance_date']}</td>
                                <td>{$row['description']}</td>
                                <td>{$row['status']}</td>
                                <td>
                                    <form action='{$_SERVER["PHP_SELF"]}' method='POST'>
                                        <input type='hidden' name='maintenance_id' value='{$row['maintenance_id']}'>
                                        <button type='submit' class='delete-button'>Delete</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No maintenance records found</td></tr>";
                }

                // Close the connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
