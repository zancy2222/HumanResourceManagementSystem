<?php
include 'db_conn.php'; // Adjust the path as needed

$query = "SELECT subject, COUNT(*) as count FROM applicant GROUP BY subject";
$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
