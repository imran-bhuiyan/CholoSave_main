<?php
session_start();

if (!isset($_SESSION['group_id']) || !isset($_SESSION['user_id'])) {
    header("Location: /test_project/error_page.php"); // Redirect if session variables are missing
    exit;
}

$group_id = $_SESSION['group_id'];
$user_id = $_SESSION['user_id'];

if (!isset($conn)) {
    include 'db.php'; // Ensure database connection
}

// Fetch members' details from the database
$memberQuery = "
    SELECT u.id, u.name, u.phone_number, g.join_date, g.is_admin, SUM(s.amount) AS group_contribution
    FROM users u
    JOIN group_membership g ON u.id = g.user_id
    LEFT JOIN savings s ON u.id = s.user_id AND s.group_id = g.group_id
    WHERE g.group_id = ? AND g.status = 'approved'
    GROUP BY u.id, u.name, u.phone_number, g.join_date, g.is_admin
";

if ($stmt = $conn->prepare($memberQuery)) {
    $stmt->bind_param('i', $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Error preparing statement.");
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced CholoSave Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="group_member_dashboard_style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
        <div class="flex-1 overflow-y-auto ">
            <!-- Table Header -->
            <header class="flex items-center justify-between p-4 bg-white shadow dark-mode-transition">
                <div class="flex items-center justify-center w-full">
                    <button id="menu-button"
                        class="md:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200 absolute left-2">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-5xl font-semibold custom-font">
                        <i class="fa-solid fa-money-bill-transfer mr-3"></i>
                        Members

                    </h1>
                </div>
            </header>
          
        <div class="p-6 w-full max-w-6xl mx-auto mt-[50px]">
            <div class="bg-white rounded-lg shadow-lg p-8">
                

                <!-- Member List Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse bg-gray-50 rounded-lg">
                        <thead>
                            <tr class="bg-blue-100 border-b">
                                <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Phone Number</th>
                                <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Join Date</th>
                                <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-gray-700 font-medium uppercase tracking-wider">Group Contribution (BDT)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                            // Check if there are members in the result
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $role = $row['is_admin'] == 1 ? 'Admin' : 'Member';
                                    echo "<tr class='hover:bg-gray-100 transition'>";
                                    echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['phone_number'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td class='px-6 py-4 text-gray-800'>" . htmlspecialchars($row['join_date'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td class='px-6 py-4 text-gray-800'>" . $role . "</td>";
                                    echo "<td class='px-6 py-4 text-gray-800'>" . (isset($row['group_contribution']) ? htmlspecialchars($row['group_contribution'], ENT_QUOTES, 'UTF-8') : '0') . " BDT</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='px-6 py-4 text-center text-gray-600'>No members found.</td></tr>";
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