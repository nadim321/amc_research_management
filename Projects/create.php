<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    die("You do not have permission to access this page.");
    exit;
}

// Encryption settings
$encryption_key = 'mySecretKey'; // This should match the key you used for encryption
$iv_length = openssl_cipher_iv_length('aes-256-cbc'); // AES encryption with CBC mode
// Decryption function (same as in create.php for researchers)
function decrypt_data($data, $encryption_key, $iv) {
    return openssl_decrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

// Encryption function (same as in create.php for researchers)
function encrypt_data($data, $encryption_key, $iv) {
    return openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

// Fetch researchers for team member selection
$stmt = $pdo->query('SELECT researcher_id, name, iv FROM researchers');
$researchers = $stmt->fetchAll();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $team_members = $_POST['team_members']; // Convert array to comma-separated string
    $funding = $_POST['funding'];

    if (strlen($title) > 90) {
        $error = 'Title cannot exceed 90 characters.';
    }else if(strlen($description) > 300){
        $error = 'Description cannot exceed 90 characters.';
    }else{

        $iv = openssl_random_pseudo_bytes($iv_length);
        // Generate a random IV for encryption (you should store this IV in the database)
      
    
        // Encrypt the sensitive fields
        $encrypted_title = encrypt_data($title, $encryption_key, $iv);
        $encrypted_description = encrypt_data($description, $encryption_key, $iv);
        $encrypted_team_members = encrypt_data($team_members, $encryption_key, $iv);
        $encrypted_funding = $funding;
    
        // Store the project in the database with the IV
        $stmt = $pdo->prepare('INSERT INTO projects (title, description, team_members, funding, created_by, iv) 
                               VALUES (:title, :description, :team_members, :funding, :created_by, :iv)');
        
        // Bind values securely to the query
        $stmt->execute([
            ':title' => $encrypted_title,
            ':description' => $encrypted_description,
            ':team_members' => $team_members,
            ':funding' => $encrypted_funding,
            ':created_by' => $_SESSION['user_id'],
            ':iv' => base64_encode($iv) // Store IV as base64 for easier retrieval
        ]);
    
        // Redirect to the projects list page
        header('Location: read.php');
        exit;
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Project</title>
    <link rel="stylesheet" href="../createStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Create Project</h1>
        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
        <input type="text" name="title" placeholder="Project Title"  value="<?= htmlspecialchars('') ?>" required>
        <textarea name="description" placeholder="Project Description"  value="<?= htmlspecialchars('') ?>" required rows="4" cols="50"></textarea>
            <label for="team_members">Assign Team Members:</label>
            <select name="team_members" > <!-- Multiple selection for team members -->
                <?php foreach ($researchers as $researcher): 
                    $rIv = base64_decode($researcher['iv']);
                    $researcherName = decrypt_data($researcher['name'], $encryption_key, $rIv);
                    ?>
                    <option value="<?= $researcher['researcher_id'] ?>">
                        <?= $researcherName ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="funding" value="<?= htmlspecialchars('') ?>" placeholder="Funding Amount" required>
            <button type="submit">Create Project</button>
        </form>
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
