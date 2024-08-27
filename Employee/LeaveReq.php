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
    $profileImage = !empty($employee['profile_filename']) ? '../Admin/Partials/uploads/' . htmlspecialchars($employee['profile_filename']) : '../Admin/Partials/resources/default_profile.png';
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

/* Leave request form styling */
.leave-request-container {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 30px;
}

.form-title {
    font-size: 1.8rem;
    margin-bottom: 20px;
    color: #333;
}

.leave-form {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: 1rem;
    margin-bottom: 10px;
    color: #555;
}

.form-input {
    font-size: 1rem;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 20px;
    width: 100%;
}

.form-input:focus {
    border-color: #ff4d6d;
    outline: none;
}

.submit-button {
    background-color: #ff4d6d;
    color: #fff;
    padding: 15px 20px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    align-self: flex-start;
}

.submit-button:hover {
    background-color: #ff1c48;
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
            <div class="nav-item" data-tooltip="File Manager">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=82790&format=png&color=000000" alt="File Manager" />
            </div>
        </a>
        <a href="LeaveReq.php">
            <div class="nav-item active" data-tooltip="Leave Request">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=14339&format=png&color=C44100" alt="Leave Request" />
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

    <div class="leave-request-container">
        <h2 class="form-title">Leave Request Form</h2>
        <form action="Partials/submit_leave_request.php" method="post" class="leave-form">
    <label for="leave-type" class="form-label">Leave Type</label>
    <select id="leave-type" name="leave_type" class="form-input">
        <option value="sick">Sick Leave</option>
        <option value="vacation">Vacation Leave</option>
        <option value="emergency">Emergency Leave</option>
        <option value="other">Other</option>
    </select>

    <label for="leave-date" class="form-label">Leave Date</label>
    <input type="date" id="leave-date" name="leave_date" class="form-input" required>

    <label for="leave-reason" class="form-label">Reason for Leave</label>
    <textarea id="leave-reason" name="leave_reason" class="form-input" rows="5" placeholder="Enter your reason..." required></textarea>

    <button type="submit" class="submit-button">Submit Request</button>
</form>

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


</body>

</html>