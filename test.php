<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Human Stories & Ideas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: #fff;
            min-height: 100vh;
        }

        header {
            padding: 2rem;
            position: fixed;
            width: 100%;
            top: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.2rem;
            font-weight: 500;
            color: #333;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #4CAF50;
        }

        main {
            padding-top: 120px;
            max-width: 1200px;
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .hero-content {
            max-width: 600px;
        }

        h1 {
            font-size: 4rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2rem;
            background-color: #1a1a1a;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .cta-button:hover {
            background-color: #333;
        }

        .decoration {
            position: relative;
        }

        .flower {
            width: 150px;
            height: 150px;
            background-color: #4CAF50;
            border-radius: 50%;
            position: relative;
            transform: rotate(45deg);
        }

        .geometric {
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border: 2px solid #1a1a1a;
            transform: rotate(-15deg);
        }

        @media (max-width: 768px) {
            main {
                flex-direction: column;
                text-align: center;
                padding-top: 100px;
            }

            h1 {
                font-size: 3rem;
            }

            .decoration {
                margin-top: 3rem;
            }

            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php
    // You can add PHP logic here, for example:
    $pageTitle = "Human Stories & Ideas";
    $subtitle = "A place to read, write, and deepen your understanding";
    $currentYear = date("Y");
    ?>

    <header>
      <?php include('includes/header2.php') ?>
    </header>

    <main>
        <div class="hero-content">
            <h1><?php echo $pageTitle; ?></h1>
            <p class="subtitle"><?php echo $subtitle; ?></p>
            <a href="#start" class="cta-button">Start reading</a>
        </div>
        <div class="decoration">
            <div class="flower"></div>
            <div class="geometric"></div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo $currentYear; ?> Human Stories & Ideas. All rights reserved.</p>
    </footer>
</body>
</html>


