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

// Fetch employee's and user's details from the database in a single query
$stmt = $conn->prepare("
    SELECT e.employee_id, u.firstname, u.middlename, u.surname, u.email, u.phone, u.experience, u.subject, u.cv_filename, u.profile_filename 
    FROM Employee e 
    JOIN ArchiveApplicant aa ON e.archive_applicant_id = aa.id 
    JOIN Users u ON aa.user_id = u.id 
    WHERE u.email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

// Check if employee information is found
if ($employee) {
    // Store data in variables
    $employeeId = htmlspecialchars($employee['employee_id']);
    $firstname = htmlspecialchars($employee['firstname']);
    $middlename = htmlspecialchars($employee['middlename']);
    $surname = htmlspecialchars($employee['surname']);
    $email = htmlspecialchars($employee['email']);
    $phone = htmlspecialchars($employee['phone']);
    $experience = htmlspecialchars($employee['experience']);
    $subject = htmlspecialchars($employee['subject']);
    $profileImage = !empty($employee['profile_filename']) ? '../Admin/Partials/uploads/' . htmlspecialchars($employee['profile_filename']) : '../Admin/Partials/resources/default_profile.png';
    $fullName = $firstname . ' ' . $middlename . ' ' . $surname;
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
        .profile-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.profile-header {
    display: flex;
    align-items: center;
    background-color: #007BFF;
    color: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.profile-header img {
    border-radius: 50%;
    width: 120px;
    height: 120px;
    margin-right: 20px;
    border: 3px solid white;
}

.profile-header h1 {
    margin: 0;
}

.employee-id {
    margin-top: 10px;
    font-size: 16px;
    color: #e3f2fd;
}

.profile-details {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

.profile-details .form-group {
    margin-bottom: 15px;
}

.profile-details label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
    color: #333;
}

.profile-details input, 
.profile-details textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 4px;
    border: 1px solid #ddd;
    font-size: 16px;
}

.profile-details .update-btn {
    background-color: #007BFF;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 20px;
}

.profile-details .update-btn:hover {
    background-color: #0056b3;
}

.resume-download {
    margin-top: 10px;
    display: inline-block;
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
}

.resume-download:hover {
    background-color: #218838;
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
            <div class="nav-item active" data-tooltip="Profile">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=uSh6qV3U0130&format=png&color=C44100" alt="Profile" />
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
        <div class="profile-container">
    <div class="profile-header">
        <img src="<?php echo $profileImage; ?>" alt="Profile Image">
        <div>
            <h1><?php echo $fullName; ?></h1>
            <p class="employee-id">Employee ID: <?php echo $employeeId; ?></p>
        </div>
    </div>

    <div class="profile-details">
        <form action="Partials/update_profile.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>" required>
            </div>

            <div class="form-group">
                <label for="middlename">Middle Name:</label>
                <input type="text" id="middlename" name="middlename" value="<?php echo $middlename; ?>" required>
            </div>

            <div class="form-group">
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" value="<?php echo $surname; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>" required>
            </div>

            <div class="form-group">
                <label for="experience">Years of Experience:</label>
                <input type="text" id="experience" name="experience" value="<?php echo $experience; ?>" required>
            </div>

            <div class="form-group">
                <label for="subject">Subject Expertise:</label>
                <input type="text" id="subject" name="subject" value="<?php echo $subject; ?>" required>
            </div>

            <div class="form-group">
                <label for="cv">Upload New Resume:</label>
                <input type="file" id="cv" name="cv">
                <?php if (!empty($employee['cv_filename'])): ?>
                    <a href="../Partials/uploads/<?php echo htmlspecialchars($employee['cv_filename']); ?>" class="resume-download" download>Download Current Resume</a>
                <?php endif; ?>
            </div>

            <button type="submit" class="update-btn">Update Profile</button>
        </form>
    </div>
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