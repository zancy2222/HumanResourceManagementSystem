<?php
include 'Partials/db_conn.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT first_name, middle_name, last_name, profile_picture FROM hr_members WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$hr_member = $result->fetch_assoc();

if ($hr_member) {
    $fullName = htmlspecialchars($hr_member['first_name'] . ' ' . $hr_member['middle_name'] . ' ' . $hr_member['last_name']);
    $profilePicture = !empty($hr_member['profile_picture']) ? '../Admin/Partials/' . htmlspecialchars($hr_member['profile_picture']) : '../Admin/Partials/resources/default_profile.png';
} else {
    header("Location: ../login.php");
    exit();
}

// Fetch employees
$employee_query = "SELECT CONCAT(firstname, ' ', middlename, ' ', surname) AS full_name, id, email FROM Users";
$employees = $conn->query($employee_query);

// Fetch branch assignments
$assignment_query = "SELECT CONCAT(firstname, ' ', middlename, ' ', surname) AS full_name, branch_name, BranchAssignments.id, Users.email
                     FROM BranchAssignments
                     JOIN Users ON BranchAssignments.employee_id = Users.id";
$assignments = $conn->query($assignment_query);

// Configure PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'danielzanbaltazar.forwork@gmail.com'; // Your email
    $mail->Password   = 'nqzk mmww mxin ikve'; // Your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['employee']) && isset($_POST['branch'])) {
        $employee_id = $_POST['employee'];
        $branch_name = $_POST['branch'];

        // Save to database
        $insert_stmt = $conn->prepare("INSERT INTO BranchAssignments (employee_id, branch_name) VALUES (?, ?)");
        $insert_stmt->bind_param("is", $employee_id, $branch_name);
        $insert_stmt->execute();

        if ($insert_stmt->affected_rows > 0) {
            // Fetch employee email
            $email_query = $conn->prepare("SELECT email FROM Users WHERE id = ?");
            $email_query->bind_param("i", $employee_id);
            $email_query->execute();
            $email_result = $email_query->get_result();
            $employee_email = $email_result->fetch_assoc()['email'];

            // Send email notification
            $mail->setFrom('danielzanbaltazar.forwork@gmail.com', 'Branch Assignment System');
            $mail->addAddress($employee_email);
            $mail->Subject = 'Branch Assignment Notification';
            $mail->Body    = "Hello,\n\nYou have been assigned to the branch: $branch_name.\n\nBest regards,\nBranch Assignment System";

            try {
                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            header("Location: Branches.php");
            exit();
        } else {
            echo '<p>Error saving branch assignment.</p>';
        }
    }

    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];

        // Fetch branch name and employee email before deletion
        $assignment_query = $conn->prepare("SELECT branch_name, Users.email 
                                            FROM BranchAssignments 
                                            JOIN Users ON BranchAssignments.employee_id = Users.id 
                                            WHERE BranchAssignments.id = ?");
        $assignment_query->bind_param("i", $delete_id);
        $assignment_query->execute();
        $assignment_result = $assignment_query->get_result();
        $assignment = $assignment_result->fetch_assoc();
        $branch_name = $assignment['branch_name'];
        $employee_email = $assignment['email'];

        // Delete from database
        $delete_stmt = $conn->prepare("DELETE FROM BranchAssignments WHERE id = ?");
        $delete_stmt->bind_param("i", $delete_id);
        $delete_stmt->execute();

        if ($delete_stmt->affected_rows > 0) {
            // Send email notification
            $mail->setFrom('danielzanbaltazar.forwork@gmail.com', 'Branch Assignment System');
            $mail->addAddress($employee_email);
            $mail->Subject = 'Branch Assignment Removal Notification';
            $mail->Body    = "Hello,\n\nYour branch assignment for the branch: $branch_name has been removed.\n\nBest regards,\nBranch Assignment System";

            try {
                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            header("Location: Branches.php");
            exit();
        } else {
            echo '<p>Error deleting branch assignment.</p>';
        }
    }
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
        table select {
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
    font-size: 16px;
    color: #333;
    background-color: #f9f9f9;
    transition: border-color 0.3s, background-color 0.3s;
}

/* Style for the select dropdown on focus */
table select:focus {
    border-color: #ff7e20; /* Change border color on focus */
    background-color: #fff; /* Change background color on focus */
    outline: none; /* Remove default outline */
}

/* Add some styling to the select options */
table select option {
    padding: 10px;
}

/* Styling the select elements in the form */
.table-header form {
    display: flex;
    gap: 15px;
    align-items: center;
}

.table-header label {
    margin-right: 10px;
    font-weight: bold;
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

         .delete-btn {
            padding: 10px 20px;
         
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            background-color: #dc3545;
            color: #fff;
        }

        .delete-btn:hover {
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


    </style>
</head>
<body>
    <div class="sidebar">
        <img src="resources/logo.png" alt="Logo" class="logo">

        <a href="HR_Dashboard.php">
            <div class="nav-item" data-tooltip="Dashboard">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular//dashboard-layout.png" alt="Dashboard"/>
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
            <div class="nav-item" data-tooltip="Leave Request">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=14339&format=png&color=000000" alt="Leave Request" />
            </div>
        </a>
        <a href="Eval.php">
            <div class="nav-item" data-tooltip="Evaluation Score">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=26001&format=png&color=000000" alt="Evaluation Score" />
            </div>
        </a>
        <a href="Branches.php">
            <div class="nav-item active" data-tooltip="Branches">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=A2JbOkejboJA&format=png&color=C44100" alt="Evaluation Score" />
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
    <div class="Branches">
        <h2>Branches</h2>
    </div>
    <div class="table-header">
        <form id="branch-form" method="POST">
            <label for="employee">Select Employee:</label>
            <select id="employee" name="employee" required>
                <!-- Options will be dynamically added here -->
            </select>
            <label for="branch">Select Branch:</label>
            <select id="branch" name="branch" required>
                <option value="Main Branch">Main Branch</option>
                <option value="Jacob">Jacob</option>
                <option value="Peñafrancia">Peñafrancia</option>
                <option value="P Diaz">P Diaz</option>
                <option value="Conception">Conception</option>
            </select>
            <button type="submit" class="add-btn">Save</button>
        </form>
    </div>
    <table id="branch-table">
    <thead>
        <tr>
            <th>Employee Name</th>
            <th>Branch</th>
            <th>Action</th> <!-- New column for actions -->
        </tr>
    </thead>
    <tbody>
        <?php if ($assignments->num_rows > 0): ?>
            <?php while ($row = $assignments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['branch_name']); ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No assignments found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


    <div class="pagination">
        <button class="disabled">&laquo; Previous</button>
        <button>1</button>
        <button>2</button>
        <button>3</button>
        <button>Next &raquo;</button>
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
document.addEventListener('DOMContentLoaded', function() {
    const employeeSelect = document.getElementById('employee');
    
    <?php if ($employees->num_rows > 0): ?>
        <?php while ($row = $employees->fetch_assoc()): ?>
            const option = document.createElement('option');
            option.value = '<?php echo $row['id']; ?>';
            option.textContent = '<?php echo htmlspecialchars($row['full_name']); ?>';
            employeeSelect.appendChild(option);
        <?php endwhile; ?>
    <?php endif; ?>
});

 
</script>

</body>
</html>
