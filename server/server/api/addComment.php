<?php
    include '../init.php';

    if (isset($_POST['user']) && !empty($_POST['user']) &&
        isset($_POST['video']) && !empty($_POST['video']) &&
        isset($_POST['message']) && !empty($_POST['message']) ) {

        $user = $_POST["user"];
        $video = $_POST["video"];
        $message = $_POST["message"];

        $stmt = $db->prepare("INSERT INTO comments (UserID, VideoID, Message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user, $video, $message);

        $stmt->execute();

        echo "Done";
    } else {
        echo "Invalid Parameter";
    }
?>