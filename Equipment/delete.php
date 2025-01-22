<?php
require 'auth.php'; // Ensure user is authenticated
require 'db.php';

if (isset($_GET['delete']) && $_SESSION['role_id'] == 1) {
    $equipment_id = $_GET['delete'];

    $stmt = $pdo->prepare('DELETE FROM equipment WHERE equipment_id = ?');
    $stmt->execute([$equipment_id]);

    header('Location: equipment.php');
    exit;
}
?>
