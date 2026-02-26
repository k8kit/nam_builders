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
    jsonResponse(true, 'Service found', $service);
} else {
    jsonResponse(false, 'Service not found');
}
?>
