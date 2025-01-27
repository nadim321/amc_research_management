<?php
require '../Common/auth.php';
require '../Common/db.php';
require '../Common/csrf.php';
require '../vendor/autoload.php';

use Ramsey\Uuid\Uuid;

// Check if the user is Admin or Research Assistant
if ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3) {
    die("You do not have permission to access this page.");
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $usage_status = $_POST['usage_status'];
    $availability = $_POST['availability'];
    $added_by = $_SESSION['user_id'];

    
    if (strlen($name) > 90) {
        $error = 'Title cannot exceed 90 characters.';
    }else{
        $stmt = $pdo->prepare('INSERT INTO equipment (equipment_id , name, usage_status, availability, added_by) 
                                VALUES (:id, :name, :usage_status, :availability, :added_by)');
            // Execute the statement with the sanitized data
            $stmt->execute([
                ':id' => Uuid::uuid4()->toString(),
                ':name' => $name,
                ':usage_status' => $usage_status,
                ':availability' => $availability,
                ':added_by' => $added_by
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
    <title>Create Equipment</title>
    <link rel="stylesheet" href="../createStyle.css">
</head>
<body>
    <div class="form-container">
        <h1>Create Equipment</h1>
        <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Equipment Name"  required>
            <select name="usage_status">
                <option value="available">Available</option>
                <option value="in use">In Use</option>
                <option value="maintenance">Maintenance</option>
            </select>
            <label>
                <input type="checkbox" name="availability" value="1" checked> Available
            </label>
            <button type="submit">Add Equipment</button>
        </form>
        <a href="../Common/dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
