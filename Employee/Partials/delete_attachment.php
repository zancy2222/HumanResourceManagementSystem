<?php
include 'db_conn.php'; // Adjust the path as needed
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Check if the file ID is provided in the query string
if (isset($_GET['id'])) {
    $fileId = intval($_GET['id']); // Ensure the ID is an integer

    // Fetch the file details from the database
    $stmt = $conn->prepare("SELECT file_path FROM AttachedFileEmployee WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();

    if ($file) {
        $filePath = '../uploads/' . htmlspecialchars($file['file_path']);

        // Delete the file from the filesystem
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the file record from the database
        $stmt = $conn->prepare("DELETE FROM AttachedFileEmployee WHERE id = ?");
        $stmt->bind_param("i", $fileId);
        $stmt->execute();
    }

    $stmt->close();
}

// Close the database connection
$conn->close();

// Redirect back to the page with the attachments table
header("Location: ../Folder.php"); // Adjust the redirect URL as needed
exit();
?>
