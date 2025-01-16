<?php
include 'session.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'includes/header2.php';


// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT id, name, email, role, phone_number FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle password change
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Verify current password
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $storedPassword = $row['password'];
    $stmt->close();

    if (password_verify($currentPassword, $storedPassword)) {
        if ($newPassword === $confirmPassword) {
            // Update password using BCRYPT
            $hashed_password = password_hash($newPassword, PASSWORD_BCRYPT);
            $query = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();
            $stmt->close();
            $message = 'Password updated successfully!';
        } else {
            $message = 'New passwords do not match!';
        }
    } else {
        $message = 'Current password is incorrect!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto">
            <!-- Main Profile Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header Section with Gradient -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8">
                    <h1 class="text-3xl font-bold text-white">My Profile</h1>
                    <p class="text-blue-100 mt-2">Manage your account information</p>
                </div>
                
                <!-- User Information -->
                <div class="p-8">
                    <div class="grid gap-6">
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                            <span class="w-1/3 font-semibold text-gray-600">ID:</span>
                            <span class="w-2/3 text-gray-800"><?php echo htmlspecialchars($user['id']); ?></span>
                        </div>
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                            <span class="w-1/3 font-semibold text-gray-600">Name:</span>
                            <span class="w-2/3 text-gray-800"><?php echo htmlspecialchars($user['name']); ?></span>
                        </div>
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                            <span class="w-1/3 font-semibold text-gray-600">Email:</span>
                            <span class="w-2/3 text-gray-800"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                            <span class="w-1/3 font-semibold text-gray-600">Phone:</span>
                            <span class="w-2/3 text-gray-800"><?php echo htmlspecialchars($user['phone_number']); ?></span>
                        </div>
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                            <span class="w-1/3 font-semibold text-gray-600">Role:</span>
                            <span class="w-2/3">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- Change Password Section -->
                    <div class="mt-12">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Change Password</h2>
                        <?php if ($message): ?>
                            <div class="mb-6 p-4 rounded-lg <?php echo strpos($message, 'successfully') !== false ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="space-y-6">
                            <div class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Current Password
                                    </label>
                                    <input type="password" 
                                           id="current_password" 
                                           name="current_password" 
                                           required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                                </div>
                                
                                <div>
                                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        New Password
                                    </label>
                                    <input type="password" 
                                           id="new_password" 
                                           name="new_password" 
                                           required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                                </div>
                                
                                <div>
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Confirm New Password
                                    </label>
                                    <input type="password" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           required 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                                </div>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-6 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition duration-150 hover:scale-[1.02]">
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php include 'includes/new_footer.php'; ?>