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

    // Fetch the current user's ID based on email
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
        // Redirect to login if user not found
        header('Location: ../login.php');
        exit();
    }

    // Process form data
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mission_budget = $_POST["mission"];
        $expenses_budget = $_POST["expenses"];
        $mission_id = 1; // Replace with actual mission selection logic
        $allocation_date = date("Y-m-d");

        // Insert data into budgetallocation table
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