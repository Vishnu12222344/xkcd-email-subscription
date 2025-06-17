<?php
require 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    $code = $_POST["verification_code"] ?? '';

    if ($email && !$code) {
        $code = generateVerificationCode();
        file_put_contents(__DIR__ . "/codes/" . md5($email) . ".txt", $code);
        sendVerificationEmail($email, $code);
        echo "Verification code sent to $email.";
    } elseif ($email && $code) {
        if (verifyCode($email, $code)) {
            registerEmail($email);
            echo "Email verified and registered.";
        } else {
            echo "Invalid verification code.";
        }
    }
}
?>

<form method="POST">
    <input type="email" name="email" required>
    <button id="submit-email">Submit</button>
</form>

<form method="POST">
    <input type="text" name="verification_code" maxlength="6" required>
    <input type="hidden" name="email" value="<?= $_POST['email'] ?? '' ?>">
    <button id="submit-verification">Verify</button>
</form>
