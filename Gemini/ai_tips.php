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
$individual_contributions = [];
$goal_amount = 0;
$emergency_fund = 0;
$active_members = 0;
$remaining_weeks = 0;

try {
    // Individual savings including contributions in each group
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total_savings FROM savings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $individual_savings = $row['total_savings'] ?? 0;

    // Individual contributions in each group
    $stmt = $conn->prepare("
        SELECT g.group_id, g.group_name, COALESCE(SUM(s.amount), 0) AS contribution 
        FROM savings s 
        JOIN my_group g ON s.group_id = g.group_id 
        WHERE s.user_id = ? 
        GROUP BY g.group_id, g.group_name
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $individual_contributions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Group savings
    $stmt = $conn->prepare("
        SELECT g.group_id, g.group_name, COALESCE(SUM(s.amount), 0) AS total_group_savings 
        FROM group_membership gm 
        JOIN my_group g ON gm.group_id = g.group_id 
        LEFT JOIN savings s ON g.group_id = s.group_id 
        WHERE gm.user_id = ? AND gm.status = 'approved' 
        GROUP BY g.group_id, g.group_name
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $group_savings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Retrieve goal_amount, emergency_fund, active_members, and remaining_weeks
    $stmt = $conn->prepare("
        SELECT goal_amount, emergency_fund, active_members, remaining_weeks 
        FROM group_goals 
        WHERE group_id = ?
    ");
    $stmt->bind_param("i", $selected_group_id); // Assume you have a selected group ID
    $stmt->execute();
    $result = $stmt->get_result();
    $goalData = $result->fetch_assoc();
    $goal_amount = $goalData['goal_amount'] ?? 0;
    $emergency_fund = $goalData['emergency_fund'] ?? 0;
    $active_members = $goalData['active_members'] ?? 0;
    $remaining_weeks = $goalData['remaining_weeks'] ?? 0;

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
                    <p class="mb-2"><strong>Total Individual Savings:</strong> 
                        <span id="individual-savings">$<?= number_format($individual_savings, 2) ?></span>
                    </p>
                    
                    <p class="mt-4"><strong>Individual Contributions:</strong></p>
                    <ul id="individual-contributions" class="list-disc pl-6">
                        <?php if (!empty($individual_contributions)): ?>
                            <?php foreach ($individual_contributions as $contribution): ?>
                                <li><?= htmlspecialchars($contribution['group_name']) ?>: 
                                    $<?= number_format($contribution['contribution'], 2) ?>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No individual contributions available</li>
                        <?php endif; ?>
                    </ul>

                    <p class="mt-4"><strong>Group Savings:</strong></p>
                    <ul id="group-savings" class="list-disc pl-6">
                        <?php if (!empty($group_savings)): ?>
                            <?php foreach ($group_savings as $group): ?>
                                <li data-group-id="<?= $group['group_id'] ?>"><?= htmlspecialchars($group['group_name']) ?>: 
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
                            <select id="savings-type" class="border rounded w-full p-2" onchange="updateGroupSelection()">
                                <option value="individual">Individual Savings</option>
                                <option value="group">Group Savings</option>
                            </select>
                        </div>
                        <div id="group-selection" class="mb-4 hidden">
                            <label class="block mb-2 font-medium">Choose Group:</label>
                            <select id="group-id" class="border rounded w-full p-2">
                                <option value="all">All Groups</option>
                                <?php foreach ($group_savings as $group): ?>
                                    <option value="<?= $group['group_id'] ?>"><?= htmlspecialchars($group['group_name']) ?></option>
                                <?php endforeach; ?>
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
    function updateGroupSelection() {
        const savingsType = document.getElementById('savings-type').value;
        const groupSelection = document.getElementById('group-selection');
        if (savingsType === 'group') {
            groupSelection.classList.remove('hidden');
        } else {
            groupSelection.classList.add('hidden');
        }
    }

async function getAITip() {
    try {
        // Show loading state
        document.getElementById('ai-tip').innerHTML = `
            <div class="flex items-center justify-center p-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <span class="ml-2">Analyzing financial data...</span>
            </div>
        `;

        const savingsType = document.getElementById('savings-type').value;
        let savingsAmount = 0;
        let groupId = null;

        if (savingsType === 'individual') {
            const individualSpan = document.getElementById('individual-savings');
            savingsAmount = parseFloat(individualSpan.textContent.replace(/[$,]/g, '')) || 0;
        } else {
            groupId = document.getElementById('group-id').value;
            if (groupId === 'all') {
                const groupItems = document.querySelectorAll('#group-savings li');
                savingsAmount = Array.from(groupItems)
                    .map(item => {
                        const amount = item.textContent.split('$')[1];
                        return amount ? parseFloat(amount.replace(/,/g, '')) : 0;
                    })
                    .reduce((a, b) => a + b, 0);
            } else {
                const groupItem = document.querySelector(`#group-savings li[data-group-id="${groupId}"]`);
                const amount = groupItem.textContent.split('$')[1];
                savingsAmount = amount ? parseFloat(amount.replace(/,/g, '')) : 0;
            }
        }

        const goalAmount = <?= $goal_amount ?>;
        const emergencyFund = <?= $emergency_fund ?>;
        const activeMembers = <?= $active_members ?>;
        const remainingWeeks = <?= $remaining_weeks ?>;

        const response = await fetch('http://localhost:5000/generate_tips', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                savings_type: savingsType,
                savings_data: savingsAmount,
                group_id: groupId,
                goal_amount: goalAmount,
                emergency_fund: emergencyFund,
                active_members: activeMembers,
                remaining_weeks: remainingWeeks
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        // Update UI with results
        document.getElementById('ai-tip').innerHTML = `
            <div class="space-y-4">
                <div class="mb-3">
                    <span class="font-semibold">Market Sentiment:</span> 
                    <span class="capitalize">${result.sentiment}</span>
                    <span class="text-gray-600 ml-2">(Confidence: ${(result.confidence * 100).toFixed(2)}%)</span>
                </div>
                <div class="mb-3 p-3 bg-blue-50 rounded">
                    <span class="font-semibold">Financial Strategy:</span>
                    <p class="mt-1">${result.advice.strategy}</p>
                </div>
                <div class="mb-3 p-3 bg-yellow-50 rounded">
                    <span class="font-semibold">Risk Assessment:</span>
                    <p class="mt-1">${result.advice.risk_assessment}</p>
                </div>
                <div class="mb-3 p-3 bg-green-50 rounded">
                    <span class="font-semibold">Recommendation:</span>
                    <p class="mt-1">${result.advice.recommendation}</p>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('ai-tip').innerHTML = `
            <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                <p class="font-bold">Error:</p>
                <p>Failed to generate financial analysis: ${error.message}</p>
            </div>
        `;
    }
}

document.getElementById('get-result').addEventListener('click', getAITip);
</script>
</body>
</html>