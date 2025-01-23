<?php
require 'auth.php'; // Authentication check
require 'db.php';
require '../csrf.php';

// Fetch user profile
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch related projects
$stmt = $pdo->prepare('SELECT * FROM projects WHERE created_by = ?');
$stmt->execute([$_SESSION['user_id']]);
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="form-container">
        <h1>Profile</h1>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

        <h2>Your Projects</h2>
        <ul>
            <?php foreach ($projects as $project): ?>
            <li><?= htmlspecialchars($project['title']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
