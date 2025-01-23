<?php
require 'Common/auth.php';
require 'Common/db.php';
require 'Common/csrf.php';

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];
    $expertise = $_POST['expertise'];
    $assigned_projects = $_POST['assigned_projects']; // comma-separated list of project IDs

    $stmt = $pdo->prepare('INSERT INTO researchers (name, contact_info, expertise, assigned_projects) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $contact_info, $expertise, $assigned_projects]);

    header('Location: read.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Researcher</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h1>Create Researcher</h1>
        <form method="POST">
            <input type="text" name="name" placeholder="Researcher Name" required>
            <input type="text" name="contact_info" placeholder="Contact Information" required>
            <input type="text" name="expertise" placeholder="Area of Expertise" required>
            <input type="text" name="assigned_projects" placeholder="Assigned Projects (comma-separated IDs)">
            <button type="submit">Create Researcher</button>
        </form>
    </div>
</body>
</html>
