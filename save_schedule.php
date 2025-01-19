<?php
session_start();
require 'db_config.php';

// Check if the user session is active
if (!isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit();
}

try {
    // Database connection
    $conn = new mysqli(
        $db_config['host'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database']
    );

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Create the `interview_schedules` table if it doesn't exist
    $createTableSQL = "CREATE TABLE IF NOT EXISTS interview_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL,
        interview_date DATE NOT NULL,
        interview_time TIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!$conn->query($createTableSQL)) {
        throw new Exception("Error creating table: " . $conn->error);
    }

    // Input validation
    $email = $_SESSION['user_email'];
    $date = $_POST['date'] ?? null;
    $time = $_POST['time'] ?? null;

    if (empty($date) || empty($time)) {
        throw new Exception("Date and time are required.");
    }

    // Insert data into the table
    $sql = "INSERT INTO interview_schedules (user_email, interview_date, interview_time) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Failed to prepare SQL statement: " . $conn->error);
    }

    $stmt->bind_param("sss", $email, $date, $time);

    if ($stmt->execute()) {
        // Send confirmation email
        $to = $email;
        $subject = "Counselling Schedule Confirmation";
        $message = "Your counselling has been scheduled for $date at $time.";
        $headers = "From: noreply@zell.com\r\n";
        $headers .= "Reply-To: noreply@zell.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8";

        if (!mail($to, $subject, $message, $headers)) {
            throw new Exception("Failed to send confirmation email.");
        }

        // Set session variables for success message
        $_SESSION['message'] = "Counselling scheduled successfully!";
        $_SESSION['message_type'] = "success";
        $_SESSION['interview_date'] = $date;
        $_SESSION['interview_time'] = $time;

        // Redirect to the thank you page
        header('Location: thank_you.php');
    } else {
        throw new Exception("Error scheduling interview: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Set error message in session and redirect to result page
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
    header('Location: result.php');
}
?>
