<?php
require_once '../config.php';

// Ensure user is logged in
requireLogin();

// Only admins can view this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	header('Location: ../login.php');
	exit();
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$error = '';
$user = null;

if ($user_id > 0) {
	$sql = "SELECT id, username, full_name, user_type, email, created_at FROM users WHERE id = $user_id LIMIT 1";
	$res = mysqli_query($conn, $sql);
	if ($res && mysqli_num_rows($res) > 0) {
		$user = mysqli_fetch_assoc($res);
	} else {
		$error = 'Profile not found.';
	}
} else {
	$error = 'Invalid user session.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Profile | FKPark</title>
	<style>
		body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
		.header { background: #667eea; color: white; padding: 20px; margin-bottom: 20px; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; }
		.header .profile-section { font-size: 18px; }
		.header .profile-section span { font-weight: bold; }
		.container { max-width: 700px; margin: 0 auto; background: white; padding: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
		h1 { margin-bottom: 10px; }
		table { width: 100%; border-collapse: collapse; }
		td, th { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
		.actions { margin-top: 20px; display:flex; gap:10px; }
		.btn { padding: 10px 16px; border-radius:4px; text-decoration: none; color: white; }
		.btn-edit { background: #2196F3; }
		.btn-back { background: #6c757d; }
		.error { color: #c00; margin-bottom: 12px; }
	</style>
</head>
<body>
	<div class="header">
		<h2>FKPark Admin Dashboard</h2>
		<div class="profile-section">
			Welcome, <span><?php echo htmlspecialchars($user['full_name'] ?? 'Admin'); ?></span> (<span><?php echo htmlspecialchars($user['user_type'] ?? 'admin'); ?></span>)
		</div>
	</div>
	<div class="container">
		<h1>My Profile</h1>
		<?php if ($error): ?>
			<div class="error"><?php echo htmlspecialchars($error); ?></div>
			<a class="btn btn-back" href="admin_list_users.php">Back</a>
		<?php else: ?>
			<table>
				<tr>
					<th>Field</th>
					<th>Value</th>
				</tr>
				<tr>
					<td>Full name</td>
					<td><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></td>
				</tr>
				<tr>
					<td>Username</td>
					<td><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
				</tr>
				<tr>
					<td>Email</td>
					<td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
				</tr>
				<tr>
					<td>Role</td>
					<td><?php echo htmlspecialchars($user['user_type'] ?? ''); ?></td>
				</tr>
				<tr>
					<td>Member since</td>
					<td><?php echo htmlspecialchars($user['created_at'] ?? ''); ?></td>
				</tr>
			</table>

			<div class="actions">
				<a class="btn btn-edit" href="admin_update_user.php?id=<?php echo (int)$user['id']; ?>">Edit Profile</a>
				<a class="btn btn-back" href="admin_list_users.php">Manage Users</a>
			</div>
		<?php endif; ?>
	</div>
</body>
</html>

