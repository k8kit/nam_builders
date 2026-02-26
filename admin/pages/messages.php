<?php
// Messages Management Page
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($query);
$messages = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
displayAlert();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Contact Messages</h2>
</div>

<!-- Messages Table -->
<div class="admin-card">
    <?php if (!empty($messages)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Service</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?php echo $message['id']; ?></td>
                        <td><?php echo sanitize($message['full_name']); ?></td>
                        <td><?php echo sanitize($message['email']); ?></td>
                        <td><?php echo sanitize($message['phone'] ?? 'N/A'); ?></td>
                        <td><?php echo sanitize($message['service_needed'] ?? 'General'); ?></td>
                        <td><?php echo substr(sanitize($message['message']), 0, 50) . '...'; ?></td>
                        <td><?php echo formatDate($message['created_at']); ?></td>
                        <td>
                            <span class="badge" style="background-color: <?php echo !$message['is_read'] ? '#FFC107' : '#28A745'; ?>;">
                                <?php echo !$message['is_read'] ? 'Unread' : 'Read'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="admin-actions">
                                <button class="btn-edit" onclick="viewMessage(<?php echo $message['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteMessage(<?php echo $message['id']; ?>)">
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
            <i class="fas fa-inbox"></i> No messages yet.
        </p>
    <?php endif; ?>
</div>

<!-- View Message Modal -->
<div class="modal-overlay" id="messageModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Message Details</h2>
            <button class="modal-close" onclick="closeMessageModal()">&times;</button>
        </div>
        <div id="messageContent" style="padding: 1rem;">
            <!-- Message content will be inserted here -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary-main" style="background-color: #6C757D; color: white; border: none;" onclick="closeMessageModal()">Close</button>
        </div>
    </div>
</div>

<script>
function viewMessage(id) {
    fetch('../backend/get_message.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const msg = data.data;
                let content = `
                    <div style="margin-bottom: 1rem;">
                        <h4>From:</h4>
                        <p>${sanitizeHtml(msg.full_name)}</p>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <h4>Email:</h4>
                        <p><a href="mailto:${sanitizeHtml(msg.email)}">${sanitizeHtml(msg.email)}</a></p>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <h4>Phone:</h4>
                        <p>${sanitizeHtml(msg.phone || 'Not provided')}</p>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <h4>Service Needed:</h4>
                        <p>${sanitizeHtml(msg.service_needed || 'General')}</p>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <h4>Message:</h4>
                        <p>${sanitizeHtml(msg.message)}</p>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <h4>Received:</h4>
                        <p>${new Date(msg.created_at).toLocaleString()}</p>
                    </div>
                `;
                document.getElementById('messageContent').innerHTML = content;
                document.getElementById('messageModal').classList.add('active');
            }
        });
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.remove('active');
}

function deleteMessage(id) {
    if (confirm('Are you sure you want to delete this message?')) {
        window.location.href = '../backend/delete_message.php?id=' + id;
    }
}

function sanitizeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Close modal when clicking outside
document.getElementById('messageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMessageModal();
    }
});
</script>
