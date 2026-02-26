<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$response = ['success' => false, 'message' => ''];

try {
    $service_id = isset($_POST['service_id']) && !empty($_POST['service_id']) ? intval($_POST['service_id']) : null;
    $service_name = sanitize($_POST['service_name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $sort_order = intval($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $image_path = null;

    if (empty($service_name)) {
        throw new Exception('Service name is required.');
    }

    if (empty($description)) {
        throw new Exception('Service description is required.');
    }

    // Handle image upload
    if (isset($_FILES['service_image']) && $_FILES['service_image']['size'] > 0) {
        $upload_result = uploadFile($_FILES['service_image'], UPLOADS_PATH . 'services/');
        if (!$upload_result['success']) {
            throw new Exception($upload_result['error']);
        }
        $image_path = 'services/' . $upload_result['filename'];
    }

    if ($service_id) {
        // Update existing service
        $old_service = getServiceById($conn, $service_id);
        
        // If new image uploaded, delete old one
        if ($image_path && $old_service['image_path']) {
            deleteFile(UPLOADS_PATH . $old_service['image_path']);
        }
        
        // Use old image if no new one provided
        if (!$image_path) {
            $image_path = $old_service['image_path'];
        }

        $query = "UPDATE services SET service_name = ?, description = ?, image_path = ?, sort_order = ?, is_active = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssiii", $service_name, $description, $image_path, $sort_order, $is_active, $service_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Service updated successfully.';
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    } else {
        // Insert new service
        $query = "INSERT INTO services (service_name, description, image_path, sort_order, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssii", $service_name, $description, $image_path, $sort_order, $is_active);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Service added successfully.';
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
