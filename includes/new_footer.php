<!DOCTYPE html>
<html>
<head>
    <style>
        /* Reset default margins and ensure full height */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* Main wrapper to create sticky footer */
        .wrapper {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Content area that will grow to push footer down */
        .content {
            flex: 1;
        }

        .footer {
            width: 100%;
            background-color: rgb(249, 249, 249);
            border-top: 1px solid rgb(229, 229, 229);
            padding: 12px 0;
            text-align: center;
            /* Remove position fixed as we're using flex */
            margin-top: auto;
        }

        .footer-nav {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .footer-link {
            color: rgb(88, 88, 88);
            text-decoration: none;
            font-size: 14px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .footer-link:hover {
            color: rgb(51, 51, 51);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="content">
            <!-- Your page content goes here -->
        </div>

        <footer class="footer">
            <nav class="footer-nav">
                <ul class="footer-links">
                    <li><a href="#" class="footer-link">Help</a></li>
                    <li><a href="#" class="footer-link">Status</a></li>
                    <li><a href="#" class="footer-link">About</a></li>
                    <li><a href="#" class="footer-link">Careers</a></li>
                    <li><a href="#" class="footer-link">Press</a></li>
                    <li><a href="#" class="footer-link">Blog</a></li>
                    <li><a href="#" class="footer-link">Privacy</a></li>
                    <li><a href="#" class="footer-link">Terms</a></li>
                    <li><a href="#" class="footer-link">Text to speech</a></li>
                    <li><a href="#" class="footer-link">Teams</a></li>
                </ul>
            </nav>
        </footer>
    </div>
</body>
</html>