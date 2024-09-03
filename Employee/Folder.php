<?php
include '../Partials/db_conn.php'; // Adjust the path as needed
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Get the email from session
$email = $_SESSION['email'];

// Fetch employee's details from the database
$stmt = $conn->prepare("
    SELECT e.employee_id, u.firstname, u.middlename, u.surname, u.profile_filename 
    FROM Employee e 
    JOIN ArchiveApplicant aa ON e.archive_applicant_id = aa.id 
    JOIN Users u ON aa.user_id = u.id 
    WHERE u.email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if ($employee) {
    $employeeId = htmlspecialchars($employee['employee_id']);
    $fullName = htmlspecialchars($employee['firstname'] . ' ' . $employee['middlename'] . ' ' . $employee['surname']);
    $profileImage = !empty($employee['profile_filename']) ? '../Partials/uploads/' . htmlspecialchars($employee['profile_filename']) : '../Partials/resources/default_profile.png';
} else {
    // If no employee data is found, redirect to login page
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR | Dashboard</title>
    <link rel="stylesheet" href="css/hr_dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;700&family=Shadows+Into+Light&display=swap" rel="stylesheet">
    <style>
        .welcome-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .welcome-container .profile-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #CED4DA;
        }

        .welcome-container .welcome-text {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 24px;
            color: #333;
        }
          /* Styling for the upload form */
          .upload-form {
            margin-top: 20px;
            text-align: center;
        }

        .upload-form input[type="file"] {
            margin-bottom: 10px;
        }

        .upload-form button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .upload-form button:hover {
            background-color: #218838;
        }

        /* Styling for the table container */
        .table-container {
            margin-top: 50px;
            width: 100%;
            max-width: 1000px; /* Adjusted to a more reasonable max-width */
            margin-left: auto;
            margin-right: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            position: relative;
            width: 300px;
            margin-right: 30px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 20px;
            outline: none;
            font-size: 16px;
        }

        .search-bar input::placeholder {
            color: #6c757d;
        }

        /* Styling for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }

        table thead {
            background-color: #ff7e20;
            color: #fff;
        }

        table th,
        table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:hover {
            background-color: #f9f9f9;
        }
        .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 60px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    /* Modal Content (image) */
    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }

    /* Add Animation - Zoom in the Modal */
    .modal-content { 
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @keyframes zoom {
        from {transform:scale(0)} 
        to {transform:scale(1)}
    }

    /* The Close Button */
    .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }
    
    </style>
</head>

<body>
    <div class="sidebar">
        <img src="resources/logo.png" alt="Logo" class="logo">

        <a href="index.php">
            <div class="nav-item" data-tooltip="Dashboard">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/dashboard-layout.png" alt="Dashboard" />
            </div>
        </a>

        <a href="Folder.php">
            <div class="nav-item active" data-tooltip="File Manager">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=82790&format=png&color=C44100" alt="File Manager" />
            </div>
        </a>
        <a href="LeaveReq.php">
            <div class="nav-item" data-tooltip="Leave Request">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=14339&format=png&color=000000" alt="Leave Request" />
            </div>
        </a>
        <a href="Eval.php">
            <div class="nav-item" data-tooltip="Evaluation Score">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=26001&format=png&color=000000" alt="Evaluation Score" />
            </div>
        </a>
        <a href="Profile.php">
            <div class="nav-item" data-tooltip="Profile">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=uSh6qV3U0130&format=png&color=000000" alt="Profile" />
            </div>
        </a>
        <a href="Resign.php">
            <div class="nav-item" data-tooltip="Resignation">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=CbeQMEkEaRur&format=png&color=000000" alt="Profile" />
            </div>
        </a>
        <div class="nav-item logout" data-tooltip="Logout" onclick="confirmLogout()">
            <img width="48" height="48" src="https://img.icons8.com/fluency-systems-regular/48/C44100/open-pane.png" alt="Logout" />
        </div>
    </div>
    <div class="content">
        <div class="welcome-container">

            <img src="<?php echo $profileImage; ?>" alt="Profile Image" class="profile-image">
            <p class="employee-id">Employee ID: <?php echo $employeeId; ?></p>

            <h1 class="welcome-text">Welcome, <?php echo $fullName; ?></h1>
        </div>
        <?php
include 'Partials/db_conn.php'; // Include your database connection file

// Fetch the count of employees
$employeeQuery = "SELECT COUNT(*) AS count FROM Employee";
$employeeResult = $conn->query($employeeQuery);
$employeeCount = $employeeResult->fetch_assoc()['count'];

// Fetch the count of applicants
$applicantQuery = "SELECT COUNT(*) AS count FROM Applicant";
$applicantResult = $conn->query($applicantQuery);
$applicantCount = $applicantResult->fetch_assoc()['count'];

// Fetch the count of pending leave requests
$leaveQuery = "SELECT COUNT(*) AS count FROM leave_requests WHERE date_submitted > NOW() - INTERVAL 30 DAY"; // Example condition for recent leave requests
$leaveResult = $conn->query($leaveQuery);
$pendingLeaveCount = $leaveResult->fetch_assoc()['count'];

// Define default branches count
$branchesCount = 5;


?>

<div class="status-container">
<div class="status-box">
        <div class="label">Branches</div>
        <div class="number"><?php echo $branchesCount; ?></div>
        <div class="icon icon-wrapper">
            <img width="30" height="30" src="https://img.icons8.com/ios/50/c44100/school.png" alt="school" />
        </div>
    </div>
    <div class="status-box">
        <div class="label">Pending Leave</div>
        <div class="number"><?php echo $pendingLeaveCount; ?></div>
        <div class="icon icon-wrapper">
            <img width="30" height="30" src="https://img.icons8.com/ios/50/c44100/leave.png" alt="leave" />
        </div>
    </div>
<div class="status-box" style="border: none;">
       
    </div>
    <div class="status-box" style="border: none;">
       
       </div>
    

</div>

<div class="upload-form">
    <h2>Upload Attachments</h2>
    <form action="Partials/save_attachment.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="attachment" required>
        <button type="submit">Upload</button>
    </form>
</div>

<!-- Table to display attachments -->
<div class="table-container">
    <h2>Uploaded Attachments</h2>
    <table class="attachments-table">
    <thead>
        <tr>
            <th>File Name</th>
            <th>Image</th>
            <th>Upload Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Assuming you have the correct employee_id from the session or previous query
    $employee_id = htmlspecialchars($employeeId);

    $stmt = $conn->prepare("SELECT id, file_name, file_path, upload_date FROM AttachedFileEmployee WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $file_id = htmlspecialchars($row['id']);
        $file_path = 'uploads/' . htmlspecialchars($row['file_path']);
        $file_name = htmlspecialchars($row['file_name']);
        $upload_date = htmlspecialchars($row['upload_date']);
        $is_image = in_array(pathinfo($file_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']);

        echo "<tr>
            <td>{$file_name}</td>";

        // Display the actual image or a placeholder if not an image
        if ($is_image) {
            echo "<td><img src='{$file_path}' alt='{$file_name}' style='width: 100px; height: auto;'></td>";
        } else {
            echo "<td>No image available</td>";
        }

        echo "<td>{$upload_date}</td>
            <td>";

        // Preview option in the Actions column if the file is an image
        if ($is_image) {
            echo "<a href='#' class='preview-link' data-src='{$file_path}'>Preview</a> | ";
        }

        // Delete link
        echo "<a href='#' class='delete-link' data-id='{$file_id}'>Delete</a>";

        echo "</td></tr>";
    }

    $stmt->close();
    $conn->close();
    ?>
    </tbody>
</table>

</div>

<!-- Modal Structure -->
<div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="fullImage">
</div>


    </div>


    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to logout?</p>
            <button class="modal-btn yes-btn" onclick="logout()">Yes</button>
            <button class="modal-btn cancel-btn" onclick="closeModal()">Cancel</button>
        </div>
    </div>
    <script>
        function confirmLogout() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }
        function logout() {
            window.location.href = '../login.php';
        }
    </script>

<script>
    // Get the modal
    var modal = document.getElementById("imageModal");

    // Get the image and insert it inside the modal
    var modalImg = document.getElementById("fullImage");
    var previewLinks = document.querySelectorAll('.preview-link');

    previewLinks.forEach(link => {
        link.onclick = function(e) {
            e.preventDefault();
            modal.style.display = "block";
            modalImg.src = this.dataset.src;
        };
    });

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() { 
        modal.style.display = "none";
    };

    // Close the modal when clicking outside of the image
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Delete link confirmation
    var deleteLinks = document.querySelectorAll('.delete-link');

    deleteLinks.forEach(link => {
        link.onclick = function(e) {
            e.preventDefault();
            var fileId = this.dataset.id;
            if (confirm("Are you sure you want to delete this file?")) {
                window.location.href = 'Partials/delete_attachment.php?id=' + fileId;
            }
        };
    });
</script>
</body>

</html>