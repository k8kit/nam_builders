<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$response = ['success' => false, 'message' => ''];

try {
    $client_id = isset($_POST['client_id']) && !empty($_POST['client_id']) ? intval($_POST['client_id']) : null;
    $client_name = sanitize($_POST['client_name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $sort_order = intval($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $image_path = null;

    if (empty($client_name)) {
        throw new Exception('Client name is required.');
    }

    // Handle image upload
    if (isset($_FILES['client_image']) && $_FILES['client_image']['size'] > 0) {
        $upload_result = uploadFile($_FILES['client_image'], UPLOADS_PATH . 'clients/');
        if (!$upload_result['success']) {
            throw new Exception($upload_result['error']);
        }
        $image_path = 'clients/' . $upload_result['filename'];
    }

    if ($client_id) {
        // Update existing client
        // Get old image path for potential deletion
        $old_client = getClientById($conn, $client_id);
        
        // If new image uploaded, delete old one
        if ($image_path && $old_client['image_path']) {
            deleteFile(UPLOADS_PATH . $old_client['image_path']);
        }
        
        // Use old image if no new one provided
        if (!$image_path) {
            $image_path = $old_client['image_path'];
        }

        $query = "UPDATE clients SET client_name = ?, description = ?, image_path = ?, sort_order = ?, is_active = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssiii", $client_name, $description, $image_path, $sort_order, $is_active, $client_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Client updated successfully.';
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    } else {
        // Insert new client
        if (!$image_path) {
            throw new Exception('Client image is required.');
        }

        $query = "INSERT INTO clients (client_name, description, image_path, sort_order, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssii", $client_name, $description, $image_path, $sort_order, $is_active);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Client added successfully.';
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
