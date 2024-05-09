<?php
session_start();

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

// SQL query to find the user by username
$sql = "SELECT username, password, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verify the password
    if (password_verify($raw_password, $user['password'])) {
        // Store user information in the session
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect to appropriate dashboard based on user role
        if ($user['role'] === 'admin') {
            header("Location: Admindashboard.html"); // Redirect to admin dashboard
        } else {
            header("Location: dashboard.html"); // Redirect to user dashboard
        }
        exit();
    } else {
        echo "Invalid password.";
    }
} else {
    echo "User not found.";
}

// Close the connection
$stmt->close();
$conn->close();
?>