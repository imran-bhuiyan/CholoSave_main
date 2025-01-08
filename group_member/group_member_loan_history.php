<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /test_project/error_page.php"); // Redirect if session variables are missing
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($conn)) {
    include 'db.php'; // Ensure database connection
}

// Fetch loan history details for the logged-in user with remaining amount
$loanHistoryQuery = "
    SELECT 
        lr.id AS loan_id,
        lr.amount AS loan_amount,
        lr.return_time AS due_date,
        lr.status AS loan_status,
        COALESCE(SUM(lr2.amount), 0) AS paid_amount,
        (lr.amount - COALESCE(SUM(lr2.amount), 0)) AS remaining_amount
    FROM loan_request lr
    LEFT JOIN loan_repayments lr2 ON lr.id = lr2.loan_id AND lr2.status = 'completed'
    WHERE lr.user_id = ? AND lr.group_id = ? and lr.status='approved'
    GROUP BY lr.id, lr.amount, lr.return_time, lr.status
    ORDER BY lr.request_time DESC
";

if ($stmt = $conn->prepare($loanHistoryQuery)) {
    $stmt->bind_param('ii', $user_id, $_SESSION['group_id']);
    $stmt->execute();
    $loanHistoryResult = $stmt->get_result();
} else {
    die("Error preparing loan history query.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="group_member_dashboard_style.css">
    <style>
        .custom-font {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 dark-mode-transition">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Page Header -->
            <header class="flex items-center justify-between p-4 bg-white shadow dark-mode-transition">
                <div class="flex items-center justify-center w-full">
                    <button id="menu-button" class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200 absolute left-2">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-5xl font-semibold custom-font">
                        <i class="fa-solid fa-file-invoice-dollar mr-3"></i>
                        Loan History
                    </h1>
                </div>
            </header>

            <div class="p-6 w-full max-w-6xl mx-auto mt-[50px]">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <!-- Loan History Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse bg-gray-50 rounded-lg">
                            <thead>
                                <tr class="bg-blue-100 border-b">
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Serial</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Loan Amount (BDT)</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Repayment Status</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Paid Amount (BDT)</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Remaining Amount (BDT)</th>
                                    <th class="px-6 py-3 text-center text-gray-700 font-medium uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                if ($loanHistoryResult->num_rows > 0) {
                                    $serial = 1;
                                    while ($row = $loanHistoryResult->fetch_assoc()) {
                                        $repaymentStatus = $row['remaining_amount'] <= 0 ? 'Paid' : 'Pending';
                                        $button = $row['remaining_amount'] > 0 ? "<button class='bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600'>Pay</button>" : "N/A";
                                        echo "<tr class='hover:bg-gray-100 transition'>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . $serial++ . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['loan_amount'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['due_date'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . $repaymentStatus . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['paid_amount'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['remaining_amount'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-center'>" . $button . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='px-6 py-4 text-center text-gray-600'>No loan history found.</td></tr>";
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
