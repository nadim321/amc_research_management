
<?php
require 'auth.php'; // Ensure user is authenticated
require 'db.php';
require 'csrf.php';

$csrf_token = generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
        header('HTTP/1.0 403 Forbidden');
        exit; // Restrict access for unauthorized roles
    }

    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO equipment (name, description, quantity, created_by) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $description, $quantity, $created_by]);

    header('Location: equipment.php');
    exit;
}
?>
