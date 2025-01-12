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



// Fetch loan history details for the logged-in user
$loanHistoryQuery = "
    SELECT 
        lr.id AS loan_id,
        lr.amount AS loan_amount,
        lr.approve_date AS approve_date,
        lr.return_time AS due_date,
        lr.status AS loan_status
    FROM loan_request lr
    WHERE lr.user_id = ? AND lr.group_id = ? AND lr.status IN ('approved', 'pending', 'repaid') 
    ORDER BY lr.request_time DESC
";

if ($stmt = $conn->prepare($loanHistoryQuery)) {
    $stmt->bind_param('ii', $user_id, $group_id);
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
    <link rel="stylesheet" type="text/css" href="group_admin_dashboard_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .custom-font {
            font-family: 'Poppins', sans-serif;
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
                        My Loans
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
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Approve Date</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-center text-gray-700 font-medium uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                if ($loanHistoryResult->num_rows > 0) {
                                    $serial = 1;
                                    while ($row = $loanHistoryResult->fetch_assoc()) {
                                        $actionButton = '';
                                        if ($row['loan_status'] == 'approved') {
                                            $actionButton = "<form action='loan_session.php' method='POST'>
                                            <input type='hidden' name='loan_id' value='" . $row['loan_id'] . "'>
                                            <button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600'>Pay</button>
                                          </form>";
                                        } elseif ($row['loan_status'] == 'repaid') {
                                            $actionButton = 'N/A';
                                        } else {
                                            $actionButton = 'N/A';
                                        }

                                        $loanStatus = ($row['loan_status'] == 'repaid') ? 'Paid' : htmlspecialchars($row['loan_status'], ENT_QUOTES, 'UTF-8');

                                        echo "<tr class='hover:bg-gray-100 transition'>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . $serial++ . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['loan_amount'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['approve_date'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['due_date'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td class='px-6 py-4 text-gray-800'>" . $loanStatus . "</td>";
                                        echo "<td class='px-6 py-4 text-center'>" . $actionButton . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='px-6 py-4 text-center text-gray-600'>No loan history found.</td></tr>";
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
