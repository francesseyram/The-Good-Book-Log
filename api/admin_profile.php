<?php
session_start();
include '../config/config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1 ) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user information
$stmt = $conn->prepare("SELECT first_name, last_name, email FROM users_gbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$success_message = $error_message = '';

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);

    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE users_gbl SET first_name = ?, last_name = ?, email = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $first_name, $last_name, $email, $user_id);
        if ($stmt->execute()) {
            $success_message = "Profile updated successfully.";
            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $user['email'] = $email;
        } else {
            $error_message = "Error updating profile. Please try again.";
        }
        $stmt->close();
    }
}

// Admin functionality: View all users
$users = [];
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, role FROM users_gbl");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "All password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users_gbl WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];
        $stmt->close();

        if (password_verify($current_password, $stored_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users_gbl SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            if ($stmt->execute()) {
                $success_message = "Password updated successfully.";
            } else {
                $error_message = "Error updating password. Please try again.";
            }
            $stmt->close();
        } else {
            $error_message = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - The Good Book Log</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=Handlee&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/styles.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            background-color: var(--color-cream-100);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .profile-header {
            background-color: var(--color-bronze);
            color: var(--color-cream-100);
            padding: 2rem;
            text-align: center;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            background-color: var(--color-cream-200);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
            color: var(--color-bronze);
        }
        .profile-name {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .profile-email {
            font-size: 1rem;
            opacity: 0.8;
        }
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid var(--color-cream-200);
        }
        .profile-tab {
            flex: 1;
            text-align: center;
            padding: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .profile-tab.active {
            background-color: var(--color-cream-200);
            font-weight: bold;
        }
        .profile-content {
            padding: 2rem;
        }
        .profile-form {
            display: grid;
            gap: 1rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            padding: 0.75rem;
            border: 1px solid var(--color-cream-300);
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--color-bronze);
        }
        .btn {
            background-color: var(--color-bronze);
            color: var(--color-cream-100);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 1rem;
            font-weight: 500;
        }
        .btn:hover {
            background-color: var(--color-gold);
        }
        .message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include '../templates/admin_sidebar.html'; ?>
        
        <div class="main-content">
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                    </div>
                    <h1 class="profile-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                    <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>

                <div class="profile-tabs">
                    <div class="profile-tab active" data-tab="profile">Profile</div>
                    <div class="profile-tab" data-tab="security">Security</div>
                </div>

                <div class="profile-content">
                    <?php if ($success_message): ?>
                        <div class="message success"><?php echo $success_message; ?></div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="message error"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <div id="profile-tab-content">
                        <form class="profile-form" method="POST">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <button type="submit" name="update_profile" class="btn">Update Profile</button>
                        </form>
                    </div>

                    <div id="security-tab-content" style="display: none;">
                        <form class="profile-form" method="POST">
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="reset_password" class="btn">Reset Password</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        const tabs = document.querySelectorAll('.profile-tab');
        const tabContents = {
            profile: document.getElementById('profile-tab-content'),
            security: document.getElementById('security-tab-content')
        };

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabName = tab.getAttribute('data-tab');
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Hide all content
                Object.values(tabContents).forEach(content => content.style.display = 'none');
                
                // Show selected content
                tabContents[tabName].style.display = 'block';
            });
        });

    </script>
</body>
</html>
