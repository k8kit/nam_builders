<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    setAlert('Invalid message ID', 'danger');
    header('Location: ../admin/dashboard.php?page=messages');
    exit();
}

// Delete from database
$query = "DELETE FROM contact_messages WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    setAlert('Message deleted successfully', 'success');
} else {
    setAlert('Failed to delete message: ' . $stmt->error, 'danger');
}
$stmt->close();

header('Location: ../admin/dashboard.php?page=messages');
exit();
?>
