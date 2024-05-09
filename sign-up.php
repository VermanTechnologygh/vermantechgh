<?php
// MySQL connection details
$host = "localhost";
$user = "root";
$password = "";
$database = "vermanbankingapp_db";

// Create a connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$username = $_POST['username'];
$raw_password = $_POST['password'];
$role = $_POST['role'];

// Hash the password for security
$hashed_password = password_hash($raw_password, PASSWORD_BCRYPT);

// SQL query to insert a new user into the database
$sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";

// Prepare the statement to avoid SQL injection
$stmt = $conn->prepare($sql);

// Bind the parameters
$stmt->bind_param("sss", $username, $hashed_password, $role);

// Execute the statement
if ($stmt->execute()) {
    echo "User account created successfully!";
} else {
    echo "Error creating user account: " . $stmt->error;
}

// Close the connection
$stmt->close();
$conn->close();
?>
