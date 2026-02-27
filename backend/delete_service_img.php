<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    jsonResponse(false, 'Invalid image ID');
}

// Get image info
$result = $conn->query("SELECT * FROM service_images WHERE id = $id");
$image = $result ? $result->fetch_assoc() : null;

if (!$image) {
    jsonResponse(false, 'Image not found');
}

// Delete file
deleteFile(UPLOADS_PATH . $image['image_path']);

// Delete from DB
$stmt = $conn->prepare("DELETE FROM service_images WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    jsonResponse(true, 'Image deleted');
} else {
    jsonResponse(false, 'Failed to delete image');
}
$stmt->close();
?>