<?php
// Services Management Page
$services = getAllServices($conn, false);
displayAlert();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Manage Services</h2>
    <button class="btn-add" onclick="openAddServiceModal()">
        <i class="fas fa-plus"></i> Add New Service
    </button>
</div>

<!-- Services Table -->
<div class="admin-card">
    <?php if (!empty($services)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service Name</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo $service['id']; ?></td>
                        <td><?php echo sanitize($service['service_name']); ?></td>
                        <td>
                            <?php if (!empty($service['image_path']) && file_exists(UPLOADS_PATH . $service['image_path'])): ?>
                                <img src="<?php echo UPLOADS_URL . $service['image_path']; ?>" alt="Service" style="max-height: 50px; max-width: 50px;">
                            <?php else: ?>
                                <span style="color: var(--text-light);">No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo substr(sanitize($service['description']), 0, 50) . '...'; ?></td>
                        <td>
                            <span class="badge" style="background-color: <?php echo $service['is_active'] ? '#28A745' : '#6C757D'; ?>;">
                                <?php echo $service['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td><?php echo $service['sort_order']; ?></td>
                        <td><?php echo formatDate($service['created_at']); ?></td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-edit" onclick="editService(<?php echo $service['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteService(<?php echo $service['id']; ?>)">
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
            <i class="fas fa-inbox"></i> No services yet. <a href="#" onclick="openAddServiceModal(); return false;">Add one now</a>
        </p>
    <?php endif; ?>
</div>

<!-- Add/Edit Service Modal -->
<div class="modal-overlay" id="serviceModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Service</h2>
            <button class="modal-close" onclick="closeServiceModal()">&times;</button>
        </div>
        <form id="serviceForm" enctype="multipart/form-data" onsubmit="submitServiceForm(event)">
            <input type="hidden" id="serviceId" name="service_id" value="">

            <div class="form-group">
                <label for="serviceName">Service Name</label>
                <input type="text" id="serviceName" name="service_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="serviceDescription">Description</label>
                <textarea id="serviceDescription" name="description" class="form-control" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="serviceImage">Service Image</label>
                <input type="file" id="serviceImage" name="service_image" class="form-control" accept="image/*">
                <small style="color: var(--text-light);">Formats: JPG, PNG, GIF. Max size: 5MB</small>
            </div>

            <div class="form-group">
                <label for="serviceOrder">Display Order</label>
                <input type="number" id="serviceOrder" name="sort_order" class="form-control" value="0" min="0">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="serviceActive" name="is_active" value="1" checked>
                    Active
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary-main" style="background-color: #6C757D; color: white; border: none;" onclick="closeServiceModal()">Cancel</button>
                <button type="submit" class="btn-add">Save Service</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddServiceModal() {
    document.getElementById('modalTitle').innerText = 'Add New Service';
    document.getElementById('serviceForm').reset();
    document.getElementById('serviceId').value = '';
    document.getElementById('serviceModal').classList.add('active');
}

function closeServiceModal() {
    document.getElementById('serviceModal').classList.remove('active');
}

function editService(id) {
    fetch('../backend/get_service.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const service = data.data;
                document.getElementById('modalTitle').innerText = 'Edit Service';
                document.getElementById('serviceId').value = service.id;
                document.getElementById('serviceName').value = service.service_name;
                document.getElementById('serviceDescription').value = service.description;
                document.getElementById('serviceOrder').value = service.sort_order;
                document.getElementById('serviceActive').checked = service.is_active == 1;
                document.getElementById('serviceModal').classList.add('active');
            }
        });
}

function deleteService(id) {
    if (confirm('Are you sure you want to delete this service?')) {
        window.location.href = '../backend/delete_service.php?id=' + id;
    }
}

function submitServiceForm(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('serviceForm'));
    
    fetch('../backend/save_service.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'dashboard.php?page=services';
        } else {
            alert('Error: ' + data.message);
        }
    });
}

// Close modal when clicking outside
document.getElementById('serviceModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeServiceModal();
    }
});
</script>
