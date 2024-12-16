<?php
session_start();
include '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_query = "SELECT first_name FROM users_gbl WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_name = $user['first_name'];

// Fetch verse of the day
$verse_query = "SELECT text FROM verse_of_the_day_gbl ORDER BY RAND() LIMIT 1";
$verse_stmt = $conn->prepare($verse_query);
$verse_stmt->execute();
$verse_result = $verse_stmt->get_result();
$verse_of_the_day = $verse_result->fetch_assoc()['text'];

// Fetch all active reading plans and their progress
$plans_query = "SELECT rp.title, urp.progress FROM reading_plans_gbl rp JOIN user_reading_plans_gbl urp ON rp.plan_id = urp.plan_id WHERE urp.user_id = ?";
$plans_stmt = $conn->prepare($plans_query);
$plans_stmt->bind_param("i", $user_id);
$plans_stmt->execute();
$plans_result = $plans_stmt->get_result();
$active_plans = $plans_result->fetch_all(MYSQLI_ASSOC);

// Prepare data for charts
$plan_titles = array_column($active_plans, 'title');
$plan_progress = array_column($active_plans, 'progress');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../public/assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
</head>
<body>
    <div class="dashboard-container">
        <?php include '../templates/sidebar.html'; ?>

        <div class="main-content">
            <h1 class="welcome-message">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>

            <div class="charts-container">
                <div class="chart-wrapper">
                    <canvas id="barChart"></canvas> <!-- Bar Chart -->
                </div>
                <div class="chart-wrapper">
                    <canvas id="pieChart"></canvas> <!-- Pie Chart -->
                </div>
            </div>

            <div class="verse-of-the-day">
                <h2>Verse of the Day</h2>
                <p><?php echo htmlspecialchars($verse_of_the_day); ?></p>
            </div>

            <!--
            <div class="active-plans">
                <h2>Active Reading Plans</h2>
                <ul>
                    <?php //foreach ($active_plans as $plan): ?>
                        <li><?php //echo htmlspecialchars($plan['title']); ?> - Progress: <?php //echo htmlspecialchars($plan['progress']); ?>%</li>
                    <?php //endforeach; ?>
                </ul>
            </div>-->
        </div>
    </div>

    <script>
        // Bar Chart
        const ctxBar = document.getElementById('barChart').getContext('2d');
        const planTitles = <?php echo json_encode($plan_titles); ?>; // Fetch plan titles
        const planProgress = <?php echo json_encode($plan_progress); ?>; // Fetch plan progress

        const barChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: planTitles,
                datasets: [{
                    label: 'Reading Progress',
                    data: planProgress,
                    backgroundColor: 'rgba(139, 69, 19, 0.6)', // Brown color
                    borderColor: 'rgba(139, 69, 19, 1)', // Darker brown
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100, // Assuming progress is out of 100
                        title: {
                            display: true,
                            text: 'Progress (%)'
                        }
                    }
                }
            }
        });

        // Pie Chart
        const ctxPie = document.getElementById('pieChart').getContext('2d');
        const totalProgress = planProgress.reduce((a, b) => a + b, 0);
        const remainingProgress = 100 - totalProgress; // Assuming total progress is out of 100

        const pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Total Progress', 'Remaining'],
                datasets: [{
                    label: 'Reading Status',
                    data: [totalProgress, remainingProgress],
                    backgroundColor: [
                        'rgba(139, 69, 19, 0.6)', // Brown color for progress
                        'rgba(255, 228, 196, 0.6)' // Light beige for remaining
                    ],
                    borderColor: [
                        'rgba(139, 69, 19, 1)', // Darker brown for progress
                        'rgba(255, 228, 196, 1)' // Light beige for remaining
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>