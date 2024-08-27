USE HRMS;

CREATE TABLE employee (
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
ALTER TABLE employee ADD COLUMN progress_status ENUM('Account Creation', 'Interview', 'Demo Teaching', 'Hire') DEFAULT 'Account Creation';
ALTER TABLE employee
ADD COLUMN account_creation_completed BOOLEAN DEFAULT 0,
ADD COLUMN interview_completed BOOLEAN DEFAULT 0,
ADD COLUMN demo_teaching_completed BOOLEAN DEFAULT 0,
ADD COLUMN hire_completed BOOLEAN DEFAULT 0;

-- Create the hr_members table
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
