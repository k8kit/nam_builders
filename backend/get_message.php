<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    jsonResponse(false, 'Invalid message ID');
}

$query = "SELECT * FROM contact_messages WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$message = $result->fetch_assoc();
$stmt->close();

if ($message) {
    // Mark as read
    $update_query = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("i", $id);
    $update_stmt->execute();
    $update_stmt->close();
    
    jsonResponse(true, 'Message found', $message);
} else {
    jsonResponse(false, 'Message not found');
}
?>
