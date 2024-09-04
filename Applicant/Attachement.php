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
        /* General styling for the content */
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

        /* Styling for the upload form */
        .upload-form {
            margin-top: 20px;
            text-align: center;
        }

        .upload-form input[type="file"] {
            margin-bottom: 10px;
        }

        .upload-form button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .upload-form button:hover {
            background-color: #218838;
        }

        .delete-btn{
            padding: 10px 20px;
            background-color: darkred;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }


        /* Styling for the table container */
        .table-container {
            margin-top: 50px;
            width: 100%;
            max-width: 1000px; /* Adjusted to a more reasonable max-width */
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

        /* Styling for the table */
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
            /* Modal CSS */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1; 
        padding-top: 60px; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgb(0,0,0); 
        background-color: rgba(0,0,0,0.9); 
    }

    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }

    .modal-content { 
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @keyframes zoom {
        from {transform:scale(0)} 
        to {transform:scale(1)}
    }

    .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
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
        <div class="nav-item" data-tooltip="Profile">
            <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/user.png" alt="Profile"/>
        </div>
    </a>
    <a href="Attachement.php">
        <div class="nav-item active" data-tooltip="Additional Attachments">
            <img width="96" height="96" src="https://img.icons8.com/?size=100&id=86460&format=png&color=C44100" alt="Attachments"/>
        </div>
    </a>
    <div class="nav-item logout" data-tooltip="Logout" onclick="confirmLogout()">
        <img width="48" height="48" src="https://img.icons8.com/fluency-systems-regular/48/C44100/open-pane.png" alt="Logout"/>
    </div>
</div>
<div class="content">
    <h1 class="page-text">Dashboard Overview</h1>

    <!-- Form to upload attachments -->
    <div class="upload-form">
        <h2>Upload Attachments</h2>
        <form action="Partials/save_attachment.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="attachment" required>
            <button type="submit">Upload</button>
        </form>
    </div>

    <!-- Table to display attachments -->
    <div class="table-container">
    <h2>Uploaded Attachments</h2>
    <table class="attachments-table">
    <thead>
        <tr>
            <th>File Name</th>
            <th>Certificates</th>
            <th>Upload Date</th>
            <th>Action</th> <!-- Action column with Preview and Delete buttons -->
        </tr>
    </thead>
    <tbody>
        <?php
        include 'Partials/db_conn.php'; // Reconnect to fetch attachments

        $stmt = $conn->prepare("SELECT id, file_name, file_path, upload_date FROM Attachments WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $file_id = $row['id'];
            $file_path = 'uploads/' . $row['file_path'];
            $file_name = $row['file_name'];
            $upload_date = $row['upload_date'];
            
            $is_image = in_array(pathinfo($file_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']);

            echo "<tr>
                <td>{$file_name}</td>";

            if ($is_image) {
                echo "<td><img src='{$file_path}' alt='{$file_name}' style='max-width: 100px; height: auto;'></td>";
            } else {
                echo "<td>No preview available</td>";
            }

            echo "<td>{$upload_date}</td>
                <td>";

            // Preview button for image files
            if ($is_image) {
                echo "<a href='#' class='preview-link' data-src='{$file_path}'>Preview</a> | ";
            }

            // Delete button
            echo "<form action='Partials/delete_attachment.php' method='post' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this attachment?\");'>
                        <input type='hidden' name='file_id' value='{$file_id}'>
                        <button type='submit' class='delete-btn'>Delete</button>
                  </form>
                  </td>
              </tr>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </tbody>
</table>

</div>
<div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="fullImage">
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
    // Get the modal
    var modal = document.getElementById("imageModal");

    // Get the image and insert it inside the modal
    var modalImg = document.getElementById("fullImage");
    var previewLinks = document.querySelectorAll('.preview-link');

    previewLinks.forEach(link => {
        link.onclick = function(e) {
            e.preventDefault();
            modal.style.display = "block";
            modalImg.src = this.dataset.src;
        };
    });

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() { 
        modal.style.display = "none";
    };

    // Close the modal when clicking outside of the image
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

</script>
</body>
</html>
