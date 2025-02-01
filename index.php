<?php
require 'Common/db.php';
require 'Common/csrf.php';

session_start();
$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email =  $_POST['email'];
    $password =  $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email); // Bind user input securely
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        if (!$user['is_password_set']) {
            // Redirect to "Forgot Password" page
            header("Location: forget_password.php?email=$email");
            exit();
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role_id'] = $user['role_id'];
        header('Location: Common/dashboard.php');
    } else {
        $error = 'Invalid email or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <!-- Left Side -->
        <div class="left-panel">
            <h1>Welcome to website</h1>
            <p>My Website</p>
        </div>

        <!-- Right Side -->
        <div class="right-panel">
            <h2>User Login</h2>

            <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
