<?php
session_start();
include '../config/config.php'; // Include your database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $delete_query = "DELETE FROM users_gbl WHERE user_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user: " . $conn->error;
    }
    $stmt->close();
}

// Handle user update
if (isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $role = intval($_POST['role']);

    // Check if the email is already taken by another user
    $check_email_query = "SELECT user_id FROM users_gbl WHERE email = ? AND user_id != ?";
    $stmt_check = $conn->prepare($check_email_query);
    $stmt_check->bind_param("si", $email, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        echo "Email already exists for another user.";
        exit();
    }
    $stmt_check->close();

    // Update user
    $update_query = "UPDATE users_gbl SET first_name = ?, last_name = ?, email = ?, role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_query);
    if ($stmt) {
        $stmt->bind_param("sssii", $first_name, $last_name, $email, $role, $user_id);
        if ($stmt->execute()) {
            echo "User updated successfully.";
        } else {
            echo "Error updating user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}


// Fetch all users
$query = "SELECT user_id, first_name, last_name, email, created_at, role FROM users_gbl";
$result = $conn->query($query);
$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    echo "Error fetching users: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
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
            margin-left: 260px;
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

        /* Form Containers */
        .edit-form,
        .form-container {
            background-color: var(--color-cream-100);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            margin-bottom: 2rem;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        /* Label Styling */
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--color-brown-800);
            font-weight: 600;
        }

        /* Input, Textarea and Select fields */
        input[type="text"],
        input[type="number"],
        input[type="email"],
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

        /* Textarea specific styling */
        textarea {
            min-height: 100px;
            resize: vertical;
        }

        /* Button Styling */
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

        /* Table Styles */
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

        /* Responsive Styles */
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

            .edit-form,
            .form-container {
                padding: 1rem;
            }

            input[type="text"],
            input[type="number"],
            input[type="email"],
            textarea,
            select {
                padding: 0.5rem;
            }
        }


    </style>
</head>
<body>
    <?php include '../templates/admin_sidebar.html'; ?> <!-- Include the admin sidebar -->

    <div class="dashboard">
        <h1>User Management</h1>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    <td><?php echo $user['role'] == 1 ? 'Admin' : 'User'; ?></td>
                    <td>
                        <a href="admin_user_management.php?edit=<?php echo $user['user_id']; ?>">Edit</a>
                        <a href="admin_user_management.php?delete=<?php echo $user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (isset($_GET['edit'])): 
            $edit_user_id = intval($_GET['edit']);
            $edit_query = "SELECT * FROM users_gbl WHERE user_id = ?";
            $stmt = $conn->prepare($edit_query);
            $stmt->bind_param("i", $edit_user_id);
            $stmt->execute();
            $edit_result = $stmt->get_result();
            $edit_user = $edit_result->fetch_assoc();
            $stmt->close();
        ?>
        <h2>Edit User</h2>
        <div class="edit-form">
            <form method="POST" action="">
                <input type="hidden" name="user_id" value="<?php echo $edit_user['user_id']; ?>">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($edit_user['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($edit_user['last_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($edit_user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Role:</label>
                    <select name="role" required>
                        <option value="1" <?php echo $edit_user['role'] == 1 ? 'selected' : ''; ?>>Admin</option>
                        <option value="2" <?php echo $edit_user['role'] == 2 ? 'selected' : ''; ?>>User</option>
                    </select>
                </div>
                <button type="submit" name="update_user">Update User</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <script src="../public/assets/js/script.js"></script>
</body>
</html>
