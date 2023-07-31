<?php
// Initialize variables
$body = '';
$to = "mikothebasto@gmail.com";

// Remove token from $_POST array
unset($_POST['token']);

// Set sender's email
$from = isset($_POST['email']) ? $_POST['email'] : "";

// Set subject with a default value if not provided
$subject = isset($_POST['subject']) ? $_POST['subject'] : 'Message from Contact Demo';

// Set sender's name if provided
$sender_name = isset($_POST['name']) ? $_POST['name'] : "";

// Build body of the message
foreach ($_POST as $k => $val) {
    $body .= ucfirst($k) . ": " . $val . "\r\n";
}

// Initialize headers
$headers = '';

// Handle attachments
if (!empty($_FILES['attach_file']['name'])) {
    // Generate boundary for multipart message
    $semi_random = md5(time());
    $mime_boundary = "==Multipart_Boundary_x{$semi_random}x";

    // Set headers for multipart attachment
    $headers .= "\nMIME-Version: 1.0\n" .
        "Content-Type: multipart/mixed;\n" .
        " boundary=\"{$mime_boundary}\"";

    // Add multipart message and text/plain content type
    $body .= "This is a multi-part message in MIME format.\n\n" .
        "--{$mime_boundary}\n" .
        "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" .
        $body . "\n\n";

    // Add attachment
    $body .= "--{$mime_boundary}\n";

    // Read attached file
    $file = fopen($_FILES['attach_file']['tmp_name'], 'rb');
    $data = fread($file, filesize($_FILES['attach_file']['tmp_name']));
    fclose($file);

    $data = chunk_split(base64_encode($data));
    $name = $_FILES['attach_file']['name'];

    // Add attachment details
    $body .= " name=\"$name\"\n" .
        "Content-Disposition: attachment;\n" .
        " filename=\"$name\"\n" .
        "Content-Transfer-Encoding: base64\n\n" .
        $data . "\n\n";

    // Close boundary
    $body .= "--{$mime_boundary}\n";
}

// Send email and check if successful
if (mail($to, $subject, $body, $headers)) {
    echo "sent";
} else {
    echo "fail";
}
?>
