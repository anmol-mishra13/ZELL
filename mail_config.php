<?php
// mail_config.php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function configure_mailer($mail) {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'zelleducation20@gmail.com';
    $mail->Password = 'hegxdbjluqvdynf';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Sender
    $mail->setFrom('zelleducation20@gmail.com', 'Zell Education');
    
    // Format
    $mail->isHTML(true);
}

function sendMail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // Configure the mailer
        configure_mailer($mail);

        // Recipients
        $mail->addAddress($to);

        // Content
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>