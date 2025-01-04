<?php 
include 'includes/header.php'; 
include 'db.php'; 

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Validate inputs
    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO contact_us (name, email, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $success_message = "Thank you for your message! We'll get back to you shortly.";
        } else {
            $error_message = "Something went wrong. Please try again.";
        }
        $stmt->close();
    } else {
        $error_message = "All fields are required.";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/test_project/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <title>CholoSave</title>
</head>

<body>  

<main class="contact-us py-16 bg-gray-100">
    <!-- Contact Us Header -->
    <section class="text-center mb-16">
        <h1 class="text-4xl font-bold text-orange-500 mb-4">Contact Us</h1>
        <p class="text-lg text-gray-700">We would love to hear from you. Get in touch for any inquiries or feedback.</p>
    </section>

    <!-- Contact Form -->
    <section class="contact-form bg-white shadow-md rounded-lg max-w-4xl mx-auto p-8 mb-16">
        <h2 class="text-3xl font-semibold text-gray-800 text-center mb-8">Send Us a Message</h2>

        <!-- Success/Error Messages -->
        <?php if (!empty($success_message)): ?>
            <p class="text-green-600 text-center font-semibold mb-4"><?php echo $success_message; ?></p>
        <?php elseif (!empty($error_message)): ?>
            <p class="text-red-600 text-center font-semibold mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="#" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Your Name</label>
                    <input type="text" id="name" name="name" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" />
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Your Email</label>
                    <input type="email" id="email" name="email" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" />
                </div>
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Your Message</label>
                <textarea id="message" name="message" rows="4" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
            </div>
            <button type="submit" class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition transform duration-300 ease-in-out hover:scale-105">Send Message</button>
        </form>
    </section>

    <!-- Contact Details Section -->
    <section class="contact-details text-center max-w-4xl mx-auto px-4">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Our Office</h2>
        <div class="flex justify-center space-x-12 text-gray-700">
            <div class="flex flex-col items-center">
                <h3 class="text-xl font-medium text-gray-800">Address</h3>
                <p class="mt-2">1234 Street Name, City, Country</p>
            </div>
            <div class="flex flex-col items-center">
                <h3 class="text-xl font-medium text-gray-800">Phone</h3>
                <p class="mt-2">(123) 456-7890</p>
            </div>
            <div class="flex flex-col items-center">
                <h3 class="text-xl font-medium text-gray-800">Email</h3>
                <p class="mt-2">contact@cholosave.com</p>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
