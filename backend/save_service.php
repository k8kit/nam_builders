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

    if (empty($service_name)) {
        throw new Exception('Service name is required.');
    }

    if (empty($description)) {
        throw new Exception('Service description is required.');
    }

    // Collect uploaded images
    $uploaded_images = [];
    if (isset($_FILES['service_images']) && is_array($_FILES['service_images']['name'])) {
        $file_count = count($_FILES['service_images']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['service_images']['size'][$i] > 0 && $_FILES['service_images']['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name'     => $_FILES['service_images']['name'][$i],
                    'type'     => $_FILES['service_images']['type'][$i],
                    'tmp_name' => $_FILES['service_images']['tmp_name'][$i],
                    'error'    => $_FILES['service_images']['error'][$i],
                    'size'     => $_FILES['service_images']['size'][$i],
                ];
                $upload_result = uploadFile($file, UPLOADS_PATH . 'services/');
                if (!$upload_result['success']) {
                    throw new Exception('Image upload failed: ' . $upload_result['error']);
                }
                $uploaded_images[] = 'services/' . $upload_result['filename'];
            }
        }
    }

    if ($service_id) {
        // Update existing service
        $query = "UPDATE services SET service_name = ?, description = ?, sort_order = ?, is_active = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssiii", $service_name, $description, $sort_order, $is_active, $service_id);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();

        // If new images uploaded, append them
        if (!empty($uploaded_images)) {
            $img_stmt = $conn->prepare("INSERT INTO service_images (service_id, image_path, sort_order) VALUES (?, ?, ?)");
            // Get current max sort order for this service
            $max_result = $conn->query("SELECT COALESCE(MAX(sort_order), -1) as max_order FROM service_images WHERE service_id = $service_id");
            $max_row = $max_result->fetch_assoc();
            $img_order = $max_row['max_order'] + 1;

            foreach ($uploaded_images as $img_path) {
                $img_stmt->bind_param("isi", $service_id, $img_path, $img_order);
                $img_stmt->execute();
                $img_order++;
            }
            $img_stmt->close();
        }

        $response['success'] = true;
        $response['message'] = 'Service updated successfully.';
    } else {
        // Insert new service
        $query = "INSERT INTO services (service_name, description, sort_order, is_active) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssii", $service_name, $description, $sort_order, $is_active);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $new_service_id = $conn->insert_id;
        $stmt->close();

        // Insert images into service_images table
        if (!empty($uploaded_images)) {
            $img_stmt = $conn->prepare("INSERT INTO service_images (service_id, image_path, sort_order) VALUES (?, ?, ?)");
            $img_order = 0;
            foreach ($uploaded_images as $img_path) {
                $img_stmt->bind_param("isi", $new_service_id, $img_path, $img_order);
                $img_stmt->execute();
                $img_order++;
            }
            $img_stmt->close();
        }

        $response['success'] = true;
        $response['message'] = 'Service added successfully.';
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>