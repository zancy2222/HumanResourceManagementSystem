<?php
include 'Partials/db_conn.php'; // Adjust the path as needed
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

// Fetch evaluation results from the database
$evaluation_query = "
    SELECT criteria, rating, comments, date_submitted
    FROM Evaluations 
    WHERE employee_id = ?
";

$evaluation_stmt = $conn->prepare($evaluation_query);
$evaluation_stmt->bind_param("s", $employeeId);
$evaluation_stmt->execute();
$evaluation_result = $evaluation_stmt->get_result();

$evaluations = [];
$comments = ""; // Initialize comments variable

while ($row = $evaluation_result->fetch_assoc()) {
    $criteria = htmlspecialchars($row['criteria']);
    $evaluations[$criteria] = [
        'rating' => $row['rating'],
        'date_submitted' => htmlspecialchars($row['date_submitted'])
    ];
    
    // Capture comments if available
    if (!empty($row['comments'])) {
        $comments = htmlspecialchars($row['comments']);
    }
}

// Close the statements and connection
$evaluation_stmt->close();
$stmt->close();
$conn->close();
// Generate an image with evaluation data, including the date submitted
function generateEvaluationImage($fullName, $evaluations, $comments) {
    // Create a blank image
    $width = 800;
    $height = 600 + (count($evaluations) * 60); // Adjust height based on the number of evaluations
    $image = imagecreate($width, $height);
    
    // Define colors
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);

    // Set background to white
    imagefilledrectangle($image, 0, 0, $width, $height, $white);

    // Set the font path (adjust the path to your TTF font)
    $font = 'League_Spartan/LeagueSpartan-VariableFont_wght.ttf'; // Path to the font file

    // Add title
    imagettftext($image, 24, 0, 20, 50, $black, $font, "Evaluation Results for {$fullName}");

    // Add evaluation results and date submitted
    $y = 100;
    foreach ($evaluations as $criteria => $details) {
        $ratingText = match ($details['rating']) {
            1 => '1 (Lowest)',
            2 => '2 (Below Average)',
            3 => '3 (Average)',
            4 => '4 (Above Average)',
            5 => '5 (Highest)',
            default => 'No Rating'
        };
        $dateSubmitted = htmlspecialchars($details['date_submitted']);

        // Display the criterion, rating, and date submitted
        imagettftext($image, 16, 0, 20, $y, $black, $font, "Criteria: $criteria - Rating: $ratingText");
        $y += 30;
        imagettftext($image, 14, 0, 40, $y, $black, $font, "Date Submitted: $dateSubmitted");
        $y += 30;
    }

    // Add comments
    imagettftext($image, 16, 0, 20, $y, $black, $font, "Evaluator's Comments:");
    $y += 30;
    imagettftext($image, 16, 0, 20, $y, $black, $font, $comments ? $comments : "No comments provided.");

    // Save the image to a file
    $filePath = __DIR__ . '/evaluation_image.png';
    imagepng($image, $filePath);
    imagedestroy($image);

    return $filePath;
}


// Handle "Save Evaluations" button click
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_evaluations'])) {
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        // Generate the evaluation image
        $imageFilePath = generateEvaluationImage($fullName, $evaluations, $comments);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'danielzanbaltazar.forwork@gmail.com'; // Change this to your email
        $mail->Password   = 'nqzk mmww mxin ikve'; // Change this to your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('danielzanbaltazar.forwork@gmail.com', 'Evaluation System');
        $mail->addAddress($email); // Send to the logged-in user's email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Evaluation Results';
        $mail->Body    = "<p>Please find attached the evaluation results.</p>";

        // Attach the image
        $mail->addAttachment($imageFilePath);

        $mail->send();

        // Delete the temporary image file
        unlink($imageFilePath);

        header("Location: Eval.php"); // Redirect to a success page or display a success message
    } catch (Exception $e) {
        echo '<p>Email could not be sent. Mailer Error: ', $mail->ErrorInfo, '</p>';
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
          /* Styling for the upload form */
          .evaluation-result {
        background-color: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-top: 40px;
    }

    .evaluation-result h2 {
        font-family: 'Montserrat', sans-serif;
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    .result-text {
        font-family: 'Montserrat', sans-serif;
        font-size: 18px;
        color: #555;
        margin-bottom: 20px;
    }

    .evaluation-result-table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
        text-align: center;
    }

    .evaluation-result-table th, .evaluation-result-table td {
        padding: 10px;
        border: 1px solid #ced4da;
        font-family: 'Montserrat', sans-serif;
        font-size: 14px;
    }

    .evaluation-result-table th {
        background-color: #ff7e20;
        color: white;
    }

    .evaluation-result-table td {
        background-color: #ffffff;
        color: #333;
    }

    .result-comments {
        padding: 15px;
        background-color: #ffffff;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-family: 'Montserrat', sans-serif;
        font-size: 14px;
        color: #555;
        white-space: pre-line;
        line-height: 1.6;
        margin-top: 20px;
    }

    .result-comments label {
        font-weight: bold;
    }
     /* CSS for Save Evaluations Button */
     button[name="save_evaluations"] {
        background-color: #4CAF50; /* Green background */
        border: none; /* Remove borders */
        color: white; /* White text */
        padding: 15px 32px; /* Padding for the button */
        text-align: center; /* Centered text */
        text-decoration: none; /* Remove underline */
        display: inline-block; /* Align with other inline elements */
        font-size: 16px; /* Font size */
        margin: 4px 2px; /* Margin around the button */
        cursor: pointer; /* Pointer cursor on hover */
        border-radius: 4px; /* Rounded corners */
        transition: background-color 0.3s ease; /* Smooth background color transition */
    }

    button[name="save_evaluations"]:hover {
        background-color: #45a049; /* Darker green on hover */
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
            <div class="nav-item active" data-tooltip="Evaluation Score">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=26001&format=png&color=C44100" alt="Evaluation Score" />
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

        <div class="evaluation-result">
    <h2>Evaluation Result</h2>
    <table class="evaluation-result-table">
        <thead>
            <tr>
                <th>Criteria</th> <!-- Placeholder header for criteria -->
                <?php
                $criteriaList = [
                    'Professionalism',
                    'Use of Technology',
                    'Feedback and Assessment',
                    'Classroom Management',
                    'Subject Knowledge',
                    'Teaching Methods',
                    'Student Engagement',
                    'Communication Skills'
                ];
                
                foreach ($criteriaList as $criteria) {
                    echo "<th>{$criteria}</th>"; // Display each criterion as a table header
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Rating</td> <!-- Row header for Ratings -->
                <?php
                foreach ($criteriaList as $criteria) {
                    $rating = isset($evaluations[$criteria]['rating']) ? $evaluations[$criteria]['rating'] : 'Not Rated';
                    $ratingText = match ($rating) {
                        1 => '1 (Lowest)',
                        2 => '2 (Below Average)',
                        3 => '3 (Average)',
                        4 => '4 (Above Average)',
                        5 => '5 (Highest)',
                        default => 'No Rating'
                    };
                    echo "<td>{$ratingText}</td>"; // Display the rating under the corresponding criterion
                }
                ?>
            </tr>
            <tr>
                <td>Date Submitted</td> <!-- Row header for Date Submitted -->
                <?php
                foreach ($criteriaList as $criteria) {
                    $dateSubmitted = isset($evaluations[$criteria]['date_submitted']) ? $evaluations[$criteria]['date_submitted'] : 'N/A';
                    echo "<td>{$dateSubmitted}</td>"; // Display the date submitted under the corresponding criterion
                }
                ?>
            </tr>
        </tbody>
    </table>

    <!-- Display evaluator's comments -->
    <label for="comments">Evaluator's Comments:</label>
    <div id="comments" class="result-comments" style="color: black;"> <!-- Set comments color to black -->
        <?php
        if (!empty($comments)) {
            echo $comments;
        } else {
            echo "No comments provided.";
        }
        ?>
    </div>

    <!-- Save Evaluations Button -->
    <form method="POST">
        <button type="submit" name="save_evaluations">Save Evaluations</button>
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