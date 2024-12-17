<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id'])|| $_SESSION['user_role'] != 2) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$plans_query = "SELECT rp.plan_id, rp.title, rp.description
                FROM reading_plans_gbl rp
                JOIN user_reading_plans_gbl urp ON rp.plan_id = urp.plan_id
                WHERE urp.user_id = ?";
$plans_stmt = $conn->prepare($plans_query);
$plans_stmt->bind_param("i", $user_id);
$plans_stmt->execute();
$plans_result = $plans_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Chapters as Read - The Good Book Log</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=Handlee&display=swap" rel="stylesheet">
    <style>
        :root {
    --color-bronze: #8B7355;
    --color-gold: #DAA520;
    --bg-light: #f9f9f9;
    --text-dark: #333;
    --white: #fff;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Fredoka', sans-serif;
    background-color: var(--bg-light);
    color: var(--text-dark);
    line-height: 1.6;
}

/* Dashboard Container */
.dashboard-container {
    display: flex;
}

/* Sidebar */
.sidebar {
    flex: 0 0 260px;
    background: var(--color-bronze);
    color: var(--white);
    min-height: 100vh;
}

/* Main Content */
.main-content {
    flex-grow: 1;
    padding: 2rem;
    margin-left: 260px;
}

/* Page Title */
.page-title {
    text-align: center;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--color-bronze);
}

/* Plans Grid */
.plans-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Plan Card (Horizontal Layout) */
.plan-card {
    display: flex;
    background-color: var(--white);
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: box-shadow 0.3s ease;
    max-height: 200px;
}

.plan-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.plan-header {
    flex: 0 0 250px;
    background-color: var(--color-bronze);
    color: var(--white);
    padding: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.plan-title {
    font-size: 1.1rem;
    font-weight: 600;
}

.plan-description {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-top: 0.5rem;
}

.plan-content {
    flex-grow: 1;
    padding: 1rem;
    overflow-y: auto;
}

/* Chapters Grid */
.chapters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
    max-height: 120px; /* Prevent grid from growing too tall */
    overflow-y: auto;
}

.chapter-item {
    background-color: var(--bg-light);
    border: 1px solid #ddd;
    border-radius: 6px;
    text-align: center;
    font-size: 0.8rem;
    padding: 0.5rem;
    transition: all 0.2s ease;
}

.chapter-item:hover {
    background-color: #ececec;
}

.chapter-item label {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    gap: 0.25rem;
}

.chapter-day {
    font-weight: 500;
    color: var(--color-bronze);
}

.chapter-reference {
    color: rgba(0, 0, 0, 0.6);
    font-size: 0.75rem;
}

/* Mark as Read Button */
.mark-read-btn {
    display: block;
    margin: 0 auto;
    width: 200px;
    padding: 0.6rem 1rem;
    background-color: var(--color-bronze);
    color: var(--white);
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    text-align: center;
    transition: background-color 0.3s ease;
}

.mark-read-btn:hover {
    background-color: var(--color-gold);
}

/* No Plans Message */
.no-plans {
    text-align: center;
    font-size: 1rem;
    color: var(--color-bronze);
    margin-top: 2rem;
}

/* Scrollbars for Overflow Content */
.plan-content::-webkit-scrollbar,
.chapters-grid::-webkit-scrollbar {
    width: 6px;
}

.plan-content::-webkit-scrollbar-thumb,
.chapters-grid::-webkit-scrollbar-thumb {
    background-color: var(--color-gold);
    border-radius: 6px;
}

    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include '../templates/sidebar.html'; ?>

        <div class="main-content">
            <h1 class="page-title">Mark Chapters as Read</h1>

            <?php
            if ($plans_result->num_rows > 0) {
                echo '<div class="plans-grid">';
                while ($plan = $plans_result->fetch_assoc()) {
                    $plan_id = $plan['plan_id'];
                    ?>
                    <div class="plan-card">
                        <div class="plan-header">
                            <h2 class="plan-title"><?php echo htmlspecialchars($plan['title']); ?></h2>
                            <?php if (!empty($plan['description'])): ?>
                                <p class="plan-description"><?php echo htmlspecialchars($plan['description']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="plan-content">
                            <?php
                            $chapters_query = "SELECT day_number, book, chapter, start_verse, end_verse
                                             FROM plan_chapters_gbl
                                             WHERE plan_id = ?
                                             ORDER BY day_number";
                            $chapters_stmt = $conn->prepare($chapters_query);
                            $chapters_stmt->bind_param("i", $plan_id);
                            $chapters_stmt->execute();
                            $chapters_result = $chapters_stmt->get_result();

                            if ($chapters_result->num_rows > 0): ?>
                                <form method="POST" action="mark_as_read.php">
                                    <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>">
                                    <div class="chapters-grid">
                                        <?php while ($chapter = $chapters_result->fetch_assoc()): 
                                            $chapter_id = "{$chapter['book']}_{$chapter['chapter']}_{$chapter['start_verse']}_{$chapter['end_verse']}"; 
                                        ?>
                                            <div class="chapter-item">
                                                <input type="checkbox" name="chapters[]" value="<?php echo $chapter_id; ?>" id="<?php echo $chapter_id; ?>">
                                                <label for="<?php echo $chapter_id; ?>">
                                                    <span class="chapter-day">Day <?php echo $chapter['day_number']; ?></span>
                                                    <span class="chapter-reference">
                                                        <?php echo $chapter['book'] . ' ' . $chapter['chapter']; ?>
                                                        (<?php echo $chapter['start_verse'] . '-' . $chapter['end_verse']; ?>)
                                                    </span>
                                                </label>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <button type="submit" class="mark-read-btn">Mark Selected as Read</button>
                                </form>
                            <?php else: ?>
                                <p>No chapters available for this plan.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                }
                echo '</div>'; // Close plans-grid
            } else {
                echo "<p class='no-plans'>You are not enrolled in any plans.</p>";
            }
            ?>
        </div>
    </div>

    <script src="../public/assets/js/script.js"></script>
    <script src="../public/assets/js/darkMode.js"></script>
</body>
</html>

<?php
$plans_stmt->close();
$conn->close();
?>