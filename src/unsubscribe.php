<?php
require 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["unsubscribe_email"] ?? '';
    $code = $_POST["verification_code"] ?? '';

    if ($email && !$code) {
        $code = generateVerificationCode();
        file_put_contents(__DIR__ . "/codes/" . md5($email) . ".txt", $code);
        $subject = "Confirm Un-subscription";
        $message = "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>";
        $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: no-reply@example.com\r\n";
        mail($email, $subject, $message, $headers);
        echo "Unsubscribe confirmation code sent.";
    } elseif ($email && $code) {
        if (verifyCode($email, $code)) {
            unsubscribeEmail($email);
            echo "You have been unsubscribed.";
        } else {
            echo "Invalid code.";
        }
    }
}
?>

<form method="POST">
    <input type="email" name="unsubscribe_email" required>
    <button id="submit-unsubscribe">Unsubscribe</button>
</form>

<form method="POST">
    <input type="text" name="verification_code" maxlength="6" required>
    <input type="hidden" name="unsubscribe_email" value="<?= $_POST['unsubscribe_email'] ?? '' ?>">
    <button id="submit-verification">Verify</button>
</form>
