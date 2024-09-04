<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

include 'Partials/db_conn.php';

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

// Update the query to fetch applicants from the 'Applicant' table
$query = "SELECT a.id, u.firstname, u.middlename, u.surname, u.email, u.phone, u.subject, a.status FROM Applicant a JOIN Users u ON a.user_id = u.id";
$applicants = $conn->query($query);

// Fetch leave requests from the 'leave_requests' table
$leave_query = "SELECT lr.id, lr.leave_type, lr.leave_date, lr.leave_reason, lr.date_submitted, u.firstname, u.middlename, u.surname, u.email 
                FROM leave_requests lr 
                JOIN Employee e ON lr.employee_id = e.employee_id 
                JOIN Users u ON e.archive_applicant_id = u.id";
$leave_requests = $conn->query($leave_query);


// Handle approve or decline actions
if (isset($_POST['action']) && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    // Fetch the email of the employee associated with the leave request
    $stmt = $conn->prepare("SELECT u.email, u.firstname, u.middlename, u.surname 
                            FROM leave_requests lr 
                            JOIN Employee e ON lr.employee_id = e.employee_id 
                            JOIN Users u ON e.archive_applicant_id = u.id 
                            WHERE lr.id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();

    if ($employee) {
        $employeeEmail = $employee['email'];
        $employeeName = htmlspecialchars($employee['firstname'] . ' ' . $employee['middlename'] . ' ' . $employee['surname']);

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'danielzanbaltazar.forwork@gmail.com'; // Change this to your email
            $mail->Password   = 'nqzk mmww mxin ikve'; // Change this to your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('danielzanbaltazar.forwork@gmail.com', 'HR Department');
            $mail->addAddress($employeeEmail, $employeeName);
            $mail->Subject = 'Leave Request Status';

            if ($action === 'approve') {
                $mail->Body    = 'Your leave request has been approved.';
            } elseif ($action === 'decline') {
                $mail->Body    = 'Your leave request has been declined.';
            }

            $mail->send();

            // Delete the leave request after sending the email
            $stmt = $conn->prepare("DELETE FROM leave_requests WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();

            header("Location: Leave_request.php");
            exit();
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }

    
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
        .table-container {
            margin-top: 50px;
            width: 100%;
            max-width: 6000px;
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

        .table-header .add-btn {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .table-header .add-btn:hover {
            background-color: #218838;
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

        .actions button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .actions .edit-btn {
            background-color: #007bff;
            color: #fff;
            margin-right: 10px;
        }

        .actions .edit-btn:hover {
            background-color: #0056b3;
        }

        .actions .delete-btn {
            background-color: #dc3545;
            color: #fff;
        }

        .actions .delete-btn:hover {
            background-color: #c82333;
        }

        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination button {
            padding: 10px 15px;
            margin: 0 5px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .pagination button:hover {
            background-color: #0056b3;
        }

        .pagination button.disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        /* Welcome Container */
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
.view-btn {
            background-color: #538392; /* Adjust button color */
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

/* Popup Modal styling */
.popup-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
}

.popup-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    width: 60%;
    max-width: 700px;
    text-align: left;
}

.popup-close {
    color: #333;
    float: right;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
}

.popup-close:hover,
.popup-close:focus {
    color: #000;
    text-decoration: none;
}

.popup-content h2 {
    margin-bottom: 20px;
    color: #538392;
}

.popup-content p {
    font-size: 16px;
    color: #555;
    margin-bottom: 10px;
}

.popup-content p strong {
    color: #333;
}

.popup-content a {
    color: #538392;
    text-decoration: underline;
}

.popup-content a:hover {
    text-decoration: none;
}
.shortlisted-applicant {
    text-align: center;
    margin-bottom: 20px;
}

.shortlisted-applicant h2 {
    font-size: 24px;
    font-weight: 600;
    color: #538392;
    margin: 0;
}
/* Style for the Approve button */
.approve-btn {
    background-color: #4CAF50; /* Green background color */
    color: white; /* White text color */
    border: none; /* Remove default border */
    padding: 10px 20px; /* Add padding */
    text-align: center; /* Center align text */
    text-decoration: none; /* Remove underline */
    display: inline-block; /* Make it inline with other elements */
    font-size: 14px; /* Font size */
    margin: 5px; /* Margin between buttons */
    cursor: pointer; /* Change cursor to pointer */
    border-radius: 4px; /* Rounded corners */
    transition: background-color 0.3s; /* Smooth transition for hover effect */
}

/* Hover effect for Approve button */
.approve-btn:hover {
    background-color: #45a049; /* Darker green on hover */
}

/* Style for the Decline button */
.decline-btn {
    background-color: #f44336; /* Red background color */
    color: white; /* White text color */
    border: none; /* Remove default border */
    padding: 10px 20px; /* Add padding */
    text-align: center; /* Center align text */
    text-decoration: none; /* Remove underline */
    display: inline-block; /* Make it inline with other elements */
    font-size: 14px; /* Font size */
    margin: 5px; /* Margin between buttons */
    cursor: pointer; /* Change cursor to pointer */
    border-radius: 4px; /* Rounded corners */
    transition: background-color 0.3s; /* Smooth transition for hover effect */
}

/* Hover effect for Decline button */
.decline-btn:hover {
    background-color: #e53935; /* Darker red on hover */
}
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="resources/logo.png" alt="Logo" class="logo">

        <a href="HR_Dashboard.php">
            <div class="nav-item" data-tooltip="Dashboard">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/dashboard-layout.png" alt="Dashboard"/>
            </div>
        </a>
        
        <a href="Employee.php">
            <div class="nav-item" data-tooltip="Employee">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=20318&format=png&color=000000" alt="employee"/>
            </div>
        </a>
        <a href="Applicant.php">
            <div class="nav-item" data-tooltip="Applicant">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/open-resume.png" alt="Applicant"/>
            </div>
        </a>
        <a href="AuditTrail.php">
            <div class="nav-item" data-tooltip="Audit Trail">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=o3t2bZRRLDfd&format=png&color=000000" alt="Audit" />
            </div>
        </a>
        <a href="ApplicantAttachment.php">
        <div class="nav-item" data-tooltip="Applicant Additional Attachments">
            <img width="96" height="96" src="https://img.icons8.com/?size=100&id=86460&format=png&color=000000" alt="Attachments"/>
        </div>
        </a>
    
        <a href="Leave_request.php">
            <div class="nav-item active" data-tooltip="Leave Request">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=14339&format=png&color=C44100" alt="Leave Request" />
            </div>
        </a>
        <a href="Eval.php">
            <div class="nav-item" data-tooltip="Evaluation Score">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=26001&format=png&color=000000" alt="Evaluation Score" />
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
            <img width="48" height="48" src="https://img.icons8.com/fluency-systems-regular/48/C44100/open-pane.png" alt="Logout"/>
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
    <div class="table-container">
 
    <!-- Table Container for Leave Requests -->
    <div class="leave-requests-container">
    <h2>Leave Requests</h2>
    <div class="table-header">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search Leave Requests...">
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Leave Type</th>
                <th>Leave Date</th>
                <th>Leave Reason</th>
                <th>Date Submitted</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="leaveRequestsTable">
            <?php
            if ($leave_requests->num_rows > 0) {
                while ($row = $leave_requests->fetch_assoc()) {
                    $employeeName = htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['surname']);
                    $leaveType = htmlspecialchars($row['leave_type']);
                    $leaveDate = htmlspecialchars($row['leave_date']);
                    $leaveReason = htmlspecialchars($row['leave_reason']);
                    $dateSubmitted = htmlspecialchars($row['date_submitted']);
                    $requestId = $row['id'];

                    echo "<tr>
                            <td>$employeeName</td>
                            <td>$leaveType</td>
                            <td>$leaveDate</td>
                            <td>$leaveReason</td>
                            <td>$dateSubmitted</td>
                            <td>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='request_id' value='$requestId'>
                                    <input type='hidden' name='action' value='approve'>
                                    <button type='submit' class='approve-btn'>Approve</button>
                                </form>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='request_id' value='$requestId'>
                                    <input type='hidden' name='action' value='decline'>
                                    <button type='submit' class='decline-btn'>Decline</button>
                                </form>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No leave requests found</td></tr>";
            }
            ?>
        </tbody>
    </table>
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
<script>
// JavaScript to filter table rows based on search input
document.getElementById('searchInput').addEventListener('keyup', function() {
    var input = this.value.toLowerCase();
    var tableRows = document.querySelectorAll('#leaveRequestsTable tr');

    tableRows.forEach(function(row) {
        var employeeName = row.cells[0].textContent.toLowerCase();
        var leaveType = row.cells[1].textContent.toLowerCase();
        var leaveDate = row.cells[2].textContent.toLowerCase();
        var leaveReason = row.cells[3].textContent.toLowerCase();
        var dateSubmitted = row.cells[4].textContent.toLowerCase();

        if (
            employeeName.includes(input) ||
            leaveType.includes(input) ||
            leaveDate.includes(input) ||
            leaveReason.includes(input) ||
            dateSubmitted.includes(input)
        ) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

</body>
</html>
