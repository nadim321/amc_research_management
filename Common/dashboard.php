<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../dashboardStyle.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, Researcher</h1>
        <p class="role">Your role: <strong>Researcher</strong></p>

        <div class="card">
            <h3>Projects</h2>
            <ul>
                <li><a href="../Projects/create.php">Create Project</a></li>
                <li><a href="../Projects/read.php">View Projects</a></li>
            </ul>
        </div>

        <div class="card">
            <h3>Equipment</h2>
            <ul>
                <li><a href="../Equipment/create.php">Add Equipment</a></li>
                <li><a href="../Equipment/read.php">View Equipment</a></li>
            </ul>
        </div>

        <div class="card">
            <h3>Reports</h2>
            <ul>
                <li><a href="../Report/create.php">Generate Report</a></li>
                <li><a href="../Report/read.php">View Reports</a></li>
            </ul>
        </div>

        <div class="card">
            <h3>Researchers</h2>
            <ul>
                <li><a href="../Researchers/create.php">Add Researcher</a></li>
                <li><a href="../Researchers/create.php">View Researchers</a></li>
            </ul>
        </div>

        <div class="footer-links">
            <a href="../Common/create_user.php" class="btn">Create User</a>
            <a href="../change_password.php" class="btn">Change Password</a>
            <a href="../logout.php" class="btn btn-logout">Logout</a>
        </div>
    </div>
</body>
</html>
