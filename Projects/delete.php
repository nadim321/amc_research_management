<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Restrict to Admin only
if ($_SESSION['role_id'] != 1) {
    die("You do not have permission to access this page.");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare('DELETE FROM projects WHERE project_id = :project_id AND status != :status');
        
// Bind values securely to the query
$stmt->execute([
    ':project_id' => $id,
    ':status' => 'completed', // Ensure the comparison is safe and clear
]);

header('Location: read.php');
exit;
?>
