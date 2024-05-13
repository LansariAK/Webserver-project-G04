<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $report_type = $_POST["report-type"];
    echo "Report Type: " . $report_type;
} else {
    header("Location: FinancialReport.html");
    exit();
}
?>