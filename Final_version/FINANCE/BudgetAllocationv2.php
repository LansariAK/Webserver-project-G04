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
        header('Location: ../login.php');
        echo "<a href='index.php'><<-- Go back to the login.</a>";
        exit();
    }

    require_once '../db.php';

    $email = $_SESSION['email'];
    $userQuery = "SELECT user_id FROM user WHERE email = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult->num_rows > 0) {
        $userRow = $userResult->fetch_assoc();
        $user_id = $userRow['user_id'];
    } else {
        
        header('Location: ../login.php');
        echo "<a href='index.php'><<-- Go back to the login.</a>";
        exit();
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mission_budget = $_POST["mission"];
        $expenses_budget = $_POST["expenses"];
        $mission_id = 1; 
        $allocation_date = date("Y-m-d");

        $insertQuery = "INSERT INTO budgetallocation (mission_id, budget_amount, expenses_budget, allocation_date, allocated_by)
                    VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iddsi", $mission_id, $mission_budget, $expenses_budget, $allocation_date, $user_id);

        if ($stmt->execute()) {
            echo "Budget allocated successfully.";
        } else {
            echo "Error allocating budget: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        header("Location: BudgetAllocation.html");
        exit();
    }