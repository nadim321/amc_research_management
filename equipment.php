<?php
require 'auth.php';
require 'db.php';

// Fetch equipment records
$stmt = $pdo->query('SELECT * FROM equipment');
$equipment = $stmt->fetchAll();

// Add new equipment (Admin and Research Assistants only)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_equipment'])) {
    if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
        header('HTTP/1.0 403 Forbidden');
        exit;
    }

    $name = $_POST['name'];
    $usage_status = 'available';
    $availability = 1;
    $added_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO equipment (name, usage_status, availability, added_by) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $usage_status, $availability, $added_by]);
    header('Location: equipment.php');
}

// Update equipment status (Admin and Research Assistants)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $equipment_id = $_POST['equipment_id'];
    $usage_status = $_POST['usage_status'];
    $stmt = $pdo->prepare('UPDATE equipment SET usage_status = ? WHERE equipment_id = ?');
    $stmt->execute([$usage_status, $equipment_id]);
    header('Location: equipment.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Equipment Management</h1>
        
        <!-- Display Equipment -->
        <table>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Added By</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($equipment as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['usage_status']) ?></td>
                <td><?= htmlspecialchars($item['added_by']) ?></td>
                <td>
                    <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3): ?>
                    <form method="POST" style="display:inline;">
                        <select name="usage_status">
                            <option value="available" <?= $item['usage_status'] == 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="in use" <?= $item['usage_status'] == 'in use' ? 'selected' : '' ?>>In Use</option>
                            <option value="maintenance" <?= $item['usage_status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                        <input type="hidden" name="equipment_id" value="<?= $item['equipment_id'] ?>">
                        <button type="submit" name="update_status">Update</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Add Equipment Form -->
        <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3): ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Equipment Name" required>
            <button type="submit" name="add_equipment">Add Equipment</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
