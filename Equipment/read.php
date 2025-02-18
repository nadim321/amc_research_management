<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Fetch equipment based on role
if ($_SESSION['role_id'] == 1) { // Admin can view all equipment
    $stmt = $pdo->query('SELECT * FROM equipment');
} elseif ($_SESSION['role_id'] == 3) { // Research Assistant can view their own equipment
    $stmt = $pdo->prepare('SELECT * FROM equipment WHERE added_by = :user_id');
        
    // Execute the statement with the sanitized session user_id
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
} else {
    die("You do not have permission to view this page.");
}
$equipment = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment List</title>
    <link rel="stylesheet" href="../readStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Equipment Inventory</h1>
        <table>
            <tr>
                <th>Name</th>
                <th>Usage Status</th>
                <th>Availability</th>
                <th>Added By</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($equipment as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['usage_status']) ?></td>
                <td><?= $item['availability'] ? 'Yes' : 'No' ?></td>
                <td><?= htmlspecialchars($item['added_by']) ?></td>
                <td>
                    <a href="update.php?id=<?= $item['equipment_id'] ?>">Edit</a>
                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <a href="delete.php?id=<?= $item['equipment_id'] ?>">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table></br>
        <a href="create.php">Add New Equipment</a> 
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
