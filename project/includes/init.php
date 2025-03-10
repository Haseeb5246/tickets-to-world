<?php
session_start();

// Define base paths
define('PROJECT_ROOT', dirname(__DIR__));
define('DB_BACKUP_PATH', PROJECT_ROOT . '/backups/');

// Load configuration
require_once dirname(PROJECT_ROOT) . '/private/config.php';

// Load required classes
require_once __DIR__ . '/backup.php';

// Admin authentication check function
function requireAdmin() {
    if (!isset($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit();
    }
}

// Create backup directory if it doesn't exist
if (!file_exists(DB_BACKUP_PATH)) {
    mkdir(DB_BACKUP_PATH, 0750, true);
}
