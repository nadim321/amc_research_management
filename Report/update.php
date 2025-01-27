<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Check if user has permission (Admin only)
if ($_SESSION['role_id'] != 1) {
    echo "You do not have permission to update reports.";
    exit;
}

// Fetch the report to edit
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE report_id = ?");
    $stmt->execute([$_GET['id']]);
    $report = $stmt->fetch();

    if (!$report) {
        echo "Report not found.";
        exit;
    }
}
$error ="";
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
            
    if (strlen($title) > 90) {
        $error = 'Title cannot exceed 90 characters.';
    }else if(strlen($description) > 300){
        $error = 'Description cannot exceed 300 characters.';
    }else{

        $stmt = $pdo->prepare('UPDATE reports SET title = :title, description = :description WHERE report_id = :report_id');
            
        // Bind values securely to the query
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':report_id' => $id,
        ]);

        header('Location: read.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Report</title>
    <link rel="stylesheet" href="../createStyle.css">
</head>
<body>
    <div class="container">
        <h1>Update Report</h1>
        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="title" value="<?= htmlspecialchars($report['title']) ?>"  required>
            <textarea name="description"  required><?= htmlspecialchars($report['description']) ?></textarea>
            <button type="submit">Update Report</button>
        </form>
    </div>
</body>
</html>
