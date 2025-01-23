<?php
require 'Common/auth.php'; // Ensures only logged-in users can access
require 'Common/db.php'; // Database connection

// Only allow Admins to create users
if ($_SESSION['role_id'] != 1) {
    echo "Access Denied.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];

    // Input validation
    if (empty($name) || empty($email) || empty($password) || empty($role_id)) {
        echo "All fields are required.";
    } else {
        // Hash the password
        $password = hash('sha256', $password);

        // Insert into the database
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $password, $role_id]);
        echo "User created successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Create User</h1>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
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
