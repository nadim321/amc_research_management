<?php
require 'auth.php'; // Ensure user is authenticated
require 'db.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fetch reports from the database
$stmt = $pdo->prepare('SELECT reports.*, users.name AS created_by FROM reports 
                       JOIN users ON reports.created_by = users.user_id');
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all reports
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="div-container">
        <h1>Reports</h1>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Content</th>
                    <th>Created By</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reports) && is_array($reports)): ?>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?= htmlspecialchars($report['report_type']) ?></td>
                            <td><?= htmlspecialchars($report['content']) ?></td>
                            <td><?= htmlspecialchars($report['created_by']) ?></td>
                            <td><?= htmlspecialchars($report['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No reports found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
