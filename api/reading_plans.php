<?php
session_start();
include '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll'])) {
    $plan_id = $_POST['plan_id'];
    $start_date = date('Y-m-d');

    // Check if user is already enrolled
    $check_stmt = $conn->prepare("SELECT * FROM user_reading_plans_gbl WHERE user_id = ? AND plan_id = ?");
    $check_stmt->bind_param("ii", $user_id, $plan_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows == 0) {
        // Enroll user
        $insert_stmt = $conn->prepare("INSERT INTO user_reading_plans_gbl (user_id, plan_id, start_date, progress) VALUES (?, ?, ?, 0)");
        $insert_stmt->bind_param("iis", $user_id, $plan_id, $start_date);
        $insert_stmt->execute();
        $success_message = "Congratulations! You have enrolled in the plan.";
    } else {
        $error_message = "You are already enrolled in this plan.";
    }
}

// Fetch available reading plans
$plans_stmt = $conn->prepare("SELECT rp.plan_id, rp.plan_name, rp.plan_description, rp.start_date FROM reading_plans rp LEFT JOIN user_reading_plans_gbl urp ON rp.plan_id = urp.plan_id AND urp.user_id = ? WHERE urp.plan_id IS NULL");
$plans_stmt->bind_param("i", $user_id);
$plans_stmt->execute();
$plans_result = $plans_stmt->get_result();

// Fetch user's enrolled plans
$enrolled_stmt = $conn->prepare("SELECT urp.plan_id, rp.plan_name, urp.progress FROM user_reading_plans_gbl urp JOIN reading_plans rp ON urp.plan_id = rp.plan_id WHERE urp.user_id = ?");
$enrolled_stmt->bind_param("i", $user_id);
$enrolled_stmt->execute();
$enrolled_result = $enrolled_stmt->get_result();
$enrolled_plans = $enrolled_result->fetch_all(MYSQLI_ASSOC);
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
        .reading-plans-container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .reading-plans-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .reading-plans-header h1 {
            font-family: var(--font-accent);
            font-size: 2.5rem;
            color: var(--color-brown-900);
        }
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        .plan-card {
            background-color: var(--color-cream-100);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .plan-header {
            background-color: var(--color-bronze);
            color: var(--color-cream-100);
            padding: 1rem;
            text-align: center;
        }
        .plan-header h3 {
            font-family: var(--font-accent);
            font-size: 1.5rem;
            margin: 0;
        }
        .plan-content {
            padding: 1.5rem;
        }
        .plan-description {
            color: var(--color-brown-800);
            margin-bottom: 1rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .plan-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--color-brown-600);
            margin-bottom: 1rem;
        }
        .enroll-btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background-color: var(--color-bronze);
            color: var(--color-cream-100);
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .enroll-btn:hover {
            background-color: var(--color-gold);
        }
        .enroll-btn:disabled {
            background-color: var(--color-cream-300);
            cursor: not-allowed;
        }
        .progress-container {
            background-color: var(--color-cream-200);
            border-radius: 8px;
            height: 20px;
            overflow: hidden;
            margin-top: 1rem;
        }
        .progress-bar {
            height: 100%;
            background-color: var(--color-bronze);
            transition: width 0.3s ease;
        }
        #successModal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: var(--color-cream-100);
            margin: 15% auto;
            padding: 20px;
            border: 1px solid var(--color-bronze);
            border-radius: 8px;
            width: 300px;
            text-align: center;
        }
        .close-modal {
            color: var(--color-bronze);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-modal:hover {
            color: var(--color-gold);
        }
    </style>
</head>
<body>
    <?php include '../templates/sidebar.html'; ?>

    <div class="main-content">
        <div class="reading-plans-container">
            <div class="reading-plans-header">
                <h1>Bible Reading Plans</h1>
            </div>

            <h2>Available Plans</h2>
            <div class="plans-grid">
                <?php while ($plan = $plans_result->fetch_assoc()): ?>
                    <div class="plan-card">
                        <div class="plan-header">
                            <h3><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                        </div>
                        <div class="plan-content">
                            <p class="plan-description"><?php echo htmlspecialchars($plan['plan_description']); ?></p>
                            <div class="plan-meta">
                                <span>Start Date: <?php echo date('M d, Y', strtotime($plan['start_date'])); ?></span>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="plan_id" value="<?php echo $plan['plan_id']; ?>">
                                <button type="submit" class="enroll-btn" name="enroll">
                                    Enroll in Plan
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <h2>Your Enrolled Plans</h2>
            <div class="plans-grid">
                <?php foreach ($enrolled_plans as $user_plan): ?>
                    <div class="plan-card">
                        <div class="plan-header">
                            <h3><?php echo htmlspecialchars($user_plan['plan_name']); ?></h3>
                        </div>
                        <div class="plan-content">
                            <p class="plan-description">Your current progress:</p>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: <?php echo $user_plan['progress']; ?>%;"></div>
                            </div>
                            <p class="plan-meta">Progress: <?php echo $user_plan['progress']; ?>%</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div id="successModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <p id="modalMessage"></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('successModal');
            const closeModal = document.querySelector('.close-modal');
            const modalMessage = document.getElementById('modalMessage');

            <?php if (isset($success_message)): ?>
                modalMessage.textContent = "<?php echo $success_message; ?>";
                modal.style.display = 'block';
            <?php endif; ?>

            closeModal.onclick = function() {
                modal.style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>