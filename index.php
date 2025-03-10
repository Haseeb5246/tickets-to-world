<?php
// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Global configuration - simplified URL handling
$base_path = '/tickets/project';
$site_root = 'http://' . $_SERVER['HTTP_HOST'];
$full_base_url = $site_root . $base_path;

// Get the requested URI and normalize it
$request_uri = $_SERVER['REQUEST_URI'];
$path = trim(parse_url($request_uri, PHP_URL_PATH), '/');

// Clean the path - remove all instances of tickets and project
$path = preg_replace('#^(tickets/*)?(project/*)?#i', '', $path);
$path = trim($path, '/');

// Simple router
function handleRequest($path) {
    global $base_path;
    // Define valid routes
    $valid_routes = [
        '' => 'home',
        'home' => 'home',
        'about' => 'about',
        // Add more routes as needed
    ];

    // Check if route exists
    if (array_key_exists($path, $valid_routes)) {
        return handleValidRequest($valid_routes[$path]);
    } else {
        return handle404();
    }
}

function handleValidRequest($page) {
    global $full_base_url;
    // Handle valid page request
    return "Welcome to $page page";
}

function handle404() {
    global $site_root, $base_path;
    header("HTTP/1.0 404 Not Found");
    // Ensure clean homepage URL
    $homepage_url = $site_root . $base_path;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Page Not Found - 404 Error</title>
        <meta name="robots" content="noindex, follow">
        <style>
            .error-container {
                text-align: center;
                padding: 50px;
                font-family: Arial, sans-serif;
            }
            .error-container h1 {
                color: #e74c3c;
            }
            .error-container a {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 20px;
                background: #3498db;
                color: white;
                text-decoration: none;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>404 - Page Not Found</h1>
            <p>The page you are looking for might have been removed or is temporarily unavailable.</p>
            <a href="<?php echo $homepage_url; ?>">Return to Homepage</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Handle the request
echo handleRequest($path);
