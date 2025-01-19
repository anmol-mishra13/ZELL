<?php
session_start();
header('Content-Type: application/json');

// Check if session is valid
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'error' => 'Session expired']);
    exit();
}

// Decode incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email'], $data['violation'], $data['timestamp'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit();
}

require_once 'db_config.php';

try {
    // Connect to the database
    $conn = new mysqli(
        $db_config['host'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database']
    );

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Create the `security_violations` table if it doesn't exist
    $createTableSQL = "CREATE TABLE IF NOT EXISTS security_violations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL,
        violation_type VARCHAR(255) NOT NULL,
        timestamp DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if (!$conn->query($createTableSQL)) {
        throw new Exception("Error creating table: " . $conn->error);
    }

    // Insert security violation record
    $sql = "INSERT INTO security_violations (user_email, violation_type, timestamp) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("sss", $data['email'], $data['violation'], $data['timestamp']);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error executing query: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit();
}
