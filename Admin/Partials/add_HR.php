<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';
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
    $stmt->bind_param("ssssiss", $firstName, $middleName, $lastName, $email, $age, $password, $profilePicture);

    if ($stmt->execute()) {
        // Account creation successful, send an email notification
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'danielzanbaltazar.forwork@gmail.com'; // Change this to your email
        $mail->Password   = 'nqzk mmww mxin ikve'; // Change this to your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('danielzanbaltazar.forwork@gmail.com', 'HRMS'); // Set the sender's email and name
        $mail->addAddress($email, $firstName . ' ' . $lastName); // Add recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Account Created Successfully';
        $mail->Body    = "<p>Hello $firstName $lastName,</p>
                          <p>Your account has been created successfully. You can now log in and update your details as needed.</p>
                          <p>Best regards,<br>HRMS</p>";
        
        // Send the email
        if(!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }

        // Redirect to the main page after the account is created and email is sent
        header("Location: ../HrAccount.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
