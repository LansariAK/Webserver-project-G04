<?php
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

// Ensure the user is a Driver
if ($userPosition != 'Driver') {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Events Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #f4f4f4;
        }

        .form-style {
            display: flex;
            flex-direction: column;
        }

        .input-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input,
        select,
        button {
            padding: 8px;
            margin-top: 5px;
        }

        button {
            cursor: pointer;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
        }

        button:hover {
            background-color: #444;
        }
    </style>
</head>

<body>
    <nav>
        <ul>
            <li><a href="driver-dashboard.php">Home</a></li>
            <li><a href="driver-update.php">Driver Update</a></li>
            <li><a href="missions.php">Missions</a></li>
            <li><a href="events-report.php">Events Report</a></li>
            <li class="logout"><a href="../disconnect.php">Logout</a></li>
        </ul>
    </nav>


    <div class="container">
        <form action="submit-event-report.php" method="POST" class="form-style">
            <h2>Report an Event</h2>
            <div class="input-group">
                <label for="event-type">Event Type:</label>
                <select name="eventType" id="event-type">
                    <option value="Accident">Accident</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Fueling">Fueling</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="input-group">
                <label for="event-description">Event Description:</label>
                <input type="text" id="event-description" name="eventDescription" required>
            </div>
            <div class="input-group">
                <label for="event-date">Event Date:</label>
                <input type="date" id="event-date" name="eventDate" required>
            </div>
            <div class="input-group">
                <button type="submit">Submit Report</button>
            </div>
        </form>
    </div>
</body>

</html>