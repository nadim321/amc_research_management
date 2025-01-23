<?php
require 'Common/auth.php'; // Ensures user is authenticated
require 'Common/db.php';

$user_role = $_SESSION['role_id'];
$user_name = $_SESSION['user_id'];

// Fetch the user's name
$stmt = $pdo->prepare('SELECT name FROM users WHERE user_id = ?');
$stmt->execute([$user_name]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Welcome, <?= htmlspecialchars($user['name']) ?></h1>
        <p>Your role: 
            <?= ($user_role == 1) ? 'Admin' : (($user_role == 2) ? 'Researcher' : 'Research Assistant') ?>
        </p>

        <nav>
            <ul style="list-style: none; padding: 0;">
                <li><a href="projects.php">Manage Projects</a></li>
                <li><a href="researchers.php">Manage Researchers</a></li>
                <li><a href="equipment.php">Manage Equipment</a></li>
                <li><a href="create_report.php">Create Reports</a></li>
                <li><a href="reports.php">View Reports</a></li>
                <?php if ($user_role == 1): // Admin-only links ?>
                    <li><a href="create_user.php">Create User</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
