<?php
include 'db_conn.php'; // Ensure this path is correct and the file establishes a proper DB connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the query to fetch applicant details
    $stmt = $conn->prepare("
        SELECT 
            u.firstname, 
            u.middlename, 
            u.surname, 
            u.email, 
            u.phone, 
            u.subject, 
            u.experience, 
            u.cv_filename, 
            u.profile_filename, 
            a.status AS progress_status, 
            u.created_at, 
            u.activated
        FROM 
            Applicant a
        JOIN 
            Users u ON a.user_id = u.id
        WHERE 
            a.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = $result->fetch_assoc();

    // Return the applicant details as a JSON response
    header('Content-Type: application/json');
    echo json_encode($applicant);

    $stmt->close();
}

$conn->close();
?>
