<?php
require_once '../../private/config.php';
require_once 'init_db.php';

session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Pagination configuration
$records_per_page = 6; // Changed from 20 to 6
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of flights
$total_flights = $conn->query("SELECT COUNT(DISTINCT flight_id) as count FROM available_flights")->fetch_assoc()['count'];
$total_pages = ceil($total_flights / $records_per_page);

// Handle form submission for adding a new flight
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_SESSION['last_insert_data']) || $_SESSION['last_insert_data'] !== $_POST) {
            $conn->begin_transaction();

            // Insert basic flight details
            $stmt = $conn->prepare("INSERT INTO available_flights (from_location, to_location) VALUES (?, ?)");
            
            $stmt->bind_param(
                "ss",
                $_POST['from_location'],
                $_POST['to_location']
            );

            if ($stmt->execute()) {
                $flight_id = $conn->insert_id;

                // Insert airline-specific flight details
                $stmt = $conn->prepare("INSERT INTO airline_flights (
                    flight_id, airline_name, total_journey_time, time_departure, 
                    arrival_time, stops, available_seats, pay_in_one_go, pay_in_installments
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"); // Fixed missing closing parenthesis

                foreach ($_POST['airlines'] as $airline => $details) {
                    $installments = empty($details['payments']['installments']) ? null : $details['payments']['installments'];
                    
                    $stmt->bind_param(
                        "isssssids",
                        $flight_id,
                        $airline,
                        $details['journey_time'],
                        $details['departure'],
                        $details['arrival'],
                        $details['stops'],
                        $details['seats'],
                        $details['payments']['one_go'],
                        $installments
                    );
                    $stmt->execute();
                }

                $conn->commit();
                $_SESSION['last_insert_data'] = $_POST;
                $_SESSION['success_message'] = "Flight added successfully!";
                header("Location: " . $_SERVER['PHP_SELF'] . "?page=1&added=true");
                exit();
            }
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF'] . "?page=1&error=true");
        exit();
    }
}

// Handle flight deletion
if (isset($_GET['delete_flight_id'])) {
    $flight_id = (int)$_GET['delete_flight_id'];
    $stmt = $conn->prepare("DELETE FROM available_flights WHERE flight_id = ?");
    $stmt->bind_param("i", $flight_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Flight deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting flight: " . $stmt->error;
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?page=1");
    exit();
}

// Check for flash messages
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Fetch all flights
$flights_result = $conn->query("
    SELECT 
        f.flight_id,
        f.from_location,
        f.to_location,
        af.airline_name,
        af.total_journey_time,
        af.time_departure,
        af.arrival_time,
        af.pay_in_one_go,
        af.pay_in_installments,
        af.stops,
        af.available_seats
    FROM (
        SELECT DISTINCT flight_id, from_location, to_location
        FROM available_flights
        ORDER BY flight_id DESC
        LIMIT $offset, $records_per_page
    ) f
    LEFT JOIN airline_flights af ON f.flight_id = af.flight_id
    ORDER BY f.flight_id DESC, af.airline_name ASC
");

function outputFlightRows($flights, $row_span) {
    static $row_number = 1;
    
    foreach ($flights as $index => $flight) {
        echo '<tr data-flight-start="' . ($index === 0 ? 'true' : 'false') . '">';
        
        // Only output these cells for the first airline of each flight
        if ($index === 0) {
            echo '<td rowspan="' . $row_span . '">' . $row_number++ . '</td>';
            echo '<td rowspan="' . $row_span . '">' . htmlspecialchars($flight['from_location']) . '</td>';
            echo '<td rowspan="' . $row_span . '">' . htmlspecialchars($flight['to_location']) . '</td>';
        }
        
        // Output airline-specific details
        echo '<td>' . htmlspecialchars($flight['airline_name']) . '</td>';
        echo '<td>' . number_format($flight['pay_in_one_go'], 2) . '</td>';
        echo '<td>' . ($flight['pay_in_installments'] ? number_format($flight['pay_in_installments'], 2) : '-') . '</td>';
        echo '<td>' . htmlspecialchars($flight['total_journey_time']) . '</td>';
        echo '<td>' . htmlspecialchars($flight['time_departure']) . '</td>';
        echo '<td>' . htmlspecialchars($flight['arrival_time']) . '</td>';
        echo '<td>' . ($flight['stops'] == '0' ? 'Non Stop' : $flight['stops'] . ' ' . ($flight['stops'] > 1 ? '' : '')) . '</td>';
        echo '<td>' . $flight['available_seats'] . ' Seats' . ($flight['available_seats'] > 1 ? '' : '') . ' Available</td>';
        
        // Only output delete button for the first airline of each flight
        if ($index === 0) {
            echo '<td rowspan="' . $row_span . '">';
            echo '<button class="action-btn" onclick="if(confirm(\'Are you sure you want to delete this flight?\')) ';
            echo 'window.location.href=\'?delete_flight_id=' . $flight['flight_id'] . '\'">';
            echo '<i class="fas fa-trash-alt"></i>';
            echo '</button>';
            echo '</td>';
        }
        
        echo '</tr>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Flights - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Update responsive styles */
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.4);
            margin-bottom: 2rem;
            border: 1px solid #cbd5e1;  /* Added border */
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            flex: 1 1 calc(33.333% - 1rem);
            min-width: 250px;
        }

        .form-group label {
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;  /* Darker border color */
            border-radius: 6px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52,152,219,0.2);
            outline: none;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid #e1e8f0;
            margin-top: 1rem;
        }

        .btn-submit {
            background-color: #3498db;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
        }

        .table-container {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid #cbd5e1;  /* Added border */
        }

        .table-container h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .flights-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.7rem;
        }

        .flights-table th {
            background-color: #f1f5f9;  /* Slightly darker background */
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            color: #475569;  /* Darker text color */
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #cbd5e1;  /* Darker border */
        }

        .flights-table td {
            padding: 1rem;
            border-bottom: 1px solid #cbd5e1;  /* Darker border */
            color: #475569;
        }

        .flights-table tbody tr:hover {
            background-color: #f8fafc;
            border-left: 3px solid #3498db;  /* Added highlight on hover */
        }

        /* Section titles */
        .section-title {
            color: #2c3e50;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        /* Message styles */
        .success-message, .error-message {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .error-message {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-group {
                flex: 1 1 100%;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-submit {
                width: 100%;
            }
        }

        /* Additional CSS for multiple select */
        .form-group select[multiple] {
            padding: 0.5rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #fff;
        }

        .form-group select[multiple] option {
            padding: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .form-group select[multiple] option:checked {
            background-color: #3498db;
            color: white;
        }

        .form-group select[multiple]:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52,152,219,0.2);
            outline: none;
        }

        /* Help text */
        .form-group .help-text {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        /* Add these new styles */
        .airlines-container {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 1rem;
            max-height: 300px;
            overflow-y: auto;
        }

        .airline-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            transition: background-color 0.2s;
        }

        .airline-checkbox:hover {
            background-color: #f1f5f9;
        }

        .airline-checkbox input[type="checkbox"] {
            margin-right: 0.75rem;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .airline-checkbox label {
            cursor: pointer;
            flex: 1;
        }

        .add-airline-container {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .add-airline-container input {
            flex: 1;
        }

        .btn-add-airline {
            background-color: #2c3e50;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-add-airline:hover {
            background-color: #34495e;
        }

        .new-airline {
            background-color: #e9f7ef;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Add/modify these styles */
        .airline-dropdown {
            position: relative;
            width: 100%;
            margin-bottom: 1rem;
        }

        .dropdown-header {
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            font-size: 0.95rem;
            color: #2c3e50;
            transition: all 0.3s ease;
        }

        .dropdown-header:hover {
            border-color: #3498db;
        }

        .dropdown-header i {
            transition: transform 0.3s ease;
        }

        .dropdown-header.active i {
            transform: rotate(180deg);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: calc(100% + 5px);
            right: 0; /* Change from left: 0 to right: 0 */
            background: white;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            min-width: 300px;
            max-width: 100%; /* Add max-width */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s;
        }

        .dropdown-content.active {
            display: block;
            opacity: 1;
            visibility: visible;
        }

        @media (min-width: 768px) {
            .dropdown-content {
                width: 400px;
                max-height: 60vh;
                overflow-y: auto;
                right: 0;
            }
        }

        @media (min-width: 1024px) {
            .dropdown-content {
                width: 450px;
                right: 0;
            }

            .form-group {
                position: relative; /* Add this */
            }
        }

        @media (min-width: 1440px) {
            .dropdown-content {
                width: 500px;
            }
        }

        /* Add this to ensure the dropdown doesn't overflow on mobile */
        @media (max-width: 767px) {
            .dropdown-content {
                left: 0;
                right: 0;
                width: auto;
            }
        }

        /* Custom scrollbar */
        .dropdown-content::-webkit-scrollbar {
            width: 8px;
        }

        .dropdown-content::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .dropdown-content::-webkit-scrollbar-thumb {
            background-color: #94a3b8;
            border-radius: 4px;
            border: 2px solid #f1f5f9;
        }

        .airline-option {
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .airline-option:hover {
            background-color: #f8fafc;
        }

        .airline-option input[type="checkbox"] {
            margin-right: 10px;
            width: 16px;
            height: 16px;
            border: 2px solid #cbd5e1;
            border-radius: 3px;
        }

        .airline-option label {
            flex: 1;
            cursor: pointer;
            padding: 0.25rem 0;
        }

        .selected-airlines {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
            min-height: 32px;
        }

        .selected-airline {
            background-color: #e2e8f0;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #2c3e50;
            transition: all 0.2s ease;
        }

        .selected-airline:hover {
            background-color: #cbd5e1;
        }

        .selected-airline button {
            background: none;
            border: none;
            padding: 2px;
            display: flex;
            align-items: center;
            color: #64748b;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .selected-airline button:hover {
            color: #ef4444;
        }

        /* Search input for airlines */
        .airline-search {
            position: sticky;
            top: 0;
            padding: 1rem;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }

        .airline-search input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.95rem;
        }

        /* Responsive adjustments */
        @media (min-width: 1024px) {
            .dropdown-content {
                max-height: 500px;
                width: 400px;
            }
        }

        .add-airline-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .add-airline-form {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }

        .add-airline-input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .add-airline-form button {
            background-color: #3498db;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .add-airline-form button:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
        }

        /* Add these new styles for pagination and responsive table */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background-color: #f1f5f9;
            border-color: #94a3b8;
        }

        .pagination .active {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }

        .pagination .disabled {
            color: #94a3b8;
            pointer-events: none;
        }

        /* Improve table responsiveness */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -1rem;
            padding: 0 0rem;
        }

        .flights-table {
            min-width: 1000px; /* Ensure horizontal scroll on small screens */
        }

        @media (max-width: 768px) {
            .table-container {
                margin: 0 -1rem;
                border-radius: 0;
            }
            
            .flights-table th, 
            .flights-table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.85rem;
            }
        }

        /* Add loading indicator */
        .table-loading {
            position: relative;
            min-height: 200px;
        }

        .table-loading::after {
            content: 'Loading...';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #64748b;
        }

        /* Add these styles to your existing CSS */
        .success-message, .error-message {
            transition: opacity 0.3s ease;
        }

        button[type="submit"]:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: 360deg); }
        }

        /* Updated table styles */
        .flights-table th:first-child,
        .flights-table td:first-child {
            width: 50px;
            text-align: center;
            font-weight: 500;
            color: #64748b;
            background: #f8fafc;
            position: sticky;
            left: 0;
            z-index: 2;
        }

        .flights-table th:first-child {
            background: #f1f5f9;
            z-index: 3;
        }

        .flights-table tr:hover td:first-child {
            background: #f1f5f9;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: #dc2626;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #fee2e2;
            background-color: transparent;
        }

        .action-btn:hover {
            background-color: #fee2e2;
            transform: translateY(-1px);
        }

        .airline-payments-container {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 1rem;
            margin-top: 1rem;
            width: 100%;
            max-width: 100%;
        }

        .airline-payment-row {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            width: 100%;
        }

        .airline-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 6px;
            margin-top: 1rem;
            width: 100%;
        }

        .airline-details-grid > div {
            padding: 0.5rem;
        }

        .airline-details-grid input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.95rem;
        }

        /* Responsive grid adjustments */
        @media (min-width: 1024px) {
            .airline-details-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1440px) {
            .airline-details-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Airline name header styling */
        .airline-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e40af;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 1rem;
        }

        /* Input group styling */
        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .input-group label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #475569;
        }

        .input-group input {
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52,152,219,0.2);
            outline: none;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px; /* Increased width */
            background-color: #1a237e; /* Add background color */
            color: white;
            height: 100vh;
            position: fixed;
            transition: all 0.3s ease;
            z-index: 1000;
            left: 0; /* Ensure sidebar starts from left */
        }

        .sidebar .admin-brand {
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar nav ul li {
            margin: 5px 15px;
        }

        .sidebar nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 5px 0;
        }

        .sidebar nav ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar nav ul li.active a {
            background: #2196f3;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .main-content {
            margin-left: 280px; /* Match sidebar width */
            flex: 1;
            padding: 2rem;
            background: #f5f7fa;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -280px; /* Hide sidebar by default on mobile */
            }

            .sidebar.active {
                left: 0; /* Show sidebar when active */
            }

            .main-content {
                margin-left: 0;
                padding-top: 4rem; /* Add space for mobile toggle */
            }

            .mobile-toggle {
                display: block;
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 1001;
                background: #1a237e;
                color: white;
                border: none;
                padding: 0.75rem;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                cursor: pointer;
            }
        }

        /* Add these new styles for flight row separation */
        .flights-table tr[data-flight-start="true"] td {
            border-top: 2px solid #3498db;  /* Strong blue border for new flight group */
        }

        .flights-table tr:not([data-flight-start="true"]) td {
            border-top: 1px solid #e2e8f0;  /* Light border for same flight group */
        }

        .flights-table tr:last-child td {
            border-bottom: 2px solid #3498db;  /* Strong bottom border for last row */
        }

        /* Add background tint to group rows together */
        .flights-table tr[data-flight-start="true"] td:first-child,
        .flights-table tr[data-flight-start="true"] td[rowspan] {
            background-color: #f8fafc;  /* Slightly darker background for first row */
        }

        /* Enhanced pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            margin: 2rem 0;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .pagination a:hover {
            background-color: #f8fafc;
            border-color: #3498db;
            color: #3498db;
            transform: translateY(-1px);
        }

        .pagination .active {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }

        .pagination .disabled {
            color: #cbd5e1;
            background-color: #f8fafc;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination-info {
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <aside class="sidebar">
            <div class="admin-brand">
                <img src="../assets/images/Tickets-To-World.png" width="170px" height="auto" alt="Logo"/>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="active"><a href="Flights.php"><i class="fas fa-plane"></i> Manage Flights</a></li>
                    <li><a href="file-management.php"><i class="fas fa-folder"></i> File Management</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="Flights.php" class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label for="from_location">From</label>
                        <input type="text" id="from_location" name="from_location" required>
                    </div>
                    <div class="form-group">
                        <label for="to_location">To</label>
                        <input type="text" id="to_location" name="to_location" required>
                    </div>
                    <div class="form-group">
                        <label>Airlines</label>
                        <div class="airline-dropdown">
                            <div class="dropdown-header" onclick="toggleDropdown(event)">
                                <span>Select Airlines</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="dropdown-content">
                                <?php
                                $airlines = array(
                                    "British Airways",
                                    "Kenya Airways",
                                    "Royal Air Maroc",
                                    "KLM",
                                    "Air France",
                                    "Turkish Airlines",
                                    "Emirates",
                                    "Qatar Airways",
                                    "Lufthansa",
                                    "Virgin Atlantic",
                                    "Rwandair Express",
                                    "Ethiopian Air Lines",
                                    "Swiss"
                                );
                                
                                foreach ($airlines as $airline): ?>
                                    <div class="airline-option">
                                        <input type="checkbox" 
                                               id="airline_<?php echo str_replace(' ', '_', $airline); ?>" 
                                               name="company[]" 
                                               value="<?php echo $airline; ?>"
                                               onchange="updateSelectedAirlines()">
                                        <label for="airline_<?php echo str_replace(' ', '_', $airline); ?>">
                                            <?php echo $airline; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="selected-airlines" id="selectedAirlines"></div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label>Add New Airline</label>
                        <div class="add-airline-form">
                            <input type="text" 
                                   id="new_airline" 
                                   placeholder="Enter new airline name"
                                   class="add-airline-input">
                            <button type="button" onclick="addNewAirline()">
                                <i class="fas fa-plus"></i>
                                Add Airline
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="width: 100%;">
                        <label>Airlines Details</label>
                        <div id="airline-payments" class="airline-payments-container">
                            <!-- Dynamic airline details rows will be added here -->
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-plus"></i> Add Flight
                    </button>
                </div>
            </form>

            <div class="table-container">
                <h2>Available Flights (<?php echo $total_flights; ?> total)</h2>
                <div class="table-responsive">
                    <table class="flights-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Airline</th>
                                <th>One Go ($)</th>
                                <th>Install. ($)</th>
                                <th>Journey Time</th>
                                <th>Departure</th>
                                <th>Arrival</th>
                                <th>Stops</th>
                                <th>Seats</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php 
    if ($flights_result && $flights_result->num_rows > 0) {
        $current_flight_id = null;
        $row_span_count = 0;
        $temp_rows = array();
        
        while ($flight = $flights_result->fetch_assoc()) {
            if ($current_flight_id !== $flight['flight_id']) {
                if ($current_flight_id !== null) {
                    outputFlightRows($temp_rows, $row_span_count);
                }
                $current_flight_id = $flight['flight_id'];
                $row_span_count = 1;
                $temp_rows = array($flight);
            } else {
                $row_span_count++;
                $temp_rows[] = $flight;
            }
        }
        // Output last flight's rows
        if (!empty($temp_rows)) {
            outputFlightRows($temp_rows, $row_span_count);
        }
    } else {
        echo '<tr><td colspan="12" style="text-align: center;">No flights available</td></tr>';
    }
    ?>
</tbody>

                    </table>
                </div>
                <div class="pagination-info">
                    Showing <?php echo min(($page - 1) * $records_per_page + 1, $total_flights); ?> to 
                    <?php echo min($page * $records_per_page, $total_flights); ?> of 
                    <?php echo $total_flights; ?> flights
                </div>
                <?php if ($total_flights > $records_per_page): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1" title="First page"><i class="fas fa-angle-double-left"></i></a>
                            <a href="?page=<?php echo ($page - 1); ?>"><i class="fas fa-angle-left"></i></a>
                        <?php else: ?>
                            <span class="disabled"><i class="fas fa-angle-double-left"></i></span>
                            <span class="disabled"><i class="fas fa-angle-left"></i></span>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $start_page + 4);
                        
                        if ($end_page - $start_page < 4) {
                            $start_page = max(1, $end_page - 4);
                        }

                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" 
                            class="<?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo ($page + 1); ?>"><i class="fas fa-angle-right"></i></a>
                            <a href="?page=<?php echo $total_pages; ?>" title="Last page"><i class="fas fa-angle-double-right"></i></a>
                        <?php else: ?>
                            <span class="disabled"><i class="fas fa-angle-right"></i></span>
                            <span class="disabled"><i class="fas fa-angle-double-right"></i></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('active');
            
            // Update main content margin
            if (window.innerWidth <= 768) {
                mainContent.style.marginLeft = sidebar.classList.contains('active') ? '280px' : '0';
            }
        }

        // Add event listener for window resize
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth > 768) {
                mainContent.style.marginLeft = '280px';
                sidebar.classList.remove('active');
            } else {
                mainContent.style.marginLeft = sidebar.classList.contains('active') ? '280px' : '0';
            }
        });

        // Add event listener for clicks outside dropdown
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.dropdown-content');
            const dropdownHeader = document.querySelector('.dropdown-header');
            
            // If clicking outside of dropdown and dropdown is open, close it
            if (!event.target.closest('.airline-dropdown') && dropdown.classList.contains('active')) {
                dropdown.classList.remove('active');
                dropdownHeader.classList.remove('active');
                dropdown.style.opacity = '0';
                dropdown.style.visibility = 'hidden';
                setTimeout(() => {
                    if (!dropdown.classList.contains('active')) {
                        dropdown.style.display = 'none';
                    }
                }, 200);
            }
        });

        // Rest of your existing functions
        function addNewAirline() {
            const newAirlineName = document.getElementById('new_airline').value.trim();
            if (newAirlineName) {
                const container = document.querySelector('.dropdown-content');
                const uniqueId = 'airline_' + newAirlineName.replace(/\s+/g, '_');
                
                const div = document.createElement('div');
                div.className = 'airline-option';
                div.innerHTML = `
                    <input type="checkbox" 
                           id="${uniqueId}" 
                           name="company[]" 
                           value="${newAirlineName}" 
                           onchange="updateSelectedAirlines()"
                           checked>
                    <label for="${uniqueId}">
                        ${newAirlineName}
                    </label>
                `;
                
                container.appendChild(div);
                document.getElementById('new_airline').value = '';
                updateSelectedAirlines();
            }
        }

        function toggleDropdown(event) {
            event.stopPropagation(); // Add this line
            const dropdown = document.querySelector('.dropdown-content');
            const header = document.querySelector('.dropdown-header');
            
            if (!dropdown.querySelector('.airline-search')) {
                const searchDiv = document.createElement('div');
                searchDiv.className = 'airline-search';
                searchDiv.innerHTML = `
                    <input type="text" 
                           placeholder="Search airlines..." 
                           onkeyup="filterAirlines(this.value)"
                           autocomplete="off">
                `;
                dropdown.insertBefore(searchDiv, dropdown.firstChild);
            }
            
            // Toggle active classes
            dropdown.classList.toggle('active');
            header.classList.toggle('active');
            
            // Force repaint and ensure visibility
            if (dropdown.classList.contains('active')) {
                dropdown.style.display = 'block';
                const searchInput = dropdown.querySelector('.airline-search input');
                setTimeout(() => {
                    searchInput.focus();
                    // Force repaint
                    dropdown.style.opacity = '1';
                    dropdown.style.visibility = 'visible';
                }, 10);
            } else {
                dropdown.style.opacity = '0';
                dropdown.style.visibility = 'hidden';
                setTimeout(() => {
                    if (!dropdown.classList.contains('active')) {
                        dropdown.style.display = 'none';
                    }
                }, 200);
            }
        }

        function filterAirlines(searchTerm) {
            const options = document.querySelectorAll('.airline-option');
            searchTerm = searchTerm.toLowerCase();
            
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }

        function updateSelectedAirlines() {
            const selected = document.querySelectorAll('input[name="company[]"]:checked');
            const container = document.getElementById('selectedAirlines');
            const paymentsContainer = document.getElementById('airline-payments');
            
            container.innerHTML = '';
            paymentsContainer.innerHTML = '';
            
            selected.forEach(checkbox => {
                // Update selected airlines display
                const div = document.createElement('div');
                div.className = 'selected-airline';
                div.innerHTML = `
                    ${checkbox.value}
                    <button type="button" onclick="removeAirline('${checkbox.id}')">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                container.appendChild(div);

                // Add airline details with improved layout
                const airlineDetailsRow = document.createElement('div');
                airlineDetailsRow.className = 'airline-payment-row';
                airlineDetailsRow.innerHTML = `
                    <div class="airline-name">
                        <i class="fas fa-plane-departure"></i> ${checkbox.value}
                    </div>
                    <div class="airline-details-grid">
                        <div class="input-group">
                            <label>Journey Time</label>
                            <input type="text" name="airlines[${checkbox.value}][journey_time]" 
                                   placeholder="e.g., 2h 30m" required>
                        </div>
                        <div class="input-group">
                            <label>Departure Time</label>
                            <input type="text" name="airlines[${checkbox.value}][departure]" 
                                   placeholder="e.g., 10:00 AM" required>
                        </div>
                        <div class="input-group">
                            <label>Arrival Time</label>
                            <input type="text" name="airlines[${checkbox.value}][arrival]" 
                                   placeholder="e.g., 12:30 PM" required>
                        </div>
                        <div class="input-group">
                            <label>Stops</label>
                            <input type="text" name="airlines[${checkbox.value}][stops]" 
                                   placeholder="e.g., Direct/1 Stop" required>
                        </div>
                        <div class="input-group">
                            <label>Available Seats</label>
                            <input type="number" name="airlines[${checkbox.value}][seats]" 
                                   placeholder="e.g., 150" required>
                        </div>
                        <div class="input-group">
                            <label>Pay in One Go</label>
                            <input type="text" name="airlines[${checkbox.value}][payments][one_go]" 
                                   placeholder="e.g., $599.99 or £450" required>
                        </div>
                        <div class="input-group">
                            <label>Pay in Installments</label>
                            <input type="text" name="airlines[${checkbox.value}][payments][installments]" 
                                   placeholder="e.g., 3x $200 or 4x £120">
                        </div>
                    </div>
                `;
                paymentsContainer.appendChild(airlineDetailsRow);
            });
        }

        function removeAirline(id) {
            document.getElementById(id).checked = false;
            updateSelectedAirlines();
        }

        // Prevent dropdown from closing when clicking inside
        document.querySelector('.dropdown-content').addEventListener('click', function(event) {
            event.stopPropagation();
        });

        // DOM ready handlers
        document.addEventListener('DOMContentLoaded', function() {
            // Check for success parameter in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('added')) {
                // Smooth scroll to top
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                // Fade out success message after 5 seconds
                const successMessage = document.querySelector('.success-message');
                if (successMessage) {
                    setTimeout(() => {
                        successMessage.style.opacity = '0';
                        setTimeout(() => {
                            successMessage.remove();
                        }, 300);
                    }, 5000);
                }
            }

            // Handle form submission
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner"></i> Adding Flight...';
            });
        });
    </script>
</body>
</html>