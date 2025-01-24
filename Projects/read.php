<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    die("You do not have permission to access this page.");
    exit;
}

$stmt = $pdo->query('SELECT pr.title , pr.description, rc.name, pr.funding, pr.status FROM projects pr
        LEFT JOIN researchers rc on rc.researcher_id = pr.team_members');
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
    <link rel="stylesheet" href="../readStyle.css">
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
                <td><?= htmlspecialchars($project['name']) ?></td>
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
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
