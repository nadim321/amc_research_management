<?php
require '../auth.php'; // Ensure user is authenticated
require '../db.php';

$stmt = $pdo->query('SELECT * FROM projects');
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Projects Management</h1>

        <!-- Display Projects -->
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Funding</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($projects as $project): ?>
            <tr>
                <td><?= htmlspecialchars($project['title']) ?></td>
                <td><?= htmlspecialchars($project['description']) ?></td>
                <td><?= htmlspecialchars($project['funding']) ?></td>
                <td><?= htmlspecialchars($project['status']) ?></td>
                <td>
                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <a href="delete.php?delete=<?= $project['project_id'] ?>">Delete</a>
                    <a href="update.php?project_id=<?= $project['project_id'] ?>">Edit</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Add Project Form -->
        <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): ?>
        <form action="create.php" method="POST">
            <input type="text" name="title" placeholder="Project Title" required>
            <textarea name="description" placeholder="Project Description" required></textarea>
            <input type="number" step="0.01" name="funding" placeholder="Funding Amount" required>
            <button type="submit" name="add_project">Add Project</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
