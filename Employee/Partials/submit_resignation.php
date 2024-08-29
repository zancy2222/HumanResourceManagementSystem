<?php
include '../Partials/db_conn.php'; // Adjust the path as needed
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Get the email from session
$email = $_SESSION['email'];

// Fetch employee's ID based on the email
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
$employee = $result->fetch_assoc();

if ($employee) {
    $employeeId = $employee['employee_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get resignation details from the form
        $resignationDate = $_POST['resignation_date'];
        $reason = $_POST['reason'];

        // Insert resignation details into the database
        $stmt = $conn->prepare("
            INSERT INTO Resignations (employee_id, resignation_date, reason) 
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss", $employeeId, $resignationDate, $reason);

        if ($stmt->execute()) {
            header("Location: ../Resign.php"); // Redirect to the main page after update
            exit();
        } else {
            // Handle the error if the query fails
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    // If no employee data is found, redirect to login page
    header("Location: ../login.php");
    exit();
}

$conn->close();
?>
