<?php
session_start();

if (!isset($_SESSION['group_id'])) {
    header("Location: /test_project/error_page.php"); // Redirect if group_id is not set
    exit;
}

$group_id = $_SESSION['group_id'];
$user_id = $_SESSION['user_id'];

if (isset($_SESSION['group_id']) && isset($_SESSION['user_id'])) {
    $group_id = $_SESSION['group_id'];
    $user_id = $_SESSION['user_id'];
   
} else {
    echo 'Group ID is not set in the session.';
}

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
    
    // If the user is the admin of the group, proceed; otherwise, redirect to an error page
    if ($group_admin_id === $user_id) {
        $is_admin = true;
    }
}

if (!$is_admin) {
    // Redirect to error page if the user is not an admin
    header("Location: /test_project/error_page.php");
    exit;
}


// Queries
$total_group_savings_query = "SELECT IFNULL(SUM(amount), 0) AS total_group_savings FROM savings WHERE group_id = ?";
$month_savings_query = "SELECT IFNULL(SUM(amount), 0) AS this_month_savings FROM savings WHERE group_id = ? AND MONTH(created_at) = MONTH(CURRENT_DATE)";
$total_members_query = "SELECT COUNT(*) AS total_members FROM group_membership WHERE group_id = ? AND status = 'approved'";
$new_members_query = "SELECT COUNT(*) AS new_members FROM group_membership WHERE group_id = ? AND status = 'approved' AND MONTH(join_date) = MONTH(CURRENT_DATE)";
$emergency_query = "SELECT emergency_fund FROM my_group WHERE group_id = ?";

// Fetch Data
function fetchSingleValue($conn, $query, $param)
{
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $param);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return array_values($row)[0]; // Return the first value
}

$total_group_savings = fetchSingleValue($conn, $total_group_savings_query, $group_id);


$this_month_savings = fetchSingleValue($conn, $month_savings_query, $group_id);


$total_members = fetchSingleValue($conn, $total_members_query, $group_id);


$new_members = fetchSingleValue($conn, $new_members_query, $group_id);


$emergency_fund = fetchSingleValue($conn, $emergency_query, $group_id);




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced CholoSave Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="group_admin_dashboard_style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .custom-font {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 dark-mode-transition">
    <div class="flex h-full">
        <!-- Sidebar -->
        <?php include 'group_admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="flex items-center justify-between p-4 bg-white shadow dark-mode-transition">
                <div class="flex items-center justify-center w-full">
                    <button id="menu-button"
                        class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200 absolute left-2">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-5xl font-semibold custom-font">
                        <i class="fa-solid fa-money-bill-transfer mr-3"></i>
                        Dashboard
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="p-2 hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <i class="fas fa-user-circle"></i>
                    </button>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-4">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="stats-card bg-white p-6 rounded-lg shadow cursor-pointer">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-gray-500">Total Savings</h3>
                                <p class="text-2xl font-bold" id="savings-counter">
                                    $<?php echo number_format($total_group_savings, 2); ?></p>
                                <p class="text-green-500 text-sm">+$<?php echo number_format($this_month_savings, 2); ?>
                                    this month</p>
                            </div>
                            <div class="text-2xl text-gray-400">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card bg-white p-6 rounded-lg shadow cursor-pointer">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-gray-500">Members</h3>
                                <p class="text-2xl font-bold" id="members-counter"><?php echo $total_members; ?></p>
                                <p class="text-green-500 text-sm">+<?php echo $new_members; ?> new this month</p>
                            </div>
                            <div class="text-2xl text-gray-400">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card bg-white p-6 rounded-lg shadow cursor-pointer">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-gray-500">Emergency Fund</h3>
                                <p class="text-2xl font-bold" id="fund-counter">
                                    $<?php echo number_format($emergency_fund, 2); ?></p>
                            </div>
                            <div class="text-2xl text-gray-400">
                                <i class="fas fa-piggy-bank"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graph or chart showing code -->
                <div class="flex justify-between gap-4">
                    <div class="flex-1">
                        <?php include 'group_admin_dashboard_graph.php'; ?>
                    </div>
                    <div class="flex-1 ">
                        <div class="h-[500px] w-full bg-blue-500 "> <?php include 'progress_bar.php'; ?> </div>
                    </div>
                </div>

                <!-- Polls Section -->
                <?php include 'polls.php'; ?>

            </main>
        </div>
    </div>



    <script>
        // Counter animation function
        function animateCounter(element, target, duration = 2000, prefix = '') {
            let start = 0;
            const increment = target / (duration / 16);
            const animate = () => {
                start += increment;
                if (start < target) {
                    element.textContent = prefix + Math.floor(start).toLocaleString();
                    requestAnimationFrame(animate);
                } else {
                    element.textContent = prefix + target.toLocaleString();
                }
            };
            animate();
        }

        // Initialize animations
        document.addEventListener('DOMContentLoaded', () => {
            // PHP values dynamically passed to JavaScript
            const totalSavings = <?php echo json_encode($total_group_savings); ?>;
            const totalMembers = <?php echo json_encode($total_members); ?>;
            const emergencyFund = <?php echo json_encode($emergency_fund); ?>;

            // Animate counters
            animateCounter(document.getElementById('savings-counter'), totalSavings, 2000, '$');
            animateCounter(document.getElementById('members-counter'), totalMembers);
            animateCounter(document.getElementById('fund-counter'), emergencyFund, 2000, '$');

            // Animate poll bars
            document.querySelectorAll('.poll-option').forEach(option => {
                const bar = option.querySelector('.bg-blue-500');
                const percentage = option.querySelector('.text-gray-500').textContent;
                setTimeout(() => {
                    bar.style.width = percentage;
                }, 500);
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