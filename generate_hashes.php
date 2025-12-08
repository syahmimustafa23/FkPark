<?php
/**
 * FKPark - Password Hash Generator
 * 
 * This script generates valid bcrypt password hashes
 * for the default test users.
 * 
 * Run this script once in your browser to see the correct hashes,
 * then use them in your database.
 */

// Test passwords
$passwords = array(
    'admin123',
    'student123',
    'security123'
);

// Generate hashes
echo "<!DOCTYPE html>
<html>
<head>
    <title>FKPark Password Hash Generator</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        h1 { color: #667eea; }
        .hash-box { background: #f0f0f0; padding: 15px; margin: 10px 0; border-left: 4px solid #667eea; border-radius: 4px; font-family: monospace; word-break: break-all; }
        .label { font-weight: bold; color: #333; }
        .instruction { color: #666; margin: 20px 0; padding: 15px; background: #e7f3ff; border-left: 4px solid #17a2b8; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>FKPark Password Hash Generator</h1>
        
        <div class='instruction'>
            <strong>✅ Instructions:</strong>
            <ol>
                <li>Copy the SQL script below</li>
                <li>Go to phpMyAdmin → SQL tab</li>
                <li>Paste the script and click Go</li>
                <li>Your database will be updated with valid hashes</li>
                <li>Then you can login normally</li>
            </ol>
        </div>

        <h2>Generated Password Hashes</h2>";

foreach ($passwords as $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "<div>
        <div class='label'>Password: " . htmlspecialchars($password) . "</div>
        <div class='hash-box'>" . htmlspecialchars($hash) . "</div>
    </div>";
}

echo "<h2>SQL Script - Copy This</h2>
        <div class='hash-box' id='sqlScript'>";

// Generate the SQL script
$hash_admin = password_hash('admin123', PASSWORD_DEFAULT);
$hash_student = password_hash('student123', PASSWORD_DEFAULT);
$hash_security = password_hash('security123', PASSWORD_DEFAULT);

$sql = "-- FKPark - Reset Users with Valid Hashes
-- Run this in phpMyAdmin SQL tab

-- Delete existing users
DELETE FROM fkpark.users;

-- Insert admin user
INSERT INTO fkpark.users (username, password, role) 
VALUES ('admin', '" . $hash_admin . "', 'admin');

-- Insert student user
INSERT INTO fkpark.users (username, password, role) 
VALUES ('student1', '" . $hash_student . "', 'student');

-- Insert security user
INSERT INTO fkpark.users (username, password, role) 
VALUES ('security1', '" . $hash_security . "', 'security');

-- Verify
SELECT * FROM fkpark.users;";

echo htmlspecialchars($sql);

echo "</div>
        
        <h2>Test After Update</h2>
        <p>Once you've run the SQL script above in phpMyAdmin, try logging in with:</p>
        <div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; border-radius: 4px;'>
            <strong>Admin:</strong> username: <code>admin</code> | password: <code>admin123</code><br>
            <strong>Student:</strong> username: <code>student1</code> | password: <code>student123</code><br>
            <strong>Security:</strong> username: <code>security1</code> | password: <code>security123</code>
        </div>

        <p style='margin-top: 30px; color: #999; font-size: 12px;'>
            Generated: " . date('Y-m-d H:i:s') . "
        </p>
    </div>

    <script>
        // Auto-select the SQL script for easy copying
        function selectSQL() {
            var sqlBox = document.getElementById('sqlScript');
            var range = document.createRange();
            range.selectNodeContents(sqlBox);
            window.getSelection().addRange(range);
            document.execCommand('copy');
            alert('SQL Script copied to clipboard!');
        }
    </script>
</body>
</html>";
?>
