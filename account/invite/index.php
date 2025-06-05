<?php
    include "../../server/init.php";

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../../vendor/phpmailer/phpmailer/src/SMTP.php';

    $email = "";

    $success_message = "";
    $error_message = "";

    /* Inite Form Submit */

    if (isset($_POST['submit'])) {

        /* Input Validation */

        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $email = mysqli_real_escape_string($db, $_POST['email']);
        } else {
            $error_message = "Please provide an email address.";
        }

        /* Send Invite Email */

        require '../../vendor/autoload.php';
        
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = 'mail.brooklyntheatre.tv'; // TODO: Update Email Host
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@brooklyntheatre.tv'; // TODO: Update Email Username
        $mail->Password   = 'YG(NQG+iP0wH'; // TODO: Update Email Password
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('noreply@artmusic.tv', 'ArtMusic TV');
        $mail->addAddress($email, "New ArtMusic TV Member");
        $mail->addReplyTo('admin@artmusic.tv', 'ArtMusic TV Admin');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'ArtMusic TV - Invite from a Friend';
        $mail->Body    = '<img src="https://www.artmusic.tv/media/images/Logo.jpg" style="width: 250px; display: block; margin: 10px auto;"> <hr> <h2 style="font-size: 32px; color:#002366;">Your Special Invite</h2> <p style="font-size: 18px;">Hello, one of your friends has requested that we send you this invite to join ArtMusic TV. Click the following link to create your account and enjoy the best music SA has to offer:<p> <br> <a style="font-size: 18px;" href="https://www.artmusic.tv/account/create/" target="_blank">Create Account</a> <br> <p style="font-size: 18px;">Kind Regards, <br> ArtMusic TV</p>';
        $mail->AltBody = 'Your email client is not HTML compatible, to receive the correct email use a different client.';

        $mail->send();

        $success_message = "Invite has been sent to your friend. Thank you for sharing ArtMusiv TV!";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Send an email invite to your friends to join ArtMusic TV!">
    <meta name="keywords" content="artmusic, invite a friend">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Invite a Friend</title>
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

    <main class="mx-4">
        <!-- Page Title -->
        <h2 class="mb-5">Invite a Friend</h2>
        <div class="row">            
            <div class="col-md-4 mx-auto">
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
                    <!-- Page Title 
                    <h2 class="mb-5">Invite a Friend</h2>-->

                    <!-- Contact Us Form -->
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success" name="submit">Send Invite</button>
                    </form>
                </div>
            </div>            
        </div>
    </main>

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