<?php
session_start();
header('Content-Type: application/json');

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

    $sql = "INSERT INTO security_violations (user_email, violation_type, timestamp) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $data['email'], $data['violation'], $data['timestamp']);
    
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo json_encode(['success' => true]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
