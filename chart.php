<?php
session_start();
require_once "db.php";

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch total income and expenses
$query = "SELECT 
    COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income,
    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense 
    FROM transactions WHERE user_id = :user_id";

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Debugging: Check what values are fetched
// var_dump($result); exit();

$total_income = $result['total_income'];
$total_expense = $result['total_expense'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Chart</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <h2>Finance Chart</h2>
        <a href="logout.php">Logout</a> <!-- Completed the <a> tag -->
    </header>

    <div class="chart-container">
        <canvas id="financeChart"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById("financeChart").getContext("2d");
            new Chart(ctx, {
                type: "bar", // You can change to "doughnut" if needed
                data: {
                    labels: ["Income", "Expense"],
                    datasets: [{
                        label: "Finance Overview",
                        data: [<?php echo $total_income; ?>, <?php echo $total_expense; ?>], 
                        backgroundColor: ["#36A2EB", "#FF6384"]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        });
    </script>

</body>
</html>
