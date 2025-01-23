<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    die("You do not have permission to access this page.");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM projects WHERE project_id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();

// Fetch researchers for team member selection
$stmt = $pdo->query('SELECT researcher_id, name FROM researchers');
$researchers = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $team_members = $_POST['team_members'];
    $funding = $_POST['funding'];

    $stmt = $pdo->prepare('UPDATE projects SET title = ?, description = ?, team_members = ?, funding = ? WHERE project_id = ?');
    $stmt->execute([$title, $description, $team_members, $funding, $id]);

    header('Location: read.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Project</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="form-container">
        <h1>Update Project</h1>
        <form method="POST">
            <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>
            <textarea name="description" required><?= htmlspecialchars($project['description']) ?></textarea>
            <label for="team_members">Update Team Members:</label>
            <select name="team_members" required>
                <?php foreach ($researchers as $researcher): ?>
                    <option value="<?= $researcher['researcher_id'] ?>" <?= in_array($researcher['researcher_id'], explode(',', $project['team_members'])) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($researcher['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" step="0.01" name="funding" value="<?= htmlspecialchars($project['funding']) ?>" required>
            <button type="submit">Update Project</button>
        </form>
    </div>
</body>
</html>
