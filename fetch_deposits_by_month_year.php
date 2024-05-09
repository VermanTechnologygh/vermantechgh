<?php
header('Content-Type: application/json'); // Set response content type to JSON

// MySQL connection details
$host = "localhost";
$user = "root";
$password = "";
$database = "vermanbankingapp_db";

// Connect to the database
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Get the specified month and year from the query string
$month = $_GET['month'] ?? null;
$year = $_GET['year'] ?? null;

if ($month && $year) {
    // SQL query to fetch deposits for the specified month and year
    $sql = "SELECT deposit_id, account_number, account_name, amount, deposit_date
            FROM deposits
            WHERE MONTH(deposit_date) = ? AND YEAR(deposit_date) = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $month, $year); // Bind month and year parameters
    $stmt->execute();
    $result = $stmt->get_result();

    $deposits = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $deposits[] = [
                "deposit_id" => $row["deposit_id"],
                "account_number" => $row["account_number"],
                "account_name" => $row["account_name"],
                "amount" => number_format((float) $row["amount"], 2),
                "deposit_date" => $row["deposit_date"]
               
            ];
        }
    }

    echo json_encode($deposits); // Return the deposit data as JSON
} else {
    echo json_encode(["error" => "Missing month or year parameters."]); // Handle missing parameters
}

$stmt->close(); // Close the prepared statement
$conn->close(); // Close the database connection
?>
