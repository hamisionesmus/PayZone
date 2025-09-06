<?php
// Logout script - Clear session BEFORE any output
require_once '../../config/config.php';

// Clear all authentication data
session_start(); // Ensure session is started
$_SESSION = []; // Clear all session variables

// Clear specific session variables
unset($_SESSION['auth_token']);
unset($_SESSION['user']);

// Clear auth cookie
setcookie('auth_token', '', time() - 3600, '/');

// Destroy session
session_destroy();

// Also clear localStorage via JavaScript
?>
<script>
    // Clear localStorage on logout
    localStorage.removeItem('auth_token');
    // Redirect to login
    window.location.href = 'login.php';
</script>