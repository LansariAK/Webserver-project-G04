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

    // Updating the mission with the driver and vehicle
    $updateQuery = "UPDATE mission SET driver_id = ?, vehicle_id = ?, status = 'assigned' WHERE mission_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("iii", $selected_driver, $selected_vehicle, $selected_mission);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Assigned mission " . htmlspecialchars($selected_mission) . 
             " to driver " . htmlspecialchars($selected_driver) . 
             " with vehicle " . htmlspecialchars($selected_vehicle) . ".";
    } else {
        echo "Error updating mission assignment: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: AssignMissionDriver.html");
    exit();
}
?>