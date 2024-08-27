<?php
include 'db_conn.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch user ID
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id FROM Users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

$user_id = $user['id'];

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['attachment'])) {
    $file = $_FILES['attachment'];
    
    if ($file['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_name = basename($file['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Save file details to the database
            $stmt = $conn->prepare("INSERT INTO Attachments (user_id, file_name, file_path, upload_date) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iss", $user_id, $file_name, $file_path);
            $stmt->execute();
            $stmt->close();

            header("Location: ../Attachement.php"); // Redirect to the main page after update
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
