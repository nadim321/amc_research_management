<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';

// Validate role
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
    die("You do not have permission to access this page.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch equipment details
    $stmt = $pdo->prepare('SELECT * FROM equipment WHERE added_by = :user_id and equipment_id=:id');
        
    // Execute the statement with the sanitized session user_id
    $stmt->execute([':user_id' => $_SESSION['user_id'] , ':id' => $id]);
    $equipment = $stmt->fetch();

    if (!$equipment) {
        die("Equipment not found.");
    }

    // Check if user can update
    if ($_SESSION['role_id'] == 3 && $equipment['added_by'] != $_SESSION['user_id']) {
        die("You do not have permission to edit this equipment.");
    }
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $usage_status = $_POST['usage_status'];
    $availability = $_POST['availability'];
    if (strlen($name) > 90) {
        $error = 'Title cannot exceed 90 characters.';
    }else{
        $stmt = $pdo->prepare('UPDATE equipment SET name = ?, usage_status = ?, availability = ? WHERE equipment_id = ?');
        $stmt->execute([$name, $usage_status, $availability, $id]);

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
    <title>Update Equipment</title>
    <link rel="stylesheet" href="../createStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Update Equipment</h1>
        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= $equipment['equipment_id'] ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($equipment['name']) ?>"  required>
            <select name="usage_status">
                <option value="available" <?= $equipment['usage_status'] == 'available' ? 'selected' : '' ?>>Available</option>
                <option value="in use" <?= $equipment['usage_status'] == 'in use' ? 'selected' : '' ?>>In Use</option>
                <option value="maintenance" <?= $equipment['usage_status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
            </select>
            <label>
                <input type="checkbox" name="availability" value="1" <?= $equipment['availability'] ? 'checked' : '' ?>> Available
            </label>
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
