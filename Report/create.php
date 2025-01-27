<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Check if user has permission (Admin or Researcher)
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    echo "You do not have permission to create reports.";
    exit;
}
$error ="";
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];

        
    if (strlen($title) > 90) {
        $error = 'Title cannot exceed 90 characters.';
    }else if(strlen($description) > 300){
        $error = 'Description cannot exceed 90 characters.';
    }else{
        $stmt = $pdo->prepare("INSERT INTO reports (title, description, created_by) VALUES (:title, :description, :created_by)");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':created_by' => $created_by
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
    <title>Create Report</title>
    <link rel="stylesheet" href="../createStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Create Report</h1>
        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="title" placeholder="Report Title"  required>
            <textarea name="description" placeholder="Report Description"  required></textarea>
            <button type="submit">Generate Report</button>
        </form>
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
