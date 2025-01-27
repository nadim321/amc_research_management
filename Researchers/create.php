<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';
require '../vendor/autoload.php';

use Ramsey\Uuid\Uuid;

// Restrict to Admin and Researchers
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2) {
    die("You do not have permission to access this page.");
    exit;
}

// Encryption settings
$encryption_key = 'mySecretKey'; // Should be a secure, random string (store securely)
$iv_length = openssl_cipher_iv_length('aes-256-cbc'); // AES encryption with CBC mode


// Encryption function
function encrypt_data($data, $encryption_key, $iv) {
    return openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

// Decryption function
function decrypt_data($data, $encryption_key, $iv) {
    return openssl_decrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];
    $expertise = $_POST['expertise'];
    $assigned_projects = isset($_POST['assigned_projects']) ? implode(',', $_POST['assigned_projects']) : ''; // Convert array to comma-separated string
    $iv = openssl_random_pseudo_bytes($iv_length);

    if (strlen($name) > 90) {
        $error = 'Name cannot exceed 90 characters.';
    }else{

        // Encrypt the data
        $encrypted_name = encrypt_data($name, $encryption_key, $iv);
        $encrypted_contact_info = encrypt_data($contact_info, $encryption_key, $iv);
        $encrypted_expertise = encrypt_data($expertise, $encryption_key, $iv);


        // Store the encrypted data and IV in the database
        $stmt = $pdo->prepare('INSERT INTO researchers (researcher_id , name, contact_info, expertise, assigned_projects, iv) 
                                VALUES (:id, :name, :contact_info, :expertise, :assigned_projects, :iv)');

        // Bind values securely to the query
        $stmt->execute([
            ':id' => Uuid::uuid4()->toString(),
            ':name' => $encrypted_name,
            ':contact_info' => $encrypted_contact_info,
            ':expertise' => $encrypted_expertise,
            ':assigned_projects' => $assigned_projects,
            ':iv' => base64_encode($iv), // Store IV as base64-encoded string
        ]);

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
    <title>Create Researcher</title>
    <link rel="stylesheet" href="../createStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Create Researcher</h1>
        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Researcher Name" value="<?= htmlspecialchars('') ?>"  required>
            <input type="text" name="contact_info" placeholder="Contact Information" value="<?= htmlspecialchars('') ?>"  required>
            <input type="text" name="expertise" placeholder="Area of Expertise" value="<?= htmlspecialchars('') ?>"  required>

            <!-- Multi-select dropdown for Assigned Projects -->
            <select name="assigned_projects[]" required multiple>
                <?php
                // Fetching all projects from the database to display in the dropdown
                $stmt = $pdo->query('SELECT project_id, title , iv FROM projects');
                $projects = $stmt->fetchAll();
                foreach ($projects as $project):
                    $pIv = base64_decode($project['iv']);
                    $projectTitle = decrypt_data($project['title'], $encryption_key, $pIv);
                ?>
                    <option value="<?= $project['project_id'] ?>"><?= htmlspecialchars($projectTitle) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Create Researcher</button>
        </form>
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
