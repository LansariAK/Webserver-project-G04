<?php
    session_start();

    if (!isset($_SESSION['email']) || $_SESSION['position'] !== 'FinanceAdmin') {
        header('Location: ../index.php');
        echo "<a href='index.php'><<-- Go back to the login.</a>";
        exit();
    }


    require_once '../db.php';

    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $expense_type = $_POST["expenseType"];
        $amount = $_POST["amount"];
        $expense_date = $_POST["date"];
        $category = $_POST["category"];
        $vehicle_id = 1; 

        
        $insertQuery = "INSERT INTO vehicle_expenses (vehicle_id, expense_type, amount, expense_date, category)
                    VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("isdis", $vehicle_id, $expense_type, $amount, $expense_date, $category);

        if ($stmt->execute()) {
            echo "Dépense de véhicule ajoutée avec succès.";
            <a href="VehExpensesManagv2.html">Go back to VehExpensesManagv2</a>
        } else {
            echo "Erreur lors de l'ajout de la dépense de véhicule : " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        header("Location: VehExpensesManag.html");
        exit();
    }
?>
