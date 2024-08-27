<?php
include 'db_conn.php';
session_start(); // Start the session to use session variables

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailOrId = $_POST['email']; // This can be either email or employee ID
    $password = $_POST['password'];

    // Default admin credentials
    $adminEmail = 'admin@gmail.com';
    $adminPassword = 'adminpassword';

    // Check if the input matches the default admin account
    if ($emailOrId === $adminEmail && $password === $adminPassword) {
        // Redirect to Admin dashboard for the default admin
        $_SESSION['email'] = $adminEmail; // Set session variable if needed
        header("Location: ../Admin/Admin_dashboard.php");
        exit();
    }

    // Check if the input is an email or an employee ID
    if (filter_var($emailOrId, FILTER_VALIDATE_EMAIL)) {
        // It's an email
        $stmt = $conn->prepare("SELECT id, firstname, middlename, surname, email, password, activated FROM Users WHERE email = ?");
        $stmt->bind_param("s", $emailOrId);
    } else {
        // Assume it's an employee ID
        $stmt = $conn->prepare("
            SELECT u.id, u.firstname, u.middlename, u.surname, u.email, u.password, u.activated 
            FROM Users u 
            JOIN Employee e ON u.id = e.archive_applicant_id 
            WHERE e.employee_id = ?");
        $stmt->bind_param("s", $emailOrId);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // If user is found in 'Users' table
    if ($user) {
        if ($user['activated'] == 0) {
            $_SESSION['message'] = ["text" => "Please activate your account", "class" => "warning"];
        } elseif (password_verify($password, $user['password'])) {
            // Set session variable
            $_SESSION['email'] = $user['email'];
            
            // Check if the user is an employee
            $employeeCheckStmt = $conn->prepare("SELECT employee_id FROM Employee WHERE archive_applicant_id = ?");
            $employeeCheckStmt->bind_param("i", $user['id']);
            $employeeCheckStmt->execute();
            $employeeCheckResult = $employeeCheckStmt->get_result();
            $employee = $employeeCheckResult->fetch_assoc();

            if ($employee) {
                // If the user is an employee, redirect to Employee/index.php
                header("Location: ../Employee/index.php");
            } else {
                // Otherwise, redirect to Applicant/Index.php
                header("Location: ../Applicant/index.php");
            }
            exit();
        } else {
            $_SESSION['message'] = ["text" => "Wrong password, please try again", "class" => "error"];
        }
    } else {
        // Check if the user exists in the 'hr_members' table
        $stmt = $conn->prepare("SELECT id, password FROM hr_members WHERE email = ?");
        $stmt->bind_param("s", $emailOrId);
        $stmt->execute();
        $result = $stmt->get_result();
        $hr_member = $result->fetch_assoc();

        // If user is found in 'hr_members' table
        if ($hr_member) {
            if (password_verify($password, $hr_member['password'])) {
                // Set session variable
                $_SESSION['email'] = $emailOrId;
                // Login successful, redirect to HR/HR_Dashboard.php
                header("Location: ../HR/HR_Dashboard.php");
                exit();
            } else {
                $_SESSION['message'] = ["text" => "Wrong password, please try again", "class" => "error"];
            }
        } else {
            $_SESSION['message'] = ["text" => "Please register, account not found", "class" => "warning"];
        }
    }

    $stmt->close();
    $conn->close();

    // Redirect back to login page with the message
    header("Location: ../login.php");
    exit();
}
?>
