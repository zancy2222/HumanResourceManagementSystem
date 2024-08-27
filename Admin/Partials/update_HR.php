<?php
include 'db_conn.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['employeeId'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $age = $_POST['age'];

    // Only hash the password if it's provided
    $passwordClause = '';
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $passwordClause = ", password = ?";
    }

    // Handle file upload for profile picture if provided
    $profilePictureClause = '';
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
        $target_dir = "uploads/";
        $profilePicture = $target_dir . basename($_FILES["profilePicture"]["name"]);
        move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $profilePicture);
        $profilePictureClause = ", profile_picture = ?";
    }

    $query = "UPDATE hr_members SET first_name = ?, middle_name = ?, last_name = ?, email = ?, age = ?{$passwordClause}{$profilePictureClause} WHERE id = ?";
    $stmt = $conn->prepare($query);

    // Bind parameters dynamically
    if (!empty($_POST['password']) && isset($profilePicture)) {
        $stmt->bind_param("ssssissi", $firstName, $middleName, $lastName, $email, $age, $password, $profilePicture, $id);
    } elseif (!empty($_POST['password'])) {
        $stmt->bind_param("ssssiis", $firstName, $middleName, $lastName, $email, $age, $password, $id);
    } elseif (isset($profilePicture)) {
        $stmt->bind_param("ssssisi", $firstName, $middleName, $lastName, $email, $age, $profilePicture, $id);
    } else {
        $stmt->bind_param("ssssii", $firstName, $middleName, $lastName, $email, $age, $id);
    }

    if ($stmt->execute()) {
        header("Location: ../HrAccount.php"); // Redirect to the main page after update
        exit();
    } else {
        echo "Error updating employee: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: ../HrAccount.php"); // Redirect to the main page after update
    exit();
}
?>
