<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: https://saslam1023.github.io");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Function to sanitize and validate user inputs
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Process form submission when it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Honeypot check: If the honeypot field is filled, it's likely a bot
    if (!empty($_POST['honeypot'])) {
        echo "Spam detected.";
        exit();
    }

    // 2. Time-based check: The form must take more than 5 seconds to be submitted
	if (time() - (int)$_POST['start_time'] < 5) {
		echo "Spam detected (form submitted too quickly).";
		exit();
	}


    // 3. Sanitize and validate inputs
    $name = sanitize_input($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $message = sanitize_input($_POST['message']);

    if (!$name || !$email || !$message) {
        echo "Invalid input. Please fill out all fields correctly.";
        exit();
    }

    // 4. Attempt to send the email
    $to = "studio@slammin-design.co.uk";
    $subject = "Pixel Perfect Connect";
    $body = "Name: $name\nEmail: $email\nMessage: $message";
    $headers = "From: $email";

    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Your message has been sent.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email. Try again later.']);
    }
    exit();
}



?>
