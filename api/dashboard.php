<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit;
}

// Assuming you fetch the username from the session or database
$userName = $_SESSION['user_email'];
$first_name = $_SESSION['first_name'] ; // Or fetch from DB if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - The Good Book Log</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <div id="sidebar-container">
            <?php include '../templates/sidebar.html'; ?>
            <!-- Sidebar content can go here -->
        </div>
        <div class="main-content">
            <header>
                <h2>Welcome, <?php echo htmlspecialchars($first_name); ?></h2>
            </header>
            <div class="dashboard-stats">
                <h3>Your Reading Stats</h3>
                <p>Books read: 3</p>
                <p>Chapters completed: 45</p>
                <p>Verses memorized: 12</p>
            </div>
            <!-- Add more dashboard content here -->
        </div>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>
