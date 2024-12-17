<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../config/config.php'; // Include your database connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$total_users = 0;
$active_users = 0;
$total_reading_plans = 0;
$total_chapters = 0;
$verses_of_the_day = 0;

// Total Users
$query = "SELECT COUNT(*) as total_users FROM users_gbl";
$result = $conn->query($query);
if ($result) {
    $total_users = $result->fetch_assoc()['total_users'];
} else {
    echo "Error fetching total users: " . $conn->error;
}

/* Active Users (last 30 days)
$query = "SELECT COUNT(*) as active_users FROM users_gbl WHERE last_login >= NOW() - INTERVAL 30 DAY";
$result = $conn->query($query);
if ($result) {
    $active_users = $result->fetch_assoc()['active_users'];
} else {
    echo "Error fetching active users: " . $conn->error;
}
    */

// Total Reading Plans
$query = "SELECT COUNT(*) as total_reading_plans FROM reading_plans_gbl"; // Adjust table name as necessary
$result = $conn->query($query);
if ($result) {
    $total_reading_plans = $result->fetch_assoc()['total_reading_plans'];
} else {
    echo "Error fetching total reading plans: " . $conn->error;
}

// Total Chapters
$query = "SELECT COUNT(*) as total_chapters FROM plan_chapters_gbl"; // Adjust table name as necessary
$result = $conn->query($query);
if ($result) {
    $total_chapters = $result->fetch_assoc()['total_chapters'];
} else {
    echo "Error fetching total chapters: " . $conn->error;
}

// Verses of the Day
$query = "SELECT COUNT(*) as verses_of_the_day FROM verse_of_the_day_gbl"; // Adjust table name as necessary
$result = $conn->query($query);
if ($result) {
    $verses_of_the_day = $result->fetch_assoc()['verses_of_the_day'];
} else {
    echo "Error fetching verses of the day: " . $conn->error;
}

// Output the values for debugging
echo "Total Users: $total_users<br>";
echo "Active Users: $active_users<br>";
echo "Total Reading Plans: $total_reading_plans<br>";
echo "Total Chapters: $total_chapters<br>";
echo "Verses of the Day: $verses_of_the_day<br>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - The Good Book Log</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=Handlee&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --color-parchment: #F4E8D1;
            --color-bronze: #CD7F32;
            --color-gold: #FFD700;
            --color-brown-900: #3E2723;
            --color-brown-800: #4E342E;
            --color-brown-600: #6D4C41;
            --color-cream-100: #FFFDD0;
            --color-cream-200: #FAFAD2;
            --font-main: 'Fredoka', sans-serif;
            --font-accent: 'Handlee', cursive;
        }
        body {
            font-family: var(--font-main);
            background-color: var(--color-parchment);
            color: var(--color-brown-900);
            margin: 0;
            padding: 0;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            position: relative;
            margin-left: 30px; /* Adjusted to match sidebar width */ 
            z-index: 0;
        }
        .main-content {
            flex-grow: 1;
            padding: 0 2rem;
            box-sizing: border-box;
        }
        .dashboard-title {
            font-family: var(--font-accent);
            color: var(--color-brown-900);
            font-size: 2rem;
            margin-top: -75px; /* Reduced top margin */
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background-color: var(--color-cream-100);
            border-radius: 8px;
            padding: 1.25rem; /* Slightly reduced padding */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card h2 {
            font-family: var(--font-accent);
            color: var(--color-bronze);
            font-size: 1.2rem;
            margin-top: 0;
            margin-bottom: 0.5rem;
        }
        .stat-card p {
            font-size: 2rem;
            font-weight: bold;
            margin: 0;
            color: var(--color-brown-900);
        }
        .chart-container {
            background-color: var(--color-cream-100);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
                max-width: 100%;
            }
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include '../templates/admin_sidebar.html'; ?>

        <div class="main-content">
            <h1 class="dashboard-title">Admin Dashboard</h1>
            <div class="stats-grid">
                <div class="stat-card">
                    <h2>Total Users</h2>
                    <p><?php echo $total_users; ?></p>
                </div>
                
                <div class="stat-card">
                    <h2>Total Reading Plans</h2>
                    <p><?php echo $total_reading_plans; ?></p>
                </div>
                <div class="stat-card">
                    <h2>Total Chapters</h2>
                    <p><?php echo $total_chapters; ?></p>
                </div>
                <div class="stat-card">
                    <h2>Verses of the Day</h2>
                    <p><?php echo $verses_of_the_day; ?></p>
                </div>
            </div>

            <div class="chart-container">
                <h2 style="font-family: var(--font-accent); color: var(--color-brown-900); margin-bottom: 1rem;">Statistics Overview</h2>
                <canvas id="statsChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('statsChart').getContext('2d');
        const statsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Users',  'Total Reading Plans', 'Total Chapters', 'Verses of the Day'],
                datasets: [{
                    label: 'Statistics',
                    data: [<?php echo $total_users; ?>, <?php echo $total_reading_plans; ?>, <?php echo $total_chapters; ?>, <?php echo $verses_of_the_day; ?>],
                    backgroundColor: [
                        'rgba(205, 127, 50, 0.7)',
                        'rgba(62, 39, 35, 0.7)',
                        'rgba(78, 52, 46, 0.7)',
                        'rgba(109, 76, 65, 0.7)'
                    ],
                    borderColor: [
                        'rgba(205, 127, 50, 1)',
                        'rgba(62, 39, 35, 1)',
                        'rgba(78, 52, 46, 1)',
                        'rgba(109, 76, 65, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

    <script src="../public/assets/js/script.js"></script>
</body>
</html>

