<?php
require '../auth.php'; // Ensure user is authenticated
require '../db.php';

if (isset($_GET['delete']) && $_SESSION['role_id'] == 1) {
    $project_id = $_GET['delete'];

    $stmt = $pdo->prepare('DELETE FROM projects WHERE project_id = ? AND status != "completed"');
    $stmt->execute([$project_id]);

    header('Location: read.php');
    exit;
}
?>
