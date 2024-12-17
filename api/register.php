<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $first_name = htmlspecialchars(trim($_POST['firstName']));
    $last_name = htmlspecialchars(trim($_POST['lastName']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirmPassword']);

    // Validate inputs
    $errors = [];
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = 'All fields are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $default_role = 2; // Set default role to 2

        // Check if email already exists
        $check_email = "SELECT user_id FROM users_gbl WHERE email = ?";
        $stmt = $conn->prepare($check_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo "<p style='color: red;'>Email is already registered.</p>";
            $stmt->close();
        } else {
            $stmt->close();

            // Insert user into the database with default role
            $sql = "INSERT INTO users_gbl (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssssi", $first_name, $last_name, $email, $hashed_password, $default_role);
                if ($stmt->execute()) {
                    // Registration successful, set session variables
                    $_SESSION['user_email'] = $email;
                    $_SESSION['first_name'] = $first_name;
                    $_SESSION['last_name'] = $last_name;
                    $_SESSION['user_role'] = $default_role;

                    header("Location: login.php");
                    exit();
                } else {
                    echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
            }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - The Good Book Log</title>
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
                <h2>Register</h2>
                <form id="registerForm" action="register.php" method="post">
                    <div class="form-group">
                        <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                    </div>
                    <button type="submit">Register</button>
                </form>
                <div class="auth-links">
                    <p>Already have an account? <a href="login.php">Login</a></p>
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
