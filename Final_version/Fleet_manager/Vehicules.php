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

// Ensure the user is authorized to view and archive vehicles
$authorizedPositions = ['MissionCoordinator', 'Fleet_Admin'];
if (!in_array($userPosition, $authorizedPositions)) {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}

// Handle form submission for archiving vehicle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vehicle_id'])) {
    $vehicleId = $_POST['vehicle_id'];

    // Update vehicle status to inactive
    $updateQuery = "UPDATE vehicle SET status = 'inactive' WHERE vehicle_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $vehicleId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $successMessage = "Vehicle archived successfully.";
    } else {
        $errorMessage = "Error archiving vehicle.";
    }

    // Close statement
    $stmt->close();
}

// Fetch active vehicles
$activeVehiclesQuery = "SELECT * FROM vehicle WHERE status = 'active'";
$activeVehiclesResult = $conn->query($activeVehiclesQuery);

// Fetch archived vehicles
$archivedVehiclesQuery = "SELECT * FROM vehicle WHERE status = 'inactive'";
$archivedVehiclesResult = $conn->query($archivedVehiclesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Vehicles</title>
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

        .update-button {
            background-color: #4CAF50;
            color: white;
        }

        .archive-button {
            background-color: #f44336;
            color: white;
        }

        .update-button:hover {
            background-color: #45a049;
        }

        .archive-button:hover {
            background-color: #e53935;
        }

        .add-user-button {
            margin-bottom: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .add-user-button:hover {
            background-color: #45a049;
        }
    </style>
    </head>
<body>
    <div class="nav">
        <nav>
            <ul>
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
            </ul>
        </nav>
    </div>

    <div class="container">
        <h2>Active Vehicles</h2>
        <a href="AddVehicules.php" class="add-user-button">Add Vehicle</a>
        <?php if (isset($successMessage)) : ?>
            <div class="message success"><?php echo $successMessage; ?></div>
        <?php elseif (isset($errorMessage)) : ?>
            <div class="message error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>Vehicle ID</th>
                    <th>Vehicle Type</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Assigned Driver ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($activeVehiclesResult->num_rows > 0) {
                    while ($row = $activeVehiclesResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['vehicle_id']}</td>
                                <td>{$row['vehicle_type']}</td>
                                <td>{$row['model']}</td>
                                <td>{$row['year']}</td>
                                <td>{$row['status']}</td>
                                <td>{$row['assigned_driver_id']}</td>
                                <td class='action-buttons'>
    <form action='UpdateVehicle.php' method='GET' style='display:inline-block;'>
        <input type='hidden' name='vehicle_id' value='{$row['vehicle_id']}'>
        <button type='submit' class='update-button'>Update</button>
    </form>
</td>

                                <td class='action-buttons'>
                                    <form action='{$_SERVER["PHP_SELF"]}' method='POST'>
                                        <input type='hidden' name='vehicle_id' value='{$row['vehicle_id']}'>
                                        <button type='submit'>Archive</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No active vehicles found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container">
        <h2>Archived Vehicles</h2>
        <table>
            <thead>
                <tr>
                    <th>Vehicle ID</th>
                    <th>Vehicle Type</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Assigned Driver ID</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($archivedVehiclesResult->num_rows > 0) {
                    while ($row = $archivedVehiclesResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['vehicle_id']}</td>
                                <td>{$row['vehicle_type']}</td>
                                <td>{$row['model']}</td>
                                <td>{$row['year']}</td>
                                <td>{$row['status']}</td>
                                <td>{$row['assigned_driver_id']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No archived vehicles found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
