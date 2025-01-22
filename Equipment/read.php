<?php
require '../auth.php'; // Ensure user is authenticated
require '../db.php';

// Fetch equipment
$stmt = $pdo->query('SELECT * FROM equipment');
$equipment = $stmt->fetchAll();
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
    <div class="content-container">
        <h1>Equipment Management</h1>

        <!-- Display Equipment -->
        <table>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($equipment as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['description']) ?></td>
                <td><?= htmlspecialchars($item['quantity']) ?></td>
                <td>
                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <a href="delete_equipment.php?delete=<?= $item['equipment_id'] ?>">Delete</a>
                    <a href="update_equipment.php?equipment_id=<?= $item['equipment_id'] ?>">Edit</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Add Equipment Form -->
        <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): ?>
        <form action="create_equipment.php" method="POST">
            <input type="text" name="name" placeholder="Equipment Name" required>
            <textarea name="description" placeholder="Equipment Description" required></textarea>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <button type="submit">Add Equipment</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
