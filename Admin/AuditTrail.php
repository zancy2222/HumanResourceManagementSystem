<?php
include 'Partials/db_conn.php'; // Adjust the path as needed


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
/* Style for the Activate Again button */
button[type='submit'] {
    background-color: #FF5400; /* Bright orange background */
    color: #FFFFFF; /* White text color */
    border: none; /* No border */
    padding: 10px 20px; /* Padding for spacing */
    border-radius: 4px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor on hover */
    font-size: 14px; /* Font size */
    transition: background-color 0.3s ease; /* Smooth transition for hover effect */
}

/* Hover effect */
button[type='submit']:hover {
    background-color: #e04900; /* Darker orange on hover */
}

/* Optional: Active state */
button[type='submit']:active {
    background-color: #cc4200; /* Even darker orange when active */
}

/* Optional: Disabled state */
button[type='submit']:disabled {
    background-color: #ddd; /* Light gray for disabled state */
    cursor: not-allowed; /* Not allowed cursor */
}

    </style>
</head>
<body>
<div class="sidebar">
        <img src="resources/logo.png" alt="Logo" class="logo">

        <a href="Admin_dashboard.php">
            <div class="nav-item" data-tooltip="Dashboard">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/dashboard-layout.png" alt="Dashboard"/>
            </div>
        </a>
        
        <a href="HrAccount.php">
            <div class="nav-item" data-tooltip="Add HR Account">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/manager.png" alt="HR"/>
            </div>
        </a>
        <a href="Applicant.php">
            <div class="nav-item" data-tooltip="Applicant">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/open-resume.png" alt="Applicant"/>
            </div>
        </a>
        <a href="Employee.php">
            <div class="nav-item" data-tooltip="Add Employee">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=19943&format=png&color=000000" alt="Applicant"/>
            </div>
        </a>
        <a href="AuditTrail.php">
            <div class="nav-item active" data-tooltip="Audit Trail">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=o3t2bZRRLDfd&format=png&color=C44100" alt="Audit"/>
            </div>
        </a>
        <a href="Graph.php">
            <div class="nav-item" data-tooltip="Statistics">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/investment-portfolio.png" alt="Stats"/>
            </div>
        </a>
        <div class="nav-item logout" data-tooltip="Logout" onclick="confirmLogout()">
            <img width="48" height="48" src="https://img.icons8.com/fluency-systems-regular/48/C44100/open-pane.png" alt="Logout"/>
        </div>
    </div>
    <div class="content">
    <h1 class="page text">Dashboard Overview</h1>

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
    <!-- Employee Details -->
    <div class="table-container">
        <div class="shortlisted-applicant">
            <h2>Employee Details</h2>
        </div>
        <div class="table-header">
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Hire Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display employee data
                $employeeStmt = $conn->query("SELECT e.employee_id, u.firstname, u.middlename, u.surname, e.hire_date FROM Employee e JOIN Users u ON e.archive_applicant_id = u.id");
                while ($employee = $employeeStmt->fetch_assoc()) {
                    echo "<tr>
                            <td>{$employee['employee_id']}</td>
                            <td>{$employee['firstname']} {$employee['middlename']} {$employee['surname']}</td>
                            <td>{$employee['hire_date']}</td>
                          </tr>";
                }
                ?>
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

    <!-- Applicant Details -->
    <div class="table-container">
        <div class="shortlisted-applicant">
            <h2>Applicant Details</h2>
        </div>
        <div class="table-header">
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Applicant ID</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display applicant data
                $applicantStmt = $conn->query("SELECT a.id, u.firstname, u.middlename, u.surname, a.status FROM Applicant a JOIN Users u ON a.user_id = u.id");
                while ($applicant = $applicantStmt->fetch_assoc()) {
                    echo "<tr>
                            <td>{$applicant['id']}</td>
                            <td>{$applicant['firstname']} {$applicant['middlename']} {$applicant['surname']}</td>
                            <td>{$applicant['status']}</td>
                          </tr>";
                }
                ?>
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
    
    <div class="table-container">
        <div class="shortlisted-applicant">
            <h2>Attachment Details</h2>
        </div>
        <table class="attachments-table">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>File Name</th>
                    <th>Certificates</th>
                    <th>Upload Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display attachment data along with user details
                $stmt = $conn->prepare("
                    SELECT u.firstname, u.middlename, u.surname, a.file_name, a.file_path, a.upload_date
                    FROM Attachments a
                    JOIN Users u ON a.user_id = u.id
                ");
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $file_path = '../Applicant/uploads/' . htmlspecialchars($row['file_path']);
                    $file_name = htmlspecialchars($row['file_name']);
                    $upload_date = htmlspecialchars($row['upload_date']);
                    $fullName = htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['surname']);

                    // Determine if the file is an image
                    $is_image = in_array(pathinfo($file_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']);

                    echo "<tr>
                        <td>{$fullName}</td>
                        <td>{$file_name}</td>";

                    // Display image preview if the file is an image
                    if ($is_image) {
                        echo "<td><img src='{$file_path}' alt='{$file_name}' style='max-width: 100px; height: auto;'></td>";
                    } else {
                        echo "<td>No preview available</td>";
                    }

                    echo "<td>{$upload_date}</td>
                    </tr>";
                }

               
                ?>
            </tbody>
        </table>
    </div>
    <!-- Failed Applicant Details -->
    <div class="table-container">
        <div class="shortlisted-applicant">
            <h2>Failed Applicant Details</h2>
        </div>
        <div class="table-header">
            <div class="search-bar">
                <input type="text" placeholder="Search...">
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Applicant ID</th>
                    <th>Name</th>
                    <th>Failure Reason</th>
                    <th>Failed At</th>
                    <th>Action</th> <!-- Added Action column -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display failed applicant data
                $failedStmt = $conn->query("SELECT f.id, u.firstname, u.middlename, u.surname, f.failure_reason, f.failed_at FROM FailedApplicant f JOIN Users u ON f.user_id = u.id");
                while ($failedApplicant = $failedStmt->fetch_assoc()) {
                    echo "<tr>
                            <td>{$failedApplicant['id']}</td>
                            <td>{$failedApplicant['firstname']} {$failedApplicant['middlename']} {$failedApplicant['surname']}</td>
                            <td>{$failedApplicant['failure_reason']}</td>
                            <td>{$failedApplicant['failed_at']}</td>
                            <td>
                                <form method='post' action='Partials/activate_again.php'>
                                    <input type='hidden' name='applicant_id' value='{$failedApplicant['id']}'>
                                    <button type='submit'>Activate Again</button>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
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


</body>
</html>
