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
            <li class="logout"><a href="/disconnect.php">Logout</a></li>
        </ul>
    </nav>
    <?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['email']) || $_SESSION['position'] !== 'FinanceAdmin') {
        header('Location: ../login.php');
        exit();
    }

    // Include the database connection
    require_once '../db.php';

    // Process form data
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $expense_type = $_POST["expenseType"];
        $amount = $_POST["amount"];
        $expense_date = $_POST["date"];
        $category = $_POST["category"];
        $vehicle_id = 1; // Replace with actual vehicle selection logic
    
        // Insert data into vehicle_expenses table
        $insertQuery = "INSERT INTO vehicle_expenses (vehicle_id, expense_type, amount, expense_date, category)
                    VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("isdis", $vehicle_id, $expense_type, $amount, $expense_date, $category);

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