<?php
include 'db_conn.php'; // Adjust the path as needed

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Generate employee ID
function generateEmployeeID() {
    $numbers = rand(1000, 9999);
    $characters = strtoupper(substr(md5(rand()), 0, 3));
    return $numbers . $characters;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $applicant_id = $_GET['id'];
    $status = $_GET['status'];

    // Map status to corresponding column
    $status_columns = [
        'Account Creation' => 'account_creation_completed',
        'Interview' => 'interview_completed',
        'Demo Teaching' => 'demo_teaching_completed',
        'Hire' => 'hire_completed'
    ];

    if (array_key_exists($status, $status_columns)) {
        $column = $status_columns[$status];

        // Prepare the update statement
        $stmt = $conn->prepare("
            UPDATE 
                Applicant
            SET 
                $column = 1,
                status = ?
            WHERE 
                id = ?
        ");
        $stmt->bind_param("si", $status, $applicant_id);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Progress status updated successfully.'];

            // If the status is 'Hire', move the applicant to the ArchiveApplicant table and send an email
            if ($status == 'Hire') {
                // Check if the applicant_id exists in the Applicant table
                $applicant_check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM Applicant WHERE id = ?");
                $applicant_check_stmt->bind_param("i", $applicant_id);
                $applicant_check_stmt->execute();
                $applicant_check_result = $applicant_check_stmt->get_result();
                $applicant_exists = $applicant_check_result->fetch_assoc()['count'];

                if ($applicant_exists > 0) {
                    // Generate employee ID
                    $employee_id = generateEmployeeID();

                    // Archive the applicant
                    $archive_stmt = $conn->prepare("
                        INSERT INTO ArchiveApplicant (id, user_id, status, account_creation_completed, interview_completed, demo_teaching_completed, hire_completed)
                        SELECT id, user_id, status, account_creation_completed, interview_completed, demo_teaching_completed, hire_completed
                        FROM Applicant
                        WHERE id = ?
                    ");
                    $archive_stmt->bind_param("i", $applicant_id);

                    if ($archive_stmt->execute()) {
                        // Insert into Employee table
                        $hire_date = date('Y-m-d');
                        $insert_stmt = $conn->prepare("
                            INSERT INTO Employee (employee_id, archive_applicant_id, hire_date)
                            VALUES (?, ?, ?)
                        ");
                        $insert_stmt->bind_param("sis", $employee_id, $applicant_id, $hire_date);

                        if ($insert_stmt->execute()) {
                            // Fetch user email
                            $email_stmt = $conn->prepare("SELECT email FROM Users WHERE id = (SELECT user_id FROM Applicant WHERE id = ?)");
                            $email_stmt->bind_param("i", $applicant_id);
                            $email_stmt->execute();
                            $email_result = $email_stmt->get_result();
                            $user = $email_result->fetch_assoc();

                            // Send email to the user
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
                                $mail->setFrom('danielzanbaltazar.forwork@gmail.com', 'Minds That Matter');
                                $mail->addAddress($user['email']);

                                // Content
                                $mail->isHTML(true);
                                $mail->Subject = 'Congratulations! You\'re Hired';
                                $mail->Body    = 'Congratulations! You have been hired. Your employee ID is ' . $employee_id;

                                $mail->send();
                                $response['email_sent'] = true;
                            } catch (Exception $e) {
                                $response['email_sent'] = false;
                                $response['email_error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                            }

                        } else {
                            $response['insert_error'] = 'Failed to insert into Employee table. Error: ' . $insert_stmt->error;
                        }

                        $insert_stmt->close();

                        // Delete records from Attachments table
                        $delete_attachments_stmt = $conn->prepare("DELETE FROM Attachments WHERE user_id = (SELECT user_id FROM Applicant WHERE id = ?)");
                        $delete_attachments_stmt->bind_param("i", $applicant_id);
                        $delete_attachments_stmt->execute();
                        $delete_attachments_stmt->close();

                        // Delete from Applicant table
                        $delete_stmt = $conn->prepare("DELETE FROM Applicant WHERE id = ?");
                        $delete_stmt->bind_param("i", $applicant_id);
                        $delete_stmt->execute();
                        $delete_stmt->close();

                        $response['archived'] = true;
                    } else {
                        $response['archive_error'] = 'Failed to archive applicant. Error: ' . $archive_stmt->error;
                    }

                    $archive_stmt->close();
                } else {
                    $response['applicant_error'] = 'Invalid applicant ID.';
                }

                $applicant_check_stmt->close();
            }

            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update progress status. Error: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid status provided.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Required parameters are missing.']);
}

$conn->close();
?>
