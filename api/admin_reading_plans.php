<?php
session_start();
include '../config/config.php'; // Include your database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle reading plan deletion
if (isset($_GET['delete_plan'])) {
    $plan_id = intval($_GET['delete_plan']);
    $delete_query = "DELETE FROM reading_plans_gbl WHERE plan_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $plan_id);
    if ($stmt->execute()) {
        echo "Reading plan deleted successfully.";
    } else {
        echo "Error deleting reading plan: " . $conn->error;
    }
    $stmt->close();
}

// Handle reading plan update
if (isset($_POST['update_plan'])) {
    $plan_id = intval($_POST['plan_id']);
    $title = $_POST['title'];
    $duration = intval($_POST['duration']);
    $category = !empty($_POST['category']) ? $_POST['category'] : 'Uncategorized'; // Ensure category is not empty
    $description = $_POST['description'];

    $update_query = "UPDATE reading_plans_gbl SET title = ?, duration = ?, category = ?, description = ? WHERE plan_id = ?";
    $stmt = $conn->prepare($update_query);
    if ($stmt) {
        $stmt->bind_param("siisi", $title, $duration, $category, $description, $plan_id);
        if ($stmt->execute()) {
            echo "Reading plan updated successfully.";
        } else {
            echo "Error updating reading plan: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}


// Handle new reading plan creation
if (isset($_POST['create_plan'])) {
    $title = $_POST['title'];
    $duration = intval($_POST['duration']);
    $category = $_POST['category'];
    $description = $_POST['description'];
    
    $insert_query = "INSERT INTO reading_plans_gbl (title, duration, category, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("siis", $title, $duration, $category, $description);
    if ($stmt->execute()) {
        echo "Reading plan created successfully.";
    } else {
        echo "Error creating reading plan: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all reading plans
$query = "SELECT * FROM reading_plans_gbl";
$result = $conn->query($query);
$reading_plans = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reading_plans[] = $row;
    }
} else {
    echo "Error fetching reading plans: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading Plans Management - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=Handlee&display=swap" rel="stylesheet">
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

        .dashboard {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 280px;
            padding: 2rem;
        }

        h1, h2 {
            font-family: var(--font-accent);
            color: var(--color-brown-800);
            margin-bottom: 1.5rem;
        }

        h1 {
            font-size: 2.5rem;
            border-bottom: 2px solid var(--color-bronze);
            padding-bottom: 0.5rem;
        }

        h2 {
            font-size: 2rem;
            margin-top: 2rem;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: var(--color-cream-100);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        thead {
            background-color: var(--color-bronze);
            color: var(--color-cream-100);
        }

        th, td {
            padding: 1rem;
            text-align: left;
        }

        th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tbody tr:nth-child(even) {
            background-color: var(--color-cream-200);
        }

        tbody tr:hover {
            background-color: var(--color-gold);
            transition: background-color 0.3s ease;
        }

        a {
            color: var(--color-brown-600);
            text-decoration: none;
            margin-right: 1rem;
            transition: color 0.3s ease;
        }

        a:hover {
            color: var(--color-bronze);
        }

/* Form Container */
.form-container {
    background-color: var(--color-cream-100);
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-width: 700px;
    width: 100%;
    margin-bottom: 2rem;
}

/* Form Elements */
.form-container label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--color-brown-800);
    font-weight: 500;
}

.form-container input[type="text"],
.form-container input[type="number"],
.form-container textarea,
.form-container select {
    width: 100%;
    padding: 0.75rem;
    margin-bottom: 1rem;
    border: 1px solid var(--color-brown-600);
    border-radius: 4px;
    font-family: var(--font-main);
    font-size: 1rem;
    background-color: white;
}

.form-container textarea {
    min-height: 100px;
    resize: vertical;
}

.form-container button[type="submit"] {
    background-color: var(--color-bronze);
    color: var(--color-cream-100);
    border: none;
    padding: 1rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-container button[type="submit"]:hover {
    background-color: var(--color-brown-600);
}

/* Responsive Styles */
@media (max-width: 768px) {
    .form-container {
        padding: 1rem;
    }
}

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--color-brown-800);
            font-weight: 500;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid var(--color-brown-600);
            border-radius: 4px;
            font-family: var(--font-main);
            font-size: 1rem;
            background-color: white;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        button[type="submit"] {
            background-color: var(--color-bronze);
            color: var(--color-cream-100);
            border: none;
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: var(--color-brown-600);
        }

        @media (max-width: 768px) {
            .dashboard {
                margin-left: 0;
                padding: 1rem;
            }

            table {
                font-size: 0.9rem;
            }

            th, td {
                padding: 0.75rem;
            }

            form {
                padding: 1rem;
            }
        }
        
    </style>
</head>
<body>
    <?php include '../templates/admin_sidebar.html'; ?> <!-- Include the admin sidebar -->

    <div class="dashboard">
        <h1>Manage Reading Plans</h1>
        
        <h2>Create New Reading Plan</h2>
        <div class="form-container"> <!-- Wrapped the create form in a div -->
            <form method="POST" action="">
                <label>Title:</label>
                <input type="text" name="title" required>
                <label>Duration (days):</label>
                <input type="number" name="duration" required>
                <label>Category:</label>
                <input type="text" name="category" required>
                <label>Description:</label>
                <textarea name="description" required></textarea>
                <button type="submit" name="create_plan">Create Plan</button>
            </form>
        </div>

        <h2>Existing Reading Plans</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Duration</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reading_plans as $plan): ?>
                <tr>
                    <td><?php echo htmlspecialchars($plan['title']); ?></td>
                    <td><?php echo htmlspecialchars($plan['duration']); ?> days</td>
                    <td><?php echo htmlspecialchars($plan['category']); ?></td>
                    <td><?php echo htmlspecialchars(substr($plan['description'], 0, 100)) . '...'; ?></td>
                    <td>
                        <a href="admin_reading_plans.php?edit=<?php echo $plan['plan_id']; ?>">Edit</a>
                        <a href="admin_reading_plans.php?delete_plan=<?php echo $plan['plan_id']; ?>" onclick="return confirm('Are you sure you want to delete this reading plan?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (isset($_GET['edit'])): 
            $edit_plan_id = intval($_GET['edit']);
            $edit_query = "SELECT * FROM reading_plans_gbl WHERE plan_id = ?";
            $stmt = $conn->prepare($edit_query);
            $stmt->bind_param("i", $edit_plan_id);
            $stmt->execute();
            $edit_result = $stmt->get_result();
            $edit_plan = $edit_result->fetch_assoc();
            $stmt->close();
        ?>
        <h2>Edit Reading Plan</h2>
        <div class="form-container"> <!-- Wrapped the edit form in a div -->
            <form method="POST" action="">
                <input type="hidden" name="plan_id" value="<?php echo $edit_plan['plan_id']; ?>">
                <label>Title:</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($edit_plan['title']); ?>" required>
                <label>Duration (days):</label>
                <input type="number" name="duration" value="<?php echo htmlspecialchars($edit_plan['duration']); ?>" required>
                <label>Category:</label>
                <input type="text" name="category" value="<?php echo htmlspecialchars($edit_plan['category']); ?>" required>
                <label>Description:</label>
                <textarea name="description" required><?php echo htmlspecialchars($edit_plan['description']); ?></textarea>
                <button type="submit" name="update_plan">Update Plan</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <script src="../public/assets/js/script.js"></script>
</body>
</html>

