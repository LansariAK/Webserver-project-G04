<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $expense_type = $_POST["expenseType"];
    $amount = $_POST["amount"];
    $date = $_POST["date"];
    $category = $_POST["category"];

    echo "Expense Type: " . $expense_type . "<br>";
    echo "Amount: $" . $amount . "<br>";
    echo "Date: " . $date . "<br>";
    echo "Category: " . $category;
} else {
    header("Location: VehExpensesManag.html");
    exit();
}
?>