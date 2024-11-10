<?php
header('Content-Type: application/json');

// Decode the JSON-formatted input data
$data = json_decode(file_get_contents('php://input'), true);

// Check if required fields are provided
if (!isset($data['name'], $data['email'], $data['userType'])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit();
}

// Database credentials
$servername = "localhost";
$username = "postgres"; // Replace with your MySQL username
$password = "password"; // Replace with your MySQL password
$database = "zell_education"; // Replace with your database name

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// SQL statement with placeholders to prevent SQL injection
$stmt = $conn->prepare("
    INSERT INTO user_profiles (name, email, qualification, university, guardian_number, designation, company, ctc, user_type)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

// Bind the parameters for the SQL query
$stmt->bind_param(
    "sssssssss",
    $data['name'],
    $data['email'],
    $data['qualification'] ?? null,
    $data['university'] ?? null,
    $data['guardian_number'] ?? null,
    $data['designation'] ?? null,
    $data['company'] ?? null,
    $data['ctc'] ?? null,
    $data['userType']
);

// Execute the query and check for errors
if ($stmt->execute()) {
    echo json_encode(["success" => "Form submitted successfully!"]);
} else {
    echo json_encode(["error" => "Error submitting form: " . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
