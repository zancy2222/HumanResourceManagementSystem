<?php
include '../Partials/db_conn.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch user data
$email = $_SESSION['email'];

// Get user ID and other details from Users table
$stmt = $conn->prepare("SELECT id, firstname, middlename, surname, cv_filename, profile_filename FROM Users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

$user_id = $user['id'];

// Fetch applicant status
$stmt = $conn->prepare("SELECT status AS progress_status FROM Applicant WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$applicant = $result->fetch_assoc();

if (!$applicant) {
    echo "Applicant details not found.";
    exit();
}

// Close the statement and connection
$stmt->close();
$conn->close();
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
.content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .page-text {
            font-family: 'Bebas Neue', cursive;
            font-size: 48px;
            margin-bottom: 20px;
            color: #333;
        }

        .welcome-message {
            font-size: 20px;
            margin-bottom: 40px;
            color: #555;
        }

        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin: 0 auto;
            max-width: 600px;
        }

        .progress-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 6px;
            background: #e0e0e0;
            z-index: 0;
            transform: translateY(-50%);
            border-radius: 3px;
        }

        .progress-step {
            position: relative;
            z-index: 1;
            width: 25%;
            text-align: center;
        }

        .progress-step .circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e0e0e0;
            margin: 0 auto 10px;
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .progress-step.active .circle,
        .progress-step.complete .circle {
            background-color: #4caf50;
        }

        .progress-step p {
            font-size: 14px;
            margin: 0;
            font-weight: bold;
            color: #333;
        }

        .progress-container .progress-step.complete::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            right: -50%;
            height: 6px;
            background: #4caf50;
            z-index: -1;
            transform: translateX(-50%) translateY(-50%);
            width: 100%;
            border-radius: 3px;
        }

        .card-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            gap: 20px;
        }

        .card {
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            width: 48%;
            text-align: center;
            padding: 20px;
        }

        .card h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
        }

        .card img,
        .card iframe {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .card p a {
            color: #4caf50;
            text-decoration: none;
        }

        .card p a:hover {
            text-decoration: underline;
        }
</style>
</head>
<body>
<div class="sidebar">
        <img src="resources/logo.png" alt="Logo" class="logo">
        <a href="index.php"><div class="nav-item active" data-tooltip="Dashboard">
            <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/C44100/dashboard-layout.png" alt="Dashboard"/>
        </div></a>
        <a href="User.php">
            <div class="nav-item" data-tooltip="Profile">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/user.png" alt="Profile"/>
            </div>
        </a>
        <a href="Attachement.php">
            <div class="nav-item" data-tooltip="Additional Attachments">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=86460&format=png&color=000000" alt="Profile"/>
            </div>
        </a>
        <div class="nav-item logout" data-tooltip="Logout" onclick="confirmLogout()">
            <img width="48" height="48" src="https://img.icons8.com/fluency-systems-regular/48/C44100/open-pane.png" alt="Logout"/>
        </div>
    </div>
    <div class="content">
    <h1 class="page-text">Dashboard Overview</h1>
    <div class="welcome-message">Welcome "<?php echo htmlspecialchars($user['firstname'] . ' ' . $user['middlename'] . ' ' . $user['surname']); ?>"</div>

    <div class="progress-container">
        <div class="progress-step <?php echo ($applicant['progress_status'] == 'Account Creation' || $applicant['progress_status'] == 'Interview' || $applicant['progress_status'] == 'Demo Teaching' || $applicant['progress_status'] == 'Hire') ? 'complete' : ''; ?>">
            <div class="circle">1</div>
            <p>Account Creation</p>
        </div>
        <div class="progress-step <?php echo ($applicant['progress_status'] == 'Interview' || $applicant['progress_status'] == 'Demo Teaching' || $applicant['progress_status'] == 'Hire') ? 'complete' : ''; ?>">
            <div class="circle">2</div>
            <p>Interview</p>
        </div>
        <div class="progress-step <?php echo ($applicant['progress_status'] == 'Demo Teaching' || $applicant['progress_status'] == 'Hire') ? 'complete' : ''; ?>">
            <div class="circle">3</div>
            <p>Demo Teaching</p>
        </div>
        <div class="progress-step <?php echo $applicant['progress_status'] == 'Hire' ? 'complete' : ''; ?>">
            <div class="circle">4</div>
            <p>Hire</p>
        </div>
    </div>

    <div class="card-container">
        <!-- PDF Card -->
        <div class="card">
            <h3>Current CV/Resume</h3>
            <iframe src="../Partials/uploads/<?php echo htmlspecialchars($user['cv_filename']); ?>" height="250"></iframe>
            <p><a href="../Partials/uploads/<?php echo htmlspecialchars($user['cv_filename']); ?>" target="_blank">View Full PDF</a></p>
        </div>

        <!-- Image Card -->
        <div class="card">
            <h3>Profile Image Preview</h3>
            <img src="../Partials/uploads/<?php echo htmlspecialchars($user['profile_filename']); ?>" alt="Image Preview">
            <p><a href="../Partials/uploads/<?php echo htmlspecialchars($user['profile_filename']); ?>" target="_blank">View Full Image</a></p>
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
