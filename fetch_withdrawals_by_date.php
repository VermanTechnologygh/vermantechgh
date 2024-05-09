<?php
header('Content-Type: application/json'); // Set response content type to JSON

// Database connection details
$host = "localhost";
$user = "root";
$password = "";
$database = "vermanbankingapp_db";

// Create a connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Get the specified date from the query string
$date = $_GET['date'] ?? null;

if ($date) {
    // SQL query to fetch withdrawals for the given date
    $sql = "SELECT withdrawal_id, account_number, account_name, amount, withdrawal_date
            FROM withdrawals
            WHERE DATE(withdrawal_date) = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $withdrawals = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $withdrawals[] = [
                "withdrawal_id" => $row["withdrawal_id"],
                "account_number" => $row["account_number"],
                "account_name" => $row["account_name"],
                "amount" => number_format((float)$row["amount"], 2),
                "withdrawal_date" => $row["withdrawal_date"],
            ];
        }
    }

    echo json_encode($withdrawals);
} else {
    echo json_encode(["error" => "Date parameter is missing."]);
}

$conn->close();
?>
