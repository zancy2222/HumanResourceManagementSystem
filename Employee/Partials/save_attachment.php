<?php
include 'db_conn.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch the employee's details using email from the session
$email = $_SESSION['email'];

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

if (!$employee) {
    // If no employee data is found, redirect to login page
    header("Location: ../login.php");
    exit();
}

$employee_id = $employee['employee_id'];

$stmt->close();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['attachment'])) {
    $file = $_FILES['attachment'];
    
    if ($file['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_name = basename($file['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Save file details to the database
            $stmt = $conn->prepare("INSERT INTO AttachedFileEmployee (employee_id, file_name, file_path, upload_date) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $employee_id, $file_name, $file_path);
            $stmt->execute();
            $stmt->close();

            header("Location: ../Folder.php"); // Redirect to the main page after update
            exit();
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "File upload error.";
    }
} else {
    echo "No file uploaded.";
}

$conn->close();
?>
