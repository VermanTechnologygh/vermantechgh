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

// Get data from POST request
$account_number = $_POST['account_number'];
$account_name = $_POST['account_name'];
$amount = (float)$_POST['amount'];


// Basic validation
if ($amount <= 0) {
    die("Deposit amount must be greater than zero.");
}

// Start a transaction to ensure atomicity
$conn->begin_transaction();

try {
    // Insert deposit into the 'deposits' table
    $sql = "INSERT INTO deposits (account_number, account_name, amount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssd", $account_number, $account_name, $amount);
    $stmt->execute();

    // Update the account's balance
    $sql_update_balance = "UPDATE accounts SET balance = balance + ? WHERE account_number = ?";
    $stmt_update_balance = $conn->prepare($sql_update_balance);
    $stmt_update_balance->bind_param("ds", $amount, $account_number);
    $stmt_update_balance->execute();

    // Commit the transaction
    $conn->commit();

    echo "Deposit successful!";
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    echo "Error processing deposit: " . $e->getMessage();
}

// Close connections
$stmt->close();
$stmt_update_balance->close();
$conn->close();
?>
