<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    setAlert('Invalid client ID', 'danger');
    header('Location: ../admin/dashboard.php?page=clients');
    exit();
}

$client = getClientById($conn, $id);

if (!$client) {
    setAlert('Client not found', 'danger');
    header('Location: ../admin/dashboard.php?page=clients');
    exit();
}

// Delete image file
if ($client['image_path']) {
    deleteFile(UPLOADS_PATH . $client['image_path']);
}

// Delete from database
$query = "DELETE FROM clients WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    setAlert('Client deleted successfully', 'success');
} else {
    setAlert('Failed to delete client: ' . $stmt->error, 'danger');
}
$stmt->close();

header('Location: ../admin/dashboard.php?page=clients');
exit();
?>
