<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$response = ['success' => false, 'message' => ''];

try {
    // ── 1. Validate OTP first ─────────────────────────────────────────────────
    $submitted_code = trim($_POST['otp_code'] ?? '');
    $otp = $_SESSION['otp_data'] ?? null;

    if (empty($submitted_code)) {
        throw new Exception('Verification code is required.');
    }
    if (!$otp) {
        throw new Exception('No verification session found. Please request a new code.');
    }
    if ($otp['used']) {
        throw new Exception('This verification code has already been used.');
    }
    if (time() > $otp['expires']) {
        unset($_SESSION['otp_data']);
        throw new Exception('Verification code has expired. Please request a new one.');
    }
    if (!hash_equals((string)$otp['code'], (string)$submitted_code)) {
        throw new Exception('Invalid verification code. Please try again.');
    }

    // Mark OTP as used immediately to prevent replay attacks
    $_SESSION['otp_data']['used'] = true;

    // ── 2. Validate form fields ───────────────────────────────────────────────
    $full_name      = sanitize($_POST['full_name'] ?? '');
    $email          = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone          = sanitize($_POST['phone'] ?? '');
    $service_needed = sanitize($_POST['service_needed'] ?? '');
    $message        = sanitize($_POST['message'] ?? '');

    if (empty($full_name)) {
        throw new Exception('Full name is required.');
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Valid email is required.');
    }
    // Ensure the email used matches the one that was verified
    if (strtolower($email) !== strtolower($otp['email'])) {
        throw new Exception('The email address does not match the verified email.');
    }
    if (empty($message)) {
        throw new Exception('Message is required.');
    }

    // ── 3. Save to database ───────────────────────────────────────────────────
    $query = "INSERT INTO contact_messages (full_name, email, phone, service_needed, message) VALUES (?, ?, ?, ?, ?)";
    $stmt  = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception($conn->error);
    }
    $stmt->bind_param("sssss", $full_name, $email, $phone, $service_needed, $message);

    if ($stmt->execute()) {
        // Clear OTP session after successful submission
        unset($_SESSION['otp_data']);

        $_SESSION['alert'] = [
            'message' => 'Thank you, ' . htmlspecialchars($full_name) . '! Your message has been received. We will contact you soon.',
            'type'    => 'success',
        ];
    } else {
        throw new Exception($stmt->error);
    }
    $stmt->close();

} catch (Exception $e) {
    $_SESSION['alert'] = [
        'message' => 'Error: ' . $e->getMessage(),
        'type'    => 'danger',
    ];
}

header('Location: ../index.php#contact');
exit();