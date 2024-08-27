<?php
include 'db_conn.php'; // Ensure this path is correct and the file establishes a proper DB connection

// Prepare the query to fetch all applicants along with their user details
$query = "
    SELECT 
        a.id,
        u.firstname,
        u.middlename,
        u.surname,
        u.email,
        u.phone,
        u.subject,
        a.account_creation_completed,
        a.interview_completed,
        a.demo_teaching_completed,
        a.hire_completed
    FROM 
        Applicant a
    JOIN 
        Users u ON a.user_id = u.id
";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Initialize an array to store the fetched data
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Return the data as a JSON response
header('Content-Type: application/json');
echo json_encode($data);

// Close the connection
$conn->close();
?>
