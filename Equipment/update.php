<?php
require 'auth.php'; // Ensure user is authenticated
require 'db.php';

if (!isset($_GET['equipment_id']) || $_SESSION['role_id'] != 1) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$equipment_id = $_GET['equipment_id'];

// Fetch equipment details
$stmt = $pdo->prepare('SELECT * FROM equipment WHERE equipment_id = ?');
$stmt->execute([$equipment_id]);
$equipment = $stmt->fetch();

if (!$equipment) {
    echo "Equipment not found!";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];

    $stmt = $pdo->prepare('UPDATE equipment SET name = ?, description = ?, quantity = ? WHERE equipment_id = ?');
    $stmt->execute([$name, $description, $quantity, $equipment_id]);

    header('Location: equipment.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Equipment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="content-container">
        <h1>Update Equipment</h1>
        <form method="POST">
            <input type="text" name="name" value="<?= htmlspecialchars($equipment['name']) ?>" required>
            <textarea name="description" required><?= htmlspecialchars($equipment['description']) ?></textarea>
            <input type="number" name="quantity" value="<?= htmlspecialchars($equipment['quantity']) ?>" required>
            <button type="submit">Update Equipment</button>
        </form>
        <a href="equipment.php">Back to Equipment</a>
    </div>
</body>
</html>
