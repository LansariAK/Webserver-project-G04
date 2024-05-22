<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/dashboards.css">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Mission Progress Tracker</title>
</head>

<body>
    <nav>
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

    <h1>Mission Progress Tracker</h1>
    <form method="post" action="MissionTracker.php">
        <label for="mission">Select Mission:</label>
        <select id="mission" name="mission">
            <?php
            require_once '../db.php'; // Database connection
            
            // Fetch all missions
            $missionQuery = "SELECT mission_id, mission_name, status, progress FROM mission ORDER BY mission_id DESC";
            $missionResult = $conn->query($missionQuery);

            if ($missionResult->num_rows > 0) {
                while ($row = $missionResult->fetch_assoc()) {
                    $selected = isset($_POST['mission']) && $_POST['mission'] == $row['mission_id'] ? 'selected' : '';
                    echo "<option value='" . $row['mission_id'] . "' $selected>" . htmlspecialchars($row['mission_name']) . " - " . htmlspecialchars($row['status']) . "</option>";
                }
            } else {
                echo "<option value=''>No missions found</option>";
            }
            ?>
        </select>
        <button type="submit" name="trackMission">Track Mission</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['trackMission'])) {
        $selected_mission = $_POST['mission'];

        // Fetch the selected mission's details
        $missionDetailsQuery = "SELECT mission_name, description, status, progress, current_task FROM mission WHERE mission_id = ?";
        $stmt = $conn->prepare($missionDetailsQuery);
        $stmt->bind_param("i", $selected_mission);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $missionDetails = $result->fetch_assoc();
            echo "<div id='mission-progress'>
                    <h2>Mission: " . htmlspecialchars($missionDetails['mission_name']) . "</h2>
                    <p>Status: " . htmlspecialchars($missionDetails['status']) . "</p>
                    <p>Current Task: " . htmlspecialchars($missionDetails['current_task']) . "</p>
                    <p>Progress: " . htmlspecialchars($missionDetails['progress']) . "%</p>
                  </div>";
        } else {
            echo "<p>Mission details not found.</p>";
        }
    }
    ?>

    <div id="communication">
        <h2>Real-Time Communication</h2>
        <form id="message-form" method="post" action="MissionTracker.php">
            <textarea id="message-input" name="message" placeholder="Type your message..." rows="4"></textarea>
            <input type="hidden" name="mission"
                value="<?php echo isset($_POST['mission']) ? $_POST['mission'] : ''; ?>">
            <button type="submit" name="sendMessage">Send</button>
        </form>
    </div>
</body>

</html>