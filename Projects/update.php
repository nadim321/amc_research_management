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

// Decrypt the existing data before displaying it in the form
$iv_length = openssl_cipher_iv_length('aes-256-cbc'); // AES encryption with CBC mode

function decrypt_data($data, $encryption_key, $iv) {
    return openssl_decrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

// Encryption function (same as in create.php)
function encrypt_data($data, $encryption_key, $iv) {
    return openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

// Fetch project details for the update form
$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM projects WHERE project_id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();

// Fetch researchers for team member selection
$stmt = $pdo->query('SELECT researcher_id, name , iv FROM researchers');
$researchers = $stmt->fetchAll();

$iv = base64_decode($project['iv']); // The IV was base64 encoded during encryption
$project['title'] = decrypt_data($project['title'], $encryption_key, $iv);
$project['description'] = decrypt_data($project['description'], $encryption_key, $iv);
$project['team_members'] = decrypt_data($project['team_members'], $encryption_key, $iv);
$project['funding'] = $project['funding'];
$project['status'] = $project['status'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Encrypt data before saving to the database
    $title = $_POST['title'];
    $description = $_POST['description'];
    $team_members =  isset($_POST['team_members']) ? implode(',', $_POST['team_members']) : ''; // Convert array to comma-separated string
    $funding = $_POST['funding'];
    $status = $_POST['status'];

    
    if (strlen($title) > 90) {
        $error = 'Title cannot exceed 90 characters.';
    }else if(strlen($description) > 300){
        $error = 'Description cannot exceed 300 characters.';
    }else if($funding > 1000){
        $error = 'Funding cannot exceed 1000.';
    }else{
        // Encrypt the fields
        $encrypted_title = encrypt_data($title, $encryption_key, $iv); 
        $encrypted_description = encrypt_data($description, $encryption_key, $iv);


        // Prepare the SQL update statement with encrypted values
        $stmt = $pdo->prepare('UPDATE projects SET title = :title, description = :description, 
                            team_members = :team_members, funding = :funding, status = :status
                            WHERE project_id = :id');
        $stmt->execute([
            ':title' => $encrypted_title,
            ':description' => $encrypted_description,
            ':team_members' => $team_members,
            ':funding' => $funding,
            ':status' => $status,
            ':id' => $id
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
    <title>Update Project</title>
    <link rel="stylesheet" href="../createStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Update Project</h1>
        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>"  required>
            <textarea name="description"  required><?= htmlspecialchars($project['description']) ?></textarea>
            <label for="team_members">Update Team Members:</label>
            <select name="team_members[]" multiple required>
                <?php foreach ($researchers as &$researcher): 
                    $rIv = base64_decode($researcher['iv']);
                   $encrypted_researcher = decrypt_data($researcher['name'], $encryption_key, $rIv);
                    ?>
                    <option value="<?= $researcher['researcher_id'] ?>" <?= in_array($researcher['researcher_id'], explode(',', $project['team_members'])) ? 'selected' : '' ?>>
                        <?= $encrypted_researcher; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number"  name="funding" value="<?= htmlspecialchars($project['funding']) ?>" required>
            <select name="status">
                <option value="ongoing" <?= $project['status'] == 'ongoing' ? 'selected' : '' ?>>Ongoing</option>
                <option value="completed" <?= $project['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <button type="submit">Update Project</button>
        </form>
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
