<?php
require 'auth.php'; // Ensure user is authenticated
require 'db.php';

if (!isset($_GET['project_id']) || $_SESSION['role_id'] != 1) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$project_id = $_GET['project_id'];

// Fetch project details
$stmt = $pdo->prepare('SELECT * FROM projects WHERE project_id = ?');
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    echo "Project not found!";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $funding = $_POST['funding'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare('UPDATE projects SET title = ?, description = ?, funding = ?, status = ? WHERE project_id = ?');
    $stmt->execute([$title, $description, $funding, $status, $project_id]);

    header('Location: projects.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Project</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Update Project</h1>
        <form method="POST">
            <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>
            <textarea name="description" required><?= htmlspecialchars($project['description']) ?></textarea>
            <input type="number" step="0.01" name="funding" value="<?= htmlspecialchars($project['funding']) ?>" required>
            <select name="status" required>
                <option value="ongoing" <?= $project['status'] == 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
                <option value="completed" <?= $project['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <button type="submit">Update Project</button>
        </form>
        <a href="projects.php">Back to Projects</a>
    </div>
</body>
</html>
