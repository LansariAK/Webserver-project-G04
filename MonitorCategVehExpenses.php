<?php

session_start();

 if (!isset($_SESSION['email']) || $_SESSION['position'] !== 'FinanceAdmin') {
        header('Location: ../index.php');
        echo "<a href='index.php'><<-- Go back to the login.</a>";
        exit();
    }

    require_once '../db.php';

function categorizeExpense($expenseType) {
    switch($expenseType) {
        case 'Fuel':
            return 'Fuel';
        case 'Maintenance':
            return 'Maintenance';
        case 'Insurance':
            return 'Insurance';
        default:
            return 'Other';
    }
}


$sql = "SELECT * FROM vehicle_expenses";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    
    while($row = $result->fetch_assoc()) {
        
        $expenseId = $row["expense_id"];
        $vehicleId = $row["vehicle_id"];
        $expenseType = $row["expense_type"];
        $amount = $row["amount"];
        $expenseDate = $row["expense_date"];
        $category = categorizeExpense($row["category"]);
        
    
        echo "Expense ID: " . $expenseId . "<br>";
        echo "Vehicle ID: " . $vehicleId . "<br>";
        echo "Expense Type: " . $expenseType . "<br>";
        echo "Amount: $" . $amount . "<br>";
        echo "Expense Date: " . $expenseDate . "<br>";
        echo "Category: " . $category . "<br>";
        echo "-------------------------------------<br>";
    }
} else {
    echo "0 results";
}
$conn->close();
?>