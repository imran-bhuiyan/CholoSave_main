<?php
session_start();
require_once 'db.php';
include 'includes/header2.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$individual_savings = 0;
$group_savings = [];

try {
    // Individual savings
    $stmt = $conn->prepare("
        SELECT SUM(amount) AS total_savings 
        FROM savings 
        WHERE user_id = ? 
        AND group_id IS NULL
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $individual_savings = $row['total_savings'] ?? 0;

    // Group savings
    $stmt = $conn->prepare("
        SELECT g.group_name, COALESCE(SUM(s.amount), 0) as total_group_savings 
        FROM group_membership gm
        JOIN my_group g ON gm.group_id = g.group_id
        LEFT JOIN savings s ON g.group_id = s.group_id
        WHERE gm.user_id = ? AND gm.status = 'approved'
        GROUP BY g.group_id, g.group_name
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $group_savings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Financial Tips</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-10">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">AI Financial Tips</h1>
            
            <!-- Savings Display -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="col-span-1 bg-gray-50 p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Savings Summary</h2>
                    <p class="mb-2"><strong>Individual Savings:</strong> 
                        <span id="individual-savings">$<?= number_format($individual_savings, 2) ?></span>
                    </p>
                    
                    <p class="mt-4"><strong>Group Savings:</strong></p>
                    <ul id="group-savings" class="list-disc pl-6">
                        <?php if (!empty($group_savings)): ?>
                            <?php foreach ($group_savings as $group): ?>
                                <li><?= htmlspecialchars($group['group_name']) ?>: 
                                    $<?= number_format($group['total_group_savings'], 2) ?>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No group savings available</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- AI Tips Section -->
                <div class="col-span-2">
                    <div class="bg-gray-50 p-4 rounded-lg shadow mb-4">
                        <h2 class="text-xl font-semibold mb-4">Get AI Tips</h2>
                        <div class="mb-4">
                            <label class="block mb-2 font-medium">Choose Savings Type:</label>
                            <select id="savings-type" class="border rounded w-full p-2">
                                <option value="individual">Individual Savings</option>
                                <option value="group">Group Savings</option>
                            </select>
                        </div>
                        <button id="get-result" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Get Financial Analysis
                        </button>
                    </div>
                    <div id="ai-tip" class="bg-gray-50 p-4 rounded-lg shadow"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    async function getAITip() {
        try {
            const savingsType = document.getElementById('savings-type').value;
            let savingsAmount = 0;

            if (savingsType === 'individual') {
                const individualSpan = document.getElementById('individual-savings');
                savingsAmount = parseFloat(individualSpan.textContent.replace(/[$,]/g, '')) || 0;
            } else {
                const groupItems = document.querySelectorAll('#group-savings li');
                savingsAmount = Array.from(groupItems)
                    .map(item => {
                        const amount = item.textContent.split('$')[1];
                        return amount ? parseFloat(amount.replace(/,/g, '')) : 0;
                    })
                    .reduce((a, b) => a + b, 0);
            }

            const response = await fetch('http://localhost:5000/generate_tips', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    savings_type: savingsType,
                    savings_data: savingsAmount
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            document.getElementById('ai-tip').innerHTML = `
                <div class="space-y-4">
                    <div class="mb-3">
                        <span class="font-semibold">Market Sentiment:</span> 
                        <span class="capitalize">${result.sentiment}</span>
                        <span class="text-gray-600 ml-2">(Confidence: ${(result.confidence * 100).toFixed(2)}%)</span>
                    </div>
                    <div class="mb-3">
                        <span class="font-semibold">Financial Analysis:</span>
                        <p class="mt-1">${result.advice.strategy}</p>
                    </div>
                    <div class="mb-3">
                        <span class="font-semibold">Risk Assessment:</span>
                        <p class="mt-1">${result.advice.risk_assessment}</p>
                    </div>
                    <div class="mt-4 p-3 bg-blue-50 rounded">
                        <span class="font-semibold">Recommendation:</span>
                        <p class="mt-1">${result.advice.recommendation}</p>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('ai-tip').textContent = 'Failed to generate financial analysis: ' + error.message;
        }
    }

    document.getElementById('get-result').addEventListener('click', getAITip);
    </script>
</body>
</html>