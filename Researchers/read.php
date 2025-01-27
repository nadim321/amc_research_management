<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    die("You do not have permission to access this page.");
    exit;
}

// Encryption settings (must match those used in create.php)
$encryption_key = 'mySecretKey'; // This should match the key you used for encryption
$iv_length = openssl_cipher_iv_length('aes-256-cbc'); // AES encryption with CBC mode

// Decryption function (same as in create.php)
function decrypt_data($data, $encryption_key, $iv) {
    return openssl_decrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

// Fetch all researchers from the database
$stmt = $pdo->query('SELECT * FROM researchers');
$researchers = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Researchers</title>
    <link rel="stylesheet" href="../readStyle.css">    
</head>
<body>
    <div class="form-container">
        <h1>Researchers</h1>
        <table>
            <tr>
                <th>Name</th>
                <th>Contact Info</th>
                <th>Expertise</th>
                <th>Assigned Projects</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($researchers as $researcher):               
                ?>
            <?php
                // Decrypt the sensitive fields
                $iv = base64_decode($researcher['iv']);

                $decrypted_name = decrypt_data($researcher['name'], $encryption_key, $iv);
                $decrypted_contact_info = decrypt_data($researcher['contact_info'], $encryption_key, $iv);
                $decrypted_expertise = decrypt_data($researcher['expertise'], $encryption_key, $iv);
                $decrypted_assigned_projects = $researcher['assigned_projects'];
                                
                $stmt2 = $pdo->query("Select * from projects where project_id in ($decrypted_assigned_projects)");
                $projects = $stmt2->fetchAll();
                $projectTitle = "";
                foreach ($projects as $project){
                    $pIv = base64_decode($project['iv']);
                    $projectTitle = $projectTitle . decrypt_data($project['title'], $encryption_key, $pIv) .", ";
                }
           ?>
            <tr>
                <td><?= htmlspecialchars($decrypted_name, ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($decrypted_contact_info, ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($decrypted_expertise, ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($projectTitle, ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <a href="update.php?id=<?= $researcher['researcher_id'] ?>">Edit</a>
                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <a href="delete.php?id=<?= $researcher['researcher_id'] ?>">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table><br>
        <a href="create.php">Add New Researcher</a>
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
