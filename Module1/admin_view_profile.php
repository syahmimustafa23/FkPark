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
	$sql = "SELECT user_id, username, full_name, user_type FROM users WHERE user_id = $user_id LIMIT 1";
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
		.navbar1{text-decoration-line: none;text-decoration: none; float: right; overflow: hidden; list-style-type: none; display: flex; text-align: center; padding: 0px; margin:0px;}
		.navbar1 a{ 
		    display: block;
		  color: black;
		  padding: 0px 20px;
		  text-decoration: none;
		text-align: center;
		}

		.navbar1 a:hover{
		    background-color: black;
		  color: white;
		}
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
		<div class="navbar1">
			<a href="admin_view_profile.php">Profile</a>
			<a href="../logout.php">Logout</a>
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
					<td>Role</td>
					<td><?php echo htmlspecialchars($user['user_type'] ?? ''); ?></td>
				</tr>
			</table>

			<div class="actions">
				<a class="btn btn-edit" href="admin_update_user.php?id=<?php echo (int)$user['user_id']; ?>">Edit Profile</a>
				<a class="btn" style="background: #dc3545;" href="admin_delete_user.php?id=<?php echo (int)$user['user_id']; ?>" onclick="return confirm('Are you sure you want to delete your account?')">Delete Account</a>
				<a class="btn btn-back" href="admin_list_users.php">Manage Users</a>
			</div>
		<?php endif; ?>
	</div>
</body>
</html>

