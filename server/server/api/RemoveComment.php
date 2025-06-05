<?php
    include '../init.php';

    if (isset($_POST['comment']) && !empty($_POST['comment'])) {

        $comment_id = $_POST["comment"];

        $stmt = $db->prepare("DELETE FROM comments WHERE id=?");
        $stmt->bind_param("i", $comment_id);

        $stmt->execute();

        echo "Done";
    } else {
        echo "Invalid Parameter";
    }
?>