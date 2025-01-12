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

try {
    // Get individual savings with detailed metrics
    $stmt = $conn->prepare("
        SELECT 
            COALESCE(SUM(s.amount), 0) as total_savings,
            COUNT(DISTINCT lr.id) as total_loans,
            SUM(CASE WHEN lr.status = 'completed' THEN 1 ELSE 0 END) as completed_loans,
            COUNT(DISTINCT i.investment_id) as total_investments,
            COALESCE(SUM(i.amount), 0) as total_invested_amount
        FROM savings s
        LEFT JOIN loan_request lr ON s.user_id = lr.user_id
        LEFT JOIN investments i ON s.group_id = i.group_id
        WHERE s.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $individual_data = $stmt->get_result()->fetch_assoc();
    $individual_savings = $individual_data['total_savings'];

    // Get individual contributions per group with additional metrics
    $stmt = $conn->prepare("
        SELECT 
            g.group_id,
            g.group_name,
            COALESCE(SUM(s.amount), 0) as contribution,
            g.goal_amount,
            g.emergency_fund,
            COUNT(DISTINCT i.investment_id) as group_investments,
            COALESCE(SUM(i.amount), 0) as invested_amount
        FROM savings s
        JOIN my_group g ON s.group_id = g.group_id
        LEFT JOIN investments i ON g.group_id = i.group_id
        WHERE s.user_id = ?
        GROUP BY g.group_id, g.group_name
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $individual_contributions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get group savings with comprehensive metrics
    $stmt = $conn->prepare("
        SELECT 
            g.group_id,
            g.group_name,
            COALESCE(SUM(s.amount), 0) as total_group_savings,
            g.goal_amount,
            g.emergency_fund,
            g.dps_type,
            g.amount as contribution_amount,
            COUNT(DISTINCT gm.user_id) as active_members,
            DATEDIFF(CURRENT_DATE(), g.start_date) as days_active,
            COUNT(DISTINCT i.investment_id) as investment_count,
            COALESCE(SUM(i.amount), 0) as total_investments,
            COUNT(DISTINCT lr.id) as active_loans
        FROM group_membership gm
        JOIN my_group g ON gm.group_id = g.group_id
        LEFT JOIN savings s ON g.group_id = s.group_id
        LEFT JOIN investments i ON g.group_id = i.group_id
        LEFT JOIN loan_request lr ON g.group_id = lr.group_id AND lr.status = 'approved'
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
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Financial Overview Section -->
                <div class="col-span-1 bg-gray-50 p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Financial Overview</h2>
                    <div class="mb-4">
                        <p class="font-medium">Total Individual Savings: <span class="text-blue-600">$<?= number_format($individual_savings, 2) ?></span></p>
                        <p class="text-sm text-gray-600">Total Investments: $<?= number_format($individual_data['total_invested_amount'], 2) ?></p>
                        <p class="text-sm text-gray-600">Loans: <?= $individual_data['total_loans'] ?> (<?= $individual_data['completed_loans'] ?> completed)</p>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="font-medium mb-2">Group Contributions</h3>
                        <ul class="space-y-2">
                            <?php foreach ($individual_contributions as $contribution): ?>
                                <li class="text-sm">
                                    <span class="font-medium"><?= htmlspecialchars($contribution['group_name']) ?>:</span>
                                    <span class="text-blue-600">$<?= number_format($contribution['contribution'], 2) ?></span>
                                    <div class="text-xs text-gray-500">
                                        Goal Progress: <?= round(($contribution['contribution'] / $contribution['goal_amount']) * 100, 1) ?>%
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- AI Tips Section -->
                <div class="col-span-2">
                    <div class="bg-gray-50 p-4 rounded-lg shadow mb-4">
                        <h2 class="text-xl font-semibold mb-4">Get AI Tips</h2>
                        <div class="mb-4">
                            <label class="block mb-2 font-medium">Analysis Type:</label>
                            <select id="analysis-type" class="border rounded w-full p-2" onchange="updateGroupSelection()">
                                <option value="individual">Individual Analysis</option>
                                <option value="group">Group Analysis</option>
                            </select>
                        </div>
                        
                        <div id="group-selection" class="mb-4 hidden">
                            <label class="block mb-2 font-medium">Select Group:</label>
                            <select id="group-id" class="border rounded w-full p-2">
                                <option value="all">All Groups</option>
                                <?php foreach ($group_savings as $group): ?>
                                    <option value="<?= $group['group_id'] ?>"><?= htmlspecialchars($group['group_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button id="get-tips" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Get Financial Analysis
                        </button>
                    </div>

                    <!-- Tips Display Section -->
                    <div id="tips-section" class="bg-gray-50 p-4 rounded-lg shadow hidden">
                        <h3 class="text-lg font-semibold mb-3">Financial Analysis</h3>
                        <div id="analysis-metrics" class="grid grid-cols-2 gap-4 mb-4">
                            <!-- Metrics will be populated by JavaScript -->
                        </div>
                        
                        <div id="tips-container" class="space-y-4">
                            <!-- Tips will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function updateGroupSelection() {
        const analysisType = document.getElementById('analysis-type').value;
        const groupSelection = document.getElementById('group-selection');
        groupSelection.style.display = analysisType === 'group' ? 'block' : 'none';
    }

    async function getFinancialTips() {
        try {
            const analysisType = document.getElementById('analysis-type').value;
            const groupId = document.getElementById('group-id')?.value;
            
            // Prepare the data to send to Python backend
            const requestData = {
                user_id: <?= $user_id ?>,
                savings_type: analysisType,
                group_id: analysisType === 'group' ? groupId : null,
                financial_data: {
                    individual: <?= json_encode($individual_data) ?>,
                    contributions: <?= json_encode($individual_contributions) ?>,
                    groups: <?= json_encode($group_savings) ?>
                }
            };

            const response = await fetch('http://localhost:5000/generate_tips', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(requestData)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            displayTips(result);
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to generate tips. Please try again later.');
        }
    }

    function displayTips(data) {
        const tipsSection = document.getElementById('tips-section');
        const metricsContainer = document.getElementById('analysis-metrics');
        const tipsContainer = document.getElementById('tips-container');
        
        // Clear previous content
        metricsContainer.innerHTML = '';
        tipsContainer.innerHTML = '';
        
        // Display metrics
        Object.entries(data.analysis).forEach(([key, value]) => {
            const metricEl = document.createElement('div');
            metricEl.className = 'p-3 bg-white rounded shadow-sm';
            metricEl.innerHTML = `
                <div class="text-sm text-gray-600">${key.replace(/_/g, ' ').toUpperCase()}</div>
                <div class="text-lg font-semibold">${value}</div>
            `;
            metricsContainer.appendChild(metricEl);
        });
        
        // Display tips
        data.tips.forEach(tip => {
            const tipEl = document.createElement('div');
            tipEl.className = `p-4 rounded ${tip.priority === 'High' ? 'bg-blue-100' : 'bg-green-100'}`;
            tipEl.innerHTML = `
                <div class="font-medium mb-1">${tip.category}</div>
                <div class="text-sm">${tip.tip}</div>
                <div class="text-xs mt-1 ${tip.priority === 'High' ? 'text-blue-600' : 'text-green-600'}">
                    Priority: ${tip.priority}
                </div>
            `;
            tipsContainer.appendChild(tipEl);
        });
        
        tipsSection.classList.remove('hidden');
    }

    document.getElementById('get-tips').addEventListener('click', getFinancialTips);
    </script>
</body>
</html>