<?php
    session_start();

    if (!isset($_SESSION['email']) || $_SESSION['position'] !== 'FinanceAdmin') {
        header('Location: ../index.php');
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
        header('Location: ../index.php');
        echo "<a href='index.php'><<-- Go back to the login.</a>";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $report_type = $_POST["report-type"];

        $insertQuery = "INSERT INTO financialreports (report_type, generated_by) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("si", $report_type, $user_id);

        if ($stmt->execute()) {
            echo "Rapport généré avec succes: " . htmlspecialchars($report_type);
        } else {
            echo "Erreur lors de la génération du rapport: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        header("Location: FinancialReport.html");
        exit();
    }
?>