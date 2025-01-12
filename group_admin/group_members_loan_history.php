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

// Handle Approve or Reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_id'], $_POST['action'])) {
    $loan_id = $_POST['loan_id'];
    $action = $_POST['action'];

    if (in_array($action, ['approved', 'declined'])) {
        $updateLoanStatusQuery = "UPDATE loan_request SET status = ? , approve_date = CURDATE()  WHERE id = ? AND group_id = ?";
        if ($stmt = $conn->prepare($updateLoanStatusQuery)) {
            $stmt->bind_param('sii', $action, $loan_id, $group_id);
            if ($stmt->execute()) {
                $message = "Loan status updated successfully.";
            } else {
                $message = "Failed to update loan status.";
            }
        } else {
            $message = "Error preparing loan status update query.";
        }
    }
}

// Fetch loan history details for the logged-in user
$loanHistoryQuery = "
    SELECT 
        lr.id AS loan_id,
        lr.user_id AS user_id,
        lr.amount AS loan_amount,
        lr.approve_date AS approve_date,
        lr.return_time AS due_date,
        lr.request_time AS request_date,
        lr.status AS loan_status,
        u.name AS user_name,
        (SELECT SUM(amount) FROM savings WHERE user_id = lr.user_id AND group_id = lr.group_id) AS group_contribution
    FROM loan_request lr
    LEFT JOIN users u ON lr.user_id = u.id
    WHERE lr.group_id = ? 
    ORDER BY lr.request_time DESC
";

if ($stmt = $conn->prepare($loanHistoryQuery)) {
    $stmt->bind_param('i', $group_id);
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
    <title>Loan Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="group_admin_dashboard_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .custom-font {
            font-family: 'Poppins', sans-serif;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .overflow-x-auto {
            overflow-x: hidden;
        }
    </style>
</head>

<body class="bg-gray-100 dark-mode-transition">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include 'group_admin_sidebar.php'; ?>

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
                        Loan Management
                    </h1>
                </div>
            </header>

            <div class="p-6 w-full max-w-full mx-auto mt-[50px]">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <!-- Filter Section -->
                    <div class="mb-6">
                        <form method="GET" action="">
                            <label for="filter" class="block text-gray-700 font-medium mb-2">Filter by Status:</label>
                            <select id="filter" name="filter" class="p-2 border border-gray-300 rounded-md">
                                <option value="">All</option>
                                <option value="pending" <?php echo isset($_GET['filter']) && $_GET['filter'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo isset($_GET['filter']) && $_GET['filter'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="repaid" <?php echo isset($_GET['filter']) && $_GET['filter'] === 'repaid' ? 'selected' : ''; ?>>Repaid</option>
                            </select>
                            <button type="submit" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Apply</button>
                        </form>
                    </div>

                    <!-- Loan History Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse bg-gray-50 rounded-lg">
                            <thead>
                                <tr class="bg-blue-100 border-b">
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Serial</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Loan Amount (BDT)</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Group Contribution (BDT)</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Request Date</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Approve Date</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-center text-gray-700 font-medium uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
                                $serial = 1;
                                if ($loanHistoryResult->num_rows > 0) {
                                    while ($row = $loanHistoryResult->fetch_assoc()) {
                                        if ($filter && $row['loan_status'] !== $filter) {
                                            continue;
                                        }

                                        $loanStatus = ($row['loan_status'] == 'repaid') ? 'Paid' : htmlspecialchars($row['loan_status'], ENT_QUOTES, 'UTF-8');

                                        echo "<tr class='hover:bg-gray-100 transition'>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . $serial++ . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['user_name'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['loan_amount'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['group_contribution'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['request_date'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['due_date'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['approve_date'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . $loanStatus . "</td>";
                                        if ($row['loan_status'] === 'pending') {
                                            echo "<td class='px-6 py-4 text-center action-buttons'>
                                                <form action='' method='POST'>
                                                    <input type='hidden' name='loan_id' value='" . $row['loan_id'] . "'>
                                                    <button type='submit' name='action' value='approved' class='bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600'>Approve</button>
                                                </form>
                                                <form action='' method='POST'>
                                                    <input type='hidden' name='loan_id' value='" . $row['loan_id'] . "'>
                                                    <button type='submit' name='action' value='declined' class='bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600'>Reject</button>
                                                </form>
                                            </td>";
                                        } else {
                                            echo "<td class='px-6 py-4 text-center text-gray-500'>N/A</td>";
                                        }
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9' class='px-6 py-4 text-center text-gray-600'>No loan history found.</td></tr>";
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
