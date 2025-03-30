<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ag_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Validate passwords match
    if ($password !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
        exit();
    }
    
    // Check if username already exists
    $checkUser = $conn->query("SELECT * FROM users WHERE username = '$username' OR email = '$email'");
    if ($checkUser->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username or email already exists"]);
        exit();
    }
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Create SQL query
    $sql = "INSERT INTO users (first_name, last_name, email, username, password, created_at) 
            VALUES ('$firstName', '$lastName', '$email', '$username', '$hashedPassword', NOW())";
    
    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Registration successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
    
    // Close the connection
    $conn->close();
    exit();
}
?>