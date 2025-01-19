<?php
include 'db.php';
include 'session.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $group_name = filter_var(trim($_POST['group_name']), FILTER_SANITIZE_STRING);
        $description = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
        $members = filter_var(trim($_POST['members']), FILTER_SANITIZE_NUMBER_INT);
        $dps_type = $_POST['dps_type'];
        $time_period = filter_var(trim($_POST['time_period']), FILTER_SANITIZE_NUMBER_INT);
        $amount = filter_var(trim($_POST['amount']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $start_date = $_POST['start_date'];
        $goal_amount = filter_var(trim($_POST['goal_amount']), FILTER_SANITIZE_NUMBER_INT);
        $warning_threshold = filter_var(trim($_POST['warning_threshold']), FILTER_SANITIZE_NUMBER_INT);
        $emergency_fund = filter_var(trim($_POST['emergency_fund']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $bkash = filter_var(trim($_POST['bkash_number']), FILTER_SANITIZE_STRING);
        $rocket = filter_var(trim($_POST['rocket_number']), FILTER_SANITIZE_STRING);
        $nagad = filter_var(trim($_POST['nagad_number']), FILTER_SANITIZE_STRING);

        if (empty($group_name) || empty($members) || empty($time_period) || empty($amount)) {
            throw new Exception("Please fill in all required fields.");
        }

        // Insert into my_group table to create the group
        $query = "INSERT INTO my_group (
            group_name, description, members, group_admin_id, dps_type, 
            time_period, amount, start_date, goal_amount, warning_time, 
            emergency_fund, bKash, Rocket, Nagad
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssiisidsiiddss",
            $group_name,
            $description,
            $members,
            $_SESSION['user_id'],
            $dps_type,
            $time_period,
            $amount,
            $start_date,
            $goal_amount,
            $warning_threshold,
            $emergency_fund,
            $bkash,
            $rocket,
            $nagad
        );

        if ($stmt->execute()) {

            
            // Get the last inserted group_id
            $group_id = $stmt->insert_id;
            // Calculate points: initial points (5) + 1% of emergency_fund
            $points = 5 + (0.01 * $emergency_fund);

            // Insert values into the leaderboard table
            $leaderboardQuery = "INSERT INTO leaderboard (group_id, points) VALUES (?, ?)";
            $leaderboardStmt = $conn->prepare($leaderboardQuery);
            $leaderboardStmt->bind_param("id", $group_id, $points); // Use 'id' for group_id (int) and points (double)

            if (!$leaderboardStmt->execute()) {
                throw new Exception("Error inserting into leaderboard: " . $conn->error);
            }







            // Store the group_id in the session
            $_SESSION['group_id'] = $group_id;

            // Insert the user into the group_membership table with status 'approved' and is_admin = 1
            $membershipQuery = "INSERT INTO group_membership (group_id, user_id, status, is_admin, join_date, time_period_remaining) VALUES (?, ?, 'approved', 1, NOW(),?)";
            $membershipStmt = $conn->prepare($membershipQuery);
            $membershipStmt->bind_param("iii", $group_id, $_SESSION['user_id'], $time_period);

            if ($membershipStmt->execute()) {
                // Role-checking logic
                $query = "SELECT is_admin FROM group_membership WHERE group_id = ? AND user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ii", $group_id, $_SESSION['user_id']);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($is_admin);
                    $stmt->fetch();

                    // Redirect based on role
                    if ($is_admin) {
                        header("Location: /test_project/group_admin/group_admin_dashboard.php?group_id=" . $group_id);
                    } else {
                        header("Location: /test_project/group_member/group_dashboard.php?group_id=" . $group_id);
                    }
                    exit();
                } else {
                    throw new Exception("Failed to retrieve user role.");
                }
            } else {
                throw new Exception("Error adding admin to group: " . $conn->error);
            }
        } else {
            throw new Exception("Error creating group: " . $conn->error);
        }
    } catch (Exception $e) {
        // Handle error
        $error_message = $e->getMessage();
        header("Location: error_page.php?error=" . urlencode($error_message));
        exit();
    }
}
?>


<?php include 'includes/header2.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .step {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease-out;
            display: none;
        }

        .step.active {
            opacity: 1;
            transform: translateY(0);
            display: block;
        }

        .progress-bar {
            transition: width 0.5s ease-out;
        }

        .content {
            flex: 1;
            /* This ensures the content area takes up the available space */
        }

        footer {
            background-color: #f1f5f9;
            padding: 2rem;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="content container mx-auto px-4 py-8 max-w-5xl">
        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-8">Create Group</h1>

        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-8">
            <div class="progress-bar bg-green-600 h-2.5 rounded-full" style="width: 25%"></div>
        </div>

        <!-- Step Indicators -->
        <div class="flex justify-between mb-8">
            <div class="step-indicator flex flex-col items-center">
                <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center">1</div>
                <span class="text-sm mt-1">Group Info</span>
            </div>
            <div class="step-indicator flex flex-col items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center">2</div>
                <span class="text-sm mt-1">Plan Details</span>
            </div>
            <div class="step-indicator flex flex-col items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center">3</div>
                <span class="text-sm mt-1">Payment</span>
            </div>
            <div class="step-indicator flex flex-col items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center">4</div>
                <span class="text-sm mt-1">Regulations</span>
            </div>
        </div>

        <form id="groupForm" class="space-y-6" method="POST" action="">
            <!-- Step 1: Group Information -->
            <div class="step active" id="step1">
                <h2 class="text-xl font-medium text-green-600 mb-4">Group Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
                        <input type="text" name="group_name" required placeholder="Enter group name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" required placeholder="Enter group description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Members</label>
                        <input type="number" name="members" required placeholder="Number of members"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-md"
                        id="nextButton1">Next</button>
                </div>
            </div>

            <!-- Step 2: Plan Details -->
            <div class="step" id="step2">
                <h2 class="text-xl font-medium text-green-600 mb-4">Plan Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">DPS Type</label>
                        <select name="dps_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time Period</label>
                        <input type="number" name="time_period" required placeholder="Enter time period (months)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="number" name="amount" required placeholder="Enter amount" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md"
                        id="prevButton1">Previous</button>
                    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-md"
                        id="nextButton2">Next</button>
                </div>
            </div>

            <!-- Step 3: Payment -->
            <div class="step" id="step3">
                <h2 class="text-xl font-medium text-green-600 mb-4">Payment Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">bKash Number</label>
                        <input type="text" name="bkash_number" placeholder="Optional, enter bKash number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rocket Number</label>
                        <input type="text" name="rocket_number" placeholder="Optional, enter Rocket number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nagad Number</label>
                        <input type="text" name="nagad_number" required placeholder="Enter Nagad number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md"
                        id="prevButton2">Previous</button>
                    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-md"
                        id="nextButton3">Next</button>
                </div>
            </div>

            <!-- Step 4: Regulations -->
            <div class="step" id="step4">
                <h2 class="text-xl font-medium text-green-600 mb-4">Regulations</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Goal Amount</label>
                        <input type="number" name="goal_amount" required placeholder="Enter goal amount" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Fund</label>
                        <input type="number" name="emergency_fund" required placeholder="Enter emergency fund amount"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warning Threshold</label>
                        <input type="number" name="warning_threshold" required
                            placeholder="Enter warning threshold amount"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md"
                        id="prevButton3">Previous</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md">Submit</button>
                </div>
            </div>
        </form>
    </div>

    <?php include 'includes/new_footer.php'; ?>

    <script>
        let currentStep = 0;
        const totalSteps = 4;
        const steps = document.querySelectorAll('.step');
        const nextButton1 = document.getElementById('nextButton1');
        const nextButton2 = document.getElementById('nextButton2');
        const nextButton3 = document.getElementById('nextButton3');
        const prevButton1 = document.getElementById('prevButton1');
        const prevButton2 = document.getElementById('prevButton2');
        const prevButton3 = document.getElementById('prevButton3');

        const showStep = (step) => {
            steps.forEach((el, index) => {
                if (index === step) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            });
        };

        const updateProgressBar = () => {
            const progressBar = document.querySelector('.progress-bar');
            progressBar.style.width = ((currentStep + 1) / totalSteps) * 100 + '%';
        };

        nextButton1.addEventListener('click', () => {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        nextButton2.addEventListener('click', () => {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        nextButton3.addEventListener('click', () => {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        prevButton1.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        prevButton2.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        prevButton3.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
                updateProgressBar();
            }
        });

        showStep(currentStep); // Initialize the first step
    </script>
</body>

</html>