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
        <p>Already Changed? <a href="/login.html">Log In</a></p>
    </div>
</form>
