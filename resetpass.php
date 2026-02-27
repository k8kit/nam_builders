<?php
/**
 * NAM Builders - Admin Password Reset Script
 * 
 * USAGE:
 *   1. Upload this file to the ROOT of your project (same folder as index.php)
 *   2. Open it in your browser: http://yourdomain.com/reset_password.php
 *   3. Enter a new password and submit
 *   4. DELETE this file immediately after use!
 */

require_once 'config/database.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm'] ?? '';

    if (empty($username) || empty($password) || empty($confirm)) {
        $message = 'All fields are required.';
        $messageType = 'error';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters.';
        $messageType = 'error';
    } elseif ($password !== $confirm) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
    } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        // Check if user exists
        $check = $conn->prepare("SELECT id FROM admin_users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            // Update existing user
            $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
            $stmt->bind_param("ss", $hashed, $username);
            if ($stmt->execute()) {
                $message = "✅ Password updated for user <strong>{$username}</strong>. <strong>Delete this file now!</strong>";
                $messageType = 'success';
            } else {
                $message = 'Database error: ' . $stmt->error;
                $messageType = 'error';
            }
            $stmt->close();
        } else {
            // Create new admin user
            $email = $username . '@nambuilders.com';
            $stmt = $conn->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed, $email);
            if ($stmt->execute()) {
                $message = "✅ New admin user <strong>{$username}</strong> created. <strong>Delete this file now!</strong>";
                $messageType = 'success';
            } else {
                $message = 'Database error: ' . $stmt->error;
                $messageType = 'error';
            }
            $stmt->close();
        }
        $check->close();
    }
}

// List existing admin users
$users = [];
$result = $conn->query("SELECT id, username, email, created_at FROM admin_users");
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - NAM Builders</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem; }
        .card { background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); padding: 2rem; width: 100%; max-width: 480px; }
        h2 { color: #2C3E50; margin-bottom: 0.25rem; }
        .subtitle { color: #666; font-size: 0.9rem; margin-bottom: 1.5rem; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 0.75rem 1rem; font-size: 0.85rem; color: #856404; margin-bottom: 1.5rem; }
        .warning strong { display: block; margin-bottom: 0.25rem; }
        label { display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.35rem; color: #333; }
        input { width: 100%; padding: 0.65rem 0.9rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; margin-bottom: 1rem; transition: border-color 0.2s; }
        input:focus { outline: none; border-color: #FF5722; box-shadow: 0 0 0 3px rgba(255,87,34,0.1); }
        button { width: 100%; padding: 0.75rem; background: #FF5722; color: white; border: none; border-radius: 5px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #E64A19; }
        .alert { padding: 0.9rem 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.95rem; }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .users-table { margin-top: 1.5rem; border-top: 1px solid #eee; padding-top: 1.5rem; }
        .users-table h4 { color: #2C3E50; margin-bottom: 0.75rem; font-size: 0.95rem; }
        table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        th, td { padding: 0.5rem 0.75rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; color: #555; }
        .badge { background: #28a745; color: white; border-radius: 3px; padding: 2px 6px; font-size: 0.75rem; }
        .login-link { display: block; text-align: center; margin-top: 1rem; color: #FF5722; font-size: 0.9rem; text-decoration: none; }
        .login-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="card">
    <h2>🔐 Password Reset</h2>
    <p class="subtitle">NAM Builders Admin Panel</p>

    <div class="warning">
        <strong>⚠️ Security Warning</strong>
        This file grants access to reset admin credentials. Delete it immediately after use.
    </div>

    <?php if ($message): ?>
        <div class="alert <?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="admin" required>

        <label for="password">New Password</label>
        <input type="password" id="password" name="password" placeholder="Min. 6 characters" required>

        <label for="confirm">Confirm Password</label>
        <input type="password" id="confirm" name="confirm" placeholder="Repeat password" required>

        <button type="submit">Reset Password</button>
    </form>

    <?php if (!empty($users)): ?>
    <div class="users-table">
        <h4>Existing Admin Users</h4>
        <table>
            <thead><tr><th>ID</th><th>Username</th><th>Email</th></tr></thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo (int)$u['id']; ?></td>
                    <td><span class="badge">admin</span> <?php echo htmlspecialchars($u['username']); ?></td>
                    <td><?php echo htmlspecialchars($u['email'] ?? '—'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <a href="admin/login.php" class="login-link">→ Go to Admin Login</a>
</div>
</body>
</html>