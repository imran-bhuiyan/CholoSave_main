<?php
// Include the database connection
include 'db.php';

// Initialize variables
$name = $email = $phone_number = $password = $retype_password = '';
$error_message = '';
$success_message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the form data and sanitize
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone_number = htmlspecialchars(trim($_POST['phone']));
        $password = htmlspecialchars(trim($_POST['password']));
        $retype_password = htmlspecialchars(trim($_POST['retype-password']));

        // Validate the inputs
        if (empty($name) || empty($email) || empty($phone_number) || empty($password) || empty($retype_password)) {
            throw new Exception("All fields are required.");
        }

        if ($password !== $retype_password) {
            throw new Exception("Passwords do not match.");
        }

        // Check if email already exists
        $check_email_query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            throw new Exception("Email is already registered.");
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user data into the database
        $insert_query = "INSERT INTO users (name, email, phone_number, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $name, $email, $phone_number, $hashed_password);

        if ($stmt->execute()) {
            $success_message = "User registered successfully.";
            // Redirect to dashboard after successful login
            header('Location: /test_project/user_landing_page.php');
            exit();
        } else {
            throw new Exception("Failed to register user. Please try again.");
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<?php include 'includes/header.php'; ?>

<main class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-4xl bg-white shadow-xl rounded-lg flex flex-col md:flex-row">
        
        <!-- Right side: Image -->
        <div class="hidden md:block w-full md:w-1/2 rounded-lg overflow-hidden">
            <img src="/test_project/assets/images/register.png" alt="Register Image" class="w-full h-full object-cover">
        </div>

        <!-- Left side: Form -->
        <div class="w-full md:w-1/2 flex flex-col space-y-6 p-8">
            <h2 class="text-3xl font-semibold text-center text-gray-900">Create an Account on <span class="text-blue-600">CholoSave</span></h2>

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

            <!-- Registration Form -->
            <form method="POST" class="space-y-6">
                
                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" placeholder="Enter your full name" required 
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400 transition ease-in-out duration-150"
                        value="<?php echo htmlspecialchars($name); ?>">
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required 
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400 transition ease-in-out duration-150"
                        value="<?php echo htmlspecialchars($email); ?>">
                </div>

                <!-- Phone Number Input -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" name="phone" id="phone" placeholder="Enter your phone number" required 
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400 transition ease-in-out duration-150"
                        value="<?php echo htmlspecialchars($phone_number); ?>">
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" placeholder="Create a password" required 
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400 transition ease-in-out duration-150">
                </div>

                <!-- Retype Password Input -->
                <div>
                    <label for="retype-password" class="block text-sm font-medium text-gray-700">Retype Password</label>
                    <input type="password" name="retype-password" id="retype-password" placeholder="Confirm your password" required 
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400 transition ease-in-out duration-150">
                </div>

                <!-- Register Button -->
                <div>
                    <button type="submit" class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition transform duration-300 ease-in-out hover:scale-105">
                        Register
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-600">Already have an account? 
                <a href="/test_project/login.php" class="text-blue-600 hover:underline">Login here</a>
            </p>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
