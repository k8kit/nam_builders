<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$response = ['success' => false, 'message' => ''];

try {
    // Validate required fields
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = sanitize($_POST['phone'] ?? '');
    $service_needed = sanitize($_POST['service_needed'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($full_name)) {
        throw new Exception('Full name is required.');
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Valid email is required.');
    }

    if (empty($message)) {
        throw new Exception('Message is required.');
    }

    // Insert into database
    $query = "INSERT INTO contact_messages (full_name, email, phone, service_needed, message) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("sssss", $full_name, $email, $phone, $service_needed, $message);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Thank you! We have received your message. We will contact you soon.';
        
        // Set session alert
        $_SESSION['alert'] = [
            'message' => $response['message'],
            'type' => 'success'
        ];
    } else {
        throw new Exception($stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    $_SESSION['alert'] = [
        'message' => 'Error: ' . $response['message'],
        'type' => 'danger'
    ];
}

// Redirect back with alert
header('Location: ../index.php#contact');
exit();
?>
