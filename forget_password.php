<?php
include 'Common/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);



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

           

        try {
            //Server settings                    //Enable verbose debug output
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'amcpassreset@gmail.com'; 
            $mail->Password = 'qmbibmqfutsejtgu';      
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;



            //Recipients
            $mail->setFrom('amcpassreset@gmail.com', 'Mailer');
            $mail->addAddress($email);               
        
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject =  "Password Reset";
            $mail->Body    = $resetLink;
        
            $mail->send();
            echo 'Message has been sent </br></br>';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
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