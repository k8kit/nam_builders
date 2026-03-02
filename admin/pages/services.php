<?php
// Services Management Page
$services = getAllServices($conn, false);
displayAlert();

// For each service, get their images
foreach ($services as &$service) {
    $sid = $service['id'];
    $img_result = $conn->query("SELECT * FROM service_images WHERE service_id = $sid ORDER BY sort_order ASC");
    $service['images'] = $img_result ? $img_result->fetch_all(MYSQLI_ASSOC) : [];
}
unset($service);
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>List of Services</h2>
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
                    <th>Service Name</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        
                        <td><?php echo sanitize($service['service_name']); ?></td>
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
    <div class="modal-content" style="max-width: 580px; max-height: 90vh; overflow-y: auto;">
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
                <label for="serviceImages">Service Images <small style="color: var(--text-light); font-weight: normal;">(select multiple)</small></label>
                <input type="file" id="serviceImages" name="service_images[]" class="form-control" 
                       accept="image/*" multiple onchange="previewNewImages(this)">
                <small style="color: var(--text-light);">Formats: JPG, PNG, GIF, WEBP. Max 5MB each. When editing, new uploads are added to existing images.</small>
            </div>

            <!-- Preview of newly selected images -->
            <div id="newImagesPreview" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1rem;"></div>

            <!-- Existing images (shown when editing) -->
            <div id="existingImagesSection" style="display: none; margin-bottom: 1rem;">
                <label style="font-weight: 600; color: var(--text-dark); display: block; margin-bottom: 0.5rem;">Current Images</label>
                <div id="existingImagesGrid" style="display: flex; gap: 8px; flex-wrap: wrap;"></div>
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
    document.getElementById('newImagesPreview').innerHTML = '';
    document.getElementById('existingImagesSection').style.display = 'none';
    document.getElementById('existingImagesGrid').innerHTML = '';
    document.getElementById('serviceModal').classList.add('active');
}

function closeServiceModal() {
    document.getElementById('serviceModal').classList.remove('active');
}

function previewNewImages(input) {
    const preview = document.getElementById('newImagesPreview');
    preview.innerHTML = '';
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.cssText = 'position:relative;';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:70px;height:70px;object-fit:cover;border-radius:6px;border:2px solid var(--primary-color);';
                div.appendChild(img);
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
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
                document.getElementById('newImagesPreview').innerHTML = '';

                // Show existing images
                const existingSection = document.getElementById('existingImagesSection');
                const existingGrid = document.getElementById('existingImagesGrid');
                existingGrid.innerHTML = '';

                const images = service.images || [];
                if (images.length > 0) {
                    existingSection.style.display = 'block';
                    images.forEach(img => {
                        const div = document.createElement('div');
                        div.style.cssText = 'position:relative;display:inline-block;';
                        div.id = 'img-wrapper-' + img.id;
                        div.innerHTML = `
                            <img src="<?php echo UPLOADS_URL; ?>${img.image_path}" 
                                 style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                            <button type="button" onclick="deleteServiceImage(${img.id}, this)" 
                                    style="position:absolute;top:-6px;right:-6px;background:#dc3545;color:white;border:none;border-radius:50%;width:18px;height:18px;font-size:11px;cursor:pointer;line-height:18px;padding:0;"
                                    title="Remove">×</button>
                        `;
                        existingGrid.appendChild(div);
                    });
                } else {
                    existingSection.style.display = 'none';
                }

                document.getElementById('serviceModal').classList.add('active');
            }
        });
}

function deleteServiceImage(imgId, btn) {
    if (!confirm('Remove this image?')) return;
    fetch('../backend/delete_service_image.php?id=' + imgId)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const wrapper = document.getElementById('img-wrapper-' + imgId);
                if (wrapper) wrapper.remove();
                // Also remove from table thumbnail
                const tableThumbs = document.querySelectorAll('[data-img-id="' + imgId + '"]');
                tableThumbs.forEach(t => t.parentElement.remove());
            } else {
                alert('Failed to delete image: ' + data.message);
            }
        });
}

function deleteService(id) {
    if (confirm('Are you sure you want to delete this service and all its images?')) {
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

document.getElementById('serviceModal').addEventListener('click', function(e) {
    if (e.target === this) closeServiceModal();
});
</script>