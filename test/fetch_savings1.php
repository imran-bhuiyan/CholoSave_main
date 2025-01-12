<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = [
    'individual_savings' => 0,
    'group_savings' => []
];

try {
    // Calculate individual savings
    $stmt = $pdo->prepare("SELECT SUM(amount) AS total_savings FROM savings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $response['individual_savings'] = $result['total_savings'] ?? 0;

    // Fetch group savings
    $stmt = $pdo->prepare(
        "SELECT g.group_id, g.group_name, COALESCE(SUM(s.amount), 0) AS total_group_savings 
        FROM group_membership gm
        JOIN my_group g ON gm.group_id = g.group_id
        LEFT JOIN savings s ON g.group_id = s.group_id
        WHERE gm.user_id = ? AND gm.status = 'approved'
        GROUP BY g.group_id"
    );
    $stmt->execute([$user_id]);
    $response['group_savings'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>