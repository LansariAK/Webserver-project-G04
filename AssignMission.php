<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_driver = $_POST["driver"];
    $selected_mission = $_POST["mission"];

    echo "Assigned mission " . $selected_mission . " to driver " . $selected_driver;
 } else {
    header("Location: AssignMission.html");
    exit();
}
?>