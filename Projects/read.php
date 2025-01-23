<?php
require '../Common/auth.php';
require '../Common/db.php';

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$stmt = $pdo->query('SELECT * FROM projects');
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="form-container">
        <h1>Projects</h1>
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Team Members</th>
                <th>Funding</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($projects as $project): ?>
            <tr>
                <td><?= htmlspecialchars($project['title']) ?></td>
                <td><?= htmlspecialchars($project['description']) ?></td>
                <td><?= htmlspecialchars($project['team_members']) ?></td>
                <td><?= htmlspecialchars($project['funding']) ?></td>
                <td><?= htmlspecialchars($project['status']) ?></td>
                <td>
                    <a href="update.php?id=<?= $project['project_id'] ?>">Edit</a>
                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <a href="delete.php?id=<?= $project['project_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="create.php">Add New Project</a>
    </div>
</body>
</html>
