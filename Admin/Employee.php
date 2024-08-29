<?php
include 'Partials/db_conn.php'; // Include your database connection file

// Fetch employee data from the database
$query = "SELECT e.employee_id, u.firstname, u.middlename, u.surname, u.email, u.phone, e.hire_date 
          FROM Employee e
          JOIN ArchiveApplicant a ON e.archive_applicant_id = a.id
          JOIN Users u ON a.user_id = u.id";
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
/* Modal background overlay */
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

/* Modal content */
.form-modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #ddd;
    width: 80%;
    max-width: 600px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    font-size: 16px;
}

/* Modal header */
.form-modal-content h2 {
    margin-top: 0;
    font-size: 24px;
    color: #333;
}

/* Form groups */
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group input[type="number"],
.form-group input[type="file"],
.form-group input[type="date"],
.form-group select {
    width: calc(100% - 20px);
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

/* File input specific styling */
.form-group input[type="file"] {
    padding: 5px;
}

/* File label */
.form-group .file-label {
    display: block;
    font-size: 14px;
    color: #555;
}

/* Modal actions */
.modal-actions {
    text-align: center;
    margin-top: 20px;
}

/* Buttons */
.modal-actions button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s, box-shadow 0.3s;
    margin: 5px;
}

/* Save button */
.modal-actions .save-btn {
    background-color: #f16e26;
    color: #fff;
    border: 1px solid #f16e26;
}

.modal-actions .save-btn:hover {
    background-color: #c44100;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Cancel button */
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
            <div class="nav-item" data-tooltip="Add HR Account">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/manager.png" alt="HR" />
            </div>
        </a>
        <a href="Applicant.php">
            <div class="nav-item" data-tooltip="Applicant">
                <img width="96" height="96" src="https://img.icons8.com/fluency-systems-regular/96/open-resume.png" alt="Applicant" />
            </div>
        </a>
        <a href="Employee.php">
            <div class="nav-item active" data-tooltip="Add Employee">
                <img width="96" height="96" src="https://img.icons8.com/?size=100&id=19943&format=png&color=C44100" alt="Add employee"/>
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
            <th>Employee ID</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Surname</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Hire Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['employee_id'] . "</td>";
                echo "<td>" . $row['firstname'] . "</td>";
                echo "<td>" . $row['middlename'] . "</td>";
                echo "<td>" . $row['surname'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['phone'] . "</td>";
                echo "<td>" . $row['hire_date'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No employees found.</td></tr>";
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
<!-- Add Modal -->
<div id="addModal" class="form-modal">
    <div class="form-modal-content">
        <h2>Add New Employee</h2>
        <form action="Partials/add_Employee.php" method="post" enctype="multipart/form-data">
            <!-- User Details -->
            <h3>User Details</h3>
            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="middlename">Middle Name:</label>
                <input type="text" id="middlename" name="middlename" required>
            </div>
            <div class="form-group">
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="subject">Majoring Subject:</label>
                <select id="subject" name="subject" required>
                    <option value="" disabled selected hidden>Select Subject</option>
                    <option value="Filipino">Filipino</option>
                    <option value="English">English</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Science">Science</option>
                    <option value="Araling Panlipunan">Araling Panlipunan</option>
                    <option value="Edukasyon sa Pagpapakatao">Edukasyon sa Pagpapakatao</option>
                    <option value="MAPEH">MAPEH</option>
                    <option value="Mother Tongue-Based Multilingual Education (Bicol)">Mother Tongue-Based Multilingual Education (Bicol)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="experience">Years of Experience:</label>
                <select id="experience" name="experience" required>
                    <option value="" disabled selected hidden>Select Experience</option>
                    <option value="fresh graduate">Fresh Graduate</option>
                    <option value="1">1 Year</option>
                    <option value="2">2 Years</option>
                    <option value="3">3 Years</option>
                    <option value="4">4 Years</option>
                    <option value="5">5 Years</option>
                    <option value="6">6 Years</option>
                    <option value="7">7 Years</option>
                    <option value="8">8 Years</option>
                    <option value="9">9 Years</option>
                    <option value="10">10 Years</option>
                    <option value="11">11 Years</option>
                    <option value="12">12 Years</option>
                    <option value="13">13 Years</option>
                    <option value="14">14 Years</option>
                    <option value="15">15 Years</option>
                    <option value="16">16 Years</option>
                    <option value="17">17 Years</option>
                    <option value="18">18 Years</option>
                    <option value="19">19 Years</option>
                    <option value="20+">20+ Years</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cv_filename">CV Filename:</label>
                <input type="file" id="cv_filename" name="cv_filename">
            </div>
            <div class="form-group">
                <label for="profile_filename">Profile Picture:</label>
                <input type="file" id="profile_filename" name="profile_filename">
            </div>

            <!-- Archive Applicant Details -->
            <h3>STATUS</h3>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="" disabled selected hidden>Select Status</option>
                    <option value="Hire">Hire</option>
                </select>
            </div>

            <!-- Employee Details -->
            <h3>Employee Details</h3>
            <div class="form-group">
                <label for="employee_id">Employee ID:</label>
                <input type="text" id="employee_id" name="employee_id" required>
            </div>
            <div class="form-group">
                <label for="hire_date">Hire Date:</label>
                <input type="date" id="hire_date" name="hire_date" required>
            </div>

            <div class="modal-actions">
                <button type="submit" name="submit" class="save-btn">Add Employee</button>
                <button type="button" class="cancel-btn" onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>


    <!-- Edit Modal -->
    <div id="editModal" class="form-modal">
        <div class="form-modal-content">
            <h2>Edit HR</h2>
            <form id="editEmployeeForm" action="Partials/update_Employee.php" method="post" enctype="multipart/form-data">

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
   

</body>

</html>