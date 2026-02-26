<?php
// Clients Management Page
$clients = getAllClients($conn, false);
displayAlert();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Manage Clients</h2>
    <button class="btn-add" onclick="openAddClientModal()">
        <i class="fas fa-plus"></i> Add New Client
    </button>
</div>

<!-- Clients Table -->
<div class="admin-card">
    <?php if (!empty($clients)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client Name</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?php echo $client['id']; ?></td>
                        <td><?php echo sanitize($client['client_name']); ?></td>
                        <td>
                            <?php if (!empty($client['image_path']) && file_exists(UPLOADS_PATH . $client['image_path'])): ?>
                                <img src="<?php echo UPLOADS_URL . $client['image_path']; ?>" alt="Client" style="max-height: 50px; max-width: 50px;">
                            <?php else: ?>
                                <span style="color: var(--text-light);">No image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge" style="background-color: <?php echo $client['is_active'] ? '#28A745' : '#6C757D'; ?>;">
                                <?php echo $client['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td><?php echo $client['sort_order']; ?></td>
                        <td><?php echo formatDate($client['created_at']); ?></td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-edit" onclick="editClient(<?php echo $client['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteClient(<?php echo $client['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
            <i class="fas fa-inbox"></i> No clients yet. <a href="#" onclick="openAddClientModal(); return false;">Add one now</a>
        </p>
    <?php endif; ?>
</div>

<!-- Add/Edit Client Modal -->
<div class="modal-overlay" id="clientModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Client</h2>
            <button class="modal-close" onclick="closeClientModal()">&times;</button>
        </div>
        <form id="clientForm" enctype="multipart/form-data" onsubmit="submitClientForm(event)">
            <input type="hidden" id="clientId" name="client_id" value="">

            <div class="form-group">
                <label for="clientName">Client Name</label>
                <input type="text" id="clientName" name="client_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="clientImage">Client Logo/Image</label>
                <input type="file" id="clientImage" name="client_image" class="form-control" accept="image/*">
                <small style="color: var(--text-light);">Formats: JPG, PNG, GIF. Max size: 5MB</small>
            </div>

            <div class="form-group">
                <label for="clientDescription">Description</label>
                <textarea id="clientDescription" name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="clientOrder">Display Order</label>
                <input type="number" id="clientOrder" name="sort_order" class="form-control" value="0" min="0">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="clientActive" name="is_active" value="1" checked>
                    Active
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary-main" style="background-color: #6C757D; color: white; border: none;" onclick="closeClientModal()">Cancel</button>
                <button type="submit" class="btn-add">Save Client</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddClientModal() {
    document.getElementById('modalTitle').innerText = 'Add New Client';
    document.getElementById('clientForm').reset();
    document.getElementById('clientId').value = '';
    document.getElementById('clientModal').classList.add('active');
}

function closeClientModal() {
    document.getElementById('clientModal').classList.remove('active');
}

function editClient(id) {
    fetch('../backend/get_client.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const client = data.data;
                document.getElementById('modalTitle').innerText = 'Edit Client';
                document.getElementById('clientId').value = client.id;
                document.getElementById('clientName').value = client.client_name;
                document.getElementById('clientDescription').value = client.description;
                document.getElementById('clientOrder').value = client.sort_order;
                document.getElementById('clientActive').checked = client.is_active == 1;
                document.getElementById('clientModal').classList.add('active');
            }
        });
}

function deleteClient(id) {
    if (confirm('Are you sure you want to delete this client?')) {
        window.location.href = '../backend/delete_client.php?id=' + id;
    }
}

function submitClientForm(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('clientForm'));
    
    fetch('../backend/save_client.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'dashboard.php?page=clients';
        } else {
            alert('Error: ' + data.message);
        }
    });
}

// Close modal when clicking outside
document.getElementById('clientModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeClientModal();
    }
});
</script>
