<?php
    include '../init.php';

    if (isset($_POST['user']) && !empty($_POST['user']) &&
        isset($_POST['video']) && !empty($_POST['video'])) {

        $user = $_POST["user"];
        $video = $_POST["video"];

        $stmt = $db->prepare("INSERT INTO shares (UserID, VideoID) VALUES (?, ?)");
        $stmt->bind_param("ii", $user, $video);

        $stmt->execute();

        echo "Done";
    } else {
        echo "Invalid Parameter";
    }
?>