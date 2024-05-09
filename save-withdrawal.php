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
$withdrawal_amount = (float)$_POST['withdrawal_amount'];

// Validate the withdrawal amount
if ($withdrawal_amount <= 0) {
    die("Withdrawal amount must be greater than zero.");
}

// Start a transaction to ensure atomicity
$conn->begin_transaction();

try {
    // Check if the account has sufficient balance
    $sql_check_balance = "SELECT balance FROM accounts WHERE account_number = ?";
    $stmt_check_balance = $conn->prepare($sql_check_balance);
    $stmt_check_balance->bind_param("s", $account_number);
    $stmt_check_balance->execute();

    $result = $stmt_check_balance->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Account not found.");
    }

    $account = $result->fetch_assoc();
    if ($account['balance'] < $withdrawal_amount) {
        throw new Exception("Insufficient balance.");
    }

    // Insert the withdrawal into the 'withdrawals' table
    $sql_withdrawal = "INSERT INTO withdrawals (account_number, account_name, amount) VALUES (?, ?, ?)";
    $stmt_withdrawal = $conn->prepare($sql_withdrawal);
    $stmt_withdrawal->bind_param("ssd", $account_number, $account_name, $withdrawal_amount);
    $stmt_withdrawal->execute();

    // Update the account's balance after withdrawal
    $sql_update_balance = "UPDATE accounts SET balance = balance - ? WHERE account_number = ?";
    $stmt_update_balance = $conn->prepare($sql_update_balance);
    $stmt_update_balance->bind_param("ds", $withdrawal_amount, $account_number);
    $stmt_update_balance->execute();

    // Commit the transaction if everything is successful
    $conn->commit();

    echo "Withdrawal successful!";
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    echo "Error processing withdrawal: " . $e->getMessage();
}

// Close connections
$stmt_check_balance->close();
$stmt_withdrawal->close();
$conn->close();
?>
