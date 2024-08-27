<?php
include 'db_conn.php'; // Ensure this path is correct and the file establishes a proper DB connection

$subject = isset($_GET['subject']) ? $_GET['subject'] : '';

if ($subject) {
    $stmt = $conn->prepare("
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
        WHERE 
            u.subject = ?
    ");
    $stmt->bind_param("s", $subject);
} else {
    $stmt = $conn->prepare("
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
    ");
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Return the data as a JSON response
header('Content-Type: application/json');
echo json_encode($data);

$stmt->close();
$conn->close();
?>
