<?php
require 'auth.php'; // Authentication check
require 'db.php';
require 'csrf.php';


// Encryption settings (must match those used in create.php)
$encryption_key = 'mySecretKey'; // This should match the key you used for encryption

// Decryption function (same as in create.php)
function decrypt_data($data, $encryption_key, $iv) {
    return openssl_decrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

// Encryption function (same as in create.php)
function encrypt_data($data, $encryption_key, $iv) {
    return openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}



// Fetch user profile
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = :user_id');
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch related projects
$stmt = $pdo->prepare('SELECT * FROM projects WHERE created_by = :user_id');
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../profileStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Profile</h1>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

        <h2>Your Projects</h2>
        <ul>
            <?php foreach ($projects as $project): 
                $iv = base64_decode($project['iv']);
                ?>
            <li><?= decrypt_data($project['title'], $encryption_key, $iv); ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
    
</body>
</html>
