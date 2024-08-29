<?php
include 'db_conn.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // User Details
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hashing password
    $subject = $_POST['subject'];
    $experience = $_POST['experience'];
    $status = $_POST['status'];
    $employee_id = $_POST['employee_id'];
    $hire_date = $_POST['hire_date'];

    // Generate activation token
    $activation_token = bin2hex(random_bytes(16));

    // Handle file uploads
    $cv_filename = $_FILES['cv_filename']['name'];
    $profile_filename = $_FILES['profile_filename']['name'];

    if ($cv_filename) {
        move_uploaded_file($_FILES['cv_filename']['tmp_name'], '../../Partials/uploads/' . $cv_filename);
    }
    if ($profile_filename) {
        move_uploaded_file($_FILES['profile_filename']['tmp_name'], '../../Partials/uploads/' . $profile_filename);
    }

    // Insert into Users table
    $stmt = $conn->prepare("INSERT INTO Users (firstname, middlename, surname, email, phone, password, subject, experience, cv_filename, profile_filename, activation_token, activated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sssssssssss", $firstname, $middlename, $surname, $email, $phone, $password, $subject, $experience, $cv_filename, $profile_filename, $activation_token);
    
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id; // Get the ID of the newly inserted user

        // Insert into ArchiveApplicant table with default values
        // Note: We set the archive applicant ID to the user ID to ensure they are linked
        $stmt = $conn->prepare("INSERT INTO ArchiveApplicant (id, user_id, status, account_creation_completed, interview_completed, demo_teaching_completed, hire_completed) VALUES (?, ?, ?, 1, 1, 1, 1)");
        $stmt->bind_param("iis", $user_id, $user_id, $status);
        
        if ($stmt->execute()) {
            $archive_applicant_id = $user_id; // Archive Applicant ID is the same as User ID

            // Insert into Employee table
            $stmt = $conn->prepare("INSERT INTO Employee (employee_id, archive_applicant_id, hire_date) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $employee_id, $archive_applicant_id, $hire_date);
            
            if ($stmt->execute()) {
                header("Location: ../Employee.php"); // Redirect to the main page after update
                exit();
            } else {
                echo 'Error: Could not insert employee record.';
            }
        } else {
            echo 'Error: Could not insert archive applicant record.';
        }
    } else {
        echo 'Error: Could not insert user record.';
    }

    $stmt->close();
    $conn->close();
}
?>
