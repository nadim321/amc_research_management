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

// Decryption function
function decrypt_data($data, $encryption_key, $iv) {
    return openssl_decrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

// Fetch all projects, including the IV used for encryption
$stmt = $pdo->query('SELECT pr.project_id, pr.title, pr.description,  rc.name, pr.team_members, pr.funding, pr.status, pr.iv 
                     FROM projects pr
                     LEFT JOIN researchers rc on rc.researcher_id = pr.team_members');
$projects = $stmt->fetchAll();
$iv_length = openssl_cipher_iv_length('aes-256-cbc'); // AES encryption with CBC mode
// Decrypt the project data
foreach ($projects as &$project) {
    // Decode the base64 encoded IV
    $iv = base64_decode($project['iv']);
    
    // Decrypt the fields
    $project['title'] = decrypt_data($project['title'], $encryption_key, $iv);
    $project['description'] = decrypt_data($project['description'], $encryption_key, $iv);
    // $project['team_members'] = decrypt_data($project['name'], $encryption_key, $iv);

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
    <link rel="stylesheet" href="../readStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Projects</h1>
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Team Members</th>
                <th>Funding</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($projects as &$project):
                
                $ids= $project['team_members'];
                $stmt2 = $pdo->query("Select * from researchers where researcher_id in ($ids)");
                $researchers = $stmt2->fetchAll();
                $researcherTitle = "";
                foreach ($researchers as $researcher){
                    $rIv = base64_decode($researcher['iv']);
                    $researcherTitle = $researcherTitle . decrypt_data($researcher['name'], $encryption_key, $rIv) .", ";
                }

                ?>
                
            <tr>
                <td><?= htmlspecialchars($project['title']) ?></td>
                <td><?= htmlspecialchars($project['description']) ?></td>
                <td><?= htmlspecialchars($researcherTitle) ?></td>
                <td><?= htmlspecialchars($project['funding']) ?></td>
                <td><?= htmlspecialchars($project['status']) ?></td>
                <td>
                    <?php if ($_SESSION['role_id'] == 1): ?>
                    <a href="delete.php?id=<?php echo $project['project_id'] ?>">Delete</a>
                    <?php endif; ?>
                    <a href="update.php?id=<?php echo $project['project_id'] ?>">Edit</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table></br>
        <a href="create.php">Add New Project</a>
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
