<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CholoSave | Dashboard</title>

    <!-- Google Fonts (optional, for better typography) -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Additional Custom Styles (Optional) -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        header {
            background-color: white;
            /* Set background color to white */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Add a subtle drop shadow */
        }

        .nav-item {
            transition: color 0.3s ease;
            /* Smooth color transition */
        }

        .nav-item:hover {
            color: #FF5722;
            /* Text color on hover */
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        .nav-item:hover .dropdown-content {
            display: block;
        }

        .logo a {
            font-size: 1.8rem;
            font-weight: 700;
            color: #003366;
            text-decoration: none;
        }

        .brand {
            color: #ff5722;
            /* Accent color */
        }
    </style>
</head>

<body>

    <header class="shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left side: Logo/Brand Name -->
                <div class="logo ">
                    <a href="/test_project/user_landing_page.php">Cholo<span class="brand">Save</span></a>
                </div>

                <!-- Right side: Navigation Menu -->
                <div class="hidden md:flex space-x-8 items-center">
                    <!-- Groups Section -->
                    <div class="relative group">
                        <button class="text-gray-800 flex items-center space-x-2  nav-item">
                            <i class="fas fa-users"></i>
                            <span>Groups</span>
                        </button>
                        <div class="absolute left-0 w-48 bg-white shadow-lg rounded-md hidden group-hover:block">
                            <a href="/test_project/create_group.php"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Create Group</a>
                            <!-- <a href="/test_project/view_groups.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">View
                                Group</a> -->
                            <a href="/test_project/groups.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Group</a>
                        </div>
                    </div>

                    <!-- Savings Section -->
                    <div class="relative group">
                        <button class="text-gray-800 flex items-center space-x-2 nav-item">
                            <i class="fas fa-piggy-bank"></i>
                            <span>Savings</span>
                        </button>
                        <div class="absolute left-0 w-48 bg-white shadow-lg rounded-md hidden group-hover:block">
                            <a href="/contribution.php"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Contribution</a>
                            <a href="/withdrawals.php"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Withdrawals</a>
                        </div>
                    </div>

                    <!-- Investments Section -->
                    <div class="relative group">
                        <button class="text-gray-800 flex items-center space-x-2 nav-item">
                            <i class="fas fa-chart-line"></i>
                            <span>Investments</span>
                        </button>
                        <div class="absolute left-0 w-48 bg-white shadow-lg rounded-md hidden group-hover:block">
                            <a href="/investment-dashboard.php"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Dashboard</a>
                            <a href="/test_project/ai_tips.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">AI Tips</a>
                        </div>
                    </div>

                    <!-- Community Section -->
                    <div class="relative group">
                        <button class="text-gray-800 flex items-center space-x-2 nav-item">
                            <i class="fas fa-users-cog"></i>
                            <span>Community</span>
                        </button>
                        <div class="absolute left-0 w-48 bg-white shadow-lg rounded-md hidden group-hover:block">
                            <a href="/leaderboard.php"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Leaderboard</a>
                            <a href="/forum.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Forum</a>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <button class="text-gray-800 flex items-center space-x-2  nav-item">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                    </button>

                    <!-- Settings Section -->
                    <div class="relative group">
                        <button class="text-gray-800 flex items-center space-x-2 nav-item">
                            <i class="fas fa-cogs"></i>
                            <span>Settings</span>
                        </button>
                        <div class="absolute left-0 w-48 bg-white shadow-lg rounded-md hidden group-hover:block">
                            <a href="/profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My
                                Profile</a>
                            <a href="/test_project/logout.php"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>



</body>

</html>