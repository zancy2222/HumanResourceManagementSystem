<?php
session_start(); // Ensure this is at the top
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | The Minds That Matter School HRMS</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;700&family=Shadows+Into+Light&display=swap" rel="stylesheet">
    <style>
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
</head>
<body>
    <form class="form" action="Partials/login_process.php" method="post">
        <div class="header">
            <img src="resources/logo.png" alt="Logo">
            <h1>THE MINDS THAT MATTER SCHOOL</h1>
            <p class="login-text">Login</p>
            <p class="subtext">Use this form to log into your account</p>
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
            <input
                required=""
                autocomplete="off"
                type="text"
                name="email"
                id="email"
            />
            <label for="email">Email</label>
        </div>
        <div class="input-field">
            <input
                required=""
                autocomplete="off"
                type="password"
                name="password"
                id="password"
            />
            <label for="password">Password</label>
            <img src="resources/icons8-show-password-48.png" alt="Show Password" id="togglePassword" class="toggle-password">
        </div>
        <button type="submit" class="login-button">Login</button>
        <div class="footer">
            <a href="forgetpass.php" class="forgot-password">Forgot Password?</a>
            <p>Not registered yet? <a href="register.php">Create an Account</a></p>
        </div>
    </form>

    <script>
        // Show password toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.src = type === 'password' ? 'resources/icons8-show-password-48.png' : 'resources/icons8-hide-password-32.png';
        });

    </script>
</body>
</html>
