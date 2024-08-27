<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

include 'db_conn.php'; // Ensure this path is correct and the file establishes a proper DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Fetch the applicant's details
    $stmt = $conn->prepare("
        SELECT 
            u.email, 
            u.firstname, 
            a.user_id
        FROM 
            Applicant a
        JOIN 
            Users u ON a.user_id = u.id
        WHERE 
            a.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicant = $result->fetch_assoc();

    if (!$applicant) {
        echo json_encode(['success' => false, 'message' => 'Applicant not found.']);
        exit();
    }

    // Insert the applicant into the FailedApplicant table
    $stmt = $conn->prepare("
        INSERT INTO FailedApplicant (user_id, status, failure_reason)
        VALUES (?, 'Failed', 'Not Selected')
    ");
    $stmt->bind_param("i", $applicant['user_id']);
    $stmt->execute();

    // Delete the applicant from the Applicant table
    $stmt = $conn->prepare("
        DELETE FROM Applicant 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Send failure email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'danielzanbaltazar.forwork@gmail.com'; // Change this to your email
        $mail->Password   = 'nqzk mmww mxin ikve'; // Change this to your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('danielzanbaltazar.forwork@gmail.com', 'HRMS'); // Replace with your email and desired sender name
        $mail->addAddress($applicant['email'], $applicant['firstname']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Application Status Update';
        $mail->Body    = "
            <p>Dear {$applicant['firstname']},</p>
            <p>We regret to inform you that you have not been selected for the position at this time. We appreciate your interest in joining our team and encourage you to apply for future openings that match your skills and experience.</p>
            <p>Thank you for considering us as a potential employer.</p>
            <p>Best regards,<br>HR Department</p>
        ";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Failure email sent and applicant moved to FailedApplicant table successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
    }

    $stmt->close();
    $conn->close();
}
?>
