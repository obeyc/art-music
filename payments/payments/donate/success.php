<?php

	include "../../server/init.php";

	if (!isset($_SESSION["user_id"])) {
		header('Location: ' . $link_prefix . '/account/signin/');
	}
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Your payment was successful!">
    <meta name="keywords" content="artmusic tv, payment success">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Payment Success</title>
	<link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../../styles/master.css">
</head>
<body class="container-fluid p-0 m-0">

	<!-- Import Navigation Bar -->
	<?php include "../../server/includes/navigation.php"; ?>

	<div class="mt-5 pt-5"><!-- Spacer --></div>

	<div class="row mx-0 my-5 p-0">
		<div class="col-md-4"><!-- Spacer --></div>
		<div class="col-md-4">
			<h1 class="text-center">Thank You!</h1>
			<hr>

			<p class="text-center">
				The transaction was successful, thank you for your support. You donations are highly appreciated!
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
