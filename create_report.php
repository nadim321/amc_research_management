<?php
session_start(); // Ensure session is started
// Check if the user is logged in and if the role_name is set in the session
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 1  && $_SESSION['role_id'] != 3)) {
    echo "You do not have permission to create a report.";
    exit; // Stop the script execution
}

require 'db.php'; // Database connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_type = $_POST['report_type'];
    $content = $_POST['content'];
    $created_by = $_SESSION['user_id']; // Get the logged-in user's ID

    // Insert the report into the database
    $stmt = $pdo->prepare('INSERT INTO reports (report_type, content, created_by) VALUES (?, ?, ?)');
    $stmt->execute([$report_type, $content, $created_by]);

    // Redirect back to the reports page after creating the report
    header('Location: reports.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div >
        <h1>Create Report</h1>
        <form method="POST">
            <label for="report_type">Report Type</label>
            <select name="report_type" id="report_type" required>
                <option value="research_progress">Research Progress</option>
                <option value="project_funding">Project Funding</option>
                <option value="equipment_usage">Equipment Usage</option>
            </select>

            <label for="content">Content</label>
            <textarea name="content" id="content" rows="5" required></textarea>

            <button type="submit">Create Report</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
