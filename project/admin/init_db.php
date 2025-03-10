<?php
require_once '../../private/config.php';

function initializeDatabase($conn) {
    // Check if deleted_at column exists
    $result = $conn->query("SHOW COLUMNS FROM bookings LIKE 'deleted_at'");
    if ($result->num_rows === 0) {
        // Add deleted_at column if it doesn't exist
        $alter_query = "ALTER TABLE bookings ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL";
        if (!$conn->query($alter_query)) {
            error_log("Failed to add deleted_at column: " . $conn->error);
            return false;
        }
    }
    return true;
}
