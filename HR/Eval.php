<?php
include '../Admin/Partials/db_conn.php'; // Adjust the path as needed
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}

// Get the email from session
$email = $_SESSION['email'];

// Fetch HR member's details from the database
$stmt = $conn->prepare("SELECT first_name, middle_name, last_name, profile_picture FROM hr_members WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$hr_member = $result->fetch_assoc();

// Check if HR member data is found
if ($hr_member) {
    $fullName = htmlspecialchars($hr_member['first_name'] . ' ' . $hr_member['middle_name'] . ' ' . $hr_member['last_name']);
    $profilePicture = !empty($hr_member['profile_picture']) ? '../Admin/Partials/' . htmlspecialchars($hr_member['profile_picture']) : '../Admin/Partials/resources/default_profile.png';
} else {
    // If no HR member found, redirect to login page
    header("Location: ../login.php");
    exit();
}

// Fetch employee names from the Employee table
$employee_query = "SELECT e.employee_id, CONCAT(u.firstname, ' ', u.middlename, ' ', u.surname) AS full_name 
                   FROM Employee e
                   JOIN Users u ON e.archive_applicant_id = u.id";
$employee_result = $conn->query($employee_query);


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

        /* Additional CSS for Evaluation Form */
        .evaluation-form {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }

        .evaluation-form h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .dropdown {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            background-color: #fff;
        }

        .evaluation-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            text-align: center;
        }

        .evaluation-table th,
        .evaluation-table td {
            padding: 10px;
            border: 1px solid #ced4da;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
        }

        .evaluation-table th {
            background-color: #e9ecef;
        }

        .comments-field {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            background-color: #fff;
            margin-bottom: 20px;
            resize: vertical;
        }

        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <img src="resources/logo.png" alt="Logo" class="logo">

        <a href="HR_Dashboard.php">
            <div class="nav-item" data-tooltip="Dashboard">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/dashboard-layout.png" alt="Dashboard" />
            </div>
        </a>

        <a href="Employee.php">
            <div class="nav-item" data-tooltip="Employee">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=20318&format=png&color=000000" alt="employee" />
            </div>
        </a>
        <a href="Applicant.php">
            <div class="nav-item" data-tooltip="Applicant">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/open-resume.png" alt="Applicant" />
            </div>
        </a>
        <a href="AuditTrail.php">
            <div class="nav-item" data-tooltip="Audit Trail">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=o3t2bZRRLDfd&format=png&color=000000" alt="Audit" />
            </div>
        </a>
        <a href="ApplicantAttachment.php">
            <div class="nav-item" data-tooltip="Applicant Additional Attachments">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=86460&format=png&color=000000" alt="Attachments" />
            </div>
        </a>

        <a href="Leave_request.php">
            <div class="nav-item" data-tooltip="Leave Request">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=14339&format=png&color=000000" alt="Leave Request" />
            </div>
        </a>
        <a href="Eval.php">
            <div class="nav-item active" data-tooltip="Evaluation Score">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=26001&format=png&color=C44100" alt="Evaluation Score" />
            </div>
        </a>
        <a href="Resign.php">
            <div class="nav-item" data-tooltip="Resignation">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=CbeQMEkEaRur&format=png&color=000000" alt="Profile" />
            </div>
        </a>
        <a href="Branches.php">
            <div class="nav-item" data-tooltip="Branches">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=A2JbOkejboJA&format=png&color=000000" alt="Evaluation Score" />
            </div>
        </a>
        <a href="Graph.php">
            <div class="nav-item" data-tooltip="Statistics">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/investment-portfolio.png" alt="Stats" />
            </div>
        </a>
        <div class="nav-item logout" data-tooltip="Logout" onclick="confirmLogout()">
            <img width="48" height="48" src="https://img.icons8.com/fluency-systems-regular/48/C44100/open-pane.png" alt="Logout" />
        </div>
    </div>
    <div class="content">
        <div class="welcome-container">
            <img src="<?php echo $profilePicture; ?>" alt="Profile Image" class="profile-image">
            <h1 class="welcome-text">Welcome HR <?php echo $fullName; ?></h1>
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
                <div class="label">Employees</div>
                <div class="number"><?php echo $employeeCount; ?></div>
                <div class="icon icon-wrapper">
                    <img width="30" height="30" src="https://img.icons8.com/ios/50/c44100/teacher.png" alt="teacher" />
                </div>
            </div>
            <div class="status-box">
                <div class="label">Branches</div>
                <div class="number"><?php echo $branchesCount; ?></div>
                <div class="icon icon-wrapper">
                    <img width="30" height="30" src="https://img.icons8.com/ios/50/c44100/school.png" alt="school" />
                </div>
            </div>
            <div class="status-box">
                <div class="label">Applicants</div>
                <div class="number"><?php echo $applicantCount; ?></div>
                <div class="icon icon-wrapper">
                    <img width="30" height="30" src="https://img.icons8.com/ios/50/c44100/open-resume.png" alt="open-resume" />
                </div>
            </div>
            <div class="status-box">
                <div class="label">Pending Leave</div>
                <div class="number"><?php echo $pendingLeaveCount; ?></div>
                <div class="icon icon-wrapper">
                    <img width="30" height="30" src="https://img.icons8.com/ios/50/c44100/leave.png" alt="leave" />
                </div>
            </div>
        </div>
        <div class="evaluation-form">
            <h2>Employee Evaluation Form</h2>
            <form method="POST" action="Partials/submit_evaluation.php">
                <label for="employee-name">Select Employee:</label>
                <select id="employee-name" name="employee-id" class="dropdown" required>
                    <?php
                    if ($employee_result->num_rows > 0) {
                        // Output data for each employee
                        while ($row = $employee_result->fetch_assoc()) {
                            $employeeId = htmlspecialchars($row['employee_id']);
                            $fullName = htmlspecialchars($row['full_name']);
                            echo "<option value='$employeeId'>$fullName</option>";
                        }
                    } else {
                        echo "<option value=''>No employees available</option>";
                    }
                    ?>
                </select>

                <table class="evaluation-table">
                    <thead>
                        <tr>
                            <th>Criteria</th>
                            <th>1 (Lowest)</th>
                            <th>2 (Below Average)</th>
                            <th>3 (Average)</th>
                            <th>4 (Above Average)</th>
                            <th>5 (Highest)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Professionalism</td>
                            <td><input type="radio" name="professionalism" value="1" required></td>
                            <td><input type="radio" name="professionalism" value="2" required></td>
                            <td><input type="radio" name="professionalism" value="3" required></td>
                            <td><input type="radio" name="professionalism" value="4" required></td>
                            <td><input type="radio" name="professionalism" value="5" required></td>
                        </tr>
                        <tr>
                            <td>Use of Technology</td>
                            <td><input type="radio" name="technology" value="1" required></td>
                            <td><input type="radio" name="technology" value="2" required></td>
                            <td><input type="radio" name="technology" value="3" required></td>
                            <td><input type="radio" name="technology" value="4" required></td>
                            <td><input type="radio" name="technology" value="5" required></td>
                        </tr>
                        <tr>
                            <td>Feedback and Assessment</td>
                            <td><input type="radio" name="feedback" value="1" required></td>
                            <td><input type="radio" name="feedback" value="2" required></td>
                            <td><input type="radio" name="feedback" value="3" required></td>
                            <td><input type="radio" name="feedback" value="4" required></td>
                            <td><input type="radio" name="feedback" value="5" required></td>
                        </tr>
                        <tr>
                            <td>Classroom Management</td>
                            <td><input type="radio" name="management" value="1" required></td>
                            <td><input type="radio" name="management" value="2" required></td>
                            <td><input type="radio" name="management" value="3" required></td>
                            <td><input type="radio" name="management" value="4" required></td>
                            <td><input type="radio" name="management" value="5" required></td>
                        </tr>
                        <tr>
                            <td>Subject Knowledge</td>
                            <td><input type="radio" name="knowledge" value="1" required></td>
                            <td><input type="radio" name="knowledge" value="2" required></td>
                            <td><input type="radio" name="knowledge" value="3" required></td>
                            <td><input type="radio" name="knowledge" value="4" required></td>
                            <td><input type="radio" name="knowledge" value="5" required></td>
                        </tr>
                        <tr>
                            <td>Teaching Methods</td>
                            <td><input type="radio" name="methods" value="1" required></td>
                            <td><input type="radio" name="methods" value="2" required></td>
                            <td><input type="radio" name="methods" value="3" required></td>
                            <td><input type="radio" name="methods" value="4" required></td>
                            <td><input type="radio" name="methods" value="5" required></td>
                        </tr>
                        <tr>
                            <td>Student Engagement</td>
                            <td><input type="radio" name="engagement" value="1" required></td>
                            <td><input type="radio" name="engagement" value="2" required></td>
                            <td><input type="radio" name="engagement" value="3" required></td>
                            <td><input type="radio" name="engagement" value="4" required></td>
                            <td><input type="radio" name="engagement" value="5" required></td>
                        </tr>
                        <tr>
                            <td>Communication Skills</td>
                            <td><input type="radio" name="communication" value="1" required></td>
                            <td><input type="radio" name="communication" value="2" required></td>
                            <td><input type="radio" name="communication" value="3" required></td>
                            <td><input type="radio" name="communication" value="4" required></td>
                            <td><input type="radio" name="communication" value="5" required></td>
                        </tr>
                    </tbody>
                </table>

                <label for="comments">Additional Comments:</label>
                <textarea id="comments" name="comments" class="comments-field" rows="4" placeholder="Enter any additional comments..." required></textarea>

                <button type="submit" class="submit-btn">Submit Evaluation</button>
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


</body>

</html>