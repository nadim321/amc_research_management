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

// Encryption function (same as in create.php)
function encrypt_data($data, $encryption_key, $iv) {
    return openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    die('Researcher ID is required.');
}

$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM researchers WHERE researcher_id = ?');
$stmt->execute([$id]);
$researcher = $stmt->fetch();

// If the researcher doesn't exist, show an error
if (!$researcher) {
    die("Researcher not found.");
}

// Fetch the IV used for encryption (this assumes it's stored in the database)
$iv = base64_decode($researcher['iv']); // The IV was base64 encoded during encryption

// Decrypt existing data
$decrypted_name = decrypt_data($researcher['name'], $encryption_key, $iv);
$decrypted_contact_info = decrypt_data($researcher['contact_info'], $encryption_key, $iv);
$decrypted_expertise = decrypt_data($researcher['expertise'], $encryption_key, $iv);
$decrypted_assigned_projects =$researcher['assigned_projects'];
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];
    $expertise = $_POST['expertise'];
    $assigned_projects = isset($_POST['assigned_projects']) ? implode(',', $_POST['assigned_projects']) : ''; // Convert array to comma-separated string;
    if (strlen($name) > 90) {
        $error = 'Name cannot exceed 90 characters.';
    }else{
        // Encrypt updated data before saving
        $encrypted_name = encrypt_data($name, $encryption_key, $iv);
        $encrypted_contact_info = encrypt_data($contact_info, $encryption_key, $iv);
        $encrypted_expertise = encrypt_data($expertise, $encryption_key, $iv);
    

        // Update the researcher data in the database
        $stmt = $pdo->prepare('UPDATE researchers SET name = :name, contact_info = :contact_info, expertise = :expertise, assigned_projects = :assigned_projects WHERE researcher_id = :researcher_id');
        
        $stmt->execute([
            ':name' => $encrypted_name,
            ':contact_info' => $encrypted_contact_info,
            ':expertise' => $encrypted_expertise,
            ':assigned_projects' =>  $assigned_projects,
            ':researcher_id' => $id
        ]);

        // Redirect to the researchers list
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
    <title>Update Researcher</title>
    <link rel="stylesheet" href="../createStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Update Researcher</h1>
        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" value="<?= htmlspecialchars($decrypted_name) ?>"  required>
            <input type="text" name="contact_info" value="<?= htmlspecialchars($decrypted_contact_info) ?>"  required>
            <input type="text" name="expertise" value="<?= htmlspecialchars($decrypted_expertise) ?>"  required>
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
            <button type="submit">Update Researcher</button>
        </form>
    </div>
</body>
</html>
