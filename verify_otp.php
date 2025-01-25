<?php
include 'db.php';

session_start();

if (!isset($_SESSION['reset_email'])) {
    header('Location: forgot_password.php');
    exit();
}

$email = $_SESSION['reset_email'];
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp']);

    // Check OTP in database
    $query = "SELECT * FROM users WHERE email = ? AND otp = ? ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $entered_otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Valid OTP, proceed to reset password
        $_SESSION['otp_verified'] = true;
        header('Location: reset_password.php');
        exit();
    } else {
        $error_message = "Invalid or expired OTP";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 space-y-8 bg-white rounded shadow-lg">
        <h2 class="text-2xl font-bold text-center">Verify OTP</h2>
        
        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700">Enter OTP</label>
                <input type="text" name="otp" required 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                       maxlength="6" pattern="\d{6}">
            </div>
            <button type="submit" 
                    class="w-full px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Verify OTP
            </button>
        </form>
    </div>
</body>
</html>