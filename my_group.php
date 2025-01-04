<?php
// Include session, database connection, and header
include 'session.php';
include 'db.php';
include 'includes/header2.php';

// Check if the user is logged in
if (!isLoggedIn()) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$user_id = getUserId();

// Query to get all group details and member count
$allGroupsQuery = "
    SELECT 
        g.group_id, 
        g.group_name, 
        g.dps_type, 
        g.amount AS installment, 
        COUNT(gm.user_id) AS members_count 
    FROM 
        my_group g
    LEFT JOIN 
        group_membership gm 
    ON 
        g.group_id = gm.group_id AND gm.status = 'approved'
    GROUP BY 
        g.group_id
";

$allGroupsResult = $conn->query($allGroupsQuery);
$allGroups = [];
if ($allGroupsResult) {
    while ($row = $allGroupsResult->fetch_assoc()) {
        $allGroups[] = [
            "group_id" => $row['group_id'],
            "group_name" => $row['group_name'],
            "dps_type" => $row['dps_type'],
            "installment" => $row['installment'],
            "members_count" => $row['members_count']
        ];
    }
}

// Query to get the user's joined groups
$joinedGroupsQuery = "
    SELECT 
        g.group_id, 
        g.group_name, 
        g.dps_type, 
        g.amount AS installment, 
        COUNT(gm2.user_id) AS members_count,
        CASE WHEN g.group_admin_id = ? THEN 1 ELSE 0 END AS is_admin
    FROM 
        group_membership gm
    INNER JOIN 
        my_group g 
    ON 
        gm.group_id = g.group_id
    LEFT JOIN 
        group_membership gm2 
    ON 
        g.group_id = gm2.group_id AND gm2.status = 'approved'
    WHERE 
        gm.user_id = ? AND gm.status = 'approved'
    GROUP BY 
        g.group_id
";

$stmt = $conn->prepare($joinedGroupsQuery);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$joinedGroupsResult = $stmt->get_result();
$joinedGroups = [];
if ($joinedGroupsResult) {
    while ($row = $joinedGroupsResult->fetch_assoc()) {
        $joinedGroups[] = [
            "group_id" => $row['group_id'],
            "group_name" => $row['group_name'],
            "dps_type" => $row['dps_type'],
            "installment" => $row['installment'],
            "members_count" => $row['members_count'],
            "is_admin" => (bool)$row['is_admin']
        ];
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f6f8fd 0%, #f1f4f9 100%);
            min-height: 100vh;
        }
        .group-card {
            max-width: 350px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .group-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        function showGroups(type) {
            document.getElementById('all-groups').style.display = type === 'all' ? 'flex' : 'none';
            document.getElementById('joined-groups').style.display = type === 'joined' ? 'flex' : 'none';
        }
    </script>
</head>
<body class="font-inter">
    <div class="container mx-auto px-6 py-16">
        <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-gray-800 mb-4">Groups</h1>
            <div class="flex justify-center gap-4">
                <button class="bg-indigo-600 text-white py-2 px-4 rounded" onclick="showGroups('all')">All Groups</button>
                <button class="bg-indigo-600 text-white py-2 px-4 rounded" onclick="showGroups('joined')">My Groups</button>
            </div>
        </div>

        <div id="all-groups" class="flex flex-wrap justify-center gap-8">
            <?php if (!empty($allGroups)): ?>
                <?php foreach ($allGroups as $group): ?>
                    <div class="group-card rounded-xl p-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($group['group_name']); ?></h3>
                        <p class="text-gray-600">Type: <?php echo htmlspecialchars($group['dps_type']); ?></p>
                        <p class="text-gray-600">Installment: $<?php echo htmlspecialchars($group['installment']); ?></p>
                        <p class="text-gray-600">Members: <?php echo htmlspecialchars($group['members_count']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No groups available.</p>
            <?php endif; ?>
        </div>

        <div id="joined-groups" class="flex flex-wrap justify-center gap-8" style="display: none;">
            <?php if (!empty($joinedGroups)): ?>
                <?php foreach ($joinedGroups as $group): ?>
                    <div class="group-card rounded-xl p-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($group['group_name']); ?></h3>
                        <p class="text-gray-600">Type: <?php echo htmlspecialchars($group['dps_type']); ?></p>
                        <p class="text-gray-600">Installment: $<?php echo htmlspecialchars($group['installment']); ?></p>
                        <p class="text-gray-600">Members: <?php echo htmlspecialchars($group['members_count']); ?></p>
                        <p class="text-gray-600">Admin: <?php echo $group['is_admin'] ? 'Yes' : 'No'; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">You have not joined any groups yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
