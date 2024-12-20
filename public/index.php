<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Good Book Log</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 6v12M8 12h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h1>The Good Book Log</h1>
            </div>
            <button id="darkModeToggle" aria-label="Toggle dark mode">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </header>

        <section class="hero">
            <div class="verse-container">
                <h2 id="verse">"Blessed is the one who delights in the law of the Lord, and on His law he meditates day and night."</h2>
                <p id="verse-source">Psalm 1:2 (ESV)</p>
            </div>
            <button class="cta">
                <a href="../api/register.php" style="text-decoration: none; color: inherit;">Get Started</a>
            </button>
            <button class="cta">
                <a href="../api/login.php" style="text-decoration: none; color: inherit;">Login</a>
            </button>
            
        </section>

        <section class="features">
            <div class="feature">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Read Bible</h3>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Daily Devotion</h3>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3>Plans</h3>
            </div>
        </section>

        <section class="quote">
            <h3>Quote of the Day</h3>
            <p>"The fear of the Lord is the beginning of wisdom."</p>
        </section>

        

        <section class="newsletter">
            <h3>Subscribe to Our Newsletter</h3>
            <form id="newsletterForm">
                <input type="email" placeholder="Enter your email" aria-label="Email for newsletter">
                <button type="submit">Sign Up</button>
            </form>
        </section>

        <footer>
            <div>
                <h4>About Us</h4>
                <p>Bringing ancient wisdom to modern times.</p>
            </div>
            <div>
                <h4>Contact</h4>
                <p>info@thegoodbooklog.com</p>
            </div>
            <div>
                <h4>Follow Us</h4>
                <div class="social-links">
                    <a href="https://x.com/i/flow/login" aria-label="Twitter">Twitter</a>
                    <a href="https://www.facebook.com/login.php/" aria-label="Facebook">Facebook</a>
                    <a href="https://www.instagram.com/accounts/login/?hl=en" aria-label="Instagram">Instagram</a>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
