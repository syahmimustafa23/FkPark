<?php
/**
 * FKPark - Apply Fix: regenerate user password hashes
 * Use this to regenerate valid bcrypt hashes for test accounts.
 * Run once by visiting http://localhost/fkpark/apply_fix_hashes.php
 * Then remove this file for safety.
 */

require_once 'config.php';

// Only allow running from localhost
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    echo "Access denied.";
    exit();
}

$accounts = [
    'admin' => 'admin123',
    'student1' => 'student123',
    'security1' => 'security123'
];

foreach ($accounts as $username => $plain) {
    $hash = password_hash($plain, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    if (!$stmt) {
        echo "Prepare failed: " . htmlspecialchars($conn->error) . "<br>";
        continue;
    }
    $stmt->bind_param('ss', $hash, $username);
    if ($stmt->execute()) {
        echo "Updated password for user: " . htmlspecialchars($username) . "<br>";
    } else {
        echo "Failed to update " . htmlspecialchars($username) . ": " . htmlspecialchars($stmt->error) . "<br>";
    }
    $stmt->close();
}

echo "<hr>Done. Please delete this file after verifying login works.";

?>
