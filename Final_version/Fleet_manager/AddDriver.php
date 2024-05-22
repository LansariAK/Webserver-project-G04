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
    $userName = $_POST['userName'];
    $userEmail = $_POST['userEmail'];
    $userPassword = password_hash($_POST['userPassword'], PASSWORD_DEFAULT); // Hash the password
    $licenseType = $_POST['licenseType'];
    $licenseNumber = $_POST['licenseNumber'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert user data
        $sql = "INSERT INTO user (name, email, password_hash, position) VALUES (?, ?, ?, 'Driver')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $userName, $userEmail, $userPassword);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Get the last inserted user_id
            $userId = $stmt->insert_id;

            // Insert driver data
            $sql = "INSERT INTO driver (user_id, license_type, license_number, driver_status) VALUES (?, ?, ?, 'active')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $userId, $licenseType, $licenseNumber);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $conn->commit();
                $successMessage = "Driver added successfully.";
            } else {
                throw new Exception("Error adding driver.");
            }
        } else {
            throw new Exception("Error adding user.");
        }

        // Close statement
        $stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        $errorMessage = $e->getMessage();
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Driver</title>
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

    <!-- Add Driver Section -->
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form-style">
            <h2>Add Driver</h2>
            <?php if (isset($successMessage)) { ?>
                <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php } elseif (isset($errorMessage)) { ?>
                <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php } ?>
            <div class="input-group">
                <label for="user-name">Name:</label>
                <input type="text" id="user-name" name="userName" required>
            </div>
            <div class="input-group">
                <label for="user-email">Email:</label>
                <input type="email" id="user-email" name="userEmail" required>
            </div>
            <div class="input-group">
                <label for="user-password">Password:</label>
                <input type="password" id="user-password" name="userPassword" required>
            </div>
            <div class="input-group">
                <label for="license-type">License Type:</label>
                <select id="license-type" name="licenseType">
                    <option value="B">B</option>
                    <option value="C1">C1</option>
                    <option value="C2">C2</option>
                    <option value="C3">C3</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                    <option value="G">G</option>
                </select>
            </div>
            <div class="input-group">
                <label for="license-number">License Number:</label>
                <input type="text" id="license-number" name="licenseNumber" required>
            </div>
            <div class="input-group">
                <button type="submit">Add Driver</button>
            </div>
        </form>
    </div>
</body>
</html>
