<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST["message"];
    echo "Message: " . $message;
} else {
    header("Location: MissionTracker.html");
    exit();
}
?>