<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mission Assignment</title>
    <link rel="stylesheet" href="../CSS/CreateMission.css">
    <link rel="stylesheet" href="../CSS/nav.css">
</head>
<body>
    <nav class="nav">
        <ul>
            <li><a href="MissionCoordinatorDashboard.php">Home</a></li>
            <li><a href="CreateMission.php">Create Mission</a></li>
            <li><a href="AssignMissionDriver.php">Assign a New Mission to a Driver</a></li>
            <li><a href="DriverManagementCommunication.php">Communicate with a Driver</a></li>
            <li><a href="MissionTracker.php">Track a Mission</a></li>
            <li class="logout"><a href="../disconnect.php">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Mission Assignment</h1>
        <form method="post" action="AssignMission.php">
            <div class="input-group">
                <label for="driver">Select Driver:</label>
                <select id="driver" name="driver">
                    <?php
                    require_once '../db.php'; // Database connection
                    
                    // Fetch available drivers
                    $driverQuery = "SELECT driver_id, user.name FROM driver
                                    JOIN user ON driver.user_id = user.user_id
                                    WHERE driver_status = 'active' AND driver_id NOT IN (
                                        SELECT driver_id FROM mission WHERE status IN ('assigned', 'in_progress')
                                    )";
                    $driverResult = $conn->query($driverQuery);

                    if ($driverResult->num_rows > 0) {
                        while ($row = $driverResult->fetch_assoc()) {
                            echo "<option value='" . $row['driver_id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No available drivers</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="input-group">
                <label for="mission">Select Mission:</label>
                <select id="mission" name="mission">
                    <?php
                    // Fetch available missions
                    $missionQuery = "SELECT mission_id, mission_name FROM mission WHERE status = 'pending'";
                    $missionResult = $conn->query($missionQuery);

                    if ($missionResult->num_rows > 0) {
                        while ($row = $missionResult->fetch_assoc()) {
                            echo "<option value='" . $row['mission_id'] . "'>" . htmlspecialchars($row['mission_name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No pending missions</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="input-group">
                <label for="vehicle">Select Vehicle:</label>
                <select id="vehicle" name="vehicle">
                    <?php
                    // Fetch available vehicles
                    $vehicleQuery = "SELECT vehicle_id, model, year FROM vehicle
                                     WHERE status = 'active' AND vehicle_id NOT IN (
                                         SELECT vehicle_id FROM mission WHERE status IN ('assigned', 'in_progress')
                                     )";
                    $vehicleResult = $conn->query($vehicleQuery);

                    if ($vehicleResult->num_rows > 0) {
                        while ($row = $vehicleResult->fetch_assoc()) {
                            echo "<option value='" . $row['vehicle_id'] . "'>" . htmlspecialchars($row['model']) . " (" . $row['year'] . ")</option>";
                        }
                    } else {
                        echo "<option value=''>No available vehicles</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit">Assign Mission</button>
        </form>
    </div>
</body>
</html>