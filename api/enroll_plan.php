<?php
// Start the session
session_start();
include '../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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

    // Query to check if the user is already enrolled in the plan
    $check_enrollment_query = "SELECT * FROM user_reading_plans_gbl WHERE user_id = ? AND plan_id = ?";
    $check_enrollment_stmt = $conn->prepare($check_enrollment_query);
    $check_enrollment_stmt->bind_param("ii", $user_id, $plan_id);
    $check_enrollment_stmt->execute();
    $check_result = $check_enrollment_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // If the user is already enrolled, display a message
        echo "<script>alert('You are already enrolled in this plan.'); window.location.href='reading_plans.php';</script>";
    } else {
        // Query to insert the user into the user_reading_plans_gbl table to enroll
        $enroll_query = "INSERT INTO user_reading_plans_gbl (user_id, plan_id, start_date, progress, status) 
                         VALUES (?, ?, NOW(), 0, 'Not Started')";
        $enroll_stmt = $conn->prepare($enroll_query);
        $enroll_stmt->bind_param("ii", $user_id, $plan_id);

        if ($enroll_stmt->execute()) {
            // Show success message after successful enrollment
            echo "<script>alert('Enrollment successful!'); window.location.href='view_plan.php?plan_id=$plan_id';</script>";
        } else {
            echo "<script>alert('Enrollment failed. Please try again later.'); window.location.href='reading_plans.php';</script>";
        }

        $enroll_stmt->close();
    }

    // Close the check enrollment statement
    $check_enrollment_stmt->close();
}

// Close the database connection
$conn->close();
?>
