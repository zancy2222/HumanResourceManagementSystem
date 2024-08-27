<?php
include 'db_conn.php'; // Adjust the path as needed
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $leave_type = isset($_POST['leave_type']) ? trim($_POST['leave_type']) : '';
    $leave_date = isset($_POST['leave_date']) ? trim($_POST['leave_date']) : '';
    $leave_reason = isset($_POST['leave_reason']) ? trim($_POST['leave_reason']) : '';

    // Ensure required fields are not empty
    if (empty($leave_type) || empty($leave_date) || empty($leave_reason)) {
        echo "All fields are required.";
        exit();
    }

    // Get the user's email from the session
    $email = $_SESSION['email'];

    // Fetch employee ID from the database
    $stmt = $conn->prepare("
        SELECT e.employee_id 
        FROM Employee e 
        JOIN ArchiveApplicant aa ON e.archive_applicant_id = aa.id 
        JOIN Users u ON aa.user_id = u.id 
        WHERE u.email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        $employee_id = $employee['employee_id'];

        // Prepare and execute the insert statement
        $stmt = $conn->prepare("
            INSERT INTO leave_requests (employee_id, leave_type, leave_date, leave_reason) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $employee_id, $leave_type, $leave_date, $leave_reason);

        if ($stmt->execute()) {
            header("Location: ../LeaveReq.php"); // Redirect to the main page after update
            exit();
        } else {
            echo "Error submitting leave request: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Employee not found.";
    }

    $conn->close();
} else {
    // If form not submitted properly
    echo "Invalid request method.";
}
?>
