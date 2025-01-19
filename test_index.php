<?php include 'includes/new_header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>CholoSave</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f9;
        }

        /* Carousel Styles */
        .carousel {
            position: relative;
            height: 100vh;
            overflow: hidden;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            transition: transform 0.5s ease-in-out;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Impact Section Styles */
        .impact-section {
            padding: 5rem 0;
            background-color: #ffffff;
        }

        .impact-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .impact-card {
            background-color: #f8fafc;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .impact-card:hover {
            transform: scale(1.05);
        }

        .impact-card img {
            width: 4rem;
            height: 4rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        /* Goals Section Styles */
        .goals-section {
            padding: 5rem 0;
            background-color: #f8fafc;
        }

        .goals-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .goal-card {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Stats Section Styles */
        .stats-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #003366 0%, #004080 100%);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .stat-card {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: scale(1.05);
        }

        .stat-icon {
            width: 4rem;
            height: 4rem;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            display: inline-block;
            background-color: #1E40AF;
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1E3A8A;
        }

        @media screen and (max-width: 768px) {
            .impact-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main>
        <!-- Carousel Section -->
        <div class="carousel">
            <div class="slide">
                <img src="4.jpg" alt="Slide 1">
                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
                    <div style="text-align: center; color: white; padding: 1rem;">
                        <h1 style="font-size: 3rem; font-weight: bold; margin-bottom: 1rem;">Welcome to CholoSave</h1>
                        <p style="font-size: 1.25rem; margin-bottom: 2rem;">Discover amazing possibilities with us</p>
                        <a href="/test_project/register.php" class="btn-primary">Get Started</a>
                    </div>
                </div>
            </div>
            <div class="slide" style="transform: translateX(100%);">
                <img src="5.jpg" alt="Slide 2">
                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
                    <div style="text-align: center; color: white; padding: 1rem;">
                        <h1 style="font-size: 3rem; font-weight: bold; margin-bottom: 1rem;">Innovation at Its Best</h1>
                        <p style="font-size: 1.25rem; margin-bottom: 2rem;">Leading the way in technology</p>
                    </div>
                </div>
            </div>
            <div class="slide" style="transform: translateX(100%);">
                <img src="6.jpg" alt="Slide 3">
                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;">
                    <div style="text-align: center; color: white; padding: 1rem;">
                        <h1 style="font-size: 3rem; font-weight: bold; margin-bottom: 1rem;">Expert Solutions</h1>
                        <p style="font-size: 1.25rem; margin-bottom: 2rem;">Professional team at your service</p>
                        <a href="/test_project/expert.php" class="btn-primary">Contact</a>
                    </div>
                </div>
            </div>

            <!-- Carousel Navigation -->
            <button onclick="previousSlide()" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); background: white; padding: 0.5rem; border-radius: 50%; border: none; cursor: pointer;">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="nextSlide()" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: white; padding: 0.5rem; border-radius: 50%; border: none; cursor: pointer;">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Carousel Indicators -->
            <div style="position: absolute; bottom: 1rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.5rem;">
                <button onclick="goToSlide(0)" class="w-3 h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition"></button>
                <button onclick="goToSlide(1)" class="w-3 h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition"></button>
                <button onclick="goToSlide(2)" class="w-3 h-3 rounded-full bg-white opacity-50 hover:opacity-100 transition"></button>
            </div>
        </div>

        <!-- Impact Section -->
        <section class="impact-section">
            <h2 style="text-align: center; font-size: 2rem; font-weight: bold; margin-bottom: 3rem;">How We Make an Impact</h2>
            <div class="impact-grid">
                <!-- Impact cards content -->
                <div class="impact-card">
                    <img src="/test_project/assets/images/cloab.png" alt="Collaboration">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Collaboration</h3>
                    <p style="color: #4B5563;">By pooling resources and working together, we unlock greater investment opportunities and savings potential for everyone involved.</p>
                </div>
                <!-- Add other impact cards similarly -->
            </div>
        </section>

        <!-- Goals Section -->
        <section class="goals-section">
            <h2 style="text-align: center; font-size: 2rem; font-weight: bold; margin-bottom: 3rem;">Our Goals</h2>
            <div class="goals-container">
                <!-- Goal cards content -->
                <div class="goal-card">
                    <img src="/test_project/assets/images/1.jpg" alt="Goal 1" style="width: 3rem; height: 3rem; border-radius: 0.5rem;">
                    <p style="font-size: 1.125rem; color: #4B5563;">Provide accessible financial tools for everyone.</p>
                </div>
                <!-- Add other goal cards similarly -->
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <h2 style="text-align: center; font-size: 2rem; font-weight: bold; margin-bottom: 3rem;">Our Growing Community</h2>
            <div class="stats-grid">
                <!-- Stats cards content -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Total Users</h3>
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;">
                        <span id="userCount" data-target="15234">0</span>
                    </div>
                    <p>Active members in our community</p>
                </div>
                <!-- Add other stat cards similarly -->
            </div>
        </section>
    </main>

    <script>
        // Carousel functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');

        function updateSlides() {
            slides.forEach((slide, index) => {
                if (index === currentSlide) {
                    slide.style.transform = 'translateX(0)';
                } else if (index < currentSlide) {
                    slide.style.transform = 'translateX(-100%)';
                } else {
                    slide.style.transform = 'translateX(100%)';
                }
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            updateSlides();
        }

        function previousSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            updateSlides();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateSlides();
        }

        // Auto-advance carousel
        setInterval(nextSlide, 5000);

        // Stats counter animation
        const countElements = document.querySelectorAll('#userCount, #groupCount');

        const animateValue = (element, start, end, duration) => {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const current = Math.floor(progress * (end - start) + start);
                element.textContent = new Intl.NumberFormat().format(current);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    animateValue(entry.target, 0, target, 2000);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });

        countElements.forEach(counter => {
            observer.observe(counter);
        });
    </script>
</body>
</html>