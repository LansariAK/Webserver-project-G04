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

// Ensure the user is authorized to view and archive drivers
$authorizedPositions = ['MissionCoordinator', 'Fleet_Admin'];
if (!in_array($userPosition, $authorizedPositions)) {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}

// Handle driver archiving
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['driver_id'])) {
    $driverId = $_POST['driver_id'];

    // Update driver status to inactive
    $updateQuery = "UPDATE driver SET driver_status = 'inactive' WHERE driver_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $driverId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $successMessage = "Driver archived successfully.";
    } else {
        $errorMessage = "Error archiving driver.";
    }

    // Close statement
    $stmt->close();
}

// Fetch active drivers
$driversQuery = "SELECT driver_id, user_id, license_type, license_number FROM driver WHERE driver_status = 'active'";
$driversResult = $conn->query($driversQuery);

// Fetch archived drivers
$archivedDriversQuery = "SELECT driver_id, user_id, license_type, license_number FROM driver WHERE driver_status = 'inactive'";
$archivedDriversResult = $conn->query($archivedDriversQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Drivers</title>
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
        <h2>Active Drivers</h2>
        <a href="AddDriver.php" class="add-user-button">Add Driver</a> <!-- Add this line -->
        <table>
            <thead>
                <tr>
                    <th>Driver ID</th>
                    <th>User ID</th>
                    <th>License Type</th>
                    <th>License Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($driversResult->num_rows > 0) {
                    while ($row = $driversResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['driver_id']}</td>
                                <td>{$row['user_id']}</td>
                                <td>{$row['license_type']}</td>
                                <td>{$row['license_number']}</td>
                                <td>
                                    <form action='{$_SERVER["PHP_SELF"]}' method='POST'>
                                        <input type='hidden' name='driver_id' value='{$row['driver_id']}'>
                                        <button type='submit'>Archive</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No active drivers found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container">
        <h2>Archived Drivers</h2>
        <table>
            <thead>
                <tr>
                    <th>Driver ID</th>
                    <th>User ID</th>
                    <th>License Type</th>
                    <th>License Number</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($archivedDriversResult->num_rows > 0) {
                    while ($row = $archivedDriversResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['driver_id']}</td>
                                <td>{$row['user_id']}</td>
                                <td>{$row['license_type']}</td>
                                <td>{$row['license_number']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No archived drivers found</td></tr>";
                }

                // Close the connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>