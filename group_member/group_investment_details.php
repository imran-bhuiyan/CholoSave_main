<?php
session_start();

// Check if group_id and user_id are set in session
if (!isset($_SESSION['group_id']) || !isset($_SESSION['user_id'])) {
    header("Location: /test_project/error_page.php"); // Redirect if session variables are missing
    exit;
}

$group_id = $_SESSION['group_id'];
$user_id = $_SESSION['user_id'];

// Ensure database connection
if (!isset($conn)) {
    include 'db.php';
}

// Fetch investments for the group
$query = "SELECT * FROM investments WHERE group_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="stylesheet" type="text/css" href="group_member_dashboard_style.css">
    <style>
        .custom-font {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex items-center justify-between p-4 bg-white shadow dark-mode-transition">
                <div class="flex items-center justify-center w-full">
                    <button id="menu-button" class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200 absolute left-2">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-semibold custom-font">
                        <i class="fa-solid fa-money-bill-wave mr-3"></i>
                        Investment Details
                    </h1>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Format the date
                            $created_date = date('F j, Y', strtotime($row['created_at']));
                            
                            // Format the amount with commas and 2 decimal places
                            $formatted_amount = number_format($row['amount'], 2);
                            
                            // Determine the icon based on investment type
                            $icon_class = match($row['investment_type']) {
                                'stocks' => 'fa-chart-line',
                                'real_estate' => 'fa-house',
                                'bonds' => 'fa-file-contract',
                                'mutual_funds' => 'fa-money-bill-trend-up',
                                default => 'fa-money-bill-wave'
                            };

                            // Escape the details for JavaScript
                            $escaped_details = htmlspecialchars($row['details'], ENT_QUOTES);
                    ?>
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas <?php echo $icon_class; ?> text-blue-600 text-xl"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-semibold text-gray-800 capitalize">
                                                <?php echo str_replace('_', ' ', $row['investment_type']); ?>
                                            </h3>
                                            <p class="text-sm text-gray-500"><?php echo $created_date; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Amount Invested:</span>
                                        <span class="text-2xl font-bold text-green-600"><?php echo $formatted_amount; ?> BDT</span>
                                    </div>
                                    <?php if (!empty($row['description'])) : ?>
                                        <p class="mt-3 text-gray-600 text-sm">
                                            <?php echo htmlspecialchars($row['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                                <button onclick="showDetails('<?php echo str_replace('_', ' ', $row['investment_type']); ?>', '<?php echo $escaped_details; ?>', '<?php echo $created_date; ?>', '<?php echo $formatted_amount; ?>')" 
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium focus:outline-none">
                                    View Details →
                                </button>
                            </div>
                        </div>
                    <?php
                        }
                    } else {
                    ?>
                        <div class="col-span-full flex flex-col items-center justify-center p-8 bg-white rounded-lg shadow">
                            <i class="fas fa-info-circle text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Investments Found</h3>
                            <p class="text-gray-500 text-center">There are currently no investments recorded for this group.</p>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Function to show investment details in a modal
        function showDetails(investmentType, details, date, amount) {
            Swal.fire({
                title: investmentType,
                html: `
                    <div class="text-left">
                        <div class="mb-4">
                            <p class="text-gray-600 mb-1">Date:</p>
                            <p class="font-semibold">${date}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-gray-600 mb-1">Amount:</p>
                            <p class="font-semibold text-green-600">BDT ${amount}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Details:</p>
                            <p class="text-sm" style="white-space: pre-line">${details}</p>
                        </div>
                    </div>
                `,
                width: '600px',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'custom-font',
                    title: 'font-semibold text-xl mb-4'
                }
            });
        }

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

        // Handle mobile menu
        const menuButton = document.getElementById('menu-button');
        const sidebar = document.querySelector('.sidebar');

        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });

        function handleResize() {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('hidden');
            } else {
                sidebar.classList.add('hidden');
            }
        }

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