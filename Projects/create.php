<?php
require '../auth.php'; // Ensure user is authenticated
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
        header('HTTP/1.0 403 Forbidden');
        exit; // Restrict access for unauthorized roles
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $funding = $_POST['funding'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO projects (title, description, funding, created_by) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $description, $funding, $created_by]);

    header('Location: read.php');
    exit;
}
?>
