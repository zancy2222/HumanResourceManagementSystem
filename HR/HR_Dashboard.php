<?php
include '../Admin/Partials/db_conn.php';
session_start();

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

// Read data from CSV file
$filename = 'shortlisted_applicants.csv';
if (($handle = fopen($filename, "r")) !== FALSE) {
    $header = fgetcsv($handle); // Read the header row
    $applicants = [];
    while (($data = fgetcsv($handle)) !== FALSE) {
        $applicants[] = $data;
    }
    fclose($handle);
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
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="resources/logo.png" alt="Logo" class="logo">

        <a href="HR_Dashboard.php">
            <div class="nav-item active" data-tooltip="Dashboard">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/C44100/dashboard-layout.png" alt="Dashboard"/>
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
            <!-- Table Container -->
            <div class="shortlisted-applicant">
    <h2>Shortlisted Applicant</h2>
</div>
    <div class="table-header">
        <div class="search-bar">
            <input type="text" placeholder="Search...">
        </div>
    </div>
    <table>
<thead>
    <tr>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Subject</th>
        <th>Experience</th>
    </tr>
</thead>
<tbody>
    <?php
    if (!empty($applicants)) {
        foreach ($applicants as $applicant) {
            $id = htmlspecialchars($applicant[0]);
            $firstname = htmlspecialchars($applicant[1]);
            $surname = htmlspecialchars($applicant[2]);
            $experience = htmlspecialchars($applicant[3]);
            $fullName = $firstname . ' ' . $surname;

            // Fetch additional details for each applicant
            $query = "SELECT email, phone, subject FROM Users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $userDetails = $stmt->get_result()->fetch_assoc();

            $email = htmlspecialchars($userDetails['email']);
            $phone = htmlspecialchars($userDetails['phone']);
            $subject = htmlspecialchars($userDetails['subject']);

            echo "<tr>
                    <td>$fullName</td>
                    <td>$email</td>
                    <td>$phone</td>
                    <td>$subject</td>
                    <td>$experience years</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No shortlisted applicants found</td></tr>";
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

<div id="viewModal" class="popup-modal">
    <div class="popup-content">
        <span class="popup-close" onclick="closeViewModal()">&times;</span>
        <h2>Applicants Details</h2>
        <div id="employeeDetails">
            <!-- Applicant details will be loaded here -->
        </div>
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
function openViewModal(id) {
    // Fetch data using AJAX
    fetch('Partials/get_applicants_details.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            // Check if data is returned properly
            if (data) {
                // Populate modal with fetched data
                const employeeDetails = document.getElementById('employeeDetails');
                employeeDetails.innerHTML = `
                    <p><strong>Profile Picture:</strong> <img style="width: 150px;" src="../Partials/uploads/${data.profile_filename}" alt="Profile Image" class="profile-image"></p> 
                    <p><strong>Full Name:</strong> ${data.firstname} ${data.middlename} ${data.surname}</p>
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>Phone:</strong> ${data.phone}</p>
                    <p><strong>Subject:</strong> ${data.subject}</p>
                    <p><strong>Experience:</strong> ${data.experience}</p>
                    <p><strong>Progress Status:</strong> ${data.progress_status}</p> <!-- Include Progress Status -->
                    <p><strong>Created At:</strong> ${data.created_at}</p>
                    <p><strong>Activated:</strong> ${data.activated == 1 ? 'Yes' : 'No'}</p>
                    <p><strong>CV:</strong> <a href="../Partials/uploads/${data.cv_filename}" target="_blank">View CV</a></p>
                `;
                // Show the modal
                document.getElementById('viewModal').style.display = 'block';
            } else {
                console.error('No data returned from server.');
            }
        })
        .catch(error => console.error('Error fetching data:', error));
}


function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

</script>

</body>
</html>
