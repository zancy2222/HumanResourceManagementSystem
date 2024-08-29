<!-- reset_password.php -->
<?php
include 'db_conn.php';
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid and not expired
    $stmt = $conn->prepare("SELECT email FROM Users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Token is valid
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();
    } else {
        // Token is invalid or expired
        $_SESSION['message'] = ['class' => 'error', 'text' => 'Invalid or expired reset token.'];
        header("Location: forgot_password.php");
        exit();
    }
} else {
    $_SESSION['message'] = ['class' => 'error', 'text' => 'No reset token provided.'];
    header("Location: forgot_password.php");
    exit();
}
?>
<style>
    /* Base styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f2f2f2;
    color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.form {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    padding: 20px;
    box-sizing: border-box;
}

.header {
    text-align: center;
    margin-bottom: 20px;
}

.header img {
    width: 80px;
    height: auto;
    margin-bottom: 10px;
}

.header h1 {
    font-size: 24px;
    color: #333;
    margin: 0;
}

.forgot-password-text {
    font-size: 18px;
    color: #ff4d6d;
    margin: 5px 0;
}

.subtext {
    font-size: 14px;
    color: #666;
}

.message {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.input-field {
    position: relative;
    margin-bottom: 20px;
}

.input-field input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    box-sizing: border-box;
}

.input-field label {
    position: absolute;
    top: 10px;
    left: 10px;
    font-size: 14px;
    color: #888;
    transition: 0.2s ease;
    pointer-events: none;
}

.input-field input:focus + label,
.input-field input:not(:placeholder-shown) + label {
    top: -10px;
    left: 5px;
    font-size: 12px;
    color: #ff4d6d;
}

.retrieve-button {
    background-color: #ff4d6d;
    color: #fff;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s, box-shadow 0.3s;
    width: 100%;
}

.retrieve-button:hover {
    background-color: #e04361;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.footer {
    text-align: center;
    margin-top: 20px;
}

.footer a {
    color: #ff4d6d;
    text-decoration: none;
}

.footer a:hover {
    text-decoration: underline;
}

</style>
<form class="form" action="process_reset_password.php" method="post">
    <div class="header">
        <img src="resources/logo.png" alt="Logo">
        <h1>THE MINDS THAT MATTER SCHOOL</h1>
        <p class="forgot-password-text">Reset Password</p>
        <p class="subtext">Set your new password</p>
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

    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />

    <div class="input-field">
        <input required type="password" name="password" id="password" />
        <label for="password">New Password</label>
    </div>

    <div class="input-field">
        <input required type="password" name="confirm_password" id="confirm_password" />
        <label for="confirm_password">Confirm Password</label>
    </div>
    
    <button type="submit" class="retrieve-button">Reset Password</button>
    <div class="footer">
        <p>Already Changed? <a href="login.php">Log In</a></p>
    </div>
</form>
