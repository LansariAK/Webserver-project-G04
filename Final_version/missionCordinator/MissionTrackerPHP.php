<?php
require_once '../db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['sendMessage']) && !empty($_POST['mission']) && !empty($_POST['message'])) {
        // Handle sending a message related to a mission
        $message = $_POST['message'];
        $mission_id = $_POST['mission'];

        // Insert the message into the communication table
        $insertMessageQuery = "INSERT INTO communication (mission_id, message) VALUES (?, ?)";
        $stmt = $conn->prepare($insertMessageQuery);
        $stmt->bind_param("is", $mission_id, $message);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<p>Message sent successfully.</p>";
        } else {
            echo "<p>Error sending message: " . $conn->error . "</p>";
        }
        $stmt->close();
    } elseif (isset($_POST['trackMission']) && !empty($_POST['mission'])) {
        // Redirect back to the HTML page to show the selected mission's status
        $selected_mission = $_POST['mission'];
        header("Location: MissionTracker.php?mission=" . $selected_mission);
        exit();
    }
} else {
    header("Location: MissionTracker.html");
    exit();
}
?>