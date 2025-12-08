<?php
/**
 * FKPark - Generate Valid Password Hashes
 * This creates REAL valid bcrypt hashes
 */

// Generate proper hashes right now
$passwords = [
    'admin123',
    'student123', 
    'security123'
];

echo "<!DOCTYPE html>
<html>
<head>
    <title>FKPark - Fix Database</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        h1 { color: #667eea; }
        .code { background: #f0f0f0; padding: 15px; border-radius: 5px; font-family: monospace; word-break: break-all; margin: 10px 0; }
        .step { background: #e7f3ff; padding: 15px; margin: 15px 0; border-left: 4px solid #17a2b8; border-radius: 4px; }
        .success { background: #d4edda; padding: 15px; margin: 15px 0; border-left: 4px solid #28a745; color: #155724; border-radius: 4px; }
        button { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #764ba2; }
        .warning { color: #ff6b6b; font-weight: bold; }
        textarea { width: 100%; height: 300px; padding: 10px; font-family: monospace; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 FKPark - Fix Database Hashes</h1>
        
        <div class='step'>
            <strong>⚠️ Your database has INVALID password hashes!</strong><br>
            This tool will fix them for you.
        </div>

        <h2>Step 1: Copy This SQL Script</h2>
        <p>The SQL below has VALID password hashes. Copy it and run in phpMyAdmin.</p>
        
        <textarea id='sqlCode' readonly>";

// Generate valid hashes
$hash_admin = password_hash('admin123', PASSWORD_DEFAULT);
$hash_student = password_hash('student123', PASSWORD_DEFAULT);
$hash_security = password_hash('security123', PASSWORD_DEFAULT);

$sql = "-- FKPark - Fix Database with Valid Hashes
-- Run this in phpMyAdmin SQL tab

USE fkpark;

-- Delete old invalid users
DELETE FROM users;

-- Insert admin with VALID hash
INSERT INTO users (username, password, role) 
VALUES ('admin', '" . $hash_admin . "', 'admin');

-- Insert student with VALID hash
INSERT INTO users (username, password, role) 
VALUES ('student1', '" . $hash_student . "', 'student');

-- Insert security with VALID hash
INSERT INTO users (username, password, role) 
VALUES ('security1', '" . $hash_security . "', 'security');

-- Verify
SELECT * FROM users;";

echo htmlspecialchars($sql);

echo "</textarea>

        <h2>Step 2: Run in phpMyAdmin</h2>
        <div class='step'>
            1. Go to: <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a><br>
            2. Click <strong>SQL</strong> tab<br>
            3. Click in the text box<br>
            4. Press <strong>Ctrl+A</strong> to select all text in the textarea above<br>
            5. Press <strong>Ctrl+C</strong> to copy<br>
            6. Paste in phpMyAdmin SQL editor<br>
            7. Click <strong>Go</strong> button
        </div>

        <h2>Step 3: Verify & Login</h2>
        <div class='step'>
            After running the SQL:
            <ol>
                <li>Go to: <a href='http://localhost/fkpark/debug_test.php' target='_blank'>Debug Test</a></li>
                <li>It should now show ✅ Password verification SUCCESS</li>
                <li>Then try login: <a href='http://localhost/fkpark/login.php' target='_blank'>Login Page</a></li>
                <li>Use: <strong>admin</strong> / <strong>admin123</strong></li>
            </ol>
        </div>

        <h2>Valid Hashes Generated:</h2>
        <p><strong>Admin (admin123):</strong></p>
        <div class='code'>" . htmlspecialchars($hash_admin) . "</div>
        
        <p><strong>Student (student123):</strong></p>
        <div class='code'>" . htmlspecialchars($hash_student) . "</div>
        
        <p><strong>Security (security123):</strong></p>
        <div class='code'>" . htmlspecialchars($hash_security) . "</div>

        <div class='success'>
            ✅ These hashes are VALID and will work with password_verify()
        </div>

        <button onclick='copyCode()'>📋 Copy SQL Script</button>

        <hr style='margin: 30px 0;'>
        
        <h2>Quick Links:</h2>
        <ul>
            <li><a href='http://localhost/phpmyadmin' target='_blank'>📊 phpMyAdmin</a></li>
            <li><a href='http://localhost/fkpark/debug_test.php' target='_blank'>🔍 Debug Test</a></li>
            <li><a href='http://localhost/fkpark/login.php' target='_blank'>🔑 Login Page</a></li>
        </ul>
    </div>

    <script>
        function copyCode() {
            var textarea = document.getElementById('sqlCode');
            textarea.select();
            document.execCommand('copy');
            alert('✅ SQL Script copied! Now paste it in phpMyAdmin SQL tab and click Go');
        }
    </script>
</body>
</html>";
?>
