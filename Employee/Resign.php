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
        .resignation-form-container {
        background-color: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-top: 40px;
        max-width:900px;
        margin-left: auto;
        margin-right: auto;
    }

    .resignation-form-container h2 {
        font-family: 'Montserrat', sans-serif;
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    .resignation-form-container label {
        font-family: 'Montserrat', sans-serif;
        font-size: 16px;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    .resignation-form-container input[type="text"],
    .resignation-form-container input[type="date"],
    .resignation-form-container textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-family: 'Montserrat', sans-serif;
        font-size: 14px;
        color: #555;
    }

    .resignation-form-container textarea {
        resize: vertical;
        height: 100px;
    }

    .resignation-form-container button[type="submit"] {
        width: 100%;
        background-color: #ff7e20;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 5px;
        font-family: 'Montserrat', sans-serif;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .resignation-form-container button[type="submit"]:hover {
        background-color: #e66a00;
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <img src="resources/logo.png" alt="Logo" class="logo">

        <a href="index.php">
            <div class="nav-item" data-tooltip="Dashboard">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/dashboard-layout.png" alt="Dashboard" />
            </div>
        </a>

        <a href="Folder.php">
            <div class="nav-item" data-tooltip="File Manager">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=82790&format=png&color=000000" alt="File Manager" />
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
            <div class="nav-item active" data-tooltip="Resignation">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=CbeQMEkEaRur&format=png&color=C44100" alt="Resign" />
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
<div class="resignation-form-container">
    <h2>Resignation Letter</h2>
    <form action="Partials/submit_resignation.php" method="post" onsubmit="return confirmResignation()">
    <label for="resignation-date">Resignation Date:</label>
    <input type="date" id="resignation-date" name="resignation_date" required>

    <label for="reason">Reason for Resignation:</label>
    <textarea id="reason" name="reason" required></textarea>

    <button type="submit">Submit Resignation</button>
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

<script>
// Disable past dates
document.getElementById('resignation-date').setAttribute('min', new Date().toISOString().split('T')[0]);

// Confirmation dialog on form submission
function confirmResignation() {
    return confirm("Are you sure you want to submit your resignation?");
}
</script>

</body>

</html>