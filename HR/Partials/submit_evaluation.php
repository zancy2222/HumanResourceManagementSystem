<?php
include 'db_conn.php'; // Adjust the path as needed
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Get POST data
$employeeId = $_POST['employee-id'] ?? null; // Fetch the employee ID
$comments = $_POST['comments'] ?? ''; // Optional comments field
$ratings = [
    'Professionalism' => $_POST['professionalism'] ?? null,
    'Use of Technology' => $_POST['technology'] ?? null,
    'Feedback and Assessment' => $_POST['feedback'] ?? null,
    'Classroom Management' => $_POST['management'] ?? null,
    'Subject Knowledge' => $_POST['knowledge'] ?? null,
    'Teaching Methods' => $_POST['methods'] ?? null,
    'Student Engagement' => $_POST['engagement'] ?? null,
    'Communication Skills' => $_POST['communication'] ?? null,
];

if ($employeeId) {
    foreach ($ratings as $criteria => $rating) {
        if ($rating !== null) {
            // Prepare and execute the insert statement
            $stmt = $conn->prepare("INSERT INTO Evaluations (employee_id, criteria, rating, comments) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $employeeId, $criteria, $rating, $comments);
            $stmt->execute();
        }
    }
    
    // Close the statement and connection
    $stmt->close();
    $conn->close();
    
    // Redirect or notify the user of successful submission
    header("Location: ../Eval.php"); // Redirect to a success page or display a success message
    exit();
} else {
    // Handle the case where employee ID is missing
    echo "Employee ID is missing. Please select an employee.";
}
?>
