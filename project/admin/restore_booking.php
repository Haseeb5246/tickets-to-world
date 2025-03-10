<?php
require_once '../../private/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

// Get and validate input
$data = json_decode(file_get_contents('php://input'), true);
$booking_id = filter_var($data['booking_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);

if (!$booking_id) {
    die(json_encode(['success' => false, 'message' => 'Invalid booking ID']));
}

try {
    // Prepare and execute the restore query
    $sql = "UPDATE bookings SET deleted_at = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        die(json_encode(['success' => false, 'message' => 'Database error']));
    }

    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Booking restored successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Booking not found or already restored'
            ]);
        }
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to restore booking'
        ]);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred'
    ]);
}

$conn->close();
