<?php
// Only start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Start the session if none exists
}

require_once '../../private/config.php';  // Include the config file for site constants and DB connection

// Initialize error variable
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = filter_var($_POST['username'], FILTER_SANITIZE_SPECIAL_CHARS);  // Safer sanitization
    $password = $_POST['password'];
    
    // Query to check the user in the database
    $sql = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if user exists and verify password
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Successful login, set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username']; // Make sure this line exists
            header("Location: index.php");  // Redirect to the admin dashboard
            exit();
        }
    }
    
    // If login fails
    $error = "Invalid username or password";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Admin Login</h2>
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="login-button">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
