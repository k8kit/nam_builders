<?php
// Admin Overview Page
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <!-- Stats Cards -->
    <div class="admin-card" style="text-align: center;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h4 style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Total Clients</h4>
                <h2 style="margin: 0.5rem 0 0; color: var(--primary-color); font-size: 2rem;">
                    <?php echo $stats['total_clients']; ?>
                </h2>
            </div>
            <i class="fas fa-users" style="font-size: 2.5rem; color: rgba(255, 87, 34, 0.2);"></i>
        </div>
    </div>

    <div class="admin-card" style="text-align: center;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h4 style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Total Services</h4>
                <h2 style="margin: 0.5rem 0 0; color: #007BFF; font-size: 2rem;">
                    <?php echo $stats['total_services']; ?>
                </h2>
            </div>
            <i class="fas fa-cogs" style="font-size: 2.5rem; color: rgba(0, 123, 255, 0.2);"></i>
        </div>
    </div>

    <div class="admin-card" style="text-align: center;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h4 style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Total Messages</h4>
                <h2 style="margin: 0.5rem 0 0; color: #28A745; font-size: 2rem;">
                    <?php echo $stats['total_messages']; ?>
                </h2>
            </div>
            <i class="fas fa-envelope" style="font-size: 2.5rem; color: rgba(40, 167, 69, 0.2);"></i>
        </div>
    </div>

    <div class="admin-card" style="text-align: center;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h4 style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Unread Messages</h4>
                <h2 style="margin: 0.5rem 0 0; color: #FFC107; font-size: 2rem;">
                    <?php echo $stats['unread_messages']; ?>
                </h2>
            </div>
            <i class="fas fa-bell" style="font-size: 2.5rem; color: rgba(255, 193, 7, 0.2);"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-card">
    <h3>Quick Actions</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem;">
        <a href="dashboard.php?page=clients" class="btn-add" style="text-align: center; padding: 1rem;">
            <i class="fas fa-plus"></i> Add Client
        </a>
        <a href="dashboard.php?page=services" class="btn-add" style="text-align: center; padding: 1rem;">
            <i class="fas fa-plus"></i> Add Service
        </a>
        <a href="dashboard.php?page=messages" class="btn-add" style="text-align: center; padding: 1rem; background-color: #007BFF;">
            <i class="fas fa-envelope"></i> View Messages
        </a>
        <a href="../backend/logout.php" class="btn-add" style="text-align: center; padding: 1rem; background-color: #6C757D;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<!-- System Info -->
<div class="admin-card" style="margin-top: 2rem;">
    <h3>System Information</h3>
    <table class="admin-table" style="margin-top: 1rem;">
        <tr>
            <td style="border: none; padding: 0.5rem 0;"><strong>PHP Version:</strong></td>
            <td style="border: none; padding: 0.5rem 0; text-align: right;"><?php echo phpversion(); ?></td>
        </tr>
        <tr>
            <td style="border: none; padding: 0.5rem 0;"><strong>Database:</strong></td>
            <td style="border: none; padding: 0.5rem 0; text-align: right;">nam_builders</td>
        </tr>
        <tr>
            <td style="border: none; padding: 0.5rem 0;"><strong>Current User:</strong></td>
            <td style="border: none; padding: 0.5rem 0; text-align: right;"><?php echo sanitize($_SESSION['admin_username']); ?></td>
        </tr>
        <tr>
            <td style="border: none; padding: 0.5rem 0;"><strong>Last Login:</strong></td>
            <td style="border: none; padding: 0.5rem 0; text-align: right;"><?php echo date('M d, Y h:i A'); ?></td>
        </tr>
    </table>
</div>
