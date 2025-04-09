<?php
session_start();
require_once 'db_config.php';
require_once 'mail_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode([
        'success' => false,
        'error' => 'You must be logged in to schedule an interview.'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Form validation
        if (empty($_POST['date']) || empty($_POST['time'])) {
            throw new Exception("Date and time are required fields");
        }

        $conn = new mysqli(
            $db_config['host'],
            $db_config['username'],
            $db_config['password'],
            $db_config['database']
        );

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $email = $_SESSION['user_email'];
        $name = $_SESSION['name'];
        $date = $conn->real_escape_string($_POST['date']);
        $time = $conn->real_escape_string($_POST['time']);

        // Insert appointment
        $sql = "INSERT INTO appointments (name, email, appointment_date, appointment_time) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $date, $time);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        // Send confirmation email
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        
        // Configure mail settings from mail_config.php
        configure_mailer($mail);
        
        $mail->addAddress($email, $name);
        $mail->Subject = 'Interview Scheduled - ZELL Education';
        
        $mailBody = "
            <h1>Interview Confirmation</h1>
            <p>Dear {$name},</p>
            <p>Your interview has been scheduled successfully for:</p>
            <p><strong>Date:</strong> {$date}</p>
            <p><strong>Time:</strong> {$time}</p>
            <p>Please be prepared 5 minutes before the scheduled time.</p>
            <p>Thank you,<br>ZELL Education Team</p>
        ";
        
        $mail->Body = $mailBody;
        $mail->AltBody = strip_tags($mailBody);
        
        if (!$mail->send()) {
            // Log email error but continue
            error_log("Email not sent. Error: " . $mail->ErrorInfo);
        }

        $_SESSION['message'] = "Your interview has been scheduled successfully!";
        $_SESSION['message_type'] = "success";
        
        echo json_encode([
            'success' => true,
            'message' => 'Your interview has been scheduled successfully!',
            'redirect' => 'thank_you.php'
        ]);
        
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "error";
        
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit();
}
?>