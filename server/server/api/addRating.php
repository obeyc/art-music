<?php
	include '../init.php';

	if (isset($_POST['user']) && !empty($_POST['user']) &&
		isset($_POST['video']) && !empty($_POST['video']) &&
		isset($_POST['rating']) && !empty($_POST['rating']) ) {

		$user = $_POST["user"];
		$video = $_POST["video"];
		$rating = $_POST["rating"];

		$stmt = $db->prepare("INSERT INTO ratings (UserID, VideoID, Rating) VALUES (?, ?, ?)");
		$stmt->bind_param("iis", $user, $video, $rating);

		$stmt->execute();

		echo "Done";
	} else {
		echo "Invalid Parameter";
	}
?>