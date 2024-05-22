<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in page</title>
    <link rel="stylesheet" href="CSS/signup_in.css">
</head>
<body>
    <div class="wrapper">
        <h2>Sign in</h2>
        
        <!-- Below, the action should point to the same script for PHP processing -->
        <form action="index.php" method="POST">
            <div class="input-box">
                <input type="text" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <div class="input-box button">
                <input type="submit" value="Sign in">
            </div>
            <div class="text">
                <h3>Log out <a href="/disconnect.php">disconnect</a></h3>
            </div>
        </form>
    </div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    require_once 'db.php';

    // Get the form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify the user exists and the password is correct
    if ($user && password_verify($password, $user['password_hash'])) {
        // Start the session and set the session variables
        session_start();
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['position'] = $user['position'];

        // Redirect to the next page based on the position
        switch ($user['position']) {
            case 'Driver':
                header("Location: driver/driver-dashboard.php");
                break;
            case 'FinanceAdmin':
                header("Location: FINANCE/FinanaceAdminDashboard.php");
                break;
            case 'MissionCoordinator':
                header("Location: missionCordinator/MissionCoordinatorDashboard.php");
                break;
            case 'Fleet_Admin':
                header("Location: Fleet_manager/FleetManagerDashboard.php");
                break;
            default:
                echo "<p>Error: Invalid user position</p>";
                exit();
        }
        exit();
    } else {
        echo "<p>Invalid email or password. Please try again.</p>";
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>
</body>
</html>