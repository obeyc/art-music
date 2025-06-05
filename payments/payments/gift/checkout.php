<?php

	include "../../server/init.php";

  	if (!isset($_SESSION["user_id"])) {
		header('Location: ' . $link_prefix . '/account/signin/');
	}

	if (isset($_GET["uid"]) && !empty($_GET["uid"])) {
		$other_user_id = $_GET['uid'];

		$query = "SELECT * FROM users WHERE id='$other_user_id'";
		$result = $db->query($query);
		
		if ($result->num_rows === 1) {
			$result_array = $result->fetch_assoc();

			$user_firstName = $result_array["FirstName"];
			$user_lastName = $result_array["LastName"];
			$user_email = $result_array["Email"];
		} else {
			header('Location: ./');
		}
	} else {
		header('Location: ./');
	}

  	if (isset($_GET["vid"]) && !empty($_GET["vid"])) {
  		$video_id = mysqli_real_escape_string($db, $_GET['vid']);

		$stmt = $db->prepare("SELECT * FROM videos WHERE id=?");
		$stmt->bind_param("i", $video_id);
		$stmt->execute();

		$result = $stmt->get_result();

  		if ($result->num_rows === 1) {
			$video = $result->fetch_assoc();
			
			$query = "SELECT * FROM theatres WHERE id=" . $video['TheatreID'];
			$result = $db->query($query);

			if ($result->num_rows === 1) {
				$theatre = $result->fetch_assoc();

				/* Cancel Payment - Theatre Disabled */
				
				if ($theatre['Status'] == 'disabled') {
					header('Location: ' . $link_prefix . '/');
				}

				$cartTotal = $video["Price"];

				$data = array(
					'merchant_id' => $theatre['MerchantID'],
					'merchant_key' => $theatre['MerchantKey'],
					'return_url' => 'https://artmusic.tv/payments/gift/success.php?vid=' . $video_id,
					'cancel_url' => 'https://artmusic.tv/payments/gift/cancel.php?vid=' . $video_id,
					'notify_url' => 'https://artmusic.tv/payments/gift/itn.php',
					'name_first' => $user_firstName,
					'name_last'  => $user_lastName,
					'email_address'=> $user_email,
					'm_payment_id' => $user_id,
					'amount' => number_format(sprintf("%.2f", $cartTotal), 2, '.', ''),
					'item_name' => $video["Title"] . " (3 Days Access)",
					'item_description' => $video["ShortDescription"],
					'custom_int1' => $theatre["id"],
					'custom_int2' => $video["id"],
					'custom_int3' => $other_user_id,
					'custom_str1' => $user_email
				);

				$pfOutput = '';
				foreach( $data as $key => $val ) {
					if(!empty($val)) {
						$pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
					}
				}
				$getString = substr($pfOutput, 0, -1);

				$passPhrase = $theatre['Passphrase'];
				if(isset($passPhrase)) {
					$getString .= '&passphrase=' . urlencode(trim($passPhrase));
				}

				$data['signature'] = md5($getString);

			} else {
				header('Location: ./');
			}
		} else {
			header('Location: ./');
		}
	} else {
		header('Location: ./');
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Purchase 3 days access gift card to a video on ArtMusic TV.">
    <meta name="keywords" content="artmusic tv, purchase, gift card">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Purchase 3 Days Access Gift Card</title>
	<link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../../styles/master.css">
</head>
<body class="container-fluid p-0 m-0">

	<!-- Import Navigation Bar -->
	<?php include "../../server/includes/navigation.php"; ?>

	<div class="mt-5 pt-5"><!-- Spacer --></div>

	<div class="row mx-0 p-0 my-5">
		<div class="col-md-4"><!-- Spacer --></div>
		<div class="col-md-4">
			<h2 class="text-center">Purchase 3 Days Access Gift Card</h2>
			<hr class="border-white">

			<p class="text-center mb-5">
				You will be redirected to the PayFast payment gateway to complete the
				transaction, please make sure to enter the correct information.
			</p>

			<h1 class="text-center">R<?php echo $data['amount']; ?></h1>
			<h5 class="d-block text-center"><?php echo $data['item_name'] . " for " . $user_firstName . " " . $user_lastName; ?></h5>

			<?php
				$testingMode = false;
				$pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
				$htmlForm = '<form action="https://'.$pfHost.'/eng/process" method="post">';
				foreach($data as $name=> $value) {
					$htmlForm .= '<input name="'.$name.'" type="hidden" value="'.$value.'" />';
				}
				$htmlForm .= '<input class="btn btn-block btn-success mt-4" type="submit" value="Pay Now" /></form>';
				echo $htmlForm;
			?>

			<p class="text-center mt-3 text-muted">
				Your friend will have access to the video for <strong>3 days (72 Hours)</strong> after you complete the purchase.
			</p>
		</div>
		<div class="col-md-4"><!-- Spacer --></div>
	</div>

	<!-- Import Footer -->
    <?php include "../../server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../../scripts/bootstrap.js"></script>
</body>
</html>
