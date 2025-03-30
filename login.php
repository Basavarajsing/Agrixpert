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

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    // Create SQL query to check user credentials
    $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a new session
            session_start();
            
            // Store user data in session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            echo json_encode(["status" => "success", "message" => "Login successful"]);
        } else {
            // Password is incorrect
            echo json_encode(["status" => "error", "message" => "Invalid username or password"]);
        }
    } else {
        // Username doesn't exist
        echo json_encode(["status" => "error", "message" => "Invalid username or password"]);
    }
    
    // Close the connection
    $conn->close();
    exit();
}
?>