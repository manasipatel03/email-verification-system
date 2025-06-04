<?php
require_once 'functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $_SESSION['verification_code'] = $code;
            $_SESSION['email_to_verify'] = $email;
            sendVerificationEmail($email, $code);
            $message = "Verification code sent to your email.";
        } else {
            $error = "Invalid email address.";
        }
    } elseif (isset($_POST['verification_code'])) {
        $user_code = filter_input(INPUT_POST, 'verification_code', FILTER_SANITIZE_STRING);
        if ($user_code === $_SESSION['verification_code']) {
            registerEmail($_SESSION['email_to_verify']);
            $message = "Email verified and registered successfully!";
            unset($_SESSION['verification_code']);
            unset($_SESSION['email_to_verify']);
        } else {
            $error = "Invalid verification code.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>GitHub Timeline Subscription</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        input[type="email"], input[type="text"] { padding: 8px; width: 100%; }
        button { padding: 10px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Subscribe to GitHub Timeline Updates</h1>
    
    <?php if ($message): ?>
        <p class="success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <input type="email" name="email" required placeholder="Enter your email">
        </div>
        <button id="submit-email">Submit Email</button>
    </form>
    
    <?php if (isset($_SESSION['verification_code'])): ?>
    <hr>
    <h2>Verify Your Email</h2>
    <form method="POST">
        <div class="form-group">
            <input type="text" name="verification_code" maxlength="6" required placeholder="Enter verification code">
        </div>
        <button id="submit_verification">Verify Code</button>
    </form>
    <?php endif; ?>
</body>
</html>