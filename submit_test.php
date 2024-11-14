<?php
// submit_test.php
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

    $email = $_SESSION['user_email'];
    $answers = json_encode($data['answers']);
    $flagged = json_encode($data['flagged']);
    
    $sql = "INSERT INTO test_submissions (email, answers, flagged_questions) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $answers, $flagged);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error saving test submission");
    }

    $stmt->close();
    $conn->close();

} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>