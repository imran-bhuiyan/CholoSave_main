<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /test_project/error_page.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = $_SESSION['group_id'];

if (!isset($conn)) {
    include 'db.php';
}

// Check if the user is an admin for the group
$is_admin = false;
$checkAdminQuery = "SELECT group_admin_id FROM my_group WHERE group_id = ?";
if ($stmt = $conn->prepare($checkAdminQuery)) {
    $stmt->bind_param('i', $group_id);
    $stmt->execute();
    $stmt->bind_result($group_admin_id);
    $stmt->fetch();
    $stmt->close();
    $is_admin = $group_admin_id === $user_id;
}
if (!$is_admin) {
    header("Location: /test_project/error_page.php");
    exit;
}

// Fetch payment history
$paymentHistoryQuery = "
    SELECT transaction_id, amount, payment_method, payment_time 
    FROM transaction_info
    WHERE user_id = ? AND group_id = ?
    ORDER BY payment_time DESC
";
if ($stmt = $conn->prepare($paymentHistoryQuery)) {
    $stmt->bind_param('ii', $user_id, $group_id);
    $stmt->execute();
    $paymentHistoryResult = $stmt->get_result();
} else {
    die("Error preparing payment history query.");
}

// Fetch user savings and contributions
$savingsQuery = "SELECT sum(amount) FROM savings WHERE user_id = ? AND group_id = ?";
$savings = 0;
if ($stmt = $conn->prepare($savingsQuery)) {
    $stmt->bind_param('ii', $user_id, $group_id);
    $stmt->execute();
    $stmt->bind_result($savings);
    $stmt->fetch();
    $stmt->close();
}

// Fetch group savings summary
$groupSavingsQuery = "SELECT SUM(amount) AS total_savings FROM savings WHERE group_id = ?";
$total_savings = 0;
if ($stmt = $conn->prepare($groupSavingsQuery)) {
    $stmt->bind_param('i', $group_id);
    $stmt->execute();
    $stmt->bind_result($total_savings);
    $stmt->fetch();
    $stmt->close();
}

// Fetch time period remaining for user
$timeRemainingQuery = "SELECT time_period_remaining FROM group_membership WHERE user_id = ? AND group_id = ?";
$time_remaining = 0;
if ($stmt = $conn->prepare($timeRemainingQuery)) {
    $stmt->bind_param('ii', $user_id, $group_id);
    $stmt->execute();
    $stmt->bind_result($time_remaining);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .custom-font {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include 'group_admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Page Header -->
            <header class="flex items-center justify-between p-4 bg-white shadow">
                <h1 class="text-4xl font-semibold custom-font ml-96">
                    <i class="fa-solid fa-chart-line text-blue-600 mr-3"></i>
                    My Payments History
                </h1>
            </header>

            <div class="p-6 w-full max-w-6xl mx-auto">
                <!-- Admin Indicator -->
                <?php if ($is_admin): ?>
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-600 rounded-lg">
                    <i class="fa-solid fa-crown mr-2"></i>
                    You are the admin of this group.
                </div>
                <?php endif; ?>

                <!-- Summary Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="p-6 bg-white rounded-lg shadow">
                        <h2 class="text-xl font-semibold custom-font mb-2">Your Savings</h2>
                        <p class="text-3xl font-bold text-green-600">BDT <?php echo number_format($savings, 2); ?></p>
                    </div>
                    <div class="p-6 bg-white rounded-lg shadow">
                        <h2 class="text-xl font-semibold custom-font mb-2">Total Group Savings</h2>
                        <p class="text-3xl font-bold text-blue-600">BDT <?php echo number_format($total_savings, 2); ?></p>
                    </div>
                    <div class="p-6 bg-white rounded-lg shadow">
                        <h2 class="text-xl font-semibold custom-font mb-2">Installment Remaining</h2>
                        <p class="text-3xl font-bold text-red-600"><?php echo $time_remaining; ?> months</p>
                    </div>
                </div>

                <!-- Payment History Table -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-semibold custom-font mb-6">Payment History</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse bg-gray-50 rounded-lg">
                            <thead>
                                <tr class="bg-blue-100 border-b">
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Serial</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Transaction ID</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Amount (BDT)</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Payment Method</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Payment Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                if ($paymentHistoryResult->num_rows > 0) {
                                    $serial = 1;
                                    while ($row = $paymentHistoryResult->fetch_assoc()) {
                                        echo "<tr class='hover:bg-gray-100 transition'>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . $serial++ . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['transaction_id'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['payment_method'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['payment_time'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='px-6 py-4 text-center text-gray-600'>No payment history found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>

// Dark mode functionality
let isDarkMode = localStorage.getItem('darkMode') === 'true';
const body = document.body;
const themeToggle = document.getElementById('theme-toggle');
const themeIcon = themeToggle.querySelector('i');
const themeText = themeToggle.querySelector('span');

function updateTheme() {
    if (isDarkMode) {
        body.classList.add('dark-mode');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        themeText.textContent = 'Light Mode';
    } else {
        body.classList.remove('dark-mode');
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        themeText.textContent = 'Dark Mode';
    }
}

// Initialize theme
updateTheme();

themeToggle.addEventListener('click', () => {
    isDarkMode = !isDarkMode;
    localStorage.setItem('darkMode', isDarkMode);
    updateTheme();
});


window.addEventListener('resize', handleResize);
handleResize();

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
</script>
</body>

</html>
