<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit;
}

// Include configuration
include '../config/config.php';

// Query to get the Verse of the Day
$query = "SELECT * FROM verse_of_the_day_gbl ORDER BY RAND() LIMIT 1";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result) {
    // Fetch the result as an associative array
    $verse_of_the_day = mysqli_fetch_assoc($result);
} else {
    // If the query fails, handle the error
    echo "Error fetching verse of the day: " . mysqli_error($conn);
    exit;
}

// Your existing logic for fetching user info and progress tracking here (e.g., user name, progress, etc.)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bible Reading - The Good Book Log</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=Handlee&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Include the sidebar -->
        <?php include '../templates/sidebar.html'; ?>

        <div class="main-content">
            <!-- Welcome Message -->
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h1>

            <!-- Verse of the Day Section -->
            <div class="verse-of-the-day">
                <h2>Verse of the Day</h2>
                <?php if ($verse_of_the_day): ?>
                    <p><strong><?php echo htmlspecialchars($verse_of_the_day['book']); ?> <?php echo $verse_of_the_day['chapter']; ?>:<?php echo $verse_of_the_day['verse']; ?>:</strong></p>
                    <p><?php echo htmlspecialchars($verse_of_the_day['text']); ?></p>
                <?php else: ?>
                    <p>Sorry, the Verse of the Day is unavailable.</p>
                <?php endif; ?>
            </div>

            <!-- Reading Progress Section (Placeholder for your charts) -->
            <div class="reading-progress">
                <h2>Your Reading Progress</h2>
                <!-- Example progress bar (replace with actual data and logic) -->
                <p>Progress: 35% of the Bible read</p>
                <div class="progress-bar">
                    <div class="progress" style="width: 35%;"></div>
                </div>
            </div>

            <!-- Optional: Bible Completion Stats -->
            <div class="completion-stats">
                <h2>Completion Stats</h2>
                <p>You have read 35% of the Bible.</p>
                <!-- Insert charts here if desired -->
            </div>
        </div>
    </div>
</body>
</html>
