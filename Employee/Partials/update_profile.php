<?php
include 'db_conn.php'; // Adjust the path as needed
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Get the email from session
$email = $_SESSION['email'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form inputs
    $firstname = htmlspecialchars($_POST['firstname']);
    $middlename = htmlspecialchars($_POST['middlename']);
    $surname = htmlspecialchars($_POST['surname']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $experience = htmlspecialchars($_POST['experience']);
    $subject = htmlspecialchars($_POST['subject']);

    // Handle file upload
    $cvFilename = null;
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == UPLOAD_ERR_OK) {
        $cvTempName = $_FILES['cv']['tmp_name'];
        $cvFilename = basename($_FILES['cv']['name']);
        $cvTargetPath = '../../Partials/uploads/' . $cvFilename;

        // Move uploaded file to target directory
        if (move_uploaded_file($cvTempName, $cvTargetPath)) {
            // If file upload is successful, update the filename
            $cvFilename = htmlspecialchars($cvFilename);
        } else {
            // Handle file upload error
            echo "Error uploading resume. Please try again.";
            exit();
        }
    } else {
        // If no new CV is uploaded, fetch the existing CV filename from the database
        $stmt = $conn->prepare("
            SELECT u.cv_filename 
            FROM Users u 
            JOIN Employee e ON u.id = e.archive_applicant_id 
            WHERE u.email = ?
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($existingCvFilename);
        $stmt->fetch();
        $stmt->close();
        
        // Use the existing CV filename
        $cvFilename = $existingCvFilename;
    }

    // Update profile in the database
    $stmt = $conn->prepare("
        UPDATE Users u
        JOIN Employee e ON u.id = e.archive_applicant_id
        SET u.firstname = ?, u.middlename = ?, u.surname = ?, u.email = ?, u.phone = ?, u.experience = ?, u.subject = ?, u.cv_filename = ?
        WHERE u.email = ?
    ");
    $stmt->bind_param("sssssssss", $firstname, $middlename, $surname, $email, $phone, $experience, $subject, $cvFilename, $email);

    if ($stmt->execute()) {
        // Profile updated successfully
        header("Location: ../Profile.php");
    } else {
        // Error updating profile
        echo "Error updating profile. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
