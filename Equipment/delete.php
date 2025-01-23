<?php
require '../Common/auth.php';
require '../Common/db.php';

// Only Admin can delete equipment
if ($_SESSION['role_id'] != 1) {
    die("You do not have permission to access this page.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare('DELETE FROM equipment WHERE equipment_id = ?');
    $stmt->execute([$id]);

    header('Location: equipment_list.php');
    exit;
}
?>
