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

// Fetch the current user's ID and position based on email
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

// Ensure the user is a Mission Coordinator
if ($userPosition != 'MissionCoordinator') {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}

// Query to select all missions
$missionQuery = "
SELECT 
    m.mission_name,
    m.status,
    m.progress,
    m.current_task,
    u.email AS driver_email,
    v.model
FROM 
    mission m
LEFT JOIN 
    driver d ON m.driver_id = d.driver_id
LEFT JOIN 
    user u ON d.user_id = u.user_id
LEFT JOIN 
    vehicle v ON m.vehicle_id = v.vehicle_id
";

// Execute the query
$stmt = $conn->prepare($missionQuery);
$stmt->execute();
$missionResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>View All Missions</title>
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
            <li><a href="MissionCoordinatorDashboard.php">Home</a></li>
            <li><a href="CreateMission.php">Create Mission</a></li>
            <li><a href="AssignMissionDriver.php">Assign a New Mission to a Driver</a></li>
            <li><a href="DriverManagementCommunication.php">Communicate with a Driver</a></li>
            <li><a href="MissionTracker.php">Track a Mission</a></li>
            <li><a href="ViewAllMissions.php">View All Missions</a></li>
            <li class="logout"><a href="../disconnect.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>All Missions</h2>
        <table>
            <thead>
                <tr>

                    <th>Mission Name</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Current Task</th>
                    <th>Driver Email</th>
                    <th>Vehicle Model</th>
                </tr>
            </thead>
            <tbody>

                <?php
                // After executing the mission query
                if ($missionResult->num_rows > 0) {
                    while ($row = $missionResult->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['mission_name'] ?? '') . "</td>
                                <td>" . htmlspecialchars($row['status'] ?? '') . "</td>
                                <td>" . htmlspecialchars($row['progress'] ?? '') . "%</td>
                                <td>" . htmlspecialchars($row['current_task'] ?? ' ') . "</td>
                                <td>" . (!empty($row['driver_email']) ? htmlspecialchars($row['driver_email']) : 'No driver assigned') . "</td>
                                <td>" . (!empty($row['model']) ? htmlspecialchars($row['model']) : 'No vehicle assigned') . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No missions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>