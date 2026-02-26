<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    jsonResponse(false, 'Invalid client ID');
}

$client = getClientById($conn, $id);

if ($client) {
    jsonResponse(true, 'Client found', $client);
} else {
    jsonResponse(false, 'Client not found');
}
?>
