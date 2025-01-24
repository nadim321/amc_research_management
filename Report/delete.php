<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Check if user has permission (Admin only)
if ($_SESSION['role_id'] != 1) {
    echo "You do not have permission to delete reports.";
    exit;
}

// Delete the report
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM reports WHERE report_id = ?");
    $stmt->execute([$_GET['id']]);

    header('Location: read.php');
    exit;
}
?>
