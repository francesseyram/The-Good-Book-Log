<?php
// Assuming you already have a connection to the database ($conn)
session_start();
include '../config/config.php';

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user ID from the session
$user_id = $_SESSION['user_id'];

// Check if the database connection is successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Query to get available plans (plans not enrolled by the user)
$available_plans_query = "SELECT rp.plan_id, rp.title, rp.duration, rp.category, rp.description 
                          FROM reading_plans_gbl rp
                          LEFT JOIN user_reading_plans_gbl urp ON rp.plan_id = urp.plan_id AND urp.user_id = ?
                          WHERE urp.plan_id IS NULL";

// Prepare the statement
$plans_stmt = $conn->prepare($available_plans_query);

// Check for errors in preparing the statement
if (!$plans_stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind the user_id to the prepared statement
$plans_stmt->bind_param("i", $user_id);

// Execute the statement
$plans_stmt->execute();

// Get the result
$plans_result = $plans_stmt->get_result();

// Query to get plans the user is enrolled in
$enrolled_query = "SELECT rp.plan_id, rp.title, rp.duration, rp.category, rp.description, urp.start_date, urp.progress, urp.status
                   FROM reading_plans_gbl rp
                   JOIN user_reading_plans_gbl urp ON rp.plan_id = urp.plan_id
                   WHERE urp.user_id = ?";

// Prepare the statement
$enrolled_stmt = $conn->prepare($enrolled_query);

// Check for errors in preparing the statement
if (!$enrolled_stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind the user_id to the prepared statement
$enrolled_stmt->bind_param("i", $user_id);

// Execute the statement
$enrolled_stmt->execute();

// Get the result
$enrolled_result = $enrolled_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading Plans - The Good Book Log</title>
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
            margin-left: 250px;
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
        .plans-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        .plan-card {
            background-color: var(--color-cream-100);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .plan-card h3 {
            font-family: var(--font-accent);
            color: var(--color-bronze);
            margin-top: 0;
        }
        .plan-card p {
            margin: 10px 0;
        }
        .plan-card a {
            display: inline-block;
            background-color: var(--color-bronze);
            color: var(--color-cream-100);
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .plan-card a:hover {
            background-color: var(--color-gold);
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
            .plans-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include '../templates/sidebar.html'; ?>
        
        <div class="main-content" style="margin-left: 250px;">
            <div class="container">
                <h1>Your Reading Plans</h1>

                <section id="available-plans">
                    <h2>Available Reading Plans</h2>
                    <div class="plans-container">
                        <?php
                        if ($plans_result->num_rows > 0) {
                            while ($plan = $plans_result->fetch_assoc()) {
                                echo "<div class='plan-card'>
                                        <h3>{$plan['title']}</h3>
                                        <p><strong>Category:</strong> {$plan['category']}</p>
                                        <p><strong>Duration:</strong> {$plan['duration']} days</p>
                                        <p>{$plan['description']}</p>
                                        <a href='enroll_plan.php?plan_id={$plan['plan_id']}'>Enroll in this plan</a>
                                      </div>";
                            }
                        } else {
                            echo "<p>No available plans.</p>";
                        }
                        ?>
                    </div>
                </section>

                <section id="enrolled-plans">
                    <h2>Your Enrolled Plans</h2>
                    <div class="plans-container">
                        <?php
                        if ($enrolled_result->num_rows > 0) {
                            while ($plan = $enrolled_result->fetch_assoc()) {
                                echo "<div class='plan-card'>
                                        <h3>{$plan['title']}</h3>
                                        <p><strong>Category:</strong> {$plan['category']}</p>
                                        <p><strong>Duration:</strong> {$plan['duration']} days</p>
                                        <p><strong>Status:</strong> {$plan['status']}</p>
                                        <p><strong>Start Date:</strong> {$plan['start_date']}</p>
                                        <div class='progress-bar'>
                                            <div class='progress-bar-fill' style='width: {$plan['progress']}%;'></div>
                                        </div>
                                        <p><strong>Progress:</strong> {$plan['progress']}%</p>
                                        <a href='view_plan.php?plan_id={$plan['plan_id']}'>View Plan</a>
                                      </div>";
                            }
                        } else {
                            echo "<p>No enrolled plans.</p>";
                        }
                        ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connections
$plans_stmt->close();
$enrolled_stmt->close();
$conn->close();
?>
