<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

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

// Delete all associated images from disk
$img_result = $conn->query("SELECT image_path FROM service_images WHERE service_id = $id");
if ($img_result) {
    while ($img = $img_result->fetch_assoc()) {
        deleteFile(UPLOADS_PATH . $img['image_path']);
    }
}

// Delete from service_images (FK cascade would handle this too, but be explicit)
$conn->query("DELETE FROM service_images WHERE service_id = $id");

// Also delete legacy single image_path if exists
if (!empty($service['image_path'])) {
    deleteFile(UPLOADS_PATH . $service['image_path']);
}

// Delete from services
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