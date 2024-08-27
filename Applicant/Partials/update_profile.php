<?php
include '../Partials/db_conn.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Get form data
$firstname = $_POST['firstname'];
$middlename = $_POST['middlename'];
$surname = $_POST['surname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$subject = $_POST['subject'];
$experience = $_POST['experience'];

// Handle file uploads
$cv_filename = $user['cv_filename'];
if (!empty($_FILES['cv']['name'])) {
    $cv_filename = basename($_FILES['cv']['name']);
    $cv_target = "../../Partials/uploads/" . $cv_filename;
    move_uploaded_file($_FILES['cv']['tmp_name'], $cv_target);
}

$profile_filename = $user['profile_filename'];
if (!empty($_FILES['profile-image']['name'])) {
    $profile_filename = basename($_FILES['profile-image']['name']);
    $profile_target = "../../Partials/uploads/" . $profile_filename;
    move_uploaded_file($_FILES['profile-image']['tmp_name'], $profile_target);
}

// Handle password update only if a new password is provided
if (!empty($_POST['password'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE Users SET firstname=?, middlename=?, surname=?, email=?, phone=?, password=?, subject=?, experience=?, cv_filename=?, profile_filename=? WHERE email=?");
    $stmt->bind_param("sssssssssss", $firstname, $middlename, $surname, $email, $phone, $password, $subject, $experience, $cv_filename, $profile_filename, $_SESSION['email']);
} else {
    $stmt = $conn->prepare("UPDATE Users SET firstname=?, middlename=?, surname=?, email=?, phone=?, subject=?, experience=?, cv_filename=?, profile_filename=? WHERE email=?");
    $stmt->bind_param("ssssssssss", $firstname, $middlename, $surname, $email, $phone, $subject, $experience, $cv_filename, $profile_filename, $_SESSION['email']);
}

// Execute the statement
if ($stmt->execute()) {
    // Update session email if it was changed
    $_SESSION['email'] = $email;
    header("Location: ../User.php"); // Redirect to the profile page
    exit();
} else {
    echo "Error updating record: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
