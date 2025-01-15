<?php
// Include the database connection file and session management
include 'db.php'; // This will handle database connection
include 'session.php'; // This will handle session start and checks

// Initialize variables
$email = '';
$password = '';
$error_message = '';
$success_message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password']);

        // Validate input
        if (empty($email) || empty($password)) {
            throw new Exception("Email and password are required.");
        }

        // Prepare SQL query to check if the user exists
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists and password is correct
        if ($result->num_rows === 0) {
            throw new Exception("Invalid email or password.");
        }

        $user = $result->fetch_assoc();

        // Verify the password
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password.");
        }

        // Start session and store user data
        $_SESSION['user_id'] = $user['id'];

        // Set success message
        $success_message = "Login successful. Redirecting...";

        // Redirect to dashboard after successful login
        header('Location: /test_project/user_landing_page.php');
        exit();

    } catch (Exception $e) {
        // Set error message
        $error_message = $e->getMessage();
    }
}
?>

<?php include 'includes/header.php'; ?>

<main class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-4xl bg-white shadow-xl rounded-lg flex flex-col md:flex-row">
        
        <!-- Left side: Image -->
        <div class="hidden md:block w-full md:w-1/2 rounded-lg overflow-hidden">
            <img src="/test_project/assets/images/login.png" alt="Login Image" class="w-full h-full object-cover">
        </div>

        <!-- Right side: Form -->
        <div class="w-full md:w-1/2 flex flex-col space-y-6 p-8">
            <h2 class="text-3xl font-semibold text-center text-gray-900">Login to <span class="text-blue-600">CholoSave</span></h2>

            <!-- Display Success or Error Messages -->
            <?php if ($success_message): ?>
                <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4">
                    <?php echo $success_message; ?>
                </div>
            <?php elseif ($error_message): ?>
                <div class="bg-red-100 text-red-800 p-4 rounded-md mb-4">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" class="space-y-6">
                
                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required 
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400 transition ease-in-out duration-150"
                        value="<?php echo htmlspecialchars($email); ?>">
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required 
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400 transition ease-in-out duration-150">
                </div>

                <!-- Login Button -->
                <div>
                    <button type="submit" class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition transform duration-300 ease-in-out hover:scale-105">
                        Login
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-600">Don't have an account? 
                <a href="/test_project/register.php" class="text-blue-600 hover:underline">Register here</a>
            </p>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
