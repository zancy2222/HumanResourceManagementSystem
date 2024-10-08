
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

include 'db_conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Generate a unique reset token
    $reset_token = bin2hex(random_bytes(16));

    // Check if the email exists in the Users table
    $stmt = $conn->prepare("UPDATE Users SET reset_token = ?, reset_token_expiry = NOW() + INTERVAL 1 HOUR WHERE email = ?");
    $stmt->bind_param("ss", $reset_token, $email);
    $stmt->execute();
    $user_updated = $stmt->affected_rows > 0;
    $stmt->close();

    // If not found in Users, check the hr_members table
    if (!$user_updated) {
        $stmt = $conn->prepare("UPDATE hr_members SET reset_token = ?, reset_token_expiry = NOW() + INTERVAL 1 HOUR WHERE email = ?");
        $stmt->bind_param("ss", $reset_token, $email);
        $stmt->execute();
        $hr_member_updated = $stmt->affected_rows > 0;
        $stmt->close();
    } else {
        $hr_member_updated = false;
    }

    if ($user_updated || $hr_member_updated) {
        // Send reset email
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'danielzanbaltazar.forwork@gmail.com'; // Change this to your email
        $mail->Password   = 'nqzk mmww mxin ikve'; // Change this to your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('danielzanbaltazar.forwork@gmail.com', 'Password Reset');
        $mail->addAddress($email);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Hello,\n\nPlease click the following link to reset your password:\n\nhttp://localhost/HRMS/Partials/reset_password.php?token=$reset_token\n\nBest regards,\nHRMS";

        if ($mail->send()) {
            $_SESSION['message'] = ['class' => 'success', 'text' => 'A password reset link has been sent to your email.'];
        } else {
            $_SESSION['message'] = ['class' => 'error', 'text' => 'Error: ' . $mail->ErrorInfo];
        }
    } else {
        $_SESSION['message'] = ['class' => 'error', 'text' => 'No user found with that email.'];
    }

    $conn->close();
    header("Location: ../forgetpass.php");
    exit();
}
?>
