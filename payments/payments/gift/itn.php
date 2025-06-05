<?php
	header('HTTP/1.0 200 OK');
	flush();

	include "../../server/init.php";

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	$pfData = $_POST;

	if ($pfData['payment_status'] == 'COMPLETE') {
		$user_id = $pfData['m_payment_id'];
		$theatre_id = $pfData['custom_int1'];
		$video_id = $pfData['custom_int2'];
		$other_user_id = $pfData['custom_int3'];
		$other_user_email = $pfData['custom_str1'];

		$payfast_id = $pfData['pf_payment_id'];
		$item = "Gift Card: " . remove_special_char($pfData['item_name']);

		$fee = $pfData['amount_fee'];
		$net = $pfData['amount_net'];

		$query = "INSERT INTO payments (UserID, TheatreID, VideoID, Description, Amount, Fee, PayFastID) VALUES (" . $other_user_id . ", " . $theatre_id. ", " . $video_id . ", '" . $item . "', '0', '0', 'FREE')";
		$db->query($query);

		$query = "INSERT INTO payments (UserID, TheatreID, VideoID, Description, Amount, Fee, PayFastID) VALUES (" . $user_id . ", " . $theatre_id. ", 0, '" . $item . "', '" . $net . "', '" . $fee . "', '" . $payfast_id . "')";
		$db->query($query);

		/* Create Notification: Gift Card */

		$notification_description = "Gift Received: " . $item;
		$notification_link = "https://artmusic.tv/watch/?vid=" . $video_id;
		$notification_theatre = $theatre_id;
		$notification_status = 0;

		$stmt = $db->prepare("INSERT INTO notifications (UserID, Description, Link, TheatreID, Status) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("issii", $other_user_id, $notification_description, $notification_link, $notification_theatre, $notification_status);
		$stmt->execute();

		$email = $pfData['email_address'];

		require '../../vendor/autoload.php';

		$mail = new PHPMailer(true);

		$mail->isSMTP();
		$mail->Host       = 'mail.brooklyntheatre.tv';
		$mail->SMTPAuth   = true;
		$mail->Username   = 'noreply@brooklyntheatre.tv';
		$mail->Password   = 'YG(NQG+iP0wH';
		$mail->SMTPSecure = 'ssl';
		$mail->Port       = 465;

		$mail->setFrom('noreply@artmusic.tv', 'ArtMusic TV');
		$mail->addAddress($other_user_email, "ArtMusic TV User");
		$mail->addReplyTo('noreply@artmusic.tv', 'No Reply');

		// Content
		$mail->isHTML(true);
		$mail->Subject = 'ArtMusic - Gift Card';
		$mail->Body    = '<img src="https://www.artmusic.tv/media/images/Logo.jpg" style="width: 250px; display: block; margin: 10px auto;"> <hr> <h2 style="font-size: 32px; color:#002366;">Gift Card</h2> <p style="font-size: 18px;">Hello, your friend sent you an ArtMusic TV Gift Card, click the link below to start watching:<p> <a style="font-size: 18px;" href="https://artmusic.tv/watch/?vid=' . $video_id . '" target="_blank">Watch Video</a> <br> <p style="font-size: 18px;">Kind Regards, <br> ArtMusic TV</p>';
		$mail->AltBody = 'Your email client is not HTML compatible, to receive the correct email use a different client.';

		$mail->send();
	}
?>
