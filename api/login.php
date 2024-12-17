<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Validate inputs
    $errors = [];
    if (empty($email) || empty($password)) {
        $errors[] = 'Email and password are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
        exit();
    }

    // Check user in the database
    $sql = "SELECT user_id, first_name, last_name, email, password, role FROM users_gbl WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Fetch user data
            $stmt->bind_result($user_id, $first_name, $last_name, $email, $hashed_password, $role);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Login successful, set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;

                // Redirect based on user role
                if ($role == 1) {
                    header("Location: admin_dashboard.php");
                } else if ($role == 2) {
                    header("Location: dashboard.php");
                } else {
                    echo "<p style='color: red;'>Invalid user role.</p>";
                    exit();
                }
                exit();
            } else {
                echo "<p style='color: red;'>Incorrect password.</p>";
            }
        } else {
            echo "<p style='color: red;'>No account found with this email.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - The Good Book Log</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/styles.css">
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

        <main class="auth-container">
            <div class="auth-form">
                <h2>Login</h2>
                <form id="loginForm" action="login.php" method="post">
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
                <div class="auth-links">
                    <p>Don't have an account? <a href="register.php">Register</a></p>
                </div>
            </div>
        </main>

        <footer>
            <div>
                <h4>About Us</h4>
                <p>Bringing ancient wisdom to modern times.</p>
            </div>
            <div>
                <h4>Contact</h4>
                <p>info@ancientwisdom.com</p>
            </div>
            <div>
                <h4>Follow Us</h4>
                <div class="social-links">
                    <a href="#" aria-label="Twitter">Twitter</a>
                    <a href="#" aria-label="Facebook">Facebook</a>
                    <a href="#" aria-label="Instagram">Instagram</a>
                </div>
            </div>
        </footer>
    </div>

    <script src="../public/assets/js/script.js"></script>
</body>
</html>
