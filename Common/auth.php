<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Helper function for checking roles
function checkRole($requiredRole) {
    if ($_SESSION['role_id'] != $requiredRole) {
        header('Location: ../index.php');
        exit;
    }
}
?>
