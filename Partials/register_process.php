<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

include 'db_conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $subject = $_POST['subject'];
    $experience = $_POST['experience'];

    // Check if email or phone number already exists
    $check_query = "SELECT * FROM Users WHERE email = ? OR phone = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email or phone already exists
        $_SESSION['message'] = "Cannot proceed to register; email or phone is already in use.";
    } else {
        // Generate a unique activation token
        $activation_token = bin2hex(random_bytes(16));

        // Handle file uploads
        $cv_filename = $_FILES['cv']['name'];
        $profile_filename = $_FILES['profile_picture']['name'];

        // Move uploaded files to a designated directory
        move_uploaded_file($_FILES['cv']['tmp_name'], "uploads/" . $cv_filename);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], "uploads/" . $profile_filename);

        // Insert into Users table
        $sql_users = "INSERT INTO Users (firstname, middlename, surname, email, phone, password, subject, experience, cv_filename, profile_filename, activation_token, activated)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql_users);
        $activated = 0; // Default value for 'activated' column
        $stmt->bind_param("ssssssssssss", $firstname, $middlename, $surname, $email, $phone, $password, $subject, $experience, $cv_filename, $profile_filename, $activation_token, $activated);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id; // Get the last inserted ID (user_id)

            // Insert into Applicant table with initial status as 'Account Creation'
            $sql_applicant = "INSERT INTO Applicant (user_id, status, account_creation_completed)
                              VALUES (?, 'Account Creation', 1)";

            $stmt = $conn->prepare($sql_applicant);
            $stmt->bind_param("i", $user_id);

            if ($stmt->execute()) {
                // Send activation email
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'danielzanbaltazar.forwork@gmail.com'; // Change this to your email
                $mail->Password   = 'nqzk mmww mxin ikve'; // Change this to your email password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('danielzanbaltazar.forwork@gmail.com', 'HRMS Account Verifications'); // Change this to your email and name
                $mail->addAddress($email);
                $mail->Subject = 'Activate Your Account';
                $mail->Body    = "Hello $firstname,\n\nThank you for registering. Please click the following link to activate your account:\n\nhttp://localhost/HRMS/Partials/activate.php?token=$activation_token\n\nBest regards,\nHRMS";

                if ($mail->send()) {
                    $_SESSION['message'] = "Successfully Registered. Check your email to activate your account.";
                } else {
                    $_SESSION['message'] = "Error: " . $mail->ErrorInfo;
                }
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
            }
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
    header("Location: ../register.php");
    exit();
}
?>
