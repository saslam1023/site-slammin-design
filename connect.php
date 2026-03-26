<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- Allowed origins ---
$allowed_origins = [
    'https://saslam1023.github.io',
    'https://headorn.com',
    'https://slammin-design.co.uk'
];

// Detect where request came from
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// If the origin is allowed, send CORS headers
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
}

// Handle preflight (OPTIONS) requests from browsers
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}



// Sanitize user inputs
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Honeypot check
    if (!empty($_POST['honeypot'])) {
        echo json_encode(['success' => false, 'message' => 'Spam detected.']);
        exit();
    }

    // 2. Time-based check (must take more than 5 seconds)
    if (time() - (int)$_POST['start_time'] < 5) {
        echo json_encode(['success' => false, 'message' => 'Spam detected (form submitted too quickly).']);
        exit();
    }

    // 3. Sanitize and validate inputs
    $name = sanitize_input($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $message = sanitize_input($_POST['message']);

    if (!$name || !$email || !$message) {
        echo json_encode(['success' => false, 'message' => 'Invalid input. Please fill out all fields correctly.']);
        exit();
    }

    // 4. Prepare email
    // --- ROUTE EMAIL BASED ON ORIGIN ---
    switch ($origin) {
        case 'https://saslam1023.github.io':
            $to = 'saslam1023@slammin-design.co.uk';
            $site_name = 'Pixel Perfect';
            break;
        case 'https://headorn.com':
            $to = 'accessories@headorn.com';
            $site_name = 'Headorn London';
            break;
        case 'https://slammin-design.co.uk':
            $to = 'saslam1023@icloud.com, saslam1023@slammin-design.co.uk';
            $site_name = 'Slammin Design';
            break;
        default:
            $to = 'studio@slammin-design.co.uk';
            $site_name = 'Default';
            break;
    }

    // --- Build email ---
    $subject = "New Message from $site_name Contact Form";
    $body = "Name: $name\nEmail: $email\nMessage:\n$message";
    $headers = "From: Web Studio <studio@slammin-design.co.uk>\r\n" .
               "Reply-To: $email\r\n" .
               "X-Mailer: PHP/" . phpversion();

    // --- Send the email ---
    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Your message has been sent.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
    }
    exit();
}
?>
