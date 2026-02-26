<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    setAlert('Invalid service ID', 'danger');
    header('Location: ../admin/dashboard.php?page=services');
    exit();
}

$service = getServiceById($conn, $id);

if (!$service) {
    setAlert('Service not found', 'danger');
    header('Location: ../admin/dashboard.php?page=services');
    exit();
}

// Delete image file
if ($service['image_path']) {
    deleteFile(UPLOADS_PATH . $service['image_path']);
}

// Delete from database
$query = "DELETE FROM services WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    setAlert('Service deleted successfully', 'success');
} else {
    setAlert('Failed to delete service: ' . $stmt->error, 'danger');
}
$stmt->close();

header('Location: ../admin/dashboard.php?page=services');
exit();
?>
