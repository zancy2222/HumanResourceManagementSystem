CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255) NOT NULL,
    middlename VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL, 
    subject VARCHAR(255) NOT NULL,
    experience VARCHAR(255) NOT NULL,
    cv_filename VARCHAR(255),
    profile_filename VARCHAR(255),
    activation_token VARCHAR(255),
    activated TINYINT(1) DEFAULT 0, -- 0 means not activated, 1 means activated
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE Users
ADD COLUMN reset_token VARCHAR(255),
ADD COLUMN reset_token_expiry DATETIME;

CREATE TABLE Employee (
    employee_id VARCHAR(7) PRIMARY KEY, -- Changed to VARCHAR to store generated ID
    archive_applicant_id INT NOT NULL,
    hire_date DATE,
    FOREIGN KEY (archive_applicant_id) REFERENCES ArchiveApplicant(id) ON DELETE CASCADE
);
-- Create ArchiveApplicant table
CREATE TABLE ArchiveApplicant (
    id INT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('Account Creation', 'Interview', 'Demo Teaching', 'Hire') DEFAULT 'Account Creation',
    account_creation_completed BOOLEAN DEFAULT 0,
    interview_completed BOOLEAN DEFAULT 0,
    demo_teaching_completed BOOLEAN DEFAULT 0,
    hire_completed BOOLEAN DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

CREATE TABLE Applicant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('Account Creation', 'Interview', 'Demo Teaching', 'Hire') DEFAULT 'Account Creation',
    account_creation_completed BOOLEAN DEFAULT 0,
    interview_completed BOOLEAN DEFAULT 0,
    demo_teaching_completed BOOLEAN DEFAULT 0,
    hire_completed BOOLEAN DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);
CREATE TABLE Attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id)
);
CREATE TABLE hr_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    middle_name VARCHAR(255),
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    age INT NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE hr_members 
ADD COLUMN reset_token VARCHAR(255),
ADD COLUMN reset_token_expiry DATETIME;

CREATE TABLE FailedApplicant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('Failed') DEFAULT 'Failed',
    failure_reason VARCHAR(255) DEFAULT 'Not Selected',
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

CREATE TABLE AttachedfileEmployee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(7) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id) ON DELETE CASCADE
);

-- Create the leave_requests table
CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type ENUM('sick', 'vacation', 'emergency', 'other') NOT NULL,
    leave_reason TEXT NOT NULL,
    date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employee(id)
);
ALTER TABLE leave_requests
ADD COLUMN leave_date DATE NOT NULL;

CREATE TABLE Evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(7) NOT NULL,
    criteria ENUM('Professionalism', 'Use of Technology', 'Feedback and Assessment', 'Classroom Management', 'Subject Knowledge', 'Teaching Methods', 'Student Engagement', 'Communication Skills') NOT NULL,
    rating TINYINT(1) NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id) ON DELETE CASCADE
);
CREATE TABLE BranchAssignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    branch_name VARCHAR(255) NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES Users(id) ON DELETE CASCADE
);
CREATE TABLE Resignations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(7) NOT NULL,
    resignation_date DATE NOT NULL,
    reason TEXT NOT NULL,
    date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id) ON DELETE CASCADE
);
