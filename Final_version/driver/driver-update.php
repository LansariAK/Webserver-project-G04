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

// Fetch the current user's ID based on email
$email = $_SESSION['email'];
$userQuery = "SELECT user_id, name, position FROM user WHERE email = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $user_id = $userRow['user_id'];
    $userName = $userRow['name'];
    $userEmail = $email;
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

// Fetch the current driver's data 
$driverQuery = "SELECT d.driver_id, d.license_type, d.license_number, d.vehicle_assigned, u.name, u.position as driver_status 
                FROM driver d
                JOIN user u ON d.user_id = u.user_id
                WHERE d.user_id = ?";
$stmt = $conn->prepare($driverQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$driverResult = $stmt->get_result();

if ($driverResult->num_rows > 0) {
    $driverRow = $driverResult->fetch_assoc();
    $driverId = $driverRow['driver_id'];
    $license = $driverRow['license_type'];
    $licenseNumber = $driverRow['license_number'];
    $vehicleAssigned = $driverRow['vehicle_assigned'];
    $driverName = $driverRow['name'];
    $driverStatus = $driverRow['driver_status'];
} else {
    $errorMessage = "No driver information found.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract POST data
    $license = $_POST['license'];
    $driverStatus = $_POST['driverStatus'];
    $newEmail = $_POST['email'];
    $newPassword = $_POST['password'];

    // Prepare the SQL queries
    $updateDriverQuery = "UPDATE driver SET license_type = ?, driver_status = ? WHERE driver_id = ?";
    $updateUserQuery = "UPDATE user SET email = ?, password_hash = ? WHERE user_id = ?";

    // Hash the new password if it's provided
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    } else {
        // Fetch the current password if not updated
        $getPasswordQuery = "SELECT password_hash FROM user WHERE user_id = ?";
        $stmt = $conn->prepare($getPasswordQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $passwordResult = $stmt->get_result();
        $passwordRow = $passwordResult->fetch_assoc();
        $hashedPassword = $passwordRow['password_hash'];
    }

    // Start the transaction
    $conn->begin_transaction();
    try {
        // Update the driver table
        $stmt = $conn->prepare($updateDriverQuery);
        $stmt->bind_param("ssi", $license, $driverStatus, $driverId);
        if (!$stmt->execute()) {
            throw new Exception("Error updating driver information: " . $stmt->error);
        }

        // Update the user table
        $stmt = $conn->prepare($updateUserQuery);
        $stmt->bind_param("ssi", $newEmail, $hashedPassword, $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating user email or password: " . $stmt->error);
        }

        // Commit the transaction
        $conn->commit();

        // Update the session email if changed
        $_SESSION['email'] = $newEmail;

        $successMessage = "Information updated successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        $errorMessage = $e->getMessage();
    }
    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/update-driver.css">
    <link rel="stylesheet" href="../CSS/nav.css">

    <title>Update Information</title>

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
        <form action="driver-update.php" method="POST" class="form-style">
            <h2>Update Your Information</h2>
            <?php if (isset($successMessage)) { ?>
                <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php } elseif (isset($errorMessage)) { ?>
                <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php } ?>
            <div class="input-group">
                <label for="driver-name">Driver's Full Name:</label>
                <input type="text" id="driver-name" name="driverName" value="<?php echo htmlspecialchars($userName); ?>"
                    required readonly>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>"
                    required>
            </div>
            <div class="input-group">
                <label for="password">Password (Leave blank to keep current):</label>
                <input type="password" id="password" name="password" placeholder="********">
            </div>
            <div class="input-group">
                <label for="license-type">License type:</label>
                <select name="license" id="license">
                    <option value="DEFAULT"></option>
                    <option value="B" <?php echo ($license == 'B') ? 'selected' : ''; ?>>B</option>
                    <option value="C1" <?php echo ($license == 'C1') ? 'selected' : ''; ?>>C1</option>
                    <option value="C2" <?php echo ($license == 'C2') ? 'selected' : ''; ?>>C2</option>
                    <option value="C3" <?php echo ($license == 'C3') ? 'selected' : ''; ?>>C3</option>
                    <option value="E" <?php echo ($license == 'E') ? 'selected' : ''; ?>>E</option>
                    <option value="F" <?php echo ($license == 'F') ? 'selected' : ''; ?>>F</option>
                    <option value="G" <?php echo ($license == 'G') ? 'selected' : ''; ?>>G</option>
                </select>
            </div>
            <div class="input-group">
                <label for="license-number">License Number:</label>
                <input type="text" id="license-number" name="licenseNumber"
                    value="<?php echo htmlspecialchars($licenseNumber); ?>">
            </div>
           
            <div class="input-group">
                <label for="driver-status">Driver Status (Active/Inactive):</label>
                <select id="driver-status" name="driverStatus">
                    <option value="active" <?php echo ($driverStatus == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($driverStatus == 'inactive') ? 'selected' : ''; ?>>Inactive
                    </option>
                </select>
            </div>
            <div class="input-group">
                <button type="submit">Update Information</button>
            </div>
        </form>
    </div>
</body>

</html>