<?php
session_start();
include '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if plan_id is set in the POST request
if (!isset($_POST['plan_id'])) {
    die("Error: plan_id is not set.");
}

$user_id = $_SESSION['user_id'];
$plan_id = $_POST['plan_id']; // Get the plan ID from the request
$chapters = $_POST['chapters']; // Array of chapter IDs marked as read

// Update the database to mark chapters as read
foreach ($chapters as $chapter_string) {
    // Split the chapter string to get book, chapter, start verse, and end verse
    list($book, $chapter, $start_verse, $end_verse) = explode('_', $chapter_string);

    // Log the chapter details being processed
    file_put_contents('debug.log', "Processing: Book: $book, Chapter: $chapter, Start Verse: $start_verse, End Verse: $end_verse\n", FILE_APPEND);

    // Fetch the corresponding chapter_id from plan_chapters_gbl
    $chapter_query = "SELECT chapter_id FROM plan_chapters_gbl WHERE plan_id = ? AND book = ? AND chapter = ? AND start_verse = ? AND (end_verse = ? OR end_verse IS NULL)";
    $chapter_stmt = $conn->prepare($chapter_query);
    $chapter_stmt->bind_param("issis", $plan_id, $book, $chapter, $start_verse, $end_verse);
    $chapter_stmt->execute();
    $chapter_result = $chapter_stmt->get_result();

    if ($chapter_row = $chapter_result->fetch_assoc()) {
        $chapter_id = $chapter_row['chapter_id'];

        // Log the chapter_id being processed
        file_put_contents('debug.log', "Found chapter_id: $chapter_id for user_id: $user_id, plan_id: $plan_id\n", FILE_APPEND);

        // Check if the chapter_id already exists in read_chapters
        $check_query = "SELECT COUNT(*) as count FROM read_chapters WHERE user_id = ? AND plan_id = ? AND chapter_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("iii", $user_id, $plan_id, $chapter_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $check_row = $check_result->fetch_assoc();

        if ($check_row['count'] == 0) {
            // Log the chapter_id being inserted
            file_put_contents('debug.log', "Inserting chapter_id: $chapter_id for user_id: $user_id, plan_id: $plan_id\n", FILE_APPEND);

            // Insert into read_chapters
            $insert_query = "INSERT INTO read_chapters (user_id, plan_id, chapter_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE chapter_id = chapter_id"; // Prevent duplicates
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iii", $user_id, $plan_id, $chapter_id);
            $insert_stmt->execute();
        } else {
            // Log that the chapter_id already exists
            file_put_contents('debug.log', "Chapter_id: $chapter_id already exists for user_id: $user_id, plan_id: $plan_id\n", FILE_APPEND);
        }
    } else {
        // Log that the chapter_id was not found
        file_put_contents('debug.log', "Chapter not found for book: $book, chapter: $chapter, start_verse: $start_verse, end_verse: $end_verse\n", FILE_APPEND);
    }
}

// Calculate the new progress
$progress_query = "SELECT COUNT(*) as total_chapters FROM plan_chapters_gbl WHERE plan_id = ?";
$progress_stmt = $conn->prepare($progress_query);
$progress_stmt->bind_param("i", $plan_id);
$progress_stmt->execute();
$total_result = $progress_stmt->get_result();
$total_chapters = $total_result->fetch_assoc()['total_chapters'];

// Debugging output for total chapters
file_put_contents('debug.log', "Total Chapters Query: $progress_query, Total Chapters: $total_chapters\n", FILE_APPEND);

// Count the read chapters
$read_query = "SELECT COUNT(DISTINCT chapter_id) as read_chapters FROM read_chapters WHERE user_id = ? AND plan_id = ?";
$read_stmt = $conn->prepare($read_query);
$read_stmt->bind_param("ii", $user_id, $plan_id);
$read_stmt->execute();
$read_result = $read_stmt->get_result();
$read_chapters = $read_result->fetch_assoc()['read_chapters'];

// Debugging output for read chapters
file_put_contents('debug.log', "Read Chapters Query: $read_query, Read Chapters: $read_chapters\n", FILE_APPEND);

// Calculate progress percentage
if ($total_chapters > 0) {
    $progress_percentage = ($read_chapters / $total_chapters) * 100;

    // Ensure progress does not exceed 100%
    if ($progress_percentage > 100) {
        $progress_percentage = 100;
    }
} else {
    $progress_percentage = 0; // Set to 0 if there are no chapters
}

// Update the progress in the reading plans table
$update_progress_query = "UPDATE user_reading_plans_gbl SET progress = ? WHERE user_id = ? AND plan_id = ?";
$update_progress_stmt = $conn->prepare($update_progress_query);
$update_progress_stmt->bind_param("dii", $progress_percentage, $user_id, $plan_id);
$update_progress_stmt->execute();

// Debugging output
file_put_contents('debug.log', "Total Chapters: $total_chapters, Read Chapters: $read_chapters, Progress Percentage: $progress_percentage\n", FILE_APPEND);

// Redirect or return a response
header("Location: reading_plans.php"); // Redirect back to the reading plans page
exit();
?>
