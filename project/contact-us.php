<?php
session_start();

// Define required variables for header
$page_settings = [
    'title' => 'Contact Us',
    'description' => 'Get in touch with our travel experts. We provide 24/7 customer support for all your travel needs.',
    'keywords' => 'contact us, travel support, customer service, travel agency contact, flight booking help',
    'meta_description' => 'Get in touch with our travel experts',
    'meta_keywords' => 'contact, travel, support, help',
    'current_page' => 'contact',
    'indexable' => true
];

// Define constants for footer
define('SITE_NAME', 'Travel Agency');
define('SITE_EMAIL', 'simpleblog42@gmail.com');
define('SITE_PHONE', '+44 123 456 7890');
define('SITE_ADDRESS', 'London, United Kingdom');

// Process form submission
$message_status = '';
$message = '';

// Check if this is a callback request
$isCallbackRequest = isset($_SESSION['callback_request']) && $_SESSION['callback_request'] === true;
$callbackData = $_SESSION['callback_data'] ?? null;

// Clear callback session data after retrieving
unset($_SESSION['callback_request']);
unset($_SESSION['callback_data']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'php_mailer/vendor/autoload.php';
    
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message_content = $_POST['message'] ?? '';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Server settings for contact form (using different credentials to avoid conflicts)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'simpleblog42@gmail.com'; // Use a different email if needed
        $mail->Password = 'tzkc uino zexm yjcj';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('simpleblog42@gmail.com', 'Contact Form');
        $mail->addAddress('simpleblog42@gmail.com');
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Contact Form: ' . $subject;
        
        // Modify subject if it's a callback request
        if (isset($_POST['is_callback']) && $_POST['is_callback'] === 'true') {
            $mail->Subject = 'Urgent: Callback Request for Flight Booking';
            
            // Add flight details to the mail content
            $mailContent = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #69247C, #DA498D); padding: 30px; border-radius: 10px 10px 0 0;'>
                        <h1 style='color: #fff; margin: 0; text-align: center;'>Callback Request</h1>
                    </div>
                    <div style='background: #fff; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                        <p><strong>Name:</strong> {$name}</p>
                        <p><strong>Email:</strong> {$email}</p>
                        <p><strong>Phone:</strong> {$_POST['phone']}</p>
                        <p><strong>Phone:</strong> {$_POST['phone']}</p>
                        <p><strong>Preferred Time:</strong> {$_POST['preferred_time']}</p>
                        
                        <h2 style='color: #69247C; margin: 30px 0 20px;'>Flight Details</h2>
                        <div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;'>
                            <p><strong>From:</strong> {$_POST['flight_from']}</p>
                            <p><strong>To:</strong> {$_POST['flight_to']}</p>
                            <p><strong>Departure Date:</strong> {$_POST['flight_departure']}</p>
                            <p><strong>Return Date:</strong> {$_POST['flight_return']}</p>
                            <p><strong>Airline:</strong> {$_POST['flight_airline']}</p>
                            <p><strong>Class:</strong> {$_POST['flight_class']}</p>
                            <p><strong>Passengers:</strong> {$_POST['flight_adults']} Adults, {$_POST['flight_children']} Children, {$_POST['flight_infants']} Infants</p>
                        </div>

                        <h2 style='color: #69247C; margin-bottom: 20px;'>Additional Notes</h2>
                        <p style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>{$message_content}</p>
                    </div>
                </div>
            ";
        } else {
            $mailContent = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #69247C, #DA498D); padding: 30px; border-radius: 10px 10px 0 0;'>
                        <h1 style='color: #fff; margin: 0; text-align: center;'>New Contact Message</h1>
                    </div>
                    <div style='background: #fff; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                        <p><strong>Name:</strong> {$name}</p>
                        <p><strong>Email:</strong> {$email}</p>
                        <p><strong>Subject:</strong> {$subject}</p>
                        <p><strong>Message:</strong></p>
                        <p style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>{$message_content}</p>
                    </div>
                </div>
            ";
        }

        $mail->Body = $mailContent;
        $mail->AltBody = strip_tags($mailContent);

        $mail->send();
        $_SESSION['contact_message_status'] = 'success';
        $_SESSION['contact_message'] = 'Thank you for your message. We\'ll get back to you soon!';
    } catch (Exception $e) {
        $_SESSION['contact_message_status'] = 'error';
        $_SESSION['contact_message'] = "Message could not be sent. Error: {$mail->ErrorInfo}";
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get message from session if exists
$message_status = $_SESSION['contact_message_status'] ?? '';
$message = $_SESSION['contact_message'] ?? '';  // Fixed: Added missing $ symbol

// Clear the session messages
unset($_SESSION['contact_message_status']);
unset($_SESSION['contact_message']);

include('./includes/header.php');
?>

<style>
    :root {
        --primary-color: #69247C;
        --secondary-color: #DA498D;
        --dark-color: #2C3E50;
        --light-color: #F8F9FA;
        --success-color: #2ECC71;
        --error-color: #E74C3C;
    }

    .contact-hero {
        position: relative;
        min-height: 400px;
        background: linear-gradient(135deg, rgba(105, 36, 124, 0.95), rgba(218, 73, 141, 0.95));
        clip-path: polygon(0 0, 100% 0, 100% 85%, 0% 100%);
        padding: 120px 0 160px;
    }

    .hero-content {
        max-width: 800px;
        margin: 0 auto;
        text-align: center;
        color: white;
        background: rgba(255, 255, 255, 0.2);
    }

    .hero-title {
        font-size: 3.5em;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    .hero-subtitle {
        font-size: 1.2em;
        opacity: 0.9;
        line-height: 1.6;
    }

    .contact-section {
        margin-top: -100px;
        padding: 0 20px;
        position: relative;
        z-index: 2;
        margin-bottom:30px;
    }

    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 30px;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    }

    .contact-info {
        background: linear-gradient(135deg, #69247C, #DA498D);
        padding: 60px 40px;
        color: white;
    }

    .lottie-container {
        display: flex;
        justify-content: center;
        margin-bottom: 40px;
    }

    .info-items {
        display: grid;
        gap: 30px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    .info-item:hover {
        transform: translateY(-5px);
    }

    .info-icon {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4em;
    }

    .contact-form {
        padding: 60px 40px;
    }

    .form-title {
        font-size: 2em;
        color: var(--dark-color);
        margin-bottom: 30px;
        position: relative;
    }

    .form-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -10px;
        width: 60px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--dark-color);
        font-weight: 500;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 20px;
        border: 2px solid #E5E7EB;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(105, 36, 124, 0.1);
        outline: none;
    }

    .submit-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 15px 30px;
        border: none;
        border-radius: 10px;
        font-size: 1.1em;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(105, 36, 124, 0.2);
    }

    @media (max-width: 992px) {
        .contact-container {
            grid-template-columns: 1fr;
        }
        
        .contact-info {
            padding: 40px 20px;
        }
        
        .contact-form {
            padding: 40px 20px;
        }
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5em;
        }
        
        .contact-hero {
            padding: 100px 0 140px;
        }
    }

    /* Add these styles to your existing CSS */
    .callback-notice {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .callback-notice i {
        font-size: 2.5em;
        color: rgba(255, 255, 255, 0.9);
    }

    .callback-text {
        flex: 1;
    }

    .callback-text h3 {
        margin: 0 0 5px 0;
        font-size: 1.2em;
        font-weight: 600;
    }

    .callback-text p {
        margin: 0;
        font-size: 0.95em;
        opacity: 0.9;
    }

    /* Preferred Time Options Styles */
    .time-options {
        display: grid;
        gap: 12px;
        margin-bottom: 15px;
    }

    .time-option {
        display: flex;
        align-items: center;
        padding: 12px;
        border: 2px solid #E5E7EB;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .time-option:hover {
        border-color: var(--primary-color);
        background: rgba(105, 36, 124, 0.05);
    }

    .time-option input[type="radio"] {
        display: none;
    }

    .time-label {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        color: var(--dark-color);
    }

    .time-label i {
        color: var(--primary-color);
        font-size: 1.2em;
    }

    .time-option input[type="radio"]:checked + .time-label {
        color: var(--primary-color);
        font-weight: 600;
    }

    .time-option input[type="radio"]:checked + .time-label i {
        color: var(--secondary-color);
    }

    .custom-time {
        width: 100%;
        padding: 12px;
        border: 2px solid #E5E7EB;
        border-radius: 10px;
        margin-top: 10px;
        display: none;
    }

    .custom-time.show {
        display: block;
    }

    /* Mobile responsiveness for time options */
    @media (max-width: 768px) {
        .time-options {
            gap: 8px;
        }

        .time-option {
            padding: 10px;
        }

        .time-label {
            font-size: 0.9em;
        }

        .time-label i {
            font-size: 1.1em;
        }
    }
</style>

<div class="contact-hero">
    <div class="hero-content">
        <?php if ($isCallbackRequest): ?>
            <h1 class="hero-title">Flight Callback Request</h1>
            <p class="hero-subtitle">We'll call you back within 10 minutes! Please provide your contact details below.</p>
        <?php else: ?>
            <h1 class="hero-title">Get in Touch</h1>
            <p class="hero-subtitle">Have questions? We'd love to hear from you. Our team is always here to help!</p>
        <?php endif; ?>
    </div>
</div>

<section class="contact-section">
    <div class="contact-container">
        <div class="contact-info">
            <div class="lottie-container">
                <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
                <dotlottie-player 
                    src="https://lottie.host/4c82fa6a-d06f-4348-a539-ad706bc7994c/v3CVpEI6ie.lottie" 
                    background="transparent" 
                    speed="1" 
                    style="width: 300px; height: 300px" 
                    loop 
                    autoplay>
                </dotlottie-player>
            </div>
            
            <div class="info-items">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h3>Our Location</h3>
                        <p><?php echo SITE_ADDRESS; ?></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <h3>Phone Number</h3>
                        <p><?php echo SITE_PHONE; ?></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h3>Email Address</h3>
                        <p><?php echo SITE_EMAIL; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <?php if ($isCallbackRequest): ?>
                <div class="callback-notice">
                    <i class="fas fa-headset"></i>
                    <div class="callback-text">
                        <h3>Flight Callback Request</h3>
                        <p>Our travel expert will call you back within 10 minutes!</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <h2 class="form-title">
                <?php echo $isCallbackRequest ? 'Schedule Your Callback' : 'Send us a Message'; ?>
            </h2>
            
            <?php if ($message_status === 'success' || $message_status === 'error'): ?>
                <div class="message <?php echo $message_status === 'success' ? 'success-message' : 'error-message'; ?>">
                    <i class="fas fa-<?php echo $message_status === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <?php if ($isCallbackRequest): ?>
                    <!-- Hidden fields for callback data - keep these to store in email -->
                    <input type="hidden" name="is_callback" value="true">
                    <?php foreach ($callbackData as $key => $value): ?>
                        <input type="hidden" name="flight_<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <?php if ($isCallbackRequest): ?>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="form-group callback-time">
                        <label for="preferred_time">Preferred Callback Time</label>
                        <div class="time-options">
                            <label class="time-option">
                                <input type="radio" name="preferred_time" value="As soon as possible" checked>
                                <span class="time-label">
                                    <i class="fas fa-bolt"></i>
                                    As soon as possible
                                </span>
                            </label>
                            <label class="time-option">
                                <input type="radio" name="preferred_time" value="Within 1 hour">
                                <span class="time-label">
                                    <i class="fas fa-clock"></i>
                                    Within 1 hour
                                </span>
                            </label>
                            <label class="time-option">
                                <input type="radio" name="preferred_time" value="custom">
                                <span class="time-label">
                                    <i class="fas fa-calendar-alt"></i>
                                    Specify time
                                </span>
                            </label>
                        </div>
                        <input type="text" id="custom_time" name="custom_time" 
                               class="custom-time" 
                               placeholder="Enter your preferred time"
                               style="display: none;">
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="message">
                        <?php echo $isCallbackRequest ? 'Additional Notes (Optional)' : 'Message'; ?>
                    </label>
                    <textarea id="message" name="message" rows="5" <?php echo !$isCallbackRequest ? 'required' : ''; ?>></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <?php if ($isCallbackRequest): ?>
                        <i class="fas fa-phone"></i> Request Callback
                    <?php else: ?>
                        <i class="fas fa-paper-plane"></i> Send Message
                    <?php endif; ?>
                </button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeOptions = document.querySelectorAll('input[name="preferred_time"]');
    const customTimeInput = document.getElementById('custom_time');

    if (timeOptions && customTimeInput) {
        timeOptions.forEach(option => {
            option.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customTimeInput.style.display = 'block';
                    customTimeInput.required = true;
                } else {
                    customTimeInput.style.display = 'none';
                    customTimeInput.required = false;
                }
            });
        });
    }
});
</script>

<?php include('./includes/footer.php'); ?>
