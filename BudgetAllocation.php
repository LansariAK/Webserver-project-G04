<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $allocated_mission_budget = $_POST["mission"];
    $allocated_expenses_budget = $_POST["expenses"];

    echo "Allocated Mission Budget: $" . $allocated_mission_budget . "<br>";
    echo "Allocated Expenses Budget: $" . $allocated_expenses_budget;
} else {
    header("Location: BudgetAllocation.html");
    exit();
}
?>