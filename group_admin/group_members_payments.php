


<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /test_project/error_page.php"); // Redirect if session variables are missing
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = $_SESSION['group_id'];

if (!isset($conn)) {
    include 'db.php'; // Ensure database connection
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
    
    if ($group_admin_id === $user_id) {
        $is_admin = true;
    }
}

if (!$is_admin) {
    header("Location: /test_project/error_page.php");
    exit;
}

$paymentMethodFilter = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';

$paymentHistoryQuery = "
    SELECT 
        t.transaction_id, 
        t.amount, 
        t.payment_method, 
        t.payment_time, 
        t.user_id, 
        u.name AS member_name
    FROM transaction_info t
    JOIN users u ON t.user_id = u.id
    WHERE t.group_id = ? 
    " . ($paymentMethodFilter ? "AND t.payment_method = ?" : "") . "
    ORDER BY t.payment_time DESC
";

if ($stmt = $conn->prepare($paymentHistoryQuery)) {
    if ($paymentMethodFilter) {
        $stmt->bind_param('is', $group_id, $paymentMethodFilter);
    } else {
        $stmt->bind_param('i', $group_id);
    }
    $stmt->execute();
    $paymentHistoryResult = $stmt->get_result();
} else {
    die("Error preparing payment history query.");
}

$transactions = [];
while ($row = $paymentHistoryResult->fetch_assoc()) {
    $transactions[] = $row;
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
    <link rel="stylesheet" type="text/css" href="group_member_dashboard_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .custom-font {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 dark-mode-transition">
    <div class="flex h-screen">
        <?php include 'group_admin_sidebar.php'; ?>

        <div class="flex-1 overflow-y-auto">
            <header class="flex items-center justify-between p-4 bg-white shadow dark-mode-transition">
                <div class="flex items-center justify-center w-full">
                    <button id="menu-button" class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200 absolute left-2">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-5xl font-semibold custom-font">
                        <i class="fa-solid fa-receipt text-blue-600 mr-3"></i>
                        Group Payment History
                    </h1>
                </div>
            </header>

            <div class="p-6 w-full max-w-6xl mx-auto mt-[50px]">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="mb-6">
                        <form method="GET" action="">
                            <label for="payment_method" class="block text-gray-700 font-medium mb-2">Filter by Payment Method:</label>
                            <select id="payment_method" name="payment_method" class="p-2 border border-gray-300 rounded-md">
                                <option value="">All</option>
                                <option value="bKash" <?php echo isset($_GET['payment_method']) && $_GET['payment_method'] === 'bKash' ? 'selected' : ''; ?>>bKash</option>
                                <option value="Rocket" <?php echo isset($_GET['payment_method']) && $_GET['payment_method'] === 'Rocket' ? 'selected' : ''; ?>>Rocket</option>
                                <option value="Nagad" <?php echo isset($_GET['payment_method']) && $_GET['payment_method'] === 'Nagad' ? 'selected' : ''; ?>>Nagad</option>
                            </select>
                            <button type="submit" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Apply</button>
                        </form>
                    </div>

                    <!-- Search Bar -->
                    <div class="mb-6">
                        <label for="search" class="block text-gray-700 font-medium mb-2">Search by Transaction ID:</label>
                        <input id="search" type="text" class="p-2 border border-gray-300 rounded-md w-full" placeholder="Enter transaction ID...">
                    </div>

                    <!-- Payment History Table -->
                    <div class="overflow-x-auto">
                        <table id="payment-table" class="min-w-full table-auto border-collapse bg-gray-50 rounded-lg">
                            <thead>
                                <tr class="bg-blue-100 border-b">
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Serial</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Member Name</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Transaction ID</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Amount (BDT)</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Payment Method</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Payment Time</th>
                                </tr>
                            </thead>
                            <tbody id="payment-table-body" class="divide-y divide-gray-200">
                                <?php
                                foreach ($transactions as $index => $row) {
                                    echo "<tr class='hover:bg-gray-100 transition'>";
                                    echo "<td class='px-6 py-4 text-gray-800'>" . ($index + 1) . "</td>";
                                    echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['member_name'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td class='px-6 py-4 text-gray-800 transaction-id'>" . htmlspecialchars($row['transaction_id'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['payment_method'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['payment_time'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "</tr>";
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
        const searchInput = document.getElementById('search');
        const tableBody = document.getElementById('payment-table-body');

        searchInput.addEventListener('input', function () {
            const filter = searchInput.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');

            Array.from(rows).forEach(row => {
                const transactionIdCell = row.querySelector('.transaction-id');
                const transactionId = transactionIdCell.textContent.toLowerCase();
                
                if (transactionId.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

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
