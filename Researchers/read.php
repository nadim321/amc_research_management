
<?php
require 'Common/auth.php';
require 'Common/db.php';

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$stmt = $pdo->query('SELECT * FROM researchers');
$researchers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Researchers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h1>Researchers</h1>
        <table>
            <tr>
                <th>Name</th>
                <th>Contact Info</th>
                <th>Expertise</th>
                <th>Assigned Projects</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($researchers as $researcher): ?>
            <tr>
                <td><?= htmlspecialchars($researcher['name']) ?></td>
                <td><?= htmlspecialchars($researcher['contact_info']) ?></td>
                <td><?= htmlspecialchars($researcher['expertise']) ?></td>
                <td><?= htmlspecialchars($researcher['assigned_projects']) ?></td>
                <td>
                    <a href="update.php?id=<?= $researcher['researcher_id'] ?>">Edit</a>
                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <a href="delete.php?id=<?= $researcher['researcher_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="create.php">Add New Researcher</a>
    </div>
</body>
</html>
