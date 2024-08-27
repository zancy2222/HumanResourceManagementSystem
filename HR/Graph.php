<?php
include 'Partials/db_conn.php'; // Adjust the path as needed
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
$result = $conn->query($query);

// Query to get the count of applicants for each subject
$query = "SELECT subject, COUNT(*) as applicant_count FROM Applicant a 
          JOIN Users u ON a.user_id = u.id
          GROUP BY subject";
$result = $conn->query($query);

$subjects = [];
$applicantCounts = [];

while ($row = $result->fetch_assoc()) {
    $subjects[] = $row['subject'];
    $applicantCounts[] = $row['applicant_count'];
}
// Query to get the number of employees hired each year
$query = "SELECT YEAR(hire_date) as year, COUNT(*) as total_employees
          FROM Employee
          GROUP BY YEAR(hire_date)
          ORDER BY year";
$result = $conn->query($query);

$years = [];
$employeeCounts = [];

while ($row = $result->fetch_assoc()) {
    $years[] = $row['year'];
    $employeeCounts[] = $row['total_employees'];
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
.charts-container {
    display: flex;
    justify-content: space-between;
    gap: 20px; /* Space between charts */
    margin-top: 40px; /* Space above the charts */
}

.chart-box {
    background-color: #fff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
    /* Optional: Adjust width for responsiveness */
    min-width: 300px;
}

.bar-chart-box {
    flex: 2; /* Make the bar chart larger */
    min-width: 400px;
}

.pie-chart-box {
    flex: 1; /* Make the pie chart smaller */
    min-width: 200px;
}

.chart-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(135deg, #ff7e20 0%, #ffe2cd 100%);
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
}

.chart-box canvas {
    width: 100% !important; /* Ensure the canvas fills the box */
    height: auto !important; /* Maintain aspect ratio */
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
            <div class="nav-item" data-tooltip="Branches">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=A2JbOkejboJA&format=png&color=000000" alt="Evaluation Score" />
            </div>
        </a>
        <a href="Graph.php">
            <div class="nav-item active" data-tooltip="Statistics">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/C44100/investment-portfolio.png" alt="Stats"/>
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
        <div class="charts-container">
    <div class="chart-box bar-chart-box">
        <canvas id="barChart"></canvas>
    </div>
    <div class="chart-box pie-chart-box">
        <canvas id="pieChart"></canvas>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
const barData = {
    labels: <?php echo json_encode($subjects); ?>,
    datasets: [{
        label: 'Number of Applicants',
        data: <?php echo json_encode($applicantCounts); ?>,
        backgroundColor: 'rgba(255, 126, 32, 0.6)',
        borderColor: 'rgba(255, 126, 32, 1)',
        borderWidth: 2,
        barPercentage: 0.7,
        categoryPercentage: 0.8
    }]
};

const barConfig = {
    type: 'bar',
    data: barData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                ticks: {
                    color: '#333',
                    font: {
                        size: 14
                    }
                },
                grid: {
                    display: false
                }
            },
            y: {
                ticks: {
                    color: '#333',
                    font: {
                        size: 14
                    }
                },
                grid: {
                    color: '#ececec',
                    borderColor: '#ececec'
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: '#333',
                titleColor: '#fff',
                bodyColor: '#fff'
            },
            title: {
                display: true,
                text: 'Applicants by Subject',
                font: {
                    size: 18,
                    weight: 'bold'
                },
                color: '#333'
            }
        }
    }
};

const barChart = new Chart(
    document.getElementById('barChart'),
    barConfig
);

const pieData = {
    labels: <?php echo json_encode($years); ?>,
    datasets: [{
        label: 'Employees Hired by Year',
        data: <?php echo json_encode($employeeCounts); ?>,
        backgroundColor: [
            'rgba(255, 126, 32, 0.7)',
            'rgba(255, 204, 153, 0.7)',
            'rgba(255, 126, 32, 0.7)',
            'rgba(255, 204, 153, 0.7)',
            // Add more colors as needed
        ],
        borderColor: 'rgba(255, 126, 32, 1)',
        borderWidth: 2
    }]
};

const pieConfig = {
    type: 'pie',
    data: pieData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'right'
            },
            tooltip: {
                backgroundColor: '#333',
                titleColor: '#fff',
                bodyColor: '#fff'
            },
            title: {
                display: true,
                text: 'Employees Hired by Year',
                font: {
                    size: 18,
                    weight: 'bold'
                },
                color: '#333'
            }
        }
    }
};

const pieChart = new Chart(
    document.getElementById('pieChart'),
    pieConfig
);
</script>

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
