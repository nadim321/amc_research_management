<?php
require 'auth.php'; // Ensures only logged-in users can access
require 'db.php'; // Database connection
require 'csrf.php';
require '../vendor/autoload.php';

use Ramsey\Uuid\Uuid;

// Only allow Admins to create users
if ($_SESSION['role_id'] != 1) {
    echo "Access Denied.";
    exit;
}

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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];


    if (validate_password($password)) {
        // Input validation
        if (empty($name) || empty($email) || empty($password) || empty($role_id)) {
            echo "All fields are required.";
        } else {
            // Hash the password
            $password = password_hash($password, PASSWORD_BCRYPT);

            // Prepare the query using named parameters
            $stmt = $pdo->prepare('INSERT INTO users (user_id , name, email, password, role_id) VALUES (:id , :name, :email, :password, :role_id)');

            // Execute the query with an associative array of named parameters
            $stmt->execute([
                ':id' => Uuid::uuid4()->toString(),
                ':name' => $name,
                ':email' => $email,
                ':password' => $password, // Ensure the password is hashed using password_hash()
                ':role_id' => $role_id
            ]);
            echo "User created successfully.";
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
    <title>Create User</title>
    <link rel="stylesheet" href="../createStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Create User</h1>
        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name"  required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role_id" required>
                <option value="">Select Role</option>
                <option value="1">Admin</option>
                <option value="2">Researcher</option>
                <option value="3">Research Assistant</option>
            </select>
            <button type="submit">Create User</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
