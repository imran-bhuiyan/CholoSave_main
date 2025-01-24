<?php
session_start();
require_once 'user_finance_info.php';
include '../includes/header2.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$financial_data = fetchUserFinancialData($conn, $user_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Financial Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-16 px-4">
    <div class="container mx-auto max-w-6xl mt-16">
        <!-- AI Caution Notice -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.494-1.646-1.742-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Caution:</strong> AI-generated financial advice is for informational purposes only.
                        Always consult with a professional financial advisor before making important financial
                        decisions.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Savings Summary -->
            <div class="md:col-span-1 bg-white/70 p-6 rounded-xl shadow-md">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">Savings Overview</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Individual Savings</span>
                        <span
                            class="font-bold text-green-600">$<?= number_format($financial_data['individual_savings'], 2) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Monthly Income</span>
                        <span
                            class="font-bold text-blue-600">$<?= number_format($financial_data['monthly_income'], 2) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Monthly Expenses</span>
                        <span
                            class="font-bold text-red-600">$<?= number_format($financial_data['monthly_expenses'], 2) ?></span>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-3 text-gray-700 border-b pb-2">Group Contributions</h3>
                    <div class="space-y-2">
                        <?php foreach ($financial_data['group_contributions'] as $group): ?>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600"><?= htmlspecialchars($group['group_name']) ?></span>
                                <span class="text-sm font-medium text-indigo-600">
                                    $<?= number_format($group['total_contribution'], 2) ?> /
                                    $<?= number_format($group['goal_amount'], 2) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- AI Assistant Section -->
            <div class="md:col-span-2 bg-white/70 p-6 rounded-xl shadow-md">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">AI Financial Guidance</h2>

                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="savings-type" class="block text-sm font-medium text-gray-700 mb-2">Savings
                            Type</label>
                        <select id="savings-type"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition">
                            <option value="individual">Individual Savings</option>
                            <option value="group">Group Savings</option>
                        </select>
                    </div>

                    <div id="group-selection" class="hidden">
                        <label for="group-id" class="block text-sm font-medium text-gray-700 mb-2">Choose Group</label>
                        <select id="group-id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition">
                            <option value="all">All Groups</option>
                            <?php foreach ($financial_data['group_contributions'] as $group): ?>
                                <option value="<?= $group['group_id'] ?>"><?= htmlspecialchars($group['group_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="question-select" class="block text-sm font-medium text-gray-700 mb-2">Financial
                        Question</label>
                    <select id="question-select"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 transition">
                        <option value="savings_strategy">What savings strategy should I follow?</option>
                        <option value="investment_advice">What investment options should I consider?</option>
                        <option value="risk_management">How should I manage my financial risks?</option>
                        <option value="emergency_fund">How should I handle my emergency fund?</option>
                        <option value="group_savings">How can we improve our group savings?</option>
                    </select>
                </div>

                <button id="get-result"
                    class="w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 transition font-semibold shadow-md">
                    Get Financial Analysis
                </button>

                <div id="ai-response" class="mt-6 hidden"></div>
            </div>
        </div>
    </div>
    </div>


    <script>
        async function displayAdvice(result) {
            const aiResponse = document.getElementById('ai-response');
            const advice = result.advice;

            aiResponse.innerHTML = `
                <div class="space-y-6 animate-fade-in">
                    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                        <h3 class="text-xl font-bold mb-4 text-gray-800">${advice.title || 'Financial Advice'}</h3>
                        <p class="text-lg text-gray-700 mb-4">${advice.main_advice}</p>
                        <div class="mt-4">
                            <h4 class="font-semibold mb-2 text-gray-700">Action Steps:</h4>
                            <ul class="space-y-2">
                                ${advice.steps.map(step => `<li class="flex items-center text-gray-600"><span class="text-green-500 mr-2">✓</span>${step}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                </div>
            `;
        }


        async function getAIResponse() {
            try {
                const aiResponse = document.getElementById('ai-response');
                const savingsType = document.getElementById('savings-type').value;
                const selectedQuestion = document.getElementById('question-select').value;

                // Show loading state
                aiResponse.classList.remove('hidden');
                aiResponse.innerHTML = `
            <div class="flex items-center justify-center p-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <span class="ml-2">Analyzing financial data...</span>
            </div>
        `;

                // Get financial data from PHP
                const financialData = <?php echo json_encode($financial_data); ?>;

                const response = await fetch('http://localhost:5000/generate_tips', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        savings_type: savingsType,
                        savings_data: financialData,
                        question: selectedQuestion
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Failed to generate advice');
                }

                const result = await response.json();
                if (result.status === 'success') {
                    displayAdvice(result);
                } else {
                    throw new Error(result.error || 'Failed to generate advice');
                }
            } catch (error) {
                console.error('Error:', error);
                aiResponse.innerHTML = `
            <div class="bg-red-100 border-l-4 border-red-500 p-4">
                <p class="text-red-700">Error: ${error.message}</p>
            </div>
        `;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('get-result').addEventListener('click', getAIResponse);
        });

        document.getElementById('savings-type').addEventListener('change', () => {
            const groupSelection = document.getElementById('group-selection');
            groupSelection.classList.toggle('hidden', document.getElementById('savings-type').value !== 'group');
        });
    </script>
</body>

</html>