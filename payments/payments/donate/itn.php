<?php
	header('HTTP/1.0 200 OK');
	flush();

	include "../../server/init.php";

	$pfData = $_POST;

	if ($pfData['payment_status'] == 'COMPLETE') {
		$user_id = $pfData['m_payment_id'];
		$theatre_id = $pfData['custom_int1'];
		$video_id = $pfData['custom_int2'];

		$payfast_id = $pfData['pf_payment_id'];
		$item = "DONATION: " . remove_special_char($pfData['item_name']);

		$fee = $pfData['amount_fee'];
		$net = $pfData['amount_net'];

		$query = "INSERT INTO payments (UserID, TheatreID, VideoID, Description, Amount, Fee, PayFastID) VALUES (" . $user_id . ", " . $theatre_id. ", 0, '" . $item . "', '" . $net . "', '" . $fee . "', '" . $payfast_id . "')";
		$db->query($query);
	}
?>
