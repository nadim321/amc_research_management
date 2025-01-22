<?php
require 'auth.php';
require 'db.php';

// Fetch projects
$stmt = $pdo->query('SELECT * FROM projects');
$projects = $stmt->fetchAll();

// Add a new project (Admin and Researchers only)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_project'])) {
    if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
        header('HTTP/1.0 403 Forbidden');
        exit;
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $funding = $_POST['funding'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO projects (title, description, funding, created_by) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $description, $funding, $created_by]);
    header('Location: projects.php');
}

// Delete a project (Admin only, if not completed)
if (isset($_GET['delete']) && $_SESSION['role_id'] == 1) {
    $project_id = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM projects WHERE project_id = ? AND status != "completed"');
    $stmt->execute([$project_id]);
    header('Location: projects.php');
}
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
                    <a href="?delete=<?= $project['project_id'] ?>">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Add Project Form -->
        <?php if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): ?>
        <form method="POST">
            <input type="text" name="title" placeholder="Project Title" required>
            <textarea name="description" placeholder="Project Description" required></textarea>
            <input type="number" step="0.01" name="funding" placeholder="Funding Amount" required>
            <button type="submit" name="add_project">Add Project</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
