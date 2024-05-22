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

// Ensure the user is authorized to view and archive users
$authorizedPositions = ['MissionCoordinator', 'Fleet_Admin'];
if (!in_array($userPosition, $authorizedPositions)) {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    // Delete user from database
    $deleteQuery = "DELETE FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $successMessage = "User deleted successfully.";
    } else {
        $errorMessage = "Error deleting user.";
    }

    // Close statement
    $stmt->close();
}

// Fetch users
$usersQuery = "SELECT user_id, name, email, position FROM user";
$usersResult = $conn->query($usersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Users</title>
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
        <h2>Users</h2>
        <a href="AddUser.php" class="add-user-button">Add User</a>
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
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($usersResult->num_rows > 0) {
                    while ($row = $usersResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['user_id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['position']}</td>
                                <td>
                                    <form action='{$_SERVER["PHP_SELF"]}' method='POST'>
                                        <input type='hidden' name='user_id' value='{$row['user_id']}'>
                                        <button type='submit' class='delete-button'>Delete</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No users found</td></tr>";
                }

                // Close the connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
