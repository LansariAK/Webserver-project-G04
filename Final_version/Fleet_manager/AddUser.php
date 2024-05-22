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
    $userPosition = $_POST['userPosition'];

    // Prepare and execute SQL query
    $sql = "INSERT INTO user (name, email, password_hash, position) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $userName, $userEmail, $userPassword, $userPosition);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "User added successfully.";
    } else {
        echo "Error adding user.";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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
    <!-- Add User Section -->
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form-style">
            <h2>Add User</h2>
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
                <label for="user-position">Position:</label>
                <select id="user-position" name="userPosition">
                    <option value="FinanceAdmin">Finance Admin</option>
                    <option value="MissionCoordinator">Mission Coordinator</option>
                    <option value="Fleet_Admin">Fleet Admin</option>
                </select>
            </div>
            <div class="input-group">
                <button type="submit">Add User</button>
            </div>
        </form>
    </div>
</body>
</html>
