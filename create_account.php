<?php
// Database connection details
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
$account_number = $_POST['account_number'];
$account_name = $_POST['account_name'];
$initial_deposit = (float)$_POST['initial_deposit'];

// Start a transaction to ensure atomicity
$conn->begin_transaction();

try {
    // Insert a new client account
    $sql = "INSERT INTO accounts (account_number,account_name,balance) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssd", $account_number, $account_name, $initial_deposit);
    $stmt->execute();

    // Insert the initial deposit into the deposits table
    $sql_deposit = "INSERT INTO deposits (account_number, account_name, amount) VALUES (?, ?, ?)";
    $stmt_deposit = $conn->prepare($sql_deposit);
    $stmt_deposit->bind_param("ssd", $account_number, $account_name, $initial_deposit);
    $stmt_deposit->execute();

    // Commit the transaction if all went well
    $conn->commit();

    echo "Savings account created successfully!";
} catch (Exception $e) {
    // Rollback the transaction in case of error
    $conn->rollback();
    echo "Error creating Savings account: " . $e->getMessage();
}

// Close the connection
$stmt->close();
$conn->close();
?>
