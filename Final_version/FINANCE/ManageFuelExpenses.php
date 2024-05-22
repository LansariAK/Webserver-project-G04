<?php
// Start session
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}

// Include the database connection
require_once '../db.php';

// Fetch the current user's position
$userPosition = $_SESSION['position'];

// Ensure the user is authorized to view and delete fuel expenses
$authorizedPositions = ['FinanceAdmin'];
if (!in_array($userPosition, $authorizedPositions)) {
    echo "Access Denied: Your account does not have access to this page.";
    exit();
}

// Handle fuel expense deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['expense_id'])) {
    $expenseId = $_POST['expense_id'];

    // Delete expense from database
    $deleteQuery = "DELETE FROM fuelexpenses WHERE expense_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $expenseId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $successMessage = "Fuel expense deleted successfully.";
    } else {
        $errorMessage = "Error deleting fuel expense.";
    }

    // Close statement
    $stmt->close();
}

// Fetch fuel expenses
$expensesQuery = "SELECT expense_id, vehicle_id, expense_type, amount, date, fuel_type FROM fuelexpenses";
$expensesResult = $conn->query($expensesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/nav.css">
    <title>Manage Fuel Expenses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #f4f4f4;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: white;
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-button {
            background-color: #f44336;
            color: white;
        }

        .delete-button:hover {
            background-color: #e53935;
        }

    </style>
</head>

<body>

    <div class="nav">
    <nav>
        <ul>
            <li><a href="FinanaceAdminDashboard.php">Home</a></li>
            <li><a href="BudgetAllocation.html">Allocate a Budget to a Mission</a></li>
            <li><a href="VehExpensesManag.html">Manage Vehicle Expenses</a></li>
            <li><a href="FinancialReport.html">Generate a Report</a></li>
            <li><a href="Displayreport.php">View Vehicule reports</a></li>
            <li><a href="ManageFuelExpenses.php">View Fuel Expenses</a></li>
            <li class="logout"><a href="/disconnect.php">Logout</a></li>
        </ul>
    </nav>
    </div>

    <div class="container">
        <h2>Manage Fuel Expenses</h2>
        <?php
        if (isset($successMessage)) {
            echo "<div class='message success'>{$successMessage}</div>";
        } elseif (isset($errorMessage)) {
            echo "<div class='message error'>{$errorMessage}</div>";
        }
        ?>
        <table>
            <thead>
                <tr>
                    <th>Expense ID</th>
                    <th>Vehicle ID</th>
                    <th>Expense Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Fuel Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($expensesResult->num_rows > 0) {
                    while ($row = $expensesResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['expense_id']}</td>
                                <td>{$row['vehicle_id']}</td>
                                <td>{$row['expense_type']}</td>
                                <td>{$row['amount']}</td>
                                <td>{$row['date']}</td>
                                <td>{$row['fuel_type']}</td>
                                <td>
                                    <form action='{$_SERVER["PHP_SELF"]}' method='POST'>
                                        <input type='hidden' name='expense_id' value='{$row['expense_id']}'>
                                        <button type='submit' class='delete-button'>Delete</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No fuel expenses found</td></tr>";
                }

                // Close the connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
