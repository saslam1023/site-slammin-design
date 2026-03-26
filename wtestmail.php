<?php
$to = "saslam1023@icloud.com";
$subject = "Test Mail";
$message = "Hello! This is a test.";
$headers = "From: admin@headorn.com\r\n";
if(mail($to, $subject, $message, $headers)){
    echo "Mail sent!";
} else {
    echo "Mail failed!";
}
?>
