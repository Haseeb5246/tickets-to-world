<?php
// Prevent any output before headers
ob_start();
require_once '../../private/config.php';
session_start();

// Clear any previous output
ob_clean();
header('Content-Type: application/json');

try {
    if (!isset($_SESSION['admin_logged_in'])) {
        throw new Exception('Unauthorized');
    }

    $raw_data = file_get_contents('php://input');
    if (!$raw_data) {
        throw new Exception('No input data received');
    }

    $data = json_decode($raw_data, true);
    if (!$data) {
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }

    $booking_id = filter_var($data['booking_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
    $action = filter_var($data['action'] ?? null, FILTER_SANITIZE_STRING);

    if (!$booking_id || !$action) {
        throw new Exception('Invalid booking ID or action');
    }

    // Different SQL for trash vs permanent delete
    if ($action === 'trash') {
        $sql = "UPDATE bookings SET deleted_at = CURRENT_TIMESTAMP WHERE id = ? AND deleted_at IS NULL";
    } elseif ($action === 'permanent') {
        // For permanent delete, first check if the booking exists and is in trash
        $check_sql = "SELECT id FROM bookings WHERE id = ? AND deleted_at IS NOT NULL";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            throw new Exception("Database prepare failed: " . $conn->error);
        }
        
        $check_stmt->bind_param("i", $booking_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception('Booking not found in trash');
        }
        $check_stmt->close();
        
        $sql = "DELETE FROM bookings WHERE id = ? AND deleted_at IS NOT NULL";
    } else {
        throw new Exception('Invalid action type');
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $booking_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        ob_clean(); // Clean again before output
        die(json_encode([
            'success' => true,
            'message' => ($action === 'trash' ? 'Booking moved to trash' : 'Booking deleted permanently'),
            'booking_id' => $booking_id
        ]));
    } else {
        throw new Exception($action === 'trash' ? 'Booking already in trash' : 'Unable to delete booking');
    }

    $stmt->close();

} catch (Exception $e) {
    error_log("Delete booking error: " . $e->getMessage());
    ob_clean(); // Clean before error output
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]));
}

$conn->close();
