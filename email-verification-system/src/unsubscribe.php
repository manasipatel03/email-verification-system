<?php
require_once 'functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unsubscribe_email'])) {
        $email = filter_input(INPUT_POST, 'unsubscribe_email', FILTER_SANITIZE_EMAIL);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $_SESSION['unsubscribe_code'] = $code;
            $_SESSION['email_to_unsubscribe'] = $email;
            sendVerificationEmail($email, $code);
            $message = "Verification code sent to your email.";
        } else {
            $error = "Invalid email address.";
        }
    } elseif (isset($_POST['unsubscribe_verification_code'])) {
        $user_code = filter_input(INPUT_POST, 'unsubscribe_verification_code', FILTER_SANITIZE_STRING);
        if ($user_code === $_SESSION['unsubscribe_code']) {
            unsubscribeEmail($_SESSION['email_to_unsubscribe']);
            $message = "You have been unsubscribed successfully.";
            unset($_SESSION['unsubscribe_code']);
            unset($_SESSION['email_to_unsubscribe']);
        } else {
            $error = "Invalid verification code.";
        }
    }
}

$prefill_email = isset($_GET['email']) ? filter_var($_GET['email'], FILTER_SANITIZE_EMAIL) : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe from GitHub Timeline</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        input[type="email"], input[type="text"] { padding: 8px; width: 100%; }
        button { padding: 10px 15px; background: #dc3545; color: white; border: none; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Unsubscribe from GitHub Timeline Updates</h1>
    
    <?php if ($message): ?>
        <p class="success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <input type="email" name="unsubscribe_email" required placeholder="Enter your email" value="<?= htmlspecialchars($prefill_email) ?>">
        </div>
        <button id="submit_unsubscribe">Request Unsubscribe</button>
    </form>
    
    <?php if (isset($_SESSION['unsubscribe_code'])): ?>
    <hr>
    <h2>Confirm Unsubscription</h2>
    <form method="POST">
        <div class="form-group">
            <input type="text" name="unsubscribe_verification_code" maxlength="6" required placeholder="Enter verification code">
        </div>
        <button id="verify-unsubscribe">Confirm Unsubscribe</button>
    </form>
    <?php endif; ?>
</body>
</html>