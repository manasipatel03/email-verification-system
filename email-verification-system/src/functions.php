<?php
session_start();

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_diff($emails, [$email]);
    file_put_contents($file, implode(PHP_EOL, $emails));
}

function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "<html><body><p>Your verification code is: <strong>$code</strong></p></body></html>";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    @mail($email, $subject, $message, $headers);
}

function fetchGitHubTimeline() {
    $url = "https://github.com/timeline";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    curl_close($ch);
    return formatGitHubData($data);
}

function formatGitHubData($data) {
    // Basic HTML formatting
    return "<div class='github-update'>$data</div>";
}

function sendGitHubUpdateToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    
    if (!file_exists($file)) return;
    
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = fetchGitHubTimeline();
    
    foreach ($emails as $email) {
        $subject = "GitHub Timeline Update";
        $unsubscribe_link = "http://" . $_SERVER['HTTP_HOST'] . "/src/unsubscribe.php?email=" . urlencode($email);
        $message = $data . "<p><a href='$unsubscribe_link'>Unsubscribe from future updates</a></p>";
        $headers = "From: no-reply@example.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        @mail($email, $subject, $message, $headers);
    }
}
?>