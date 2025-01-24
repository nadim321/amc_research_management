<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    die("You do not have permission to access this page.");
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

    $stmt = $pdo->prepare('INSERT INTO projects (title, description, team_members, funding , created_by) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$title, $description, $team_members, $funding , $_SESSION['user_id']]);

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
    <link rel="stylesheet" href="../createStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Create Project</h1>
        <form method="POST">
            <input type="text" name="title" placeholder="Project Title" required>
            <textarea name="description" placeholder="Project Description" required rows="4" cols="50"></textarea>
            <label for="team_members">Assign Team Members:</label>
            <select name="team_members" required>
                <?php foreach ($researchers as $researcher): ?>
                    <option value="<?= $researcher['researcher_id'] ?>"><?= htmlspecialchars($researcher['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" step="0.01" name="funding" placeholder="Funding Amount" required>
            <button type="submit">Create Project</button>
        </form>
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
