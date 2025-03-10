<?php
require_once '../../private/config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = filter_var($data['booking_id'], FILTER_SANITIZE_NUMBER_INT);
$status = filter_var($data['status'], FILTER_SANITIZE_STRING);

$sql = "UPDATE bookings SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $booking_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
