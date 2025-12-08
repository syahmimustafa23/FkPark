<?php
/**
 * FKPark - Simple Login Test
 * This script tests if the database connection and users are working
 */

require_once 'config.php';

echo "<h1>FKPark - Debug Test</h1>";

// Test 1: Database connection
echo "<h2>1. Database Connection</h2>";
if ($conn) {
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

// Test 2: Check if users table exists
echo "<h2>2. Users Table</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM users");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p style='color: green;'>✅ Users table exists with " . $row['count'] . " users</p>";
} else {
    echo "<p style='color: red;'>❌ Users table not found</p>";
}

// Test 3: Display all users
echo "<h2>3. User Accounts</h2>";
$result = $conn->query("SELECT user_id, username, role FROM users");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Username</th><th>Role</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No users found in database</p>";
}

// Test 4: Test password verification
echo "<h2>4. Password Test</h2>";
$result = $conn->query("SELECT username, password FROM users WHERE username = 'admin'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<p><strong>Admin user password hash:</strong></p>";
    echo "<p><code>" . htmlspecialchars($row['password']) . "</code></p>";
    
    // Test password_verify
    if (password_verify('admin123', $row['password'])) {
        echo "<p style='color: green;'>✅ Password 'admin123' verification: SUCCESS</p>";
    } else {
        echo "<p style='color: red;'>❌ Password 'admin123' verification: FAILED</p>";
        echo "<p style='color: orange;'>The password hash might be corrupted. Let me regenerate it.</p>";
    }
}

// Test 5: Try login manually
echo "<h2>5. Manual Login Test</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['test_username']);
    $password = $_POST['test_password'];
    
    echo "<p><strong>Testing:</strong> Username: " . htmlspecialchars($username) . "</p>";
    
    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            echo "<p style='color: green;'>✅ Login successful! User ID: " . $user['user_id'] . ", Role: " . $user['role'] . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Password incorrect</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ User not found</p>";
    }
}

echo "<h2>Test Login Form</h2>";
echo "<form method='POST'>";
echo "<input type='text' name='test_username' placeholder='Username' required><br>";
echo "<input type='password' name='test_password' placeholder='Password' required><br>";
echo "<button type='submit'>Test Login</button>";
echo "</form>";

echo "<hr>";
echo "<p><a href='login.php'>← Back to Login</a></p>";
?>
