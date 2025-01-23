<?php
require '../Common/auth.php';
require '../Common/db.php';

// Restrict to Admin only
if ($_SESSION['role_id'] != 1) {
    die("You do not have permission to access this page.");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare('DELETE FROM researchers WHERE researcher_id = ?');
$stmt->execute([$id]);

header('Location: read.php');
exit;
?>
