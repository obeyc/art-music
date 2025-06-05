<?php
    include '../init.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require '../../vendor/autoload.php';

    $query = "SELECT * FROM emails WHERE EmailSent=0 LIMIT 10";
    $emails = $db->query($query);

    while ($email = $emails->fetch_assoc()) {
        $email_user_id = $email['UserID'];
        $email_video_id = $email['VideoID'];
        $email_video_title = $email['VideoTitle'];

        $query = "SELECT * FROM users WHERE id='$email_user_id'";
        $email_user = $db->query($query);

        if ($email_user->num_rows == 1) {
            $email_user = $email_user->fetch_assoc();

            $mail = new PHPMailer(true);
    
            $mail->isSMTP();
            $mail->Host       = 'mail.brooklyntheatre.tv';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'noreply@brooklyntheatre.tv';
            $mail->Password   = 'YG(NQG+iP0wH';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;
    
            $mail->setFrom('noreply@artmusic.tv', 'ArtMusic TV');
            $mail->addAddress($email_user['Email'], "ArtMusic TV User");
            $mail->addReplyTo('noreply@artmusic.tv', 'No Reply');
    
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'ArtMusic - New Video';
            $mail->Body    = '<img src="https://www.artmusic.tv/media/images/Logo.jpg" style="width: 250px; display: block; margin: 10px auto;"> <hr> <h2 style="font-size: 32px; color:#002366;">New Video</h2> <p style="font-size: 18px;">Hello, we are happy to announce the launch of a new video on ArtMusic TV, click the link below to start watching:<p> <a style="font-size: 18px;" href="https://artmusic.tv/watch/?vid=' . $email_video_id . '" target="_blank">Watch: ' . $email_video_title . '</a> <br> <p style="font-size: 18px;">Kind Regards, <br> ArtMusic TV</p>';
            $mail->AltBody = 'Your email client is not HTML compatible, to receive the correct email use a different client.';
    
            $mail->send();
        }

        $query = "UPDATE emails SET EmailSent=1 WHERE id=" . $email['id'];
        $db->query($query);
    }
?>