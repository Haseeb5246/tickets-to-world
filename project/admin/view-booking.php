<?php
require_once '../../private/config.php';  // Ensure this path is correct
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
$sql = "SELECT b.*, COALESCE(b.FClsType, 'ECONOMY') as class_type FROM bookings b WHERE b.id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

// Function to get airline name from code
function getAirlineName($code) {
    $airlines = [
        'ALL' => 'Any Airline',
        'BA' => 'British Airways',
        'KA' => 'Kenya Airways',
        'RAM' => 'Royal Air Maroc',
        'KLM' => 'KLM',
        'AF' => 'Air France',
        'TK' => 'Turkish Airlines',
        'EK' => 'Emirates',
        'QR' => 'Qatar Airways',
        'LH' => 'Lufthansa',
        'VS' => 'Virgin Atlantic',
        'WB' => 'Rwandair Express',
        'ET' => 'Ethiopian Air Lines',
        'LX' => 'Swiss'
    ];
    return $airlines[$code] ?? $code;
}

// Update the formatClassType function
function formatClassType($classType) {
    // Debug the input value
    error_log("Input class type value: " . var_export($classType, true));
    
    // Force uppercase and trim
    $cleanType = strtoupper(trim($classType ?? 'ECONOMY'));
    
    // Simple direct mapping
    $formattedTypes = [
        'ECONOMY' => 'Economy Class',
        'PREMIUM' => 'Premium Economy',
        'BUSINESS' => 'Business Class'
    ];
    
    return $formattedTypes[$cleanType] ?? 'Economy Class';
}

if (!$booking) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
    $update_sql = "UPDATE bookings SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $booking_id);
    
    if ($update_stmt->execute()) {
        // Send email notification to customer
        $to = $booking['email'];
        $subject = "Booking Status Update - " . SITE_NAME;
        $message = "Dear {$booking['name']},\n\n";
        $message .= "Your booking status has been updated to: " . ucfirst($new_status) . "\n\n";
        $message .= "Booking Details:\n";
        $message .= "From: {$booking['from_location']}\n";
        $message .= "To: {$booking['to_location']}\n";
        $message .= "Departure: {$booking['departure_date']}\n";
        $message .= "Return: {$booking['return_date']}\n";
        $message .= "Adults: " . (isset($booking['FAdult']) ? $booking['FAdult'] : 'N/A') . "\n";
        $message .= "Children: " . (isset($booking['FChild']) ? $booking['FChild'] : 'N/A') . "\n";
        $message .= "Infants: " . (isset($booking['FInfant']) ? $booking['FInfant'] : 'N/A') . "\n";
        $message .= "Class Type: " . (isset($booking['FClsType']) ? $booking['FClsType'] : 'N/A') . "\n";
        $message .= "Passengers: {$booking['passengers']}\n\n";
        $message .= "Best regards,\n" . SITE_NAME;
        
        $headers = "From: " . SITE_EMAIL;
        
        mail($to, $subject, $message, $headers);
        
        header("Location: view-booking.php?id=$booking_id&updated=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booking - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                    <li class="active"><a href="bookings.php"><i class="fas fa-calendar"></i> Bookings</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="main-content">
            <header class="admin-header">
                <h1>Booking Details #<?php echo $booking['id']; ?></h1>
                <div class="admin-user">
                    <span>Welcome, Admin</span>
                </div>
            </header>
            
            <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">
                Booking status updated successfully!
            </div>
            <?php endif; ?>
            
            <div class="booking-details">
                <div class="detail-card">
                    <h3>Customer Information</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['phone']); ?></p>
                </div>
                
                <div class="detail-card">
                    <h3>Travel Details</h3>
                    <p><strong>From:</strong> <?php echo htmlspecialchars($booking['from_location']); ?></p>
                    <p><strong>To:</strong> <?php echo htmlspecialchars($booking['to_location']); ?></p>
                    <p><strong>Departure:</strong> <?php echo date('d M Y', strtotime($booking['departure_date'])); ?></p>
                    <p><strong>Return:</strong> <?php echo $booking['return_date'] ? date('d M Y', strtotime($booking['return_date'])) : 'N/A'; ?></p>
                    <p><strong>Adults:</strong> <?php echo htmlspecialchars($booking['FAdult']); ?></p>
                    <p><strong>Children:</strong> <?php echo htmlspecialchars($booking['FChild']); ?></p>
                    <p><strong>Infants:</strong> <?php echo htmlspecialchars($booking['FInfant']); ?></p>
                    <p><strong>Class Type:</strong> <?php 
                        $classType = $booking['FClsType'] ?? 'ECONOMY';
                        error_log("Class type from database: " . $classType);
                        echo htmlspecialchars(formatClassType($classType));
                    ?></p>
                    <p><strong>Airline:</strong> <?php echo htmlspecialchars(getAirlineName($booking['FAirLine'])); ?></p>
                    <p><strong>Trip Type:</strong> <?php echo htmlspecialchars($booking['trip_type']); ?></p>
                </div>
                
                <div class="detail-card">
                    <h3>Status Update</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="status">Update Status:</label>
                            <select name="status" id="status">
                                <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-update">Update Status</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>