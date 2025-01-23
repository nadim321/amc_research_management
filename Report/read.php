<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Check if user has permission (Admin or Researcher)
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    echo "You do not have permission to view reports.";
    exit;
}

// Fetch reports
$stmt = $pdo->query("SELECT reports.*, users.name AS created_by_name FROM reports JOIN users ON reports.created_by = users.user_id");
$reports = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Reports</title>
    <link rel="stylesheet" href="../style.css">  
</head>
<body>
    <div class="container">
        <h1>Reports</h1>
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($reports as $report): ?>
            <tr>
                <td><?= htmlspecialchars($report['title']) ?></td>
                <td><?= htmlspecialchars($report['description']) ?></td>
                <td><?= htmlspecialchars($report['created_by_name']) ?></td>
                <td><?= htmlspecialchars($report['created_at']) ?></td>
                <td>
                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <a href="delete_report.php?id=<?= $report['report_id'] ?>">Delete</a>
                    <?php endif; ?>
                    <a href="update_report.php?id=<?= $report['report_id'] ?>">Edit</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
