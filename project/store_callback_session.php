<?php
session_start();

// Get POST data
$json = file_get_contents('php://input');
$callbackData = json_decode($json, true);

// Store in session
$_SESSION['callback_data'] = $callbackData;
$_SESSION['callback_request'] = true;

echo json_encode(['status' => 'success']);
