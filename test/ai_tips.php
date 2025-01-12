<?php
// Start session and load dependencies
include 'session.php';
include 'db.php';
include 'includes/header2.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Tips</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto py-10">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">AI Financial Tips</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Savings Info -->
                <div class="col-span-1 bg-gray-50 p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Savings Summary</h2>
                    <p><strong>Your Savings:</strong> <span id="individual-savings">$0.00</span></p>
                    <p><strong>Total Group Savings:</strong></p>
                    <ul id="group-savings" class="list-disc pl-6">
                        <li>No group data</li>
                    </ul>
                </div>

                <!-- Options and Results -->
                <div class="col-span-2">
                    <div class="bg-gray-50 p-4 rounded-lg shadow mb-4">
                        <h2 class="text-xl font-semibold mb-4">Get Tips</h2>
                        <div class="mb-4">
                            <label class="block mb-2 font-medium">Choose Savings Type:</label>
                            <select id="savings-type" class="border rounded w-full p-2">
                                <option value="individual">Individual</option>
                                <option value="group">Group</option>
                            </select>
                        </div>
                        <button id="get-result" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Get Result</button>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg shadow">
                        <h2 class="text-xl font-semibold mb-4">AI Tip</h2>
                        <p id="ai-tip" class="text-gray-700">Your financial tip will appear here...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fetch savings data and populate the UI
        async function fetchSavings() {
            const response = await fetch('fetch_savings.php');
            const data = await response.json();
            
            // Populate individual savings
            document.getElementById('individual-savings').textContent = `$${data.individual_savings || 0.00}`;
            
            // Populate group savings
            const groupSavingsElement = document.getElementById('group-savings');
            groupSavingsElement.innerHTML = '';
            if (data.group_savings.length > 0) {
                data.group_savings.forEach(group => {
                    const li = document.createElement('li');
                    li.textContent = `${group.group_name}: $${group.total_group_savings}`;
                    groupSavingsElement.appendChild(li);
                });
            } else {
                groupSavingsElement.innerHTML = '<li>No group data</li>';
            }
        }

        // Send data to the backend and fetch the AI tip
        async function getAITip() {
            const savingsType = document.getElementById('savings-type').value;
            const savingsAmount =
                savingsType === 'individual'
                    ? parseFloat(document.getElementById('individual-savings').textContent.replace('$', ''))
                    : parseFloat(
                          Array.from(document.querySelectorAll('#group-savings li'))
                              .map((item) => parseFloat(item.textContent.split(': $')[1] || 0))
                              .reduce((a, b) => a + b, 0)
                      );

            const response = await fetch('http://localhost:5000/generate_tips', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    savings_type: savingsType,
                    savings_data: savingsAmount,
                }),
            });

            const result = await response.json();
            if (result.tip) {
                document.getElementById('ai-tip').textContent = result.tip;
            } else {
                document.getElementById('ai-tip').textContent = 'An error occurred. Please try again.';
            }
        }

        document.getElementById('get-result').addEventListener('click', getAITip);

        // Initialize
        fetchSavings();
    </script>
</body>
</html>
