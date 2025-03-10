<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // Change to your MySQL username (usually 'root' for local development)
define('DB_PASS', '');         // Change to your MySQL password (often blank for local development)
define('DB_NAME', 'tickets_to_world');  // Changed from 'bus_booking_db' to match database.sql

// Add backup path if not already defined
if (!defined('DB_BACKUP_PATH')) {
    define('DB_BACKUP_PATH', dirname(__DIR__) . '/project/backups/');
}

// Create connection with error handling
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    // First, try to create the database if it doesn't exist
    $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    
    // Select the database
    $conn->select_db(DB_NAME);
    
    // Set charset to handle special characters
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}

// Add debug mode
define('DEBUG', true);

// Enhanced error handling
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Test database connection
    $test_query = "SELECT COUNT(*) as count FROM bookings";
    $result = $conn->query($test_query);
    if ($result === false) {
        error_log("Database test query failed: " . $conn->error);
    } else {
        error_log("Database test successful. Found " . $result->fetch_assoc()['count'] . " bookings");
    }
}

// Site configuration
define('SITE_NAME', 'Tickets To World');
define('SITE_EMAIL', 'info@ticketstoworld.co.uk');
define('ADMIN_EMAIL', 'admin@ticketstoworld.co.uk');

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Page configurations
$page_config = [
    'index.php' => [
        'title' => 'Tickets to World - Book Your Flights',
        'description' => 'Book affordable flights worldwide with Tickets to World. Find the best travel deals for every adventure.',
        'keywords' => 'flight booking, cheap flights, travel deals, airline tickets',
        'indexable' => true
    ],
    'about.php' => [
        'title' => 'About Us - Tickets to World',
        'description' => 'Learn about Tickets to World and our mission to provide affordable travel solutions.',
        'keywords' => 'about us, travel company, flight booking service',
        'indexable' => true
    ],
    'privacy-policy.php' => [
        'title' => 'Privacy Policy - Tickets to World',
        'description' => 'Read our privacy policy to learn how we protect your personal information.',
        'keywords' => 'privacy policy, data protection, personal information',
        'indexable' => false  // This makes the page non-indexable
    ],
    // Add more pages as needed
];

// Get current page settings
$current_page = basename($_SERVER['PHP_SELF']);
$page_settings = $page_config[$current_page] ?? [
    'title' => 'Tickets to World',
    'description' => '',
    'keywords' => '',
    'indexable' => false
];

?>
