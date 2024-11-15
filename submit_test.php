<?php
session_start();
require 'mail_config.php';
require 'db_config.php';

if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'error' => 'Session expired']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $conn = $conn = new mysqli(
        $db_config['host'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database']
    );

    if ($conn->connect_error) throw new Exception("Connection failed: " . $conn->connect_error);

    // Get correct answers from questions
    $questions = json_decode(include 'questions.php', true);
    $score = 0;
    $totalQuestions = count($questions);

    // Calculate score
    foreach ($questions as $question) {
        if (isset($data['answers'][$question['id']]) && 
            $data['answers'][$question['id']] === $question['correct_answer']) {
            $score++;
        }
    }

    $percentageScore = ($score / $totalQuestions) * 100;

    $email = $_SESSION['user_email'];
    $answers = json_encode($data['answers']);
    $flagged = json_encode($data['flagged']);
    $violations = json_encode($data['violations'] ?? null);
    
    $sql = "INSERT INTO test_submissions (
        user_email, answers, flagged_questions, total_questions, score, violations, status
    ) VALUES (?, ?, ?, ?, ?, ?, 'completed')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiis", 
        $email, $answers, $flagged, $totalQuestions, $score, $violations
    );
    
    if ($stmt->execute()) {
        $message = "
            <html>
            <body>
                <h2>Test Completion Confirmation</h2>
                <p>Dear Student,</p>
                <p>Thank you for completing the test.</p>
                <p>Please proceed to schedule your interview.</p>
                <p>Best regards,<br>Zell Education Team</p>
            </body>
            </html>";
        
        sendMail($email, "Test Completion Confirmation", $message);
        
        $_SESSION['test_score'] = $score;
        $_SESSION['total_questions'] = $totalQuestions;
        
        echo json_encode(['success' => true]);
    }

    $stmt->close();
    $conn->close();

} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>