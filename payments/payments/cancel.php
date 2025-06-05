<?php
    include "../server/init.php";

	if (!isset($_SESSION["user_id"])) {
		header('Location: ' . $link_prefix . '/account/signin/');
	}

	if (isset($_GET["vid"]) && !empty($_GET["vid"])) {
		$video_id = mysqli_real_escape_string($db, $_GET['vid']);
	} else {
		header('Location: ' . $link_prefix . '/');
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Your payment was canceled!">
    <meta name="keywords" content="artmusic tv, payment canceled">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Payment Canceled</title>
	<link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/master.css">
	<link rel="stylesheet" href="../styles/custom.css">
</head>
<body class="container-fluid p-0 m-0">
	<!-- Import Navigation Bar -->
	<?php include "../server/includes/navigation.php"; ?>

	<div class="mt-5 pt-5"><!-- Spacer --></div>

	<div class="row mx-0 my-5 p-0">
		<div class="col-md-4"><!-- Spacer --></div>
		<div class="col-md-4">
			<h1 class="text-center">Canceled</h1>
			<hr>

			<p class="text-center">
				Your transaction has been canceled. If this was an accident you can click the button below to resume your payment.
			</p>

			<div class="text-center">
				<a href="<?php echo $link_prefix; ?>/payments/checkout.php?vid=<?php echo $video_id; ?>" class="btn btn-success mt-4">Resume Payment</a>
			</div>
			<div class="text-center mt-5 pt-4">
				Go To: 
				<a href="<?php echo $link_prefix; ?>/browse/latest/" class="d-inline-block mt-3">Videos</a> / 
				<a href="<?php echo $link_prefix; ?>/browse/theatre/?tid=0" class="d-inline-block mt-3">Theaters</a> /
				<a href="<?php echo $link_prefix; ?>/browse/category/?cid=0" class="d-inline-block mt-3">Categories</a>
			</div>
		</div>
		<div class="col-md-4"><!-- Spacer --></div>
	</div>

	<!-- Import Footer -->
	<?php include "../server/includes/footer.php"; ?>

	<!-- JS Files -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="../scripts/bootstrap.js"></script>
</body>
</html>
