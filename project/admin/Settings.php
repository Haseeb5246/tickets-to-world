<?php
require_once '../../private/config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Make sure we have the username in session
if (!isset($_SESSION['admin_username'])) {
    $error = "Session error: Username not found";
    header("Location: logout.php");
    exit();
}

// Function definitions
function changeAdminPassword($username, $newPassword) {
    global $conn;
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $hashedPassword, $username);
    return $stmt->execute();
}

function addAdminUser($username, $password) {
    global $conn;
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);
    return $stmt->execute();
}

function deleteAdminUser($username) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    return $stmt->execute();
}

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password with better error handling
        $stmt = $conn->prepare("SELECT username, password FROM admin_users WHERE username = ?");
        if (!$stmt) {
            $error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("s", $_SESSION['admin_username']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $user = $result->fetch_assoc()) {
                if (password_verify($current_password, $user['password'])) {
                    if ($new_password === $confirm_password) {
                        if (changeAdminPassword($_SESSION['admin_username'], $new_password)) {
                            $message = "Password changed successfully!";
                        } else {
                            $error = "Error changing password.";
                        }
                    } else {
                        $error = "New passwords do not match.";
                    }
                } else {
                    $error = "Current password is incorrect.";
                }
            } else {
                $error = "User not found.";
            }
        }
    }
    
    if (isset($_POST['add_admin'])) {
        $new_username = $_POST['new_username'];
        $new_admin_password = $_POST['new_admin_password'];
        
        if (addAdminUser($new_username, $new_admin_password)) {
            $message = "New admin user added successfully!";
        } else {
            $error = "Error adding new admin user.";
        }
    }
}

// Update admin users query with error handling and column check
try {
    // First, check if created_at column exists
    $columnCheck = $conn->query("SHOW COLUMNS FROM admin_users LIKE 'created_at'");
    
    if ($columnCheck->num_rows === 0) {
        // Add created_at column if it doesn't exist
        $conn->query("ALTER TABLE admin_users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }
    
    // Now fetch users
    $admin_users = $conn->query("SELECT username, 
        IFNULL(created_at, CURRENT_TIMESTAMP) as created_at 
        FROM admin_users");
        
    if ($admin_users) {
        $admin_users = $admin_users->fetch_all(MYSQLI_ASSOC);
    } else {
        $error = "Database error: " . $conn->error;
        $admin_users = [];
    }
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
    $admin_users = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Include your sidebar here -->
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="settings-header">
                <h1>Settings</h1>
                <div class="current-user">
                    <i class="fas fa-user-circle"></i>
                    <span>Logged in as: <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Admin Users Table at Top -->
            <div class="admin-users-section">
                <div class="section-card">
                    <h2><i class="fas fa-users"></i> Admin Users</h2>
                    <?php if (!empty($admin_users)): ?>
                    <div class="table-responsive">
                        <table class="admin-users-table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admin_users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <i class="fas fa-user"></i>
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <?php if ($user['username'] !== $_SESSION['admin_username']): ?>
                                        <button onclick="deleteAdmin('<?php echo htmlspecialchars($user['username']); ?>')" 
                                                class="btn btn-danger" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php else: ?>
                                        <span class="current-user-badge">Current User</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <p class="no-data">No admin users found or error loading users.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Two Column Layout for Forms -->
            <div class="settings-grid">
                <!-- Change Password Section -->
                <div class="settings-card">
                    <h2><i class="fas fa-key"></i> Change Password</h2>
                    <form method="POST" class="settings-form">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-save"></i> Change Password
                        </button>
                    </form>
                </div>

                <!-- Add New Admin Section -->
                <div class="settings-card">
                    <h2><i class="fas fa-user-plus"></i> Add New Admin</h2>
                    <form method="POST" class="settings-form">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="new_username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="new_admin_password" required>
                        </div>
                        <button type="submit" name="add_admin" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Admin User
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function deleteAdmin(username) {
            if (confirm('Are you sure you want to delete this admin user?')) {
                fetch('delete_admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ username: username })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Error deleting admin user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting admin user');
                });
            }
        }
    </script>
</body>
</html>
