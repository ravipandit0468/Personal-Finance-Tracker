<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle transaction addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["amount"], $_POST["type"], $_POST["datetime"])) {
    $amount = $_POST["amount"];
    $type = $_POST["type"];
    $datetime = $_POST["datetime"];
    
    $query = "INSERT INTO transactions (user_id, amount, type, datetime) VALUES (:user_id, :amount, :type, :datetime)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(["user_id" => $user_id, "amount" => $amount, "type" => $type, "datetime" => $datetime]);
    
    header("Location: home.php");
    exit();
}

// Fetch total income, expenses, and balance
$query = "SELECT 
    COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income,
    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense 
    FROM transactions WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_income = $result['total_income'];
$total_expense = $result['total_expense'];
$balance = $total_income - $total_expense;

// Fetch transactions
$query = "SELECT amount, type, datetime FROM transactions WHERE user_id = :user_id ORDER BY datetime DESC";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$transactions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Tracker Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('financeChart').getContext('2d');
        var financeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Income', 'Expense'],
                datasets: [{
                    data: [<?php echo $total_income; ?>, <?php echo $total_expense; ?>], // Dynamic PHP data
                    backgroundColor: ['#36A2EB', '#FF6384']
                }]
            },
            options: {
                responsive: true
            }
        });
    });
</script>

</head>
<body>
    <nav class="navbar">
        <div class="logo">Finance Tracker</div>
        <ul class="nav-links">
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="dashboard">
        <div class="card balance-card">
            <h2>💰 Current Balance</h2>
            <p>$<?php echo number_format($balance, 2); ?></p>
        </div>
        <div class="card income-card">
            <h2>📈 Total Income</h2>
            <p>$<?php echo number_format($total_income, 2); ?></p>
        </div>
        <div class="card expense-card">
            <h2>📉 Total Expense</h2>
            <p>$<?php echo number_format($total_expense, 2); ?></p>
        </div>
    </div>

    <div class="tabs">
        <button class="tab-btn active" onclick="showTab('all')">All</button>
        <button class="tab-btn" onclick="showTab('income')">Income</button>
        <button class="tab-btn" onclick="showTab('expense')">Expense</button>
        <button class="tab-btn" onclick="showTab('chart')">Chart</button>
        <button class="tab-btn" onclick="showTab('add_transaction')">Add Transaction</button>
    </div>

    <div class="tab-content" id="all">
        <table>
            <thead>
                <tr>
                    <th>Amount ($)</th>
                    <th>Type</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td>$<?= htmlspecialchars($transaction['amount']) ?></td>
                        <td class="<?= ($transaction['type'] === 'income') ? 'income' : 'expense' ?>">
                            <?= ucfirst(htmlspecialchars($transaction['type'])) ?>
                        </td>
                        <td><?= htmlspecialchars($transaction['datetime']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="tab-content" id="income" style="display: none;">
        <table>
            <thead>
                <tr>
                    <th>Amount ($)</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <?php if ($transaction['type'] === 'income'): ?>
                        <tr>
                            <td>$<?= htmlspecialchars($transaction['amount']) ?></td>
                            <td><?= htmlspecialchars($transaction['datetime']) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="tab-content" id="expense" style="display: none;">
        <table>
            <thead>
                <tr>
                    <th>Amount ($)</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <?php if ($transaction['type'] === 'expense'): ?>
                        <tr>
                            <td>$<?= htmlspecialchars($transaction['amount']) ?></td>
                            <td><?= htmlspecialchars($transaction['datetime']) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

      <div class="tab-content" id="chart" style="display: none;">
        <canvas id="financeChart" width="200" height="200"></canvas>
    </div>

    <div class="tab-content" id="add_transaction" style="display: none;">
        <div class="transaction-form">
            <h2>Enter details</h2>
            <form action="home.php" method="POST">
                <label>Amount ($):</label>
                <input type="number" name="amount" required>
                <label>Type:</label>
                <select name="type">
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
                <label>Date & Time:</label>
                <input type="datetime-local" name="datetime" required>
                <button type="submit">Add Transaction</button>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            document.querySelectorAll(".tab-content").forEach(tab => tab.style.display = "none");
            document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
            document.getElementById(tabId).style.display = "block";
            document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add("active");
        }
        document.getElementById("all").style.display = "block";
    </script>
</body>
</html>
