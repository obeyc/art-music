<?php

    include "../../server/init.php";
    //require '../../vendor/autoload.php';
    // use PHPMailer\PHPMailer\src\PHPMailer;
    // use PHPMailer\PHPMailer\src\Exception;
    require_once "../../vendor/phpmailer/phpmailer/src/Exception.php";
    require_once "../../vendor/phpmailer/phpmailer/src/PHPMailer.php";
    require_once "../../vendor/phpmailer/phpmailer/src/SMTP.php";
    
    $email = "";

    $success_message = "";
    $error_message = "";

    /* Forgot Password Form Submit */

    if (isset($_POST['submit'])) {

        /* Input Validation */

        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $email = mysqli_real_escape_string($db, $_POST['email']);
        
            $stmt = $db->prepare("SELECT * FROM users WHERE Email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
        
            $result = $stmt->get_result();
        
            if ($result->num_rows === 1) {
                $reset_code = uniqid();
        
                $stmt = $db->prepare("UPDATE users SET ResetCode=? WHERE Email=?");
                $stmt->bind_param("ss", $reset_code, $email);
                $stmt->execute();
        
                $mail = new PHPMailer\PHPMailer\PHPMailer();
                $mail->isSMTP();
                $mail->Host       = 'mail.brooklyntheatre.tv';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'noreply@brooklyntheatre.tv';
                $mail->Password   = 'YG(NQG+iP0wH';
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;
        
                $mail->setFrom('noreply@artmusic.tv', 'ArtMusic TV');
                $mail->addAddress($email, "ArtMusic TV Member");
                $mail->addReplyTo('admin@artmusic.tv', 'ArtMusic TV Admin');
                $mail->addBCC('rimi.dev2020@gmail.com');
        
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'ArtMusic TV - Password Reset Link';
                $mail->Body    = '<img src="https://www.artmusic.tv/media/images/Logo.jpg" style="width: 250px; display: block; margin: 10px auto;"> <hr> <h2 style="font-size: 32px; color:#002366;">Reset Password</h2> <p style="font-size: 18px;">Hello, there has been a password reset requested for your account. Click the link below to reset your password:<p> <a style="font-size: 18px;" href="https://artmusic.tv/account/password/reset.php?r_c=' . $reset_code . '" target="_blank">Reset Password</a> <br> <p style="font-size: 18px;">If this was not you take no action.<p> <br> <p style="font-size: 18px;">Kind Regards, <br> ArtMusic TV</p>';
                $mail->AltBody = 'Your email client is not HTML compatible, to receive the correct email use a different client.';
        
                $mail->send();
        
                $success_message = "Reset Email Sent!";
            } else {
                $error_message = "Please provide your account email address.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Forgot your password? Have a reset link sent right to your email.">
    <meta name="keywords" content="artmusic, forgot password">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Forgot Password</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../../styles/master.css">
    <link rel="stylesheet" href="../../styles/custom.css">
</head>
<body class="container-fluid p-0 m-0">
    <!-- Import Navigation Bar -->
    <?php include "../../server/includes/navigation.php"; ?>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

    <div class="container">
        <div class="row">
            <div class="col-md-3"><!-- Spacer --></div>
            <div class="col-md-6">

                <?php if (!empty($success_message)) : ?>
                    <div class="alert alert-success text-center" role="alert">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)) : ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <div class="jumbotron py-4 mb-5">
                    <!-- Page Title -->
                    <h2 class="mb-3">Forgot Password</h2>
                    <p class="mb-4">
                        Enter your account email address below and check your inbox for the 
                        reset password email. Follow the link and enter your new password.
                    </p>

                    <!-- Forgot Password Form -->
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-success" name="submit">Send Reset Email</button>
                    </form>
                    
                    <div class="text-right">
                        <a class="d-inline-block mt-3" href="../signin/">Sign In</a>
                        <br>
                        <a class="d-inline-block mt-1" href="../create/">Create Account</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3"><!-- Spacer --></div>
        </div>
    </div>

    <!-- Import Footer -->
    <?php include "../../server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../../scripts/bootstrap.js"></script>

    <!-- Hide Alert Messages After Delay -->
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').css({"visibility":"hidden"});
            }, 5000);
        });
    </script>
</body>
</html>