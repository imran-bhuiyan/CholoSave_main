<?php
session_start();

$group_id = $_SESSION['group_id'];
$user_id = $_SESSION['user_id'];

// Check if user is logged in and belongs to a group
if (!isset($group_id) || !isset($user_id)) {
    header("Location: /test_project/error_page.php");
    exit;
}

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

// Fetch group members' details
$membersQuery = "
    SELECT 
        um.user_id, 
        u.name, 
        u.email, 
        um.join_date, 
        um.is_admin, 
        COALESCE(SUM(s.amount), 0) AS contribution
    FROM 
        group_membership um
    JOIN 
        users u ON um.user_id = u.id
    LEFT JOIN 
        savings s ON um.user_id = s.user_id AND um.group_id = s.group_id
    WHERE 
        um.group_id = ? AND um.status = 'approved'
    GROUP BY 
        um.user_id
";

$members = [];
if ($stmt = $conn->prepare($membersQuery)) {
    $stmt->bind_param('i', $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $row['role'] = $row['is_admin'] == 1 ? 'Admin' : 'Member';
        $members[] = $row;
    }
    $stmt->close();
}

// Update member role to admin or member
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['make_admin'])) {
        $target_user_id = $_POST['user_id'];
        
        // Update the group_membership table to make the selected user an admin
        $updateRoleQuery = "UPDATE group_membership SET is_admin = 1 WHERE user_id = ? AND group_id = ?";
        if ($stmt = $conn->prepare($updateRoleQuery)) {
            $stmt->bind_param('ii', $target_user_id, $group_id);
            if ($stmt->execute()) {
                header("Location: group_members.php");  // Refresh the page after update
                exit;
            }
            $stmt->close();
        }
    } elseif (isset($_POST['make_member'])) {
        $target_user_id = $_POST['user_id'];
        
        // Update the group_membership table to make the selected user a normal member
        $updateRoleQuery = "UPDATE group_membership SET is_admin = 0 WHERE user_id = ? AND group_id = ?";
        if ($stmt = $conn->prepare($updateRoleQuery)) {
            $stmt->bind_param('ii', $target_user_id, $group_id);
            if ($stmt->execute()) {
                header("Location: group_members.php");  // Refresh the page after update
                exit;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Members</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .editable {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            padding: 0.25rem;
            border-radius: 0.25rem;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-white-50 to-blue-100 min-h-screen">
    <div class="flex h-screen">
        <?php include 'group_admin_sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="glass-effect shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex justify-center">
                    <div class="flex items-center justify-center">
                        <h1 class="text-2xl font-semibold text-gray-800 ml-4">
                            <i class="fa-solid fa-users text-blue-600 mr-3"></i>
                            Group Members
                        </h1>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Serial</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Join Date</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Role</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Contribution</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php
                                        $serial = 1;
                                        foreach ($members as $member):
                                            ?>
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo $serial++; ?> </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($member['name']); ?> </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($member['email']); ?> </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo date('Y-m-d', strtotime($member['join_date'])); ?> </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo $member['role']; ?> </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo $member['contribution']; ?> </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <?php if ($member['role'] === 'Member'): ?>
                                                        <form method="POST" class="inline-block">
                                                            <input type="hidden" name="user_id" value="<?php echo $member['user_id']; ?>">
                                                            <button type="submit" name="make_admin" value="make_admin"
                                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                                                <i class="fas fa-user-shield mr-2"></i> Make Admin
                                                            </button>
                                                        </form>
                                                    <?php elseif ($member['role'] === 'Admin'): ?>
                                                        <form method="POST" class="inline-block">
                                                            <input type="hidden" name="user_id" value="<?php echo $member['user_id']; ?>">
                                                            <button type="submit" name="make_member" value="make_member"
                                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                                                <i class="fas fa-user-minus mr-2"></i> Make Member
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($members)): ?>
                                            <tr>
                                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                                    <p>No members found</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
