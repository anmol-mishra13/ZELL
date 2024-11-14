<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

try {
    $conn = new mysqli('localhost', 'root', '', 'zell_education');
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $email = $_SESSION['user_email'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    
    $sql = "INSERT INTO interview_schedules (user_email, interview_date, interview_time) 
            VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $date, $time);
    
    if ($stmt->execute()) {
        // Send confirmation email
        $to = $email;
        $subject = "Councelling Schedule Confirmation";
        $message = "Your Councelling has been scheduled for $date at $time.";
        $headers = "From: noreply@zell.com";
        
        mail($to, $subject, $message, $headers);
        
        $_SESSION['message'] = "Councelling Scheduled successfully!";
        $_SESSION['message_type'] = "success";
        $_SESSION['interview_date'] = $date;
        $_SESSION['interview_time'] = $time;
    } else {
        throw new Exception("Error scheduling interview");
    }

    $stmt->close();
    $conn->close();

    header('Location: thank_you.php');
} catch(Exception $e) {
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    header('Location: result.php');
}
?>