<?php
include 'db_conn.php';
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Update the activation status in the Users table
    $stmt = $conn->prepare("UPDATE Users SET activated = 1 WHERE activation_token = ?");
    $stmt->bind_param("s", $token);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Your account has been successfully activated; you can log in now.";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['message'] = "Invalid activation token.";
}

header("Location: ../register.php");
exit();
?>
