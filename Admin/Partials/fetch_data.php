<?php
include 'db_conn.php'; // Include your database connection file

// Set default values for pagination
$limit = 5; // Number of rows per page
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$searchTerm = isset($_POST['search']) ? '%' . $conn->real_escape_string($_POST['search']) . '%' : '%';

// Calculate offset
$offset = ($page - 1) * $limit;

// Prepare SQL query
$sql = "SELECT * FROM hr_members WHERE CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $searchTerm, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Output data for each row
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Get total number of records for pagination
$countSql = "SELECT COUNT(*) as total FROM hr_members WHERE CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("s", $searchTerm);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Return JSON response
echo json_encode([
    'data' => $data,
    'totalPages' => $totalPages
]);

$stmt->close();
$countStmt->close();
$conn->close();
?>
