<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($email) || empty($password)) {
        die("Email and password are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Retrieve user
    $query = "SELECT * FROM users_gbl WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            die("Invalid password.");
        }
    } else {
        die("No user found with this email.");
    }
}
?>
