<?php
include 'db_conn.php';
$id = $_GET['id']; // Get the employee ID from the request

$stmt = $conn->prepare("
    SELECT 
        u.firstname, u.middlename, u.surname, u.email, u.phone, u.subject, u.experience, 
        u.cv_filename, u.profile_filename, u.activation_token, u.activated, u.created_at,
        aa.status as progress_status 
    FROM Employee e 
    JOIN ArchiveApplicant aa ON e.archive_applicant_id = aa.id 
    JOIN Users u ON aa.user_id = u.id 
    WHERE e.employee_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
$stmt->close();
$conn->close();
?>
