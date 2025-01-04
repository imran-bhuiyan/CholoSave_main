<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /test_project/login.php");
    exit();
}
include 'includes/header2.php'; 

?>


<main class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Display User ID in the Corner -->
        <?php
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            echo '<div class="absolute top-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-full shadow-md">';
            echo 'User ID: ' . $user_id;
            echo '</div>';
        }
        ?>

        <!-- Hero Section -->
        <section class="text-center">
            <h1 class="text-4xl font-bold text-blue-600">Welcome to CholoSave!</h1>
            <p class="mt-4 text-lg text-gray-600">Your financial journey starts here.</p>
        </section>

        <!-- Video Section -->
        <section class="mt-12">
            <h2 class="text-2xl font-semibold text-center">Watch This Video to Get Started</h2>
            <div class="flex justify-center mt-6">
                <!-- Replace with your video embed link -->
                <iframe width="1903" height="748" src="https://www.youtube.com/embed/Gw9kMQMWZ88" 
                    title="Group Savings - Taking Group Contributions To The Next Level" 
                    frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
        </section>

        <!-- Motivation Section -->
        <section class="mt-16 bg-blue-50 py-12 rounded-lg shadow-md">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-3xl font-bold text-blue-600">Stay Motivated!</h2>
                <p class="mt-6 text-xl text-gray-700">“The best way to predict the future is to create it.” – Abraham Lincoln</p>
                <p class="mt-4 text-lg text-gray-600">Keep pushing, stay focused, and achieve your goals. You’ve got this!</p>
            </div>
        </section>

        <!-- Documentation Section -->
        <section class="mt-16">
            <h2 class="text-2xl font-semibold text-center">Documentation</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                <!-- Documentation Items -->
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition">
                    <h3 class="text-lg font-semibold text-gray-800">Getting Started</h3>
                    <p class="mt-2 text-gray-600">A guide to help you get started with using CholoSave.</p>
                    <a href="/documentation/getting-started.php" class="mt-4 inline-block text-blue-600 hover:underline">Read More</a>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition">
                    <h3 class="text-lg font-semibold text-gray-800">Managing Groups</h3>
                    <p class="mt-2 text-gray-600">Learn how to create and manage groups for your savings.</p>
                    <a href="/documentation/groups.php" class="mt-4 inline-block text-blue-600 hover:underline">Read More</a>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition">
                    <h3 class="text-lg font-semibold text-gray-800">Saving & Investments</h3>
                    <p class="mt-2 text-gray-600">Understand how to make contributions and investments in your group.</p>
                    <a href="/documentation/savings-investments.php" class="mt-4 inline-block text-blue-600 hover:underline">Read More</a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
