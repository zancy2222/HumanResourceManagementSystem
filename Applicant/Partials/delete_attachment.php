<?php
include '../Partials/db_conn.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Check if the file ID is provided
if (isset($_POST['file_id'])) {
    $file_id = $_POST['file_id'];

    // Fetch the file details from the database
    $stmt = $conn->prepare("SELECT file_path FROM Attachments WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $file_path = '../../Partials/uploads/' . $row['file_path']; // Adjust the path as needed

        // Delete the file from the server
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete the record from the database
        $stmt = $conn->prepare("DELETE FROM Attachments WHERE id = ?");
        $stmt->bind_param("i", $file_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Attachment deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete attachment.";
        }
    } else {
        $_SESSION['error'] = "Attachment not found.";
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "No file ID provided.";
}

$conn->close();
header("Location: ../Attachement.php");
exit();
?>
