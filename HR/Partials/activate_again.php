<?php
include 'db_conn.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicantId = $_POST['applicant_id'];

    // Fetch failed applicant details before deletion
    $failedApplicant = $conn->query("SELECT * FROM FailedApplicant WHERE id = $applicantId")->fetch_assoc();

    if ($failedApplicant) {
        // Insert back to Applicant table with the status 'Account Creation' and account_creation_completed set to 1
        $userId = $failedApplicant['user_id'];
        
        $insertApplicant = $conn->prepare("
            INSERT INTO Applicant (user_id, status, account_creation_completed)
            VALUES (?, 'Account Creation', 1)
        ");
        $insertApplicant->bind_param('i', $userId);
        $insertApplicant->execute();
        
        // Remove from FailedApplicant table
        $deleteFailed = $conn->prepare("DELETE FROM FailedApplicant WHERE id = ?");
        $deleteFailed->bind_param('i', $applicantId);
        $deleteFailed->execute();

        // Redirect back to the page or show a success message
        header('Location: ../AuditTrail.php?status=success');
        exit;
    } else {
        // Handle case where the applicant doesn't exist in FailedApplicant
        header('Location: ../AuditTrail.php?status=error');
        exit;
    }
}
?>
