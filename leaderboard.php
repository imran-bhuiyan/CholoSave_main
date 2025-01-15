<?php
require_once 'db.php';
include 'includes/header2.php'; // Include the header file

// Get sort parameters
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'points';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Base query
$query = "SELECT l.group_id, l.points, l.updated_at, mg.group_name 
          FROM leaderboard l
          JOIN my_group mg ON l.group_id = mg.group_id";

if (!empty($search)) {
    $query .= " WHERE mg.group_name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
}

$query .= " ORDER BY " . mysqli_real_escape_string($conn, $sort) . " " . $order;

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Groups Leaderboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .gradient-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #818cf8 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .top-3-row {
            background: linear-gradient(90deg, rgba(249, 250, 251, 1) 0%, rgba(243, 244, 246, 0.5) 100%);
            transition: all 0.3s ease;
        }

        .top-3-row:hover {
            background: linear-gradient(90deg, rgba(243, 244, 246, 1) 0%, rgba(249, 250, 251, 0.8) 100%);
        }

        .trophy-icon {
            font-size: 1.2em;
            margin-right: 0.5em;
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <!-- Header -->
        <div class="text-center mb-12 animate-fade-in">
            <h1
                class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-4">
                Champions
            </h1>
            <p class="text-xl text-gray-600">Where Excellence Meets Investment</p>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 card-hover animate-fade-in" style="animation-delay: 0.2s">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <!-- Search Form -->
                <form action="" method="GET" class="w-full md:w-auto">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search champions..."
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="w-full md:w-72 pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-300">
                        <div class="absolute left-4 top-3.5">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </form>

                <!-- Sort Options -->
                <div class="flex gap-4">
                    <a href="?sort=points&order=DESC<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                        class="px-6 py-3 rounded-xl font-medium transition-all duration-300 <?php echo ($sort == 'points' && $order == 'DESC') ? 'gradient-bg text-white shadow-lg' : 'bg-gray-100 hover:bg-gray-200'; ?>">
                        <i class="fas fa-trophy mr-2"></i>Highest Points
                    </a>
                    <a href="?sort=points&order=ASC<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                        class="px-6 py-3 rounded-xl font-medium transition-all duration-300 <?php echo ($sort == 'points' && $order == 'ASC') ? 'gradient-bg text-white shadow-lg' : 'bg-gray-100 hover:bg-gray-200'; ?>">
                        <i class="fas fa-sort-amount-down mr-2"></i>Lowest Points
                    </a>
                </div>
            </div>
        </div>

        <!-- Leaderboard Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover animate-fade-in"
            style="animation-delay: 0.4s">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                            Rank</th>
                        <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                            Group Name</th>
                        <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                            Points</th>
                        <th class="px-8 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                            Last Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $rank = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        $rowClass = $rank <= 3 ? 'top-3-row' : '';
                        ?>
                        <tr class="<?php echo $rowClass; ?> hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div
                                    class="text-lg font-bold <?php echo $rank <= 3 ? 'text-indigo-600' : 'text-gray-900'; ?>">
                                    <?php
                                    if ($rank == 1)
                                        echo '<i class="fas fa-crown trophy-icon text-yellow-500 pulse-animation"></i>';
                                    else if ($rank == 2)
                                        echo '<i class="fas fa-medal trophy-icon text-gray-400"></i>';
                                    else if ($rank == 3)
                                        echo '<i class="fas fa-award trophy-icon text-yellow-700"></i>';
                                    echo $rank++;
                                    ?>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-lg font-semibold text-gray-900">
                                    <?php echo htmlspecialchars($row['group_name']); ?></div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-lg font-bold text-indigo-600"><?php echo number_format($row['points']); ?>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    <i class="far fa-clock mr-2"></i>
                                    <?php echo date('M d, Y H:i', strtotime($row['updated_at'])); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchForm = document.querySelector('form');
            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const searchValue = this.search.value.trim();
                const currentUrl = new URL(window.location.href);
                if (searchValue) {
                    currentUrl.searchParams.set('search', searchValue);
                } else {
                    currentUrl.searchParams.delete('search');
                }
                window.location.href = currentUrl.toString();
            });
        });
    </script>
</body>

</html>

<?php include 'includes/new_footer.php'; ?>