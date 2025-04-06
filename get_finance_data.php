error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "Unauthorized"]));
}

$user_id = $_SESSION['user_id'];

$query = "SELECT amount, type, category, datetime FROM transactions WHERE user_id = :user_id ORDER BY datetime DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($transactions)) {
    die(json_encode(["error" => "No transactions found."]));
}

echo json_encode($transactions);
