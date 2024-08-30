<?php
include 'Partials/db_conn.php'; // Include your database connection file

// Fetch all employee records from the database, including the 'id' column
$query = "SELECT id, first_name, middle_name, last_name, email, age, password, profile_picture FROM hr_members";
$result = $conn->query($query);
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
        .form-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .form-modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 60%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-size: 16px;
        }

        .form-modal-content h2 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="number"],
        .form-group input[type="file"] {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group input[type="file"] {
            padding: 3px;
        }

        .form-group .file-label {
            display: block;
            font-size: 14px;
            color: #555;
        }

        .modal-actions {
            text-align: center;
            margin-top: 20px;
        }

        .modal-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, box-shadow 0.3s;
            margin: 5px;
        }

        .modal-actions .save-btn {
            background-color: #f16e26;
            color: #fff;
            border: 1px solid #f16e26;
        }

        .modal-actions .save-btn:hover {
            background-color: #c44100;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-actions .cancel-btn {
            background-color: #fff;
            color: #5e5e5e;
            border: 1px solid #5e5e5e;
        }

        .modal-actions .cancel-btn:hover {
            background-color: #5e5e5e;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .table-container {
            margin-top: 50px;
            width: 90%;
            max-width: 1000px;
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
    </style>
</head>

<body>
    <div class="sidebar">
        <img src="resources/logo.png" alt="Logo" class="logo">

        <a href="Admin_dashboard.php">
            <div class="nav-item" data-tooltip="Dashboard">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/dashboard-layout.png" alt="Dashboard" />
            </div>
        </a>

        <a href="HrAccount.php">
            <div class="nav-item active" data-tooltip="Add HR Account">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/C44100/manager.png" alt="HR" />
            </div>
        </a>
        <a href="Applicant.php">
            <div class="nav-item" data-tooltip="Applicant">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/open-resume.png" alt="Applicant" />
            </div>
        </a>
        <a href="Employee.php">
            <div class="nav-item" data-tooltip="Add Employee">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=19943&format=png&color=000000" alt="Applicant"/>
            </div>
        </a>
        <a href="AuditTrail.php">
            <div class="nav-item" data-tooltip="Audit Trail">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=o3t2bZRRLDfd&format=png&color=000000" alt="Audit" />
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

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-header">
                <button class="add-btn" onclick="openAddModal()">Add</button>
                <div class="search-bar">
                    <input type="text" placeholder="Search...">
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Age</th>
                        <th>Password</th>
                        <th>Profile Picture</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data for each row
                        while ($row = $result->fetch_assoc()) {
                            $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
                            $email = htmlspecialchars($row['email']);
                            $age = htmlspecialchars($row['age']);
                            $password = htmlspecialchars($row['password']); // Display masked password
                            $profilePicture = !empty($row['profile_picture']) ? htmlspecialchars($row['profile_picture']) : 'resources/default profile.png';

                            // Ensure profile picture path is correct
                            $profilePicturePath = 'Partials/' . $profilePicture;

                            echo "<tr>
                        <td>$fullName</td>
                        <td>$email</td>
                        <td>$age</td>
                        <td>••••••••</td> <!-- Masked password for security reasons -->
                        <td><img class='profile-img' src='$profilePicturePath' alt='Profile Image'></td>
                        <td class='actions'>
                            <button class='edit-btn' onclick='openEditModal(" . $row['id'] . ")'>Edit</button>
<form method='post' action='Partials/delete.php' style='display:inline;'>
                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                <button type='submit' class='delete-btn'>Delete</button>
                            </form>                        </td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No records found</td></tr>";
                    }

                    // Close the database connection
                    $conn->close();
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

    <!-- Add Modal -->
    <div id="addModal" class="form-modal">
        <div class="form-modal-content">
            <h2>Add New HR</h2>
            <form action="Partials/add_HR.php" method="post" enctype="multipart/form-data">
                <!-- Form fields for adding new employee -->
                <div class="form-group">
                    <label for="addFirstName">First Name</label>
                    <input type="text" id="addFirstName" name="firstName" required>
                </div>
                <div class="form-group">
                    <label for="addMiddleName">Middle Name</label>
                    <input type="text" id="addMiddleName" name="middleName">
                </div>
                <div class="form-group">
                    <label for="addLastName">Last Name</label>
                    <input type="text" id="addLastName" name="lastName" required>
                </div>
                <div class="form-group">
                    <label for="addEmail">Email Address</label>
                    <input type="email" id="addEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="addAge">Age</label>
                    <input type="number" id="addAge" name="age" required>
                </div>
                <div class="form-group">
                    <label for="addPassword">Password</label>
                    <input type="password" id="addPassword" name="password" required>
                </div>
                <div class="form-group">
                    <label class="file-label" for="addProfilePicture">Profile Picture</label>
                    <input type="file" id="addProfilePicture" name="profilePicture">
                </div>
                <div class="modal-actions">
                    <button type="submit" class="save-btn">Save</button>
                    <button type="button" class="cancel-btn" onclick="closeAddModal()">Cancel</button>
                </div>
            </form>

        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="form-modal">
        <div class="form-modal-content">
            <h2>Edit HR</h2>
            <form id="editEmployeeForm" action="Partials/update_HR.php" method="post" enctype="multipart/form-data">
                <input type="hidden" id="editEmployeeId" name="employeeId">

                <!-- Form fields for editing employee data -->
                <div class="form-group">
                    <label for="editFirstName">First Name</label>
                    <input type="text" id="editFirstName" name="firstName" required>
                </div>
                <div class="form-group">
                    <label for="editMiddleName">Middle Name</label>
                    <input type="text" id="editMiddleName" name="middleName">
                </div>
                <div class="form-group">
                    <label for="editLastName">Last Name</label>
                    <input type="text" id="editLastName" name="lastName" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email Address</label>
                    <input type="email" id="editEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="editAge">Age</label>
                    <input type="number" id="editAge" name="age" required>
                </div>
                <div class="form-group">
                    <label for="editPassword">Re Enter Password</label>
                    <input type="password" id="editPassword" name="password" required>
                </div>
                <div class="form-group">
                    <label class="file-label" for="editProfilePicture">Profile Picture</label>
                    <input type="file" id="editProfilePicture" name="profilePicture">
                </div>
                <div class="modal-actions">
                    <button type="submit" class="save-btn">Save</button>
                    <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
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
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function openEditModal() {
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
    <script>
        function openEditModal(id) {
            fetch('Partials/get_HR.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editEmployeeId').value = id;
                    document.getElementById('editFirstName').value = data.first_name;
                    document.getElementById('editMiddleName').value = data.middle_name;
                    document.getElementById('editLastName').value = data.last_name;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editAge').value = data.age;
                    document.getElementById('editPassword').value = ''; // Leave empty
                    document.getElementById('editModal').style.display = 'block';
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>

</body>

</html>