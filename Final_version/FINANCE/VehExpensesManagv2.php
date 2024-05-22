<link rel="stylesheet" href="../CSS/nav.css">
<link rel="stylesheet" href="../CSS/dashboards.css">

</head>

<body>
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
        $expense_date = date("Y-m-d", strtotime($_POST["date"]));
        $category = $_POST["category"];
        $vehicle_id = 1; 
    
        $insertQuery = "INSERT INTO vehicle_expenses (vehicle_id, expense_type, amount, expense_date, category)
                    VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("issis", $vehicle_id, $expense_type, $amount, $expense_date, $category);

        if ($stmt->execute()) {
            echo "Vehicle expense added successfully.";
        } else {
            echo "Error adding vehicle expense: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        header("Location: VehExpensesManag.html");
        exit();
    }
    ?>