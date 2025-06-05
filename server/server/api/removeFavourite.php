<?php
    include '../init.php';

    if (isset($_POST['favourite']) && !empty($_POST['favourite'])) {

        $favourite_id = $_POST["favourite"];

        $stmt = $db->prepare("DELETE FROM favourites WHERE id=?");
        $stmt->bind_param("i", $favourite_id);

        $stmt->execute();

        echo "Done";
    } else {
        echo "Invalid Parameter";
    }
?>