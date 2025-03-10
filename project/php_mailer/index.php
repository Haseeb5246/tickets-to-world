<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

$message_status = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'simpleblog42@gmail.com';
        $mail->Password = 'tzkc uino zexm yjcj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('simpleblog42@gmail.com', 'Booking System');
        $mail->addAddress('simpleblog42@gmail.com');
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Booking from' . $name;
        
        $mailContent = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 10px;'>
                <div style='background: linear-gradient(135deg, #69247C, #DA498D); padding: 20px; border-radius: 10px 10px 0 0;'>
                    <h2 style='color: #fff; margin: 0; text-align: center; font-size: 20px;'>New Booking</h2>
                </div>
                
                <div style='background: #fff; padding: 20px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 8px; color: #666; width: 30%;'><strong style='color: #69247C;'>Name:</strong></td>
                            <td style='padding: 8px;'>{$name}</td>
                        </tr>
                        <tr style='background: #f8f4fb;'>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>Phone:</strong></td>
                            <td style='padding: 8px;'>{$phone}</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>Email:</strong></td>
                            <td style='padding: 8px;'>{$email}</td>
                        </tr>
                        <tr style='background: #f8f4fb;'>
                            <td style='padding: 8px; color: #666;'><strong style='color: #69247C;'>Message:</strong></td>
                            <td style='padding: 8px;'>{$message}</td>
                        </tr>
                    </table>
                </div>
                
                <div style='text-align: center; margin-top: 20px; color: #666; font-size: 12px;'>
                    <p>This is an automated message from your booking system.</p>
                </div>
            </div>";

        $mail->Body = $mailContent;
        $mail->AltBody = strip_tags($mailContent);

        $mail->send();
        echo '<div class="alert alert-success">Message has been sent successfully!</div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-error">Message could not be sent. Error: ' . $mail->ErrorInfo . '</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        .alert {
            padding: 15px;
            margin: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #dff0d8;
            border-color: #d6e9c6;
            color: #3c763d;
        }
        .alert-error {
            background-color: #f2dede;
            border-color: #ebccd1;
            color: #a94442;
        }
    </style>
</head>
<body>
    <a href="../booking.php">← Back to Booking Form</a>
</body>
</html>