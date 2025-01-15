<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroupVest - Group Savings & Investment Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1a365d 0%, #2563eb 100%);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/header2.php' ?>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 gradient-bg">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 text-center md:text-left text-white">
                    <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">
                        Save & Invest Together, Grow Together
                    </h1>
                    <p class="text-lg md:text-xl mb-8 text-blue-100">
                        Join a community of smart savers and investors. Pool resources, share knowledge, and achieve your financial goals together.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center md:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="/register" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                            Start Saving Now
                        </a>
                        <a href="#how-it-works" class="border border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 mt-12 md:mt-0">
                    <img src="4.jpg" alt="Group Savings Illustration" class="rounded-lg shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Why Choose GroupVest</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="feature-card bg-white p-6 rounded-lg shadow-md transition duration-300">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-piggy-bank text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Group Savings</h3>
                    <p class="text-gray-600">Pool resources with trusted friends and family to reach your savings goals faster.</p>
                </div>
                <div class="feature-card bg-white p-6 rounded-lg shadow-md transition duration-300">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Smart Investments</h3>
                    <p class="text-gray-600">Access curated investment opportunities and make informed decisions together.</p>
                </div>
                <div class="feature-card bg-white p-6 rounded-lg shadow-md transition duration-300">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Secure Platform</h3>
                    <p class="text-gray-600">Bank-level security and transparent tracking of all group transactions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Create Group</h3>
                    <p class="text-gray-600">Start a savings group and invite trusted members</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Set Goals</h3>
                    <p class="text-gray-600">Define savings targets and contribution schedules</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Save Together</h3>
                    <p class="text-gray-600">Make regular contributions and track progress</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-xl font-bold">4</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Invest & Grow</h3>
                    <p class="text-gray-600">Choose investments and watch your money grow</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">What Our Users Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <img src="/api/placeholder/40/40" alt="User" class="rounded-full">
                        <div class="ml-4">
                            <h4 class="font-semibold">Sarah Johnson</h4>
                            <div class="text-yellow-400">★★★★★</div>
                        </div>
                    </div>
                    <p class="text-gray-600">"GroupVest made it easy for our family to save together for our annual vacation. The transparency and organization are fantastic!"</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <img src="/api/placeholder/40/40" alt="User" class="rounded-full">
                        <div class="ml-4">
                            <h4 class="font-semibold">Michael Chen</h4>
                            <div class="text-yellow-400">★★★★★</div>
                        </div>
                    </div>
                    <p class="text-gray-600">"The investment features are incredible. Our investment club has seen great returns thanks to the collective decision-making tools."</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <img src="/api/placeholder/40/40" alt="User" class="rounded-full">
                        <div class="ml-4">
                            <h4 class="font-semibold">Emma Davis</h4>
                            <div class="text-yellow-400">★★★★★</div>
                        </div>
                    </div>
                    <p class="text-gray-600">"Started saving with my roommates for house expenses. It's so much easier now to manage our shared financial goals!"</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-white mb-8">Ready to Start Your Group Savings Journey?</h2>
            <a href="/register" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300 inline-block">
                Create Your Group Now
            </a>
            <p class="text-blue-100 mt-4">No credit card required • Free to get started</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">GroupVest</h3>
                    <p class="text-gray-400">Making group savings and investments easy, secure, and rewarding.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white">Features</a></li>
                        <li><a href="#how-it-works" class="text-gray-400 hover:text-white">How It Works</a></li>
                        <li><a href="#testimonials" class="text-gray-400 hover:text-white">Testimonials</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Security</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2">
                        <li class="text-gray-400">Email: support@groupvest.com</li>
                        <li class="text-gray-400">Phone: (555) 123-4567</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 GroupVest. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 10) {
                header.classList.add('shadow');
            } else {
                header.classList.remove('shadow');
            }
        });
    </script>
</body>
</html>