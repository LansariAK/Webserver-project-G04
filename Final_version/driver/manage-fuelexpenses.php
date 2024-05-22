<?php
// Start the session
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit;
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

// Attempt to find an active mission for the driver
$missionQuery = "SELECT vehicle_id FROM mission WHERE driver_id = ? AND status != 'completed'";
$stmt = $conn->prepare($missionQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$missionResult = $stmt->get_result();

$vehicleId = null;
if ($missionResult->num_rows > 0) {
    $missionRow = $missionResult->fetch_assoc();
    $vehicleId = $missionRow['vehicle_id'];
}

// Close the statement
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Manage Fuel Expenses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 60%;
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
        <form action="submit-fuelexpenses.php" method="POST" class="form-style">
            <h2>Add Fuel Expense</h2>
            <?php if (isset($_GET['success'])): ?>
                <div class="message success">Fuel expense submitted successfully!</div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="message error">Error submitting fuel expense. Please try again.</div>
            <?php endif; ?>
            <div class="input-group">
                <label for="vehicle-id">Vehicle ID:</label>
                <?php if ($vehicleId !== null): ?>
                    <input type="number" id="vehicle-id" name="vehicleId" value="<?php echo $vehicleId; ?>" readonly>
                <?php else: ?>
                    <select id="vehicle-id" name="vehicleId" required>
                        <option value="">Select a Vehicle</option>
                        <?php
                        // Fetch all vehicles assigned to this driver
                        $vehiclesQuery = "SELECT vehicle_id FROM vehicle WHERE assigned_driver_id = ?";
                        $stmt = $conn->prepare($vehiclesQuery);
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $vehiclesResult = $stmt->get_result();

                        while ($vehicle = $vehiclesResult->fetch_assoc()) {
                            echo '<option value="' . $vehicle['vehicle_id'] . '">' . $vehicle['vehicle_id'] . '</option>';
                        }
                        $stmt->close();
                        ?>
                    </select>
                <?php endif; ?>
            </div>
            <div class="input-group">
                <label for="expense-type">Expense Type:</label>
                <input type="text" id="expense-type" name="expenseType" value="Fuel" readonly>
            </div>
            <div class="input-group">
                <label for="amount">Amount:</label>
                <input type="text" id="amount" name="amount" required>
            </div>
            <div class="input-group">
                <label for="fuel-type">Fuel Type:</label>
                <select name="fuelType" id="fuel-type">
                    <option value="diesel">Diesel</option>
                    <option value="gasoline">Gasoline</option>
                    <option value="electric">Electric</option>
                </select>
            </div>
            <div class="input-group">
                <label for="expense-date">Date:</label>
                <input type="date" id="expense-date" name="expenseDate" required>
            </div>
            <div class="input-group">
                <button type="submit">Submit Expense</button>
            </div>
        </form>
    </div>
</body>

</html>