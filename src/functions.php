<?php

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string {
    return strval(rand(100000, 999999));
}

/**
 * Send a verification code to an email.
 */
function sendVerificationEmail(string $email, string $code): bool {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= 'From: <no-reply@example.com>' . "\r\n";
    return mail($email, $subject, $message, $headers);
}

/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

    if (!in_array($email, $emails)) {
        return file_put_contents($file, $email . PHP_EOL, FILE_APPEND) !== false;
    }

    return false; // Already registered
}

/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $updated = array_filter($emails, fn($e) => trim($e) !== trim($email));

    return file_put_contents($file, implode(PHP_EOL, $updated) . PHP_EOL) !== false;
}

/**
 * Fetch random XKCD comic and format data as HTML.
 */
function fetchAndFormatXKCDData(): string {
    $random = rand(1, 2800); // XKCD has ~2800 comics, update as needed
    $url = "https://xkcd.com/$random/info.0.json";
    $json = @file_get_contents($url);

    if (!$json) return "<p>Unable to fetch comic.</p>";

    $data = json_decode($json, true);
    if (!$data) return "<p>Error parsing comic data.</p>";

    return "
        <h2>XKCD Comic</h2>
        <img src='{$data['img']}' alt='XKCD Comic'>
        <p><a href='#' id='unsubscribe-button'>Unsubscribe</a></p>
    ";
}

/**
 * Send the formatted XKCD updates to registered emails.
 */
function sendXKCDUpdatesToSubscribers(): void {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

    $content = fetchAndFormatXKCDData();
    $subject = "Your XKCD Comic";

    foreach ($emails as $email) {
        $unsubscribeLink = "http://yourdomain.com/unsubscribe.php?email=" . urlencode($email);
        $message = str_replace('#', $unsubscribeLink, $content);

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: <no-reply@example.com>\r\n";

        mail($email, $subject, $message, $headers);
    }
}
