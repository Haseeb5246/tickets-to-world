<?php
require_once '../../private/config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

// Get the JSON data
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';

try {
    // First check if there's more than one admin user
    $countQuery = $conn->query("SELECT COUNT(*) as total FROM admin_users");
    $totalAdmins = $countQuery->fetch_assoc()['total'];

    if ($totalAdmins <= 1) {
        die(json_encode(['success' => false, 'message' => 'Cannot delete the last admin account']));
    }

    // Make sure we're not deleting our own account
    if ($username === $_SESSION['admin_username']) {
        die(json_encode(['success' => false, 'message' => 'Cannot delete your own account while logged in']));
    }

    // Proceed with deletion
    $stmt = $conn->prepare("DELETE FROM admin_users WHERE username = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Admin user deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No admin user found with that username']);
    }

} catch (Exception $e) {
    error_log("Error deleting admin user: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error deleting admin user: ' . $e->getMessage()]);
}
