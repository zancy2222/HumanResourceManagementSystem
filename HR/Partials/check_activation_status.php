<?php
include 'db_conn.php'; // Adjust the path as needed

if (isset($_GET['id'])) {
    $applicant_id = $_GET['id'];

    // Fetch user activation status based on applicant_id
    $stmt = $conn->prepare("
        SELECT activated 
        FROM Users 
        WHERE id = (SELECT user_id FROM Applicant WHERE id = ?)
    ");
    $stmt->bind_param("i", $applicant_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            echo json_encode(['activated' => $user['activated']]);
        } else {
            echo json_encode(['activated' => false, 'message' => 'User not found']);
        }
    } else {
        echo json_encode(['activated' => false, 'message' => 'Failed to check activation status. Error: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['activated' => false, 'message' => 'Required parameter is missing.']);
}

$conn->close();
?>
