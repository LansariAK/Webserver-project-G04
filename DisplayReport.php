<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['position'] !== 'FinanceAdmin') {
    header('Location: ../index.php');
    echo "<a href='index.php'><<-- Go back to the login.</a>";
    exit();
}

require_once '../db.php';

$sql = "SELECT * FROM financialreports";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reportId = $row["report_id"];
        $reportType = $row["report_type"];
        $generatedDate = $row["generated_date"];
        
        echo "Report ID: " . $reportId . "<br>";
        echo "Report Type: " . $reportType . "<br>";
        echo "Generated Date: " . $generatedDate . "<br>";
        echo "-------------------------------------<br>";
    }
} else {
    echo "0 results";
}

$conn->close();
?>