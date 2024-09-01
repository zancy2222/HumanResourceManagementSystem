<?php
// Display any message that was set in the session
session_start();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | The Minds That Matter School HRMS</title>
    <link rel="stylesheet" href="css/register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;700&family=Shadows+Into+Light&display=swap" rel="stylesheet">
    <style>
        .password-requirements {
            display: none;
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }
        .password-requirements p {
            margin: 5px 0;
            font-size: 14px;
        }
        .password-requirements p.valid {
            color: green;
        }
        .password-requirements p.invalid {
            color: red;
        }
        .upload-field {
            position: relative;
        }
        .clear-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            display: none;
        }
        .cv-filename,
        .profile-filename {
            padding-right: 30px; /* Add space for the clear icon */
        }

        .message {
    padding: 10px;
    margin: 10px 0;
    background-color: #d4edda; /* Light green background for success */
    color: #155724; /* Dark green text for success */
    border: 1px solid #c3e6cb; /* Light green border */
    border-radius: 5px;
    text-align: center;
}

    </style>
</head>
<body>
    <form class="form" action="Partials/register_process.php" method="post" enctype="multipart/form-data">
        <div class="header">
            <img src="resources/logo.png" alt="Logo">
            <h1>THE MINDS THAT MATTER SCHOOL</h1>
            <p class="login-text">Register</p>
            <p class="subtext">Apply Online Now</p>
        </div>
            <!-- Success or error message -->
            <?php if ($message): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <div class="name-fields">
            <div class="input-field">
                <input
                    required=""
                    autocomplete="off"
                    type="text"
                    name="firstname"
                    id="firstname"
                />
                <label for="firstname">First Name</label>
            </div>
            <div class="input-field">
                <input
                    required=""
                    autocomplete="off"
                    type="text"
                    name="middlename"
                    id="middlename"
                />
                <label for="middlename">Middle Name</label>
            </div>
            <div class="input-field">
                <input
                    required=""
                    autocomplete="off"
                    type="text"
                    name="surname"
                    id="surname"
                />
                <label for="surname">Surname</label>
            </div>
        </div>
        <div class="contact-fields">
            <div class="input-field">
                <input
                    required=""
                    autocomplete="off"
                    type="email"
                    name="email"
                    id="email"
                />
                <label for="email">Email</label>
            </div>
            <div class="input-field">
                <input
                    required=""
                    autocomplete="off"
                    type="tel"
                    name="phone"
                    id="phone"
                />
                <label for="phone">Phone Number</label>
            </div>
        </div>

        <div class="password-fields">
            <div class="input-field password-input-field">
                <input
                    required=""
                    autocomplete="off"
                    type="password"
                    name="password"
                    id="password"
                />
                <label for="password" class="password-label">Password</label>
                <img src="resources/icons8-show-password-48.png" alt="Show Password" id="togglePassword" class="toggle-password">
                <div id="password-requirements" class="password-requirements">
                    <p id="min-length" class="invalid">Minimum 8 characters</p>
                    <p id="uppercase" class="invalid">At least 1 capital letter</p>
                    <p id="number" class="invalid">At least 1 number</p>
                    <p id="special-char" class="invalid">At least 1 special character</p>
                </div>
            </div>
            <div class="input-field confirm-password-input-field">
                <input
                    required=""
                    autocomplete="off"
                    type="password"
                    name="confirmpassword"
                    id="confirmpassword"
                />
                <label for="confirmpassword" class="confirm-password-label">Confirm Password</label>
                <img src="resources/icons8-show-password-48.png" alt="Show Password" id="toggleConfirmPassword" class="toggle-password">
            </div>
        </div>

        <div class="dropdown-fields">
            <div class="input-field">
                <select
                    required=""
                    name="subject"
                    id="subject"
                >
                    <option value="" disabled selected hidden></option>
                    <option value="Filipino">Filipino</option>
                    <option value="English">English</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Science">Science</option>
                    <option value="Araling Panlipunan">Araling Panlipunan</option>
                    <option value="Edukasyon sa Pagpapakatao">Edukasyon sa Pagpapakatao</option>
                    <option value="MAPEH">MAPEH</option>
                    <option value="Mother Tongue-Based Multilingual Education (Bicol)">Mother Tongue-Based Multilingual Education (Bicol)</option>
                </select>
                <label for="subject">Majoring Subject</label>
            </div>
            <div class="input-field">
                <select
                    required=""
                    name="experience"
                    id="experience"
                >
                    <option value="" disabled selected hidden></option>
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
                <label for="experience">Years of Experience</label>
            </div>
        </div>

        <div class="upload-field">
            <input type="file" id="cv-upload" name="cv" accept=".pdf,.doc,.docx" hidden>
            <button type="button" class="button" onclick="document.getElementById('cv-upload').click();">
                <svg
                    id="UploadToCloud"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    height="16px"
                    width="16px"
                    class="icon"
                >
                    <path d="M0 0h24v24H0V0z" fill="none"></path>
                    <path
                        class="color000000 svgShape"
                        fill="#000000"
                        d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l4.65-4.65c.2-.2.51-.2.71 0L17 13h-3z"
                    ></path>
                </svg>
                Upload Resume
            </button>
            <input type="text" id="cv-filename" readonly placeholder="No file chosen" class="cv-filename">
            <span class="clear-icon" id="clear-cv">&times;</span>
        </div>

        <div class="upload-field">
            <input type="file" id="profile-upload" name="profile_picture" accept="image/*" hidden>
            <button type="button" class="button" onclick="document.getElementById('profile-upload').click();">
                <svg
                    id="UploadToCloud"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    height="16px"
                    width="16px"
                    class="icon"
                >
                    <path d="M0 0h24v24H0V0z" fill="none"></path>
                    <path
                        class="color000000 svgShape"
                        fill="#000000"
                        d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l4.65-4.65c.2-.2.51-.2.71 0L17 13h-3z"
                    ></path>
                </svg>
                Upload Profile Picture
            </button>
            <input type="text" id="profile-filename" readonly placeholder="No file chosen" class="profile-filename">
            <span class="clear-icon" id="clear-profile">&times;</span>
        </div>

        <button type="submit" class="login-button">Create Account</button>
        <div class="footer">
            <p>Already Registered? <a href="login.php">Log In</a></p>
        </div>
    </form>


    <script>
        const phoneInput = document.getElementById('phone');

phoneInput.addEventListener('input', function () {
    // Remove any non-numeric characters
    this.value = this.value.replace(/\D/g, '');

    // Limit to 11 digits
    if (this.value.length > 11) {
        this.value = this.value.slice(0, 11);
    }
});

        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.src = type === 'password' ? 'resources/icons8-show-password-48.png' : 'resources/icons8-hide-password-32.png';
        });

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPassword = document.getElementById('confirmpassword');

        toggleConfirmPassword.addEventListener('click', function () {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.src = type === 'password' ? 'resources/icons8-show-password-48.png' : 'resources/icons8-hide-password-32.png';
        });

        const cvUpload = document.getElementById('cv-upload');
        const cvFilename = document.getElementById('cv-filename');
        const clearCv = document.getElementById('clear-cv');

        cvUpload.addEventListener('change', function () {
            const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
            cvFilename.value = fileName;
            clearCv.style.display = fileName === 'No file chosen' ? 'none' : 'inline';
        });

        clearCv.addEventListener('click', function () {
            cvUpload.value = '';
            cvFilename.value = 'No file chosen';
            this.style.display = 'none';
        });

        const profileUpload = document.getElementById('profile-upload');
        const profileFilename = document.getElementById('profile-filename');
        const clearProfile = document.getElementById('clear-profile');

        profileUpload.addEventListener('change', function () {
            const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
            profileFilename.value = fileName;
            clearProfile.style.display = fileName === 'No file chosen' ? 'none' : 'inline';
        });

        clearProfile.addEventListener('click', function () {
            profileUpload.value = '';
            profileFilename.value = 'No file chosen';
            this.style.display = 'none';
        });

        const passwordInput = document.getElementById('password');
        const passwordRequirements = document.getElementById('password-requirements');
        const minLength = document.getElementById('min-length');
        const uppercase = document.getElementById('uppercase');
        const number = document.getElementById('number');
        const specialChar = document.getElementById('special-char');

        passwordInput.addEventListener('focus', function () {
            passwordRequirements.style.display = 'block';
        });

        passwordInput.addEventListener('blur', function () {
            passwordRequirements.style.display = 'none';
        });

        passwordInput.addEventListener('input', function () {
            const value = passwordInput.value;
            minLength.classList.toggle('valid', value.length >= 8);
            minLength.classList.toggle('invalid', value.length < 8);

            uppercase.classList.toggle('valid', /[A-Z]/.test(value));
            uppercase.classList.toggle('invalid', !/[A-Z]/.test(value));

            number.classList.toggle('valid', /[0-9]/.test(value));
            number.classList.toggle('invalid', !/[0-9]/.test(value));

            specialChar.classList.toggle('valid', /[!@#$%^&*(),.?":{}|<>]/.test(value));
            specialChar.classList.toggle('invalid', !/[!@#$%^&*(),.?":{}|<>]/.test(value));
        });
 // Confirm password validation
 const form = document.querySelector('.form');

form.addEventListener('submit', function (e) {
    if (password.value !== confirmPassword.value) {
        e.preventDefault();
        alert('Passwords do not match. Please check and try again.');
    }
});
          // Form submission validation
    document.querySelector('.form').addEventListener('submit', function (event) {
        const cvUpload = document.getElementById('cv-upload');
        const profileUpload = document.getElementById('profile-upload');

        if (!cvUpload.files.length || !profileUpload.files.length) {
            alert('Please upload both your resume and profile picture before submitting the form.');
            event.preventDefault(); // Prevent form submission
        }
    });
    </script>
</body>
</html>