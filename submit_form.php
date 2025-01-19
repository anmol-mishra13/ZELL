<?php
session_start();

// Database configuration for MySQL
require_once 'db_config.php';

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // MySQL connection
        $conn = new mysqli(
            $db_config['host'],
            $db_config['username'],
            $db_config['password'],
            $db_config['database']
        );

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Create the user_profiles table if it doesn't exist
        $createTableSQL = "CREATE TABLE IF NOT EXISTS user_profiles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            qualification VARCHAR(255),
            university VARCHAR(255),
            guardian_number VARCHAR(15),
            designation VARCHAR(255),
            company VARCHAR(255),
            ctc VARCHAR(255),
            user_type ENUM('student', 'professional') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if (!$conn->query($createTableSQL)) {
            throw new Exception("Error creating table: " . $conn->error);
        }

        $userType = sanitize_input($_POST['user_type']);
        
        // Initialize fields
        $formData = [
            'name' => null,
            'email' => null,
            'qualification' => null,
            'university' => null,
            'guardian_number' => null,
            'designation' => null,
            'company' => null,
            'ctc' => null,
            'user_type' => $userType
        ];

        // Set required fields based on user type
        $requiredFields = ($userType === 'student') 
            ? ['name', 'email', 'qualification', 'university', 'guardian_number']
            : ['name', 'email', 'designation', 'company', 'ctc'];

        // Validate and sanitize fields
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
            $formData[$field] = sanitize_input($_POST[$field]);
        }

        // Prepare MySQL statement
        $sql = "INSERT INTO user_profiles (
                name, email, qualification, university, guardian_number,
                designation, company, ctc, user_type
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        $stmt->bind_param("sssssssss",
            $formData['name'],
            $formData['email'],
            $formData['qualification'],
            $formData['university'],
            $formData['guardian_number'],
            $formData['designation'],
            $formData['company'],
            $formData['ctc'],
            $formData['user_type']
        );

        if ($stmt->execute()) {
            $_SESSION['message'] = "Form submitted successfully!";
            $_SESSION['message_type'] = "success";
            $_SESSION['name'] = $formData['name'];
            $_SESSION['user_email'] = $formData['email'];

            
            // Return success with redirect URL for AJAX
            echo json_encode([
                'success' => true,
                'message' => 'Form submitted successfully!',
                'redirect' => 'test.php'
            ]);
        } else {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();

    } catch(Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}
?>
