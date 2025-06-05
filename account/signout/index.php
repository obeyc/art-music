<?php
    include_once "../../server/init.php";

    if (!isset($_SESSION["user_id"])) {
        header('Location: ../signin/');
    }

    session_unset();
    session_destroy();

    //setcookie("user_session_id", "", time() - 3600, "/");
    unset($_SESSION["user_session_id"]);
    $default_session_id = "";

    $stmt = $db->prepare("UPDATE users SET SessionID=? WHERE id=?");
    $stmt->bind_param("si", $default_session_id, $user_id);
    $stmt->execute();

    header('Location: ' . $link_prefix . '/');
?>