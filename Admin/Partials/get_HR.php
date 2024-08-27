<?php
include 'db_conn.php'; // Include your database connection file

$id = $_GET['id'];

$query = "SELECT first_name, middle_name, last_name, email, age, password FROM hr_members WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

// Return the employee data as JSON
echo json_encode($employee);

$stmt->close();
$conn->close();
?>
