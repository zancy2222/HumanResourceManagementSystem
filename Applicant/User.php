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
$stmt = $conn->prepare("SELECT id, firstname, middlename, surname, email, phone, subject, experience, cv_filename, profile_filename FROM Users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
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
        .content {
            text-align: center;
            max-width: 1000px;
            width: 90%;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 10px;
        }

        .page-text {
            font-family: 'Bebas Neue', cursive;
            font-size: 36px;
            margin-bottom: 30px;
            color: #333;
        }

        .profile-form {
            text-align: left;
            max-width: 800px;
            margin: 0 auto;
            background: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-form label {
            display: block;
            font-weight: bold;
            margin: 15px 0 5px;
        }

        .profile-form input[type="text"],
        .profile-form input[type="email"],
        .profile-form input[type="password"],
        .profile-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        .profile-form input[type="file"] {
            margin-bottom: 20px;
        }

        .profile-form button {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .profile-form button:hover {
            background-color: #45a049;
        }

        .card-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-top: 40px;
            gap: 20px;
        }

        .card {
            background: #fff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            width: calc(50% - 10px);
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .card h3 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #333;
        }

        .card img,
        .card iframe {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
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
        <a href="index.php"><div class="nav-item" data-tooltip="Dashboard">
            <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/dashboard-layout.png" alt="Dashboard"/>
        </div></a>
        <a href="User.php">
            <div class="nav-item active" data-tooltip="Profile">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/C44100/96/user.png" alt="Profile"/>
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
    <h1 class="page-text">Profile Overview</h1>

    <form class="profile-form" action="Partials/update_profile.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>">

        <label for="middlename">Middle Name:</label>
        <input type="text" id="middlename" name="middlename" value="<?php echo htmlspecialchars($user['middlename']); ?>">

        <label for="surname">Surname:</label>
        <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" value="">

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($user['subject']); ?>">

        <label for="experience">Experience:</label>
        <textarea id="experience" name="experience" rows="5"><?php echo htmlspecialchars($user['experience']); ?></textarea>

        <label for="cv">Upload CV (PDF):</label>
        <input type="file" id="cv" name="cv" accept=".pdf">

        <label for="profile-image">Upload Profile Image:</label>
        <input type="file" id="profile-image" name="profile-image" accept="image/*">

        <button type="submit">Update Profile</button>
    </form>

    <div class="card-container">
        <!-- PDF Card -->
        <div class="card">
            <h3>Current CV/Resume</h3>
            <iframe src="../Partials/uploads/<?php echo htmlspecialchars($user['cv_filename']); ?>" height="300"></iframe>
            <p><a href="../Partials/uploads/<?php echo htmlspecialchars($user['cv_filename']); ?>" target="_blank">View Full CV</a></p>
        </div>

        <!-- Image Card -->
        <div class="card">
            <h3>Profile Image Preview</h3>
            <img src="../Partials/uploads/<?php echo htmlspecialchars($user['profile_filename']); ?>" alt="Profile Image">
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

<script>
        function validateForm() {
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const cv = document.getElementById('cv').files[0];
            const profileImage = document.getElementById('profile-image').files[0];
            const password = document.getElementById('password').value;
            
            // Email validation: must be a valid email address
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            // Phone number validation: must be 10 digits
            const phonePattern = /^\d{11}$/;
            if (!phonePattern.test(phone)) {
                alert("Phone number must be 11 digits long.");
                return false;
            }

            // File type validation: Check CV and profile image file types
            if (cv && !cv.name.endsWith('.pdf')) {
                alert("CV must be a PDF file.");
                return false;
            }
            if (profileImage && !profileImage.type.startsWith('image/')) {
                alert("Profile image must be an image file.");
                return false;
            }

            // Password validation if provided
            if (password) {
                const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
                if (!passwordPattern.test(password)) {
                    alert("Password must be at least 8 characters long, include 1 uppercase letter, 1 number, and 1 special character.");
                    return false;
                }
            }

            // Confirmation dialog
            return confirm("Are you sure you want to update your profile?");
        }
    </script>
</body>
</html>
