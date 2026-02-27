<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    jsonResponse(false, 'Invalid service ID');
}

$service = getServiceById($conn, $id);

if ($service) {
    // Get all images for this service
    $img_result = $conn->query("SELECT * FROM service_images WHERE service_id = $id ORDER BY sort_order ASC");
    $service['images'] = $img_result ? $img_result->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(true, 'Service found', $service);
} else {
    jsonResponse(false, 'Service not found');
}
?>