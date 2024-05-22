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
            <li><a href="ViewAllMissions.php">View All Missions</a></li>
            <li class="logout"><a href="../disconnect.php">Logout</a></li>
        </ul>
    </nav>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once '../db.php'; // Ensure the database connection is made
    
        $selected_driver = $_POST["driver"];
        $selected_mission = $_POST["mission"];
        $selected_vehicle = $_POST["vehicle"];

        // Validate the input to ensure all fields are selected
        if (empty($selected_driver) || empty($selected_mission) || empty($selected_vehicle)) {
            echo "Please ensure you select a driver, a mission, and a vehicle.";
            exit();
        }

        try {
            // Begin a transaction
            $conn->begin_transaction();

            // Updating the driver with the assigned vehicle
            $updateDriver = "UPDATE driver SET vehicle_assigned= ? WHERE driver_id = ?";
            $stmt = $conn->prepare($updateDriver);
            $stmt->bind_param("ii", $selected_vehicle, $selected_driver);
            $stmt->execute();

            // Updating the vehicle with the assigned driver
            $updateCar = "UPDATE vehicle SET assigned_driver_id= ? WHERE vehicle_id = ?";
            $stmt = $conn->prepare($updateCar);
            $stmt->bind_param("ii", $selected_driver, $selected_vehicle);
            $stmt->execute();

            // Updating the mission with the driver and vehicle
            $updateMission = "UPDATE mission SET driver_id = ?, vehicle_id = ?, status = 'assigned' WHERE mission_id = ?";
            $stmt = $conn->prepare($updateMission);
            $stmt->bind_param("iii", $selected_driver, $selected_vehicle, $selected_mission);
            $stmt->execute();

            // Check if any rows were affected by the last update
            if ($stmt->affected_rows > 0) {
                // Commit the transaction
                $conn->commit();
                echo "Assigned mission " . htmlspecialchars($selected_mission) .
                    " to driver " . htmlspecialchars($selected_driver) .
                    " with vehicle " . htmlspecialchars($selected_vehicle) . ".";
            } else {
                throw new Exception("No rows were affected by the mission assignment update.");
            }

            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            $conn->rollback();
            echo "Error updating mission assignment: " . $e->getMessage();
            if (isset($stmt) && $stmt !== false) {
                $stmt->close();
            }
            $conn->close();
        }
    } else {
        header("Location: AssignMissionDriver.html");
        exit();
    }
    ?>