<?php
require '../auth.php';
require '../db.php';
require '../csrf.php';

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// Fetch researchers for team member selection
$stmt = $pdo->query('SELECT researcher_id, name FROM researchers');
$researchers = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $team_members = $_POST['team_members']; // Convert array to comma-separated string
    $funding = $_POST['funding'];

    $stmt = $pdo->prepare('INSERT INTO projects (title, description, team_members, funding) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $description, $team_members, $funding]);

    header('Location: read.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Project</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h1>Create Project</h1>
        <form method="POST">
            <input type="text" name="title" placeholder="Project Title" required>
            <textarea name="description" placeholder="Project Description" required></textarea>
            <label for="team_members">Assign Team Members:</label>
            <select name="team_members" required>
                <?php foreach ($researchers as $researcher): ?>
                    <option value="<?= $researcher['researcher_id'] ?>"><?= htmlspecialchars($researcher['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" step="0.01" name="funding" placeholder="Funding Amount" required>
            <button type="submit">Create Project</button>
        </form>
    </div>
</body>
</html>
