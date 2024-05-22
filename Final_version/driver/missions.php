<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start the session
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}

// Include the database connection
require_once '../db.php';

// Fetch the current user's ID based on email
$email = $_SESSION['email'];
$userQuery = "SELECT user_id, position FROM user WHERE email = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $user_id = $userRow['user_id'];
    $userPosition = $userRow['position'];
} else {
    // Redirect to login if user not found
    header('Location: ../login.php');
    exit();
}

// Ensure the user is a Driver
if ($userPosition != 'Driver') {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mission_id = $_POST['mission_id'];
    $new_status = $_POST['status'];
    $new_progress = intval($_POST['progress']);
    $new_current_task = $_POST['current_task'];

    // Update the mission details in the database
    $updateQuery = "UPDATE mission SET status = ?, progress = ?, current_task = ? WHERE mission_id = ? AND driver_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sisii", $new_status, $new_progress, $new_current_task, $mission_id, $user_id);

    if ($stmt->execute()) {
        $successMessage = "Mission details updated successfully.";
        // If the mission is marked as completed, free up the driver and vehicle
        if ($new_status == 'completed') {
            // Update the driver table to remove the vehicle association
            $updateDriverQuery = "UPDATE driver SET vehicle_assigned = NULL WHERE driver_id = ?";
            $stmtDriver = $conn->prepare($updateDriverQuery);
            $stmtDriver->bind_param("i", $user_id);
            $stmtDriver->execute();
            $stmtDriver->close();

            // Update the vehicle table to remove the driver association
            $updateVehicleQuery = "UPDATE vehicle SET assigned_driver_id = NULL WHERE vehicle_id = (SELECT vehicle_id FROM mission WHERE mission_id = ?)";
            $stmtVehicle = $conn->prepare($updateVehicleQuery);
            $stmtVehicle->bind_param("i", $mission_id);
            $stmtVehicle->execute();
            $stmtVehicle->close();
        }
    } else {
        $errorMessage = "Error updating mission details.";
    }
}

// Fetch missions assigned to the driver
$missionQuery = "SELECT mission_id, mission_name, status, progress, current_task FROM mission WHERE driver_id = ? AND status != 'completed'";
$stmt = $conn->prepare($missionQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$missionResult = $stmt->get_result();

// Fetch completed missions assigned to the driver
$completedMissionQuery = "SELECT mission_id, mission_name, status, progress, current_task FROM mission WHERE driver_id = ? AND status = 'completed'";
$stmt = $conn->prepare($completedMissionQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$completedMissionResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Missions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #f4f4f4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
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

        input[type="text"],
        select,
        input[type="number"],
        input[type="submit"] {
            padding: 5px;
            margin: 5px;
        }
    </style>
</head>

<body>
    <nav>
        <ul>
            <li><a href="driver-dashboard.php">Home</a></li>
            <li><a href="driver-update.php">Update Profile</a></li>
            <li><a href="missions.php">Missions</a></li>
            <li><a href="events-report.php">Events Report</a></li>
            <li><a href="manage-fuelexpenses.php">Fuel Expenses</a></li>
            <li class="logout"><a href="../disconnect.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Your Mission Assignments</h2>
        <?php if (isset($successMessage)) { ?>
            <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php } elseif (isset($errorMessage)) { ?>
            <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php } ?>
        <table>
            <thead>
                <tr>
                    <th>Mission ID</th>
                    <th>Mission Name</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Current Task</th>
                    <th>Change Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($missionResult->num_rows > 0) {
                    // Output data of each row
                    while ($row = $missionResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['mission_id']}</td>
                                <td>{$row['mission_name']}</td>
                                <td>{$row['status']}</td>
                                <td>{$row['progress']}%</td>
                                <td>{$row['current_task']}</td>
                                <td>
                                    <form action='missions.php' method='POST'>
                                        <input type='hidden' name='mission_id' value='{$row['mission_id']}'>
                                        <select name='status'>
                                            <option value='assigned'" . ($row['status'] == 'assigned' ? ' selected' : '') . ">Assigned</option>
                                            <option value='in_progress'" . ($row['status'] == 'in_progress' ? ' selected' : '') . ">In Progress</option>
                                            <option value='completed'" . ($row['status'] == 'completed' ? ' selected' : '') . ">Completed</option>
                                        </select>
                                        <input type='number' name='progress' value='{$row['progress']}' min='0' max='100' required>
                                        <input type='text' name='current_task' value='" . htmlspecialchars($row['current_task']) . "' required>
                                        <input type='submit' value='Update'>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No missions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container">
        <h2>Archived Missions</h2>
        <table>
            <thead>
                <tr>
                    <th>Mission ID</th>
                    <th>Mission Name</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Current Task</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($completedMissionResult->num_rows > 0) {
                    // Output data of each row
                    while ($row = $completedMissionResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['mission_id']}</td>
                                <td>{$row['mission_name']}</td>
                                <td>{$row['status']}</td>
                                <td>{$row['progress']}%</td>
                                <td>{$row['current_task']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No archived missions found</td></tr>";
                }

                // Close the statement and connection
                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>