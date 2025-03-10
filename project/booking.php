<?php 
session_start(); 
$selected_flight = $_SESSION['selected_flight'] ?? null;


$page_settings = [
    'title' => 'Book Your Flight Now',
    'meta_description' => 'Book your travel appointment with us',
    'meta_keywords' => 'booking, travel, appointment',
    'current_page' => 'booking',
    'indexable' => false  
];


define('SITE_NAME', 'Travel Agency');  // Add this line for footer.php
define('SITE_EMAIL', 'simpleblog42@gmail.com');
define('SITE_PHONE', '+44 123 456 7890');
define('SITE_ADDRESS', 'London, United Kingdom');

// Add form processing logic at the top
$message_status = '';
$error_messages = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'php_mailer/vendor/autoload.php';
    
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'simpleblog42@gmail.com';
        $mail->Password = 'tzkc uino zexm yjcj';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('simpleblog42@gmail.com', 'Booking System');
        $mail->addAddress('simpleblog42@gmail.com');
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'New Booking from ' . $name;
        
        // Add flight details to email if available
        $flightDetails = '';
        if ($selected_flight) {
            $flightDetails = "
                <div style='margin-top: 30px; padding: 20px; background: #f8f4fb; border-left: 4px solid #69247C; border-radius: 5px;'>
                    <h3 style='color: #69247C; margin-bottom: 20px; font-size: 20px;'>Flight Details</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>Flight ID:</strong></td>
                            <td style='padding: 8px;'>{$selected_flight['id']}</td>
                        </tr>
                        <tr style='background: #fff;'>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>Airline:</strong></td>
                            <td style='padding: 8px;'>{$selected_flight['airline']}</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>From:</strong></td>
                            <td style='padding: 8px;'>{$selected_flight['from']}</td>
                        </tr>
                        <tr style='background: #fff;'>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>To:</strong></td>
                            <td style='padding: 8px;'>{$selected_flight['to']}</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>Departure:</strong></td>
                            <td style='padding: 8px;'>{$selected_flight['departure']}</td>
                        </tr>
                        <tr style='background: #fff;'>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>Return:</strong></td>
                            <td style='padding: 8px;'>{$selected_flight['return']}</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>Passengers:</strong></td>
                            <td style='padding: 8px;'>{$selected_flight['adults']} Adults, {$selected_flight['children']} Children, {$selected_flight['infants']} Infants</td>
                        </tr>
                        <tr style='background: #fff;'>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>Class:</strong></td>
                            <td style='padding: 8px;'>{$selected_flight['class']}</td>
                        </tr>
                    </table>
                </div>
            ";
        }

        $mailContent = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                padding: 10px !important;
            }
            .header {
                padding: 15px !important;
            }
            .content {
                padding: 15px !important;
            }
            .detail-table {
                display: block !important;
                width: 100% !important;
            }
            .detail-table tr {
                display: block !important;
                margin-bottom: 10px !important;
            }
            .detail-table td {
                display: block !important;
                width: 100% !important;
                padding: 5px 0 !important;
            }
            .flight-details {
                padding: 10px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f6f6;">
    <div class="email-container" style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <div class="header" style="background: linear-gradient(135deg, #69247C, #DA498D); padding: 25px; border-radius: 10px 10px 0 0; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px; line-height: 1.2;">New Booking Request</h1>
        </div>
        
        <div class="content" style="background: #ffffff; padding: 25px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <!-- Customer Details -->
            <div style="margin-bottom: 25px;">
                <h2 style="color: #69247C; font-size: 20px; margin: 0 0 15px 0;">Customer Information</h2>
                <table class="detail-table" style="width: 100%; border-collapse: collapse;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding: 10px 0; color: #666666; width: 120px;"><strong style="color: #69247C;">Name:</strong></td>
                        <td style="padding: 10px 0;">{$name}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666666;"><strong style="color: #69247C;">Phone:</strong></td>
                        <td style="padding: 10px 0;">{$phone}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666666;"><strong style="color: #69247C;">Email:</strong></td>
                        <td style="padding: 10px 0;">{$email}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666666;"><strong style="color: #69247C;">Message:</strong></td>
                        <td style="padding: 10px 0;">{$message}</td>
                    </tr>
                </table>
            </div>
HTML;

        if ($selected_flight) {
            $mailContent .= <<<HTML
            <!-- Flight Details -->
            <div class="flight-details" style="background: #f8f4fb; border-left: 4px solid #69247C; padding: 20px; border-radius: 5px; margin-top: 25px;">
                <h2 style="color: #69247C; font-size: 20px; margin: 0 0 15px 0;">Flight Details</h2>
                <table class="detail-table" style="width: 100%; border-collapse: collapse;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding: 10px 0; color: #666666; width: 120px;"><strong style="color: #69247C;">Flight ID:</strong></td>
                        <td style="padding: 10px 0;">{$selected_flight['id']}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666666;"><strong style="color: #69247C;">Airline:</strong></td>
                        <td style="padding: 10px 0;">{$selected_flight['airline']}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666666;"><strong style="color: #69247C;">From:</strong></td>
                        <td style="padding: 10px 0;">{$selected_flight['from']}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666666;"><strong style="color: #69247C;">To:</strong></td>
                        <td style="padding: 10px 0;">{$selected_flight['to']}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666666;"><strong style="color: #69247C;">Departure:</strong></td>
                        <td style="padding: 10px 0;">{$selected_flight['departure']}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666666;"><strong style="color: #69247C;">Return:</strong></td>
                        <td style="padding: 10px 0;">{$selected_flight['return']}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666666;"><strong style="color: #69247C;">Passengers:</strong></td>
                        <td style="padding: 10px 0;">{$selected_flight['adults']} Adults, {$selected_flight['children']} Children, {$selected_flight['infants']} Infants</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666666;"><strong style="color: #69247C;">Class:</strong></td>
                        <td style="padding: 10px 0;">{$selected_flight['class']}</td>
                    </tr>
                </table>
            </div>
HTML;
        }

        $mailContent .= <<<HTML
        </div>
        <div style="text-align: center; margin-top: 20px; color: #666666; font-size: 12px;">
            <p style="margin: 0;">This is an automated message from your booking system.</p>
        </div>
    </div>
</body>
</html>
HTML;

        $mail->Body = $mailContent;
        $mail->AltBody = strip_tags($mailContent);

        $mail->send();
        $_SESSION['message_status'] = 'success';
        $_SESSION['message'] = 'Your booking request has been sent successfully!';
        
        // Clear the selected flight from session after successful submission
        unset($_SESSION['selected_flight']);
    } catch (Exception $e) {
        $_SESSION['message_status'] = 'error';
        $_SESSION['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get message from session if exists
$message_status = $_SESSION['message_status'] ?? '';
$message = $_SESSION['message'] ?? '';

// Clear the session messages
unset($_SESSION['message_status']);
unset($_SESSION['message']);

// Include header
include('./includes/header.php'); 
?>

<style>
    :root {
        --primary-color: #69247C;
        --primary-dark: #541d63;
        --primary-rgb: 105, 36, 124;
    }

    .booking-section {
        background: #F9E6CF;
        padding: 110px 0 30px 0;
    }
    .flex-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        gap: 30px;
        padding: 0 20px;
    }
    .promo-section {
        flex: 1;
        background: linear-gradient(145deg, #fff, #f8f8f8);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        border-left: 4px solid var(--primary-color);
    }
    .booking-container {
        flex: 1;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .promo-title {
        color: var(--primary-color);
        font-size: 1.8em;
        margin-bottom: 15px;
    }
    .promo-text {
        font-size: 1.1em;
        line-height: 1.6;
        margin-bottom: 30px;
    }
    .reasons-list {
        text-align: left;
        margin: 5px 0;
        padding-left: 0;
        list-style: none;
    }
    .reasons-list li {
        margin-bottom: 5px;
        position: relative;
        padding-left: 35px;
    }
    .reasons-list li:before {
        content: "";
        font-family: "Font Awesome 5 Free";
        color: var(--primary-color);
        position: absolute;
        left: 0;
        top: 3px;
        font-weight: 900;
        font-size: 1.4em;
    }
    .reasons-list li:nth-child(1):before {
        content: "\f155"; /* dollar sign */
    }
    .reasons-list li:nth-child(2):before {
        content: "\f3c5"; /* map marker */
    }
    .reasons-list li:nth-child(3):before {
        content: "\f017"; /* clock */
    }
    .reason-title {
        display: block;
        color: var(--primary-color);
        font-size: 1em;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .reason-text {
        color: #666;
        line-height: 1.5;
    }
    .call-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        font-size: 1.2em;
        font-weight: bold;
        color: var(--primary-color);
        margin: 30px 0;
        padding: 15px;
        background: rgba(105, 36, 124, 0.1);
        border-radius: 8px;
        text-align: center;
    }
    @media (max-width: 768px) {
        .flex-container {
            flex-direction: column;
            padding: 15px;
        }
    }
    .form-group {
        margin-bottom: 25px;
        position: relative;
    }
    .form-group i {
        position: absolute;
        left: 12px;
        top: 42px;
        color: var(--primary-color);
        font-size: 1.2em;
    }
    .form-group input, 
    .form-group textarea {
        width: 100%;
        padding: 12px 12px 12px 40px;
        border: 2px solid #eee;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 1rem;
    }
    .form-group input:focus, .form-group textarea:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 5px rgba(var(--primary-rgb), 0.2);
    }
    .submit-btn {
        background: var(--primary-color);
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
    }
    .submit-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }
    .booking-title {
        text-align: center;
        margin-bottom: 30px;
        color: var(--primary-color);
        font-size: 1.8em;
        position: relative;
        padding-bottom: 15px;
    }
    .booking-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 2px;
    }
    @media (max-width: 768px) {
        .booking-container {
            margin: 15px;
            padding: 20px;
        }
        .promo-section, .booking-container {
            margin: 0;
            border-radius: 8px;
        }
    }

    .phone-button-container {
        position: fixed;
        left: 30px;  /* Changed from right to left */
        bottom: 30px;
        z-index: 1000;
    }

    .phone-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        background: linear-gradient(135deg, var(--primary-color), #8c2b9f);
        color: white;
        padding: 15px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 1.2em;
        font-weight: bold;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(105, 36, 124, 0.4);
    }

    .phone-button:hover {
        background: linear-gradient(135deg, #8c2b9f, var(--primary-color));
        transform: translateY(-3px);
        box-shadow: 0 6px 25px rgba(105, 36, 124, 0.6);
    }

    .phone-button i {
        font-size: 1.3em;
        animation: pulse 1.5s infinite;
        color: #ffd700;
    }

    .phone-number {
        border-left: 2px solid rgba(255, 255, 255, 0.3);
        padding-left: 15px;
    }

    @media (max-width: 768px) {
        .phone-button-container {
            left: 20px;  /* Changed from right to left */
            bottom: 20px;
        }
        
        .phone-button {
            padding: 12px 25px;
            font-size: 1.1em;
        }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .message {
        padding: 15px;
        margin: 20px 0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .success-message {
        background-color: rgba(75, 181, 67, 0.1);
        border: 1px solid #4BB543;
        color: #4BB543;
    }

    .error-message {
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid #EF4444;
        color: #EF4444;
    }

    .message i {
        font-size: 1.2em;
    }

    .selected-flight-details {
        background: rgba(105, 36, 124, 0.05);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        border-left: 4px solid var(--primary-color);
    }

    .flight-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-item .label {
        font-size: 0.9em;
        color: var(--primary-color);
        font-weight: bold;
    }

    .info-item .value {
        font-size: 1.1em;
        color: var(--charcoal-gray);
    }

    .booking-summary {
        margin-top: 40px;
        padding-top: 40px;
        border-top: 2px dashed rgba(105, 36, 124, 0.2);
    }

    .booking-summary h2 {
        color: var(--primary-color);
        font-size: 1.8em;
        margin-bottom: 20px;
        text-align: center;
        position: relative;
    }

    .booking-summary h2:after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 2px;
    }

    .flight-details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        background: linear-gradient(145deg, #fff, #f8f4fb);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(105, 36, 124, 0.1);
        border-left: 4px solid var(--primary-color);
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 8px;
    }

    .detail-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-color);
        border-radius: 50%;
        color: white;
        font-size: 1.2em;
    }

    .detail-content {
        flex: 1;
    }

    .detail-label {
        font-size: 0.9em;
        color: var(--charcoal-gray);
        margin-bottom: 4px;
    }

    .detail-value {
        font-size: 1.1em;
        color: var(--primary-color);
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .flight-details-grid {
            grid-template-columns: 1fr;
        }
    }

    .selected-flight-section {
        padding: 60px 0;
        background: linear-gradient(145deg, #f8f4fb, #fff);
        margin-top: 20px;
    }

    .section-title {
        text-align: center;
        font-size: 2em;
        color: var(--primary-color);
        margin-bottom: 40px;
        position: relative;
    }

    .section-title:after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--pink-vivid));
        border-radius: 2px;
    }

    .selected-flight-section .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .selected-flight-section .flight-details-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-top: 30px;
    }

    @media (max-width: 992px) {
        .selected-flight-section .flight-details-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .selected-flight-section .flight-details-grid {
            grid-template-columns: 1fr;
        }
    }

    .why-book-section {
        padding: 80px 0;
        background: #F9E6CF;
        position: relative;
        overflow: hidden;
    }

    .why-book-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(to right, transparent, var(--primary-color), transparent);
    }

    .why-book-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        text-align: center;
    }

    .why-book-intro {
        max-width: 800px;
        margin: 0 auto 60px;
    }

    .why-book-intro h2 {
        font-size: 2.5em;
        color: var(--primary-color);
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
    }

    .why-book-intro h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--pink-vivid));
        border-radius: 2px;
    }

    .why-book-intro p {
        font-size: 1.2em;
        color: var(--charcoal-gray);
        line-height: 1.6;
    }

    .reasons-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-bottom: 50px;
    }

    .reason-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(105, 36, 124, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .reason-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, var(--primary-color), var(--pink-vivid));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .reason-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(105, 36, 124, 0.15);
    }

    .reason-card:hover::before {
        opacity: 1;
    }

    .reason-icon {
        font-size: 2.5em;
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .reason-title {
        font-size: 1.3em;
        color: var(--primary-color);
        margin-bottom: 15px;
        font-weight: bold;
    }

    .reason-description {
        color: var(--charcoal-gray);
        line-height: 1.6;
    }

    .book-cta {
        text-align: center;
        margin-top: 40px;
        padding: 30px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(105, 36, 124, 0.1);
        font-size: 1.3em;
        color: var(--primary-color);
        font-weight: bold;
    }

    .book-cta i {
        color: var(--pink-vivid);
        margin-right: 10px;
    }

    @media (max-width: 992px) {
        .reasons-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .reasons-grid {
            grid-template-columns: 1fr;
        }
        
        .why-book-intro h2 {
            font-size: 2em;
        }
    }

    .reasons-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin: 40px 0;
    }

    .reason-card {
        background: linear-gradient(145deg, #fff, #f8f4fb);
        padding: 25px;
        text-align: left;
    }

    .reason-icon {
        background: var(--primary-color);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .reason-icon i {
        color: white;
        font-size: 1.5em;
    }

    .book-cta {
        background: linear-gradient(135deg, var(--primary-color), var(--pink-vivid));
        color: white;
        padding: 25px;
        border-radius: 15px;
        font-size: 1.4em;
        margin-top: 40px;
        box-shadow: 0 10px 30px rgba(105, 36, 124, 0.2);
    }

    .book-cta i {
        color: #ffd700;
        animation: pulse 1.5s infinite;
    }
</style>

<script>
    // Add this before closing </head> tag
    document.addEventListener('DOMContentLoaded', function() {
        const messageDiv = document.querySelector('.message');
        if (messageDiv) {
            setTimeout(function() {
                messageDiv.style.transition = 'opacity 0.5s ease-out';
                messageDiv.style.opacity = '0';
                setTimeout(function() {
                    messageDiv.style.display = 'none';
                }, 500);
            }, 5000); // Message will start fading after 5 seconds
        }
    });
</script>

<div class="phone-button-container">
    <a href="tel:+44 20 3286 8301" class="phone-button">
        <i class="fas fa-phone-alt"></i>
        <span class="phone-number">020 3286 8301</span>
    </a>
</div>

<section class="booking-section">
    <div class="flex-container">
        <div class="promo-section">
            <h2 class="promo-title">Why Book with Us Over the Phone?</h2>
            <p class="promo-text">
                Looking for exclusive deals you won't find online? Our phone-only special fares are just a call away. 
                Let us help you unlock unbeatable savings tailored just for you!
            </p>
            
            <h3>Top Reasons to Call Now</h3>
            <ul class="reasons-list">
                <li>
                    <span class="reason-title">Exclusive Deals</span>
                    <span class="reason-text">Access special fares available only over the phone.</span>
                </li>
                <li>
                    <span class="reason-title">Priority Assistance</span>
                    <span class="reason-text">Your calls are answered on top priority by our expert travel advisors.</span>
                </li>
                <li>
                    <span class="reason-title">Real-Time Availability</span>
                    <span class="reason-text">Check the current availability of our best-buy fares instantly.</span>
                </li>
            </ul>
            
            <div class="call-action">
                <p>📞 Don't wait—call now and secure the best deal for your next journey!</p>
            </div>
            
        </div>

        <div class="booking-container">
            <h1 class="booking-title">Fill the Form We Call You Right Back</h1>
            
            

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" required 
                           placeholder="Enter your full name">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <i class="fas fa-phone"></i>
                    <input type="tel" id="phone" name="phone" required 
                           placeholder="Enter your phone number">
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" required 
                           placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label for="message">Message (Optional)</label>
                    <i class="fas fa-comment"></i>
                    <textarea id="message" name="message" rows="4" 
                              placeholder="Enter any additional information"></textarea>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Submit Booking
                </button>

                <?php if ($message_status === 'success'): ?>
                    <div class="message success-message">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php elseif ($message_status === 'error'): ?>
                    <div class="message error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<?php if ($selected_flight): ?>
<section class="selected-flight-section">
    <div class="container">
        <h2 class="section-title">Your Selected Flight Details</h2>
        <div class="flight-details-grid">
            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-plane"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Airline</div>
                    <div class="detail-value"><?php echo htmlspecialchars($selected_flight['airline']); ?></div>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-plane-departure"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">From</div>
                    <div class="detail-value"><?php echo htmlspecialchars($selected_flight['from']); ?></div>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-plane-arrival"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">To</div>
                    <div class="detail-value"><?php echo htmlspecialchars($selected_flight['to']); ?></div>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Departure</div>
                    <div class="detail-value"><?php echo htmlspecialchars($selected_flight['departure']); ?></div>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Return</div>
                    <div class="detail-value"><?php echo htmlspecialchars($selected_flight['return']); ?></div>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Passengers</div>
                    <div class="detail-value">
                        <?php echo htmlspecialchars($selected_flight['adults']); ?> Adults, 
                        <?php echo htmlspecialchars($selected_flight['children']); ?> Children, 
                        <?php echo htmlspecialchars($selected_flight['infants']); ?> Infants
                    </div>
                </div>
            </div>

            <div class="detail-item">
                <div class="detail-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="detail-content">
                    <div class="detail-label">Class</div>
                    <div class="detail-value"><?php echo htmlspecialchars($selected_flight['class']); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="why-book-section">
    <div class="why-book-container">
        <div class="why-book-intro">
            <h2>Why Book With Us?</h2>
            <p>When it comes to planning your perfect trip, we make every step effortless and rewarding. Here's why travelers trust us:</p>
        </div>
        <div class="reasons-grid">
            <div class="reason-card">
                <div class="reason-icon">
                    <i class="fas fa-pound-sign"></i>
                </div>
                <div class="reason-title">Unbeatable Fares</div>
                <div class="reason-description">We offer competitive prices that suit every budget, ensuring you get the best value for your money.</div>
            </div>
            <div class="reason-card">
                <div class="reason-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="reason-title">Personalized Service</div>
                <div class="reason-description">Our travel experts are dedicated to tailoring your journey to your specific needs and preferences.</div>
            </div>
            <div class="reason-card">
                <div class="reason-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="reason-title">Exclusive Deals</div>
                <div class="reason-description">Gain access to special offers and promotions you won't find elsewhere in the market.</div>
            </div>
            <div class="reason-card">
                <div class="reason-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="reason-title">24/7 Support</div>
                <div class="reason-description">Whether it's day or night, we're here to assist you anytime you need help.</div>
            </div>
            <div class="reason-card">
    <div class="reason-icon">
        <i class="fas fa-tags"></i>
    </div>
    <div class="reason-title">Exclusive Deals</div>
    <div class="reason-description">Gain access to special offers and discounts you won’t find anywhere else.</div>
</div>
<div class="reason-card">
    <div class="reason-icon">
        <i class="fas fa-clock"></i>
    </div>
    <div class="reason-title">Real-Time Availability</div>
    <div class="reason-description">Book instantly with real-time updates on availability and pricing for your convenience.</div>
</div>
            <div class="reason-card">
                <div class="reason-icon">
                    <i class="fas fa-award"></i>
                </div>
                <div class="reason-title">Trusted Expertise</div>
                <div class="reason-description">Years of experience ensure your travel is smooth and stress-free from booking to arrival.</div>
            </div>
            
        </div>
        <div class="book-cta">
            <i class="fas fa-phone-alt"></i> Book with us today for a seamless experience and incredible savings!
        </div>
    </div>
</section>

<?php include('./includes/footer.php'); ?>
