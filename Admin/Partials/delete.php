<?php
include 'db_conn.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the ID from the POST request
    $id = intval($_POST['id']);

    if ($id) {
        // Prepare and execute the DELETE query
        $stmt = $conn->prepare("DELETE FROM hr_members WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Redirect back to the page with a success message
            header("Location: ../HrAccount.php");
        } else {
            // Redirect back to the page with an error message
            header("Location: ../HrAccount.php");
        }

        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>
