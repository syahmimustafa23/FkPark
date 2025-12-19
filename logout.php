<?php
// 1. Initialize the session
session_start();

// 2. Unset all session variables (clears the data in memory)
session_unset();

// 3. Destroy the session (deletes the session file on the server)
session_destroy();

// 4. Redirect to the login page with a logout message
header("Location: login.php?status=success");
exit();
?>