<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'error' => 'Session expired']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $conn = new mysqli('localhost', 'root', '', 'zell_education');
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Create table if not exists
    $createTableSQL = "CREATE TABLE IF NOT EXISTS test_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL,
        answers JSON NOT NULL,
        flagged_questions JSON,
        score INT,
        total_questions INT NOT NULL,
        time_taken INT,
        submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('completed', 'in_progress', 'abandoned') DEFAULT 'completed',
        violations JSON,
        INDEX idx_user_email (user_email),
        INDEX idx_submission_date (submission_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->query($createTableSQL);

    $email = $_SESSION['user_email'];
    $answers = json_encode($data['answers']);
    $flagged = json_encode($data['flagged']);
    $totalQuestions = 10;
    $violations = json_encode($data['violations'] ?? null);
    
    $sql = "INSERT INTO test_submissions (
        user_email, answers, flagged_questions, total_questions, violations, status
    ) VALUES (?, ?, ?, ?, ?, 'completed')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", 
        $email, $answers, $flagged, $totalQuestions, $violations
    );
    
    if ($stmt->execute()) {
        // Send email notification
        $to = $email;
        $subject = "Test Completion Confirmation";
        $message = "Thank you for completing the test. Please proceed to schedule your interview.";
        $headers = "From: noreply@zell.com";
        
        mail($to, $subject, $message, $headers);
        
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error saving test submission");
    }

    $stmt->close();
    $conn->close();

} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}