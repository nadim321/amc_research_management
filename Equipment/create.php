<?php
require 'Common/auth.php';
require 'Common/db.php';
require 'Common/csrf.php';

// Check if the user is Admin or Research Assistant
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
    die("You do not have permission to access this page.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && validate_csrf_token($_POST['csrf_token'])) {
    $name = $_POST['name'];
    $usage_status = $_POST['usage_status'];
    $availability = $_POST['availability'];
    $added_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO equipment (name, usage_status, availability, added_by) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $usage_status, $availability, $added_by]);

    header('Location: equipment_list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Equipment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h1>Create Equipment</h1>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <input type="text" name="name" placeholder="Equipment Name" required>
            <select name="usage_status">
                <option value="available">Available</option>
                <option value="in use">In Use</option>
                <option value="maintenance">Maintenance</option>
            </select>
            <label>
                <input type="checkbox" name="availability" value="1" checked> Available
            </label>
            <button type="submit">Add Equipment</button>
        </form>
    </div>
</body>
</html>
