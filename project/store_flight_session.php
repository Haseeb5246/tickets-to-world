<?php
session_start();

// Get POST data
$json = file_get_contents('php://input');
$flight = json_decode($json, true);

// Store in session
$_SESSION['selected_flight'] = $flight;

echo json_encode(['status' => 'success']);
