<?php
require 'Common/auth.php';
require 'Common/db.php';
require 'Common/csrf.php';


function validate_password($password) {
    // Regular expression for password validation
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

    if (preg_match($pattern, $password)) {
        return true; // Password is valid
    } else {
        return false; // Password is invalid
    }
}
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    if (validate_password($new_password)) {
        // Fetch user details
        $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (password_verify($current_password, $user['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE user_id = ?');
            $stmt->execute([$hashed_password, $_SESSION['user_id']]);

            echo "Password updated successfully.";
    } else {
        $error = "Current password is incorrect.";
    }
    } else {
        $error = "Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, one number, and one special character."; 
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="createStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Change Password</h1>
        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="conf_password" placeholder="Confirm Password" required>
            <button type="submit">Change Password</button>
        </form>
        <a href="Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
