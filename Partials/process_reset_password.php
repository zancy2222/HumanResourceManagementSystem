<?php
include 'db_conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match
    if ($password !== $confirm_password) {
        $_SESSION['message'] = ['class' => 'error', 'text' => 'Passwords do not match.'];
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }

    // Check if the token is valid and not expired in Users table
    $stmt = $conn->prepare("SELECT email FROM Users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    
    $is_user = $stmt->num_rows == 1;

    if ($is_user) {
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();
    } else {
        // Check if the token is valid and not expired in hr_members table
        $stmt->close();
        $stmt = $conn->prepare("SELECT email FROM hr_members WHERE reset_token = ? AND reset_token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        $is_hr_member = $stmt->num_rows == 1;

        if ($is_hr_member) {
            $stmt->bind_result($email);
            $stmt->fetch();
            $stmt->close();
        } else {
            $_SESSION['message'] = ['class' => 'error', 'text' => 'Invalid or expired reset token.'];
            header("Location: forgot_password.php");
            exit();
        }
    }

    // Update password and clear reset token
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $update_query = $is_user ? 
        "UPDATE Users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?" :
        "UPDATE hr_members SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?";
    
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        $_SESSION['message'] = ['class' => 'success', 'text' => 'Password has been reset successfully.'];
        header("Location: ../login.php");
        exit();
    } else {
        $_SESSION['message'] = ['class' => 'error', 'text' => 'Error: ' . $stmt->error];
    }

    $stmt->close();
    $conn->close();
}
?>