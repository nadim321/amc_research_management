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
$iv = "mySecretKey12345";

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
$stmt = $pdo->query('SELECT researcher_id, name FROM researchers');
$researchers = $stmt->fetchAll();

$project['title'] = decrypt_data($project['title'], $encryption_key, $iv);
$project['description'] = decrypt_data($project['description'], $encryption_key, $iv);
$project['team_members'] = decrypt_data($project['team_members'], $encryption_key, $iv);
$project['funding'] = $project['funding'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Encrypt data before saving to the database
    $title = $_POST['title'];
    $description = $_POST['description'];
    $team_members = $_POST['team_members'];
    $funding = $_POST['funding'];

    // Encrypt the fields
    $encrypted_title = encrypt_data($title, $encryption_key, $iv); 
    $encrypted_description = encrypt_data($description, $encryption_key, $iv);


    // Prepare the SQL update statement with encrypted values
    $stmt = $pdo->prepare('UPDATE projects SET title = :title, description = :description, 
                           team_members = :team_members, funding = :funding
                           WHERE project_id = :id');
    $stmt->execute([
        ':title' => $encrypted_title,
        ':description' => $encrypted_description,
        ':team_members' => $team_members,
        ':funding' => $funding,
        ':id' => $id
    ]);

    header('Location: read.php');
    exit;
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
        <form method="POST">
            <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" maxlength="90" required>
            <textarea name="description" maxlength="300" required><?= htmlspecialchars($project['description']) ?></textarea>
            <label for="team_members">Update Team Members:</label>
            <select name="team_members" required>
                <?php foreach ($researchers as &$researcher): 
                   $encrypted_researcher = decrypt_data($researcher['name'], $encryption_key, $iv);
                    ?>
                    <option value="<?= $researcher['researcher_id'] ?>" <?= in_array($researcher['researcher_id'], explode(',', $project['team_members'])) ? 'selected' : '' ?>>
                        <?= $encrypted_researcher; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number"  name="funding" value="<?= htmlspecialchars($project['funding']) ?>" required>
            <button type="submit">Update Project</button>
        </form>
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
