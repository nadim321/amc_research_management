
<?php
require '../auth.php';
require '../db.php';
require '../csrf.php';

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM researchers WHERE researcher_id = ?');
$stmt->execute([$id]);
$researcher = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];
    $expertise = $_POST['expertise'];
    $assigned_projects = $_POST['assigned_projects'];

    $stmt = $pdo->prepare('UPDATE researchers SET name = ?, contact_info = ?, expertise = ?, assigned_projects = ? WHERE researcher_id = ?');
    $stmt->execute([$name, $contact_info, $expertise, $assigned_projects, $id]);

    header('Location: read.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Researcher</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="form-container">
        <h1>Update Researcher</h1>
        <form method="POST">
            <input type="text" name="name" value="<?= htmlspecialchars($researcher['name']) ?>" required>
            <input type="text" name="contact_info" value="<?= htmlspecialchars($researcher['contact_info']) ?>" required>
            <input type="text" name="expertise" value="<?= htmlspecialchars($researcher['expertise']) ?>" required>
            <input type="text" name="assigned_projects" value="<?= htmlspecialchars($researcher['assigned_projects']) ?>">
            <button type="submit">Update Researcher</button>
        </form>
    </div>
</body>
</html>
