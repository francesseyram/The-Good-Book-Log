<?php
session_start();
include '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])|| $_SESSION['user_role'] != 2) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user ID from the session
$user_id = $_SESSION['user_id'];

// Check if the plan ID is provided in the URL
if (isset($_GET['plan_id'])) {
    $plan_id = $_GET['plan_id'];

    // Check if the database connection is successful
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Query to get plan details
    $plan_query = "SELECT rp.plan_id, rp.title, rp.duration, rp.category, rp.description
                   FROM reading_plans_gbl rp
                   WHERE rp.plan_id = ?";
    $plan_stmt = $conn->prepare($plan_query);
    $plan_stmt->bind_param("i", $plan_id);
    $plan_stmt->execute();
    $plan_result = $plan_stmt->get_result();

    if ($plan_result->num_rows > 0) {
        $plan = $plan_result->fetch_assoc();
    } else {
        echo "Plan not found.";
        exit();
    }

    // Query to get the chapters for this plan
    $chapters_query = "SELECT day_number, book, chapter, start_verse, end_verse
                       FROM plan_chapters_gbl
                       WHERE plan_id = ?
                       ORDER BY day_number";
    $chapters_stmt = $conn->prepare($chapters_query);
    $chapters_stmt->bind_param("i", $plan_id);
    $chapters_stmt->execute();
    $chapters_result = $chapters_stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $plan['title']; ?> - The Good Book Log</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=Handlee&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/styles.css">
    <style>
        body {
            background-color: var(--color-parchment);
            color: var(--color-brown-900);
            font-family: var(--font-main);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex-grow: 1;
            padding: 2rem;
            margin-left: 250px; /* Adjust for sidebar width */
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        h1, h2 {
            font-family: var(--font-accent);
            color: var(--color-brown-900);
        }
        .reading-list {
            margin-top: 40px;
        }
        .reading-item {
            background-color: var(--color-cream-100);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .reading-item h3 {
            font-family: var(--font-accent);
            color: var(--color-bronze);
            margin-top: 0;
        }
        .reading-item p {
            margin: 10px 0;
        }
        .progress-bar {
            background-color: var(--color-cream-200);
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
            margin-top: 10px;
        }
        .progress-bar-fill {
            background-color: var(--color-bronze);
            height: 100%;
            transition: width 0.3s ease;
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            .reading-list {
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include '../templates/sidebar.html'; ?>
        
        <div class="main-content">
            <div class="container">
                <h1><?php echo $plan['title']; ?></h1>
                <p><strong>Category:</strong> <?php echo $plan['category']; ?></p>
                <p><strong>Duration:</strong> <?php echo $plan['duration']; ?> days</p>
                <p><?php echo $plan['description']; ?></p>

                <h2>Readings for this Plan</h2>
                <div class="reading-list">
                    <?php
                    if ($chapters_result->num_rows > 0) {
                        while ($chapter = $chapters_result->fetch_assoc()) {
                            echo "<div class='reading-item'>
                                    <h3>Day {$chapter['day_number']}: {$chapter['book']} {$chapter['chapter']} ({$chapter['start_verse']}-{$chapter['end_verse']})</h3>
                                    <p><strong>Book:</strong> {$chapter['book']}</p>
                                    <p><strong>Chapter:</strong> {$chapter['chapter']}</p>
                                    <p><strong>Verses:</strong> {$chapter['start_verse']}-{$chapter['end_verse']}</p>
                                  </div>";
                        }
                    } else {
                        echo "<p>No readings available for this plan.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connections
$plan_stmt->close();
$chapters_stmt->close();
$conn->close();
?>
