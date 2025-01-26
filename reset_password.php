<?php
include 'Common/db.php';


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

if (isset($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']);

    // Validate token
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $reset = $stmt->fetch();

    if ($reset) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = htmlspecialchars($_POST['password']);

            if (validate_password($password)) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Update the user's password
                $stmt = $pdo->prepare("UPDATE users SET password = :password, is_password_set = 1 WHERE email = :email");
                $stmt->execute(['password' => $hashedPassword, 'email' => $reset['email']]);
    
                // Delete the reset token
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
                $stmt->execute(['email' => $reset['email']]);
    
                echo "Password has been set successfully. <a href='index.php'>Login</a>";
                exit();
            } else {
                $error = "Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, one number, and one special character."; 
            }

        }
    } else {
        echo "Invalid or expired token.";
        exit();
    }
} else {
    echo "No token provided.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
<form method="POST">
    <input type="password" name="password" placeholder="Enter new password" required>
    <button type="submit">Set Password</button>
</form>
