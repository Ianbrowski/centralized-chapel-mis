<?php
require 'mailer.php'; // Make sure this matches the filename of your mailer code

$test_email = "hydrogen2303@gmail.com"; // Put your own email here to check
$test_name = "Test User";
$subject = "System Test: IFI Cathedral Mailer";
$message = "<h1>nigga!</h1><p>This is a test notification from your Centralized Chapel System.</p>";

echo "Attempting to send email to $test_email...<br>";

if (sendChurchEmail($test_email, $test_name, $subject, $message)) {
    echo "<b style='color:green;'>Success! Check your inbox (and Spam folder).</b>";
} else {
    echo "<b style='color:red;'>Failed! Check your PHPMailer path or App Password.</b>";
}
?>