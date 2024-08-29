<?php
session_start(); // Ensure this is at the top
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | The Minds That Matter School HRMS</title>
    <link rel="stylesheet" href="css/forgotpassword.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        .toggle-password {
            position: absolute;
            right: 140px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #f16e26;
            cursor: pointer;
            font-size: 0.9rem;
            padding: 0;
        }
        .message {
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    text-align: center;
    width: 76%;
    color: #fff;
}

.message.success {
    background-color: #d4edda; /* Light green background for success */
    color: #155724; /* Dark green text for success */
    border: 1px solid #c3e6cb; /* Light green border */
}

.message.warning {
    background-color: #fff3cd; /* Light yellow background for warning */
    color: #856404; /* Dark yellow text for warning */
    border: 1px solid #ffeeba; /* Light yellow border */
}

.message.error {
    background-color: #f8d7da; /* Light red background for error */
    color: #721c24; /* Dark red text for error */
    border: 1px solid #f5c6cb; /* Light red border */
}

    </style>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;700&family=Shadows+Into+Light&display=swap" rel="stylesheet">
</head>
<body>


<!-- forgot_password.php -->
<form class="form" action="Partials/process_forgot_password.php" method="post">
    <div class="header">
        <img src="resources/logo.png" alt="Logo">
        <h1>THE MINDS THAT MATTER SCHOOL</h1>
        <p class="forgot-password-text">Forgot Password</p>
        <p class="subtext">Retrieve your account</p>
    </div>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?php echo htmlspecialchars($_SESSION['message']['class']); ?>">
            <?php
            echo htmlspecialchars($_SESSION['message']['text']);
            unset($_SESSION['message']); // Clear the message after displaying it
            ?>
        </div>
    <?php endif; ?>

    <div class="input-field">
        <input required autocomplete="off" type="email" name="email" id="email" />
        <label for="email">Email</label>
    </div>
    
    <button type="submit" class="retrieve-button">Retrieve</button>
    <div class="footer">
        <p>Already Changed? <a href="login.php">Log In</a></p>
    </div>
</form>


    <script>
        function togglePasswordVisibility(inputId) {
            const inputField = document.getElementById(inputId);
            const button = inputField.nextElementSibling;
        
            if (inputField.type === 'password') {
                inputField.type = 'text';
                button.textContent = 'Hide';
            } else {
                inputField.type = 'password';
                button.textContent = 'Show';
            }
        }
        
    </script>
</body>
</html>
