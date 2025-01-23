<?php
require 'auth.php'; // Ensures user is authenticated
require 'db.php';

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
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div >
        <h1>Welcome, <?= htmlspecialchars($user['name']) ?></h1>
        <p>Your role: 
            <?= ($user_role == 1) ? 'Admin' : (($user_role == 2) ? 'Researcher' : 'Research Assistant') ?>
        </p>

        <nav>
            <div class="menu-container">
                <!-- Project Section -->
                <div class="menu-item">
                    <h2>Projects</h2>
                    <ul>
                        <li><a href="../Projects/create.php">Create Project</a></li>
                        <li><a href="../Projects/read.php">View Projects</a></li>
                    </ul>
                </div>

                <!-- Equipment Section -->
                <div class="menu-item">
                    <h2>Equipment</h2>
                    <ul>
                        <li><a href="../Equipment/create.php">Add Equipment</a></li>
                        <li><a href="../Equipment/read.php">View Equipment</a></li>
                    </ul>
                </div>

                <!-- Report Section -->
                <div class="menu-item">
                    <h2>Reports</h2>
                    <ul>
                        <li><a href="../Report/create.php">Generate Report</a></li>
                        <li><a href="../Report/read.php">View Reports</a></li>
                    </ul>
                </div>

                <!-- Researchers Section -->
                <div class="menu-item">
                    <h2>Researchers</h2>
                    <ul>
                        <li><a href="../Researchers/create.php">Add Researcher</a></li>
                        <li><a href="../Researchers/read.php">View Researchers</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</body>
</html>
