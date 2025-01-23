<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Helper function for checking roles
function checkRole($requiredRole) {
    if ($_SESSION['role_id'] != $requiredRole) {
        header('HTTP/1.0 403 Forbidden');
        echo 'Access Denied';
        exit;
    }
}
?>
