<?php
header('Content-Type: application/json'); // Return JSON response

// MySQL connection details
$host = "localhost";
$user = "root";
$password = "";
$database = "vermanbankingapp_db";

// Connect to the database
$conn = new mysqli($host, $user, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Get the specified month and year from the query string
$month_year = $_GET['month_year'] ?? null;

if ($month_year) {
    // Extract month and year from the input
    list($year, $month) = explode('-', $month_year);

    // SQL query to fetch withdrawals for the given month and year
    $sql = "SELECT withdrawal_id, account_number, account_name, amount, withdrawal_date
            FROM withdrawals
            WHERE MONTH(withdrawal_date) = ? AND YEAR(withdrawal_date) = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $month, $year); // Bind month and year
    $stmt->execute();
    $result = $stmt->get_result();

    $withdrawals = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $withdrawals[] = [
                "withdrawal_id" => $row["withdrawal_id"],
                "account_number" => $row["account_number"],
                "account_name" => $row["account_name"],
                "amount" => number_format((float) $row["amount"], 2),
                "withdrawal_date" => $row["withdrawal_date"]
              
            ];
        }
    }

    echo json_encode($withdrawals); // Return the withdrawal data as JSON
} else {
    echo json_encode(["error" => "Missing month_year parameter."]); // Error handling
}

$stmt->close();
$conn->close();
?>
