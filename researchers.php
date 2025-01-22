<?php
require 'auth.php';
require 'db.php';

// Fetch researchers
$stmt = $pdo->query('SELECT * FROM users WHERE role_id = 2'); // Only Researchers
$researchers = $stmt->fetchAll();

// Add a new researcher (Admin only)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_researcher'])) {
    checkRole(1); // Admin role
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = 2; // Researcher role

    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, $password, $role_id]);
    header('Location: researchers.php');
}

// Delete a researcher (Admin only)
if (isset($_GET['delete']) && checkRole(1)) {
    $user_id = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
    $stmt->execute([$user_id]);
    header('Location: researchers.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Researchers Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Researchers Management</h1>
        
        <!-- Display Researchers -->
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($researchers as $researcher): ?>
            <tr>
                <td><?= htmlspecialchars($researcher['name']) ?></td>
                <td><?= htmlspecialchars($researcher['email']) ?></td>
                <td>
                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <a href="?delete=<?= $researcher['user_id'] ?>">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Add Researcher Form (for Admin) -->
        <?php if ($_SESSION['role_id'] == 1): ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="add_researcher">Add Researcher</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
