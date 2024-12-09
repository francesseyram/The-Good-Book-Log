<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $first_name = htmlspecialchars(trim($_POST['fname'])); // Adjusted to match "fname"
    $last_name = htmlspecialchars(trim($_POST['lname']));  // Adjusted to match "lname"
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if (strlen($password) < 8) {
        die("Password must be at least 8 characters long.");
    }

    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_user = $db->prepare("SELECT * FROM users_gbl WHERE email = ?");
    $check_user->execute([$email]);

    if ($check_user->rowCount() > 0) {
        die("Email already exists.");
    }

    // Insert into database
    $query = "INSERT INTO users_gbl (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);

    if ($stmt->execute([$first_name, $last_name, $email, $hashed_password])) {
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'User'; // Default role
        header("Location: dashboard.php");
        exit();
    } else {
        die("Error registering user.");
    }
}
?>
