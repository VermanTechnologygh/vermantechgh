<?php
// Database connection variables
$host = "localhost";  
$username = "root"; 
$password = "";  
$database = "vermanbankingapp_db";  

// Connect to MySQL
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $accountName = $_POST["accountName"];
    $accountNumber = $_POST["accountNumber"];
    $accountBalance = (float)$_POST["accountBalance"];

    // Check if the username or email already exists
$sql_check = "SELECT * FROM accounts WHERE accountNumber = ?";
$stmt_check = $conn->prepare($sql_check);

$stmt_check->bind_param("s", $accountNumber);
$stmt_check->execute();
$result = $stmt_check->get_result();


if ($result->num_rows > 0) {
    // There's at least one matching record, indicating a duplicate
    echo "Duplicate entry: Account Number already exists. Click the back button to enter a new account";

} else {

    // No duplicate found, SQL query to insert data into the 'deposits' table
    $sql = "INSERT INTO accounts (accountName, accountNumber, accountBalance)
            VALUES (?, ?, ?)";

    // Prepare the SQL statement to avoid SQL injection
    $stmt = $conn->prepare($sql);

    // Bind parameters to the statement
    $stmt->bind_param("ssd", $accountName, $accountNumber, $accountBalance);

    // Execute the SQL statement
    if ($stmt->execute()) {
        echo "Customer Account registered successfully!";
    } else {
        echo "Error inserting record: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}
}

// Close the connection
$conn->close();
?>
