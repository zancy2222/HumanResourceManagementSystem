<?php
include 'db_conn.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Handle profile picture upload
    $profilePicture = null; // Default value if no file is uploaded
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
        $targetDir = "uploads/"; // Directory where files will be uploaded
        $profilePicture = $targetDir . basename($_FILES["profilePicture"]["name"]);
        if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $profilePicture)) {
            die("Error uploading file.");
        }
    }

    // Prepare and execute SQL to insert the new employee into the database
    $stmt = $conn->prepare("INSERT INTO hr_members (first_name, middle_name, last_name, email, age, password, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // Bind parameters, ensuring correct types
    // "s" for strings, "i" for integers
    $stmt->bind_param("ssssiss", $firstName, $middleName, $lastName, $email, $age, $password, $profilePicture);

    if ($stmt->execute()) {
        header("Location: ../HrAccount.php"); // Redirect to the main page after update
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
