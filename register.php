<?php
require 'db.php';

$error = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role_id = 2; // Default to Researcher

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email is already in use.';
        } else {
            $password = password($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $password, $role_id]);
            header('Location: login.php');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Register</h1>

        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>

        <a href="login.php">Already have an account? Login</a>
    </div>
</body>
</html>
