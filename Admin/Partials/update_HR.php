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
    $params = [$firstName, $middleName, $lastName, $email, $age];
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $passwordClause = ", password = ?";
        $params[] = $password;
    }

    // Handle file upload for profile picture if provided
    $profilePictureClause = '';
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
        $target_dir = "uploads/";
        $profilePicture = $target_dir . basename($_FILES["profilePicture"]["name"]);
        move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $profilePicture);
        $profilePictureClause = ", profile_picture = ?";
        $params[] = $profilePicture;
    }

    $query = "UPDATE hr_members SET first_name = ?, middle_name = ?, last_name = ?, email = ?, age = ?{$passwordClause}{$profilePictureClause} WHERE id = ?";
    $params[] = $id;

    $stmt = $conn->prepare($query);

    // Bind parameters dynamically
    $types = str_repeat('s', count($params) - 1) . 'i';
    $stmt->bind_param($types, ...$params);

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
