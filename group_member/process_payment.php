<?php
// Start session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Assume you have logic here to handle payment processing
$paymentStatus = 'success'; // or 'failure' based on the payment processing logic

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .container {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn {
            display: inline-block;
            margin: 10px;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        .btn-success {
            background-color: green;
        }
        .btn-failure {
            background-color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Payment Status</h2>
    
    <?php if ($paymentStatus === 'success'): ?>
        <p>Your payment was successful!</p>
        <a href="success_page.php" class="btn btn-success">Proceed to Success Page</a>
    <?php else: ?>
        <p>Your payment failed. Please try again.</p>
        <a href="retry_payment.php" class="btn btn-failure">Retry Payment</a>
    <?php endif; ?>
</div>

</body>
</html>
