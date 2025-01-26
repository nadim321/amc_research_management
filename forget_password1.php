<?php
include 'Common/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    if ($user) {
        $token = bin2hex(openssl_random_pseudo_bytes(32)); // Generate a secure token
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save the token in the database
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token )");
        $stmt->execute(['email' => $email, 'token' => $token ]);

        // Send the reset link via email
        $resetLink = "http://localhost/amc_research_management/reset_password.php?token=$token";
        // mail($email, "Password Setup Link", "Click here to set your password: $resetLink");

            $to = $email;
            $subject = "Password Reset";
            $message =  $resetLink;
            $headers = "From: amcpassreset@gmail.com";

            if (mail($to, $subject, $message, $headers)) {
                echo "A password setup link has been sent to your email.<br/>";
            } else {
                echo "Failed to send email.<br/>";
            }
    } else {
        echo "Email not found.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Password Setup Link</button>
</form>

<?php 
    if(isset($resetLink)){ ?>
    <div class="login-container">
    <a href="<?php echo $resetLink ; ?>"> <?php echo $resetLink ; ?></a>
    </div>
<?php } ?>