<?php
    include "../server/init.php";

    if (!isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/account/signin/');
    }

    /* Check User Access */

    if ($user_access !== "admin") {
        header('Location: ' . $link_prefix . '/');
    }

    /* Page View */

    if (isset($_GET['pv']) && !empty($_GET['pv'])) {
        $page_view = $_GET['pv'];

        if ($page_view !== "add" && $page_view !== "view") {
            $page_view = "default";
        }
    } else {
        $page_view = "default";
    }

    if (isset($_GET["search"]) && !empty($_GET["search"])) {
        $search_query = mysqli_real_escape_string($db, $_GET['search']);
    } else {
        $search_query = "No Search Term!";
    }

    if ($search_query === "No Search Term!") {

        /* Load Theatres List */

        $query = "SELECT * FROM theatres";
        $theatres = $db->query($query);
    } else {

        /* Load Theatres List by Search Term */

        $query = "SELECT * FROM theatres WHERE LOWER(Title) LIKE '%$search_query%'";
        $theatres = $db->query($query);
    }

    /* Update Theatre Status - Enable */

    if (isset($_GET['enable']) && $_GET['enable'] === "true") {
        if (isset($_GET['tid']) && !empty($_GET['tid'])) {
            $theatre_id = $_GET['tid'];

            $query = "UPDATE theatres SET Status = 'enabled' WHERE id='$theatre_id'";
            $db->query($query);

            header('Location: ./theatres.php');
        }
    }

    /* Update Theatre Status - Disable */

    if (isset($_GET['disable']) && $_GET['disable'] === "true") {
        if (isset($_GET['tid']) && !empty($_GET['tid'])) {
            $theatre_id = $_GET['tid'];

            $query = "UPDATE theatres SET Status = 'disabled' WHERE id='$theatre_id'";
            $db->query($query);

            header('Location: ./theatres.php');
        }
    }

    $title = "";
    $email = "";
    $merchantID = "";
    $merchantKey = "";
    $passphrase = "";

    $success_message = "";
    $error_message = "";

    /* Create Theatre Form Submit */

    if (isset($_POST['addSubmit'])) {

        /* Input Validation */

        if (isset($_POST['addTitle']) && !empty($_POST['addTitle'])) {
            $title = mysqli_real_escape_string($db, $_POST['addTitle']);

            $query = "SELECT * FROM theatres WHERE Title='$title'";
            $result = $db->query($query);

            if ($result->num_rows != 0) {
                $error_message = "Title unavailable. Please provide a title that is not already in use!";
            }
        } else {
            $error_message = "Please provide a title.";
        }

        if (isset($_POST['addEmail']) && !empty($_POST['addEmail'])) {
            $email = mysqli_real_escape_string($db, $_POST['addEmail']);

            $query = "SELECT * FROM users WHERE Email='$email'";
            $result = $db->query($query);

            if ($result->num_rows === 0) {
                $error_message = "Account not found. Please provide an email that belongs to an ArtMusic TV user.";
            }
        } else {
            $error_message = "Please provide a valid email address.";
        }

        if (isset($_POST['addMerchantID']) && !empty($_POST['addMerchantID'])) {
            $merchantID = mysqli_real_escape_string($db, $_POST['addMerchantID']);
        } else {
            $error_message = "Please provide a PayFast Merchant ID.";
        }

        if (isset($_POST['addMerchantKey']) && !empty($_POST['addMerchantKey'])) {
            $merchantKey = mysqli_real_escape_string($db, $_POST['addMerchantKey']);
        } else {
            $error_message = "Please provide a PayFast Merchant Key.";
        }

        if (isset($_POST['addPassphrase']) && !empty($_POST['addPassphrase'])) {
            $passphrase = mysqli_real_escape_string($db, $_POST['addPassphrase']);
        } else {
            $error_message = "Please provide a PayFast Passphrase.";
        }

        if ($_FILES["addLogo"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['addLogo']['name'],PATHINFO_EXTENSION));
        
            $target_dir = "../media/images/logos/";
            $target_file = $target_dir . file_name($title) . '-logo.' . $imageFileType;
        
            $uploadOk = 1;
        
            if ($imageFileType != "png") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES["addLogo"]["tmp_name"], $target_file);
            } else {
                $error_message = "File Upload Error. Try Again!";
            }
        } else {
            $error_message = "Please provide a logo file (.PNG).";
        }

        /* Create Theatre */

        $default_membership_fee = "400";
        $default_status = "enabled";
    
        if (empty($error_message)) {
            $stmt = $db->prepare("INSERT INTO theatres (Title, MerchantID, MerchantKey, Passphrase, MembershipFee, Status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $title, $merchantID, $merchantKey, $passphrase, $default_membership_fee, $default_status);
            $stmt->execute();

            /* Give User Theatre Access */

            $query = "SELECT * FROM users WHERE Email='$email'";
            $result = $db->query($query);
    
            if ($result->num_rows === 1) {
                $result_array = $result->fetch_assoc();

                $theatre_access_user_id = $result_array["id"];
            }

            $query = "SELECT * FROM theatres WHERE Title='$title'";
            $result = $db->query($query);
    
            if ($result->num_rows === 1) {
                $result_array = $result->fetch_assoc();

                $theatre_access_theatre_id = $result_array["id"];
            }

            $stmt = $db->prepare("INSERT INTO theatreaccess (UserID, TheatreID) VALUES (?, ?)");
            $stmt->bind_param("ii", $theatre_access_user_id, $theatre_access_theatre_id);
            $stmt->execute();

            /* Create Notification: New Theatre */

            $notification_description = "New Theatre: " . $title;
            $notification_link = "https://artmusic.tv/browse/theatre/?tid=" . $theatre_access_theatre_id;
            $notification_theatre = $theatre_access_theatre_id;
            $notification_status = 0;

            $query = "SELECT * FROM users";
            $notification_users = $db->query($query);

            while ($notification_u = $notification_users->fetch_assoc()) {
                $stmt = $db->prepare("INSERT INTO notifications (UserID, Description, Link, TheatreID, Status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issii", $notification_u['id'], $notification_description, $notification_link, $notification_theatre, $notification_status);
                $stmt->execute();
            }

            $title = "";
            $email = "";
            $merchantID = "";
            $merchantKey = "";
            $passphrase = "";
        
            $success_message = "Theatre has been added!";
        }
    }

    /* Load Selected Theatre */

    if ($page_view === "view") {
        if (isset($_GET['tid']) && !empty($_GET['tid'])) {
            $theatre_id = $_GET['tid'];

            $query = "SELECT * FROM theatres WHERE id='$theatre_id'";
            $result = $db->query($query);

            if ($result->num_rows === 1) {
                $selected_theatre = $result->fetch_assoc();

                $title = $selected_theatre['Title'];
                $merchantID = $selected_theatre['MerchantID'];
                $merchantKey = $selected_theatre['MerchantKey'];
                $passphrase = $selected_theatre['Passphrase'];
            } else {
                header('Location: ./theatres.php');
            }
        } else {
            header('Location: ./theatres.php');
        }
    }

    /* Update Theatre Form Submit */

    if (isset($_POST['updateSubmit'])) {

        /* Input Validation */

        if (isset($_POST['updateTitle']) && !empty($_POST['updateTitle'])) {
            $title = mysqli_real_escape_string($db, $_POST['updateTitle']);

            $query = "SELECT * FROM theatres WHERE Title='$title' AND id<>'$theatre_id'";
            $result = $db->query($query);

            if ($result->num_rows != 0) {
                $error_message = "Title unavailable. Please provide a title that is not already in use!";
            }
        } else {
            $error_message = "Please provide a title.";
        }

        if (isset($_POST['updateMerchantID']) && !empty($_POST['updateMerchantID'])) {
            $merchantID = mysqli_real_escape_string($db, $_POST['updateMerchantID']);
        } else {
            $error_message = "Please provide a PayFast Merchant ID.";
        }

        if (isset($_POST['updateMerchantKey']) && !empty($_POST['updateMerchantKey'])) {
            $merchantKey = mysqli_real_escape_string($db, $_POST['updateMerchantKey']);
        } else {
            $error_message = "Please provide a PayFast Merchant Key.";
        }

        if (isset($_POST['updatePassphrase']) && !empty($_POST['updatePassphrase'])) {
            $passphrase = mysqli_real_escape_string($db, $_POST['updatePassphrase']);
        } else {
            $error_message = "Please provide a PayFast Passphrase.";
        }

        if ($_FILES["updateLogo"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['updateLogo']['name'],PATHINFO_EXTENSION));
        
            $target_dir = "../media/images/logos/";
            $target_file = $target_dir . file_name($title) . '-logo.' . $imageFileType;
        
            $uploadOk = 1;
        
            if ($imageFileType != "png") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES["updateLogo"]["tmp_name"], $target_file);
            } else {
                $error_message = "File Upload Error. Try Again!";
            }
        }

        /* Update Theatre */
    
        if (empty($error_message)) {
            $stmt = $db->prepare("UPDATE theatres SET Title=?, MerchantID=?, MerchantKey=?, Passphrase=? WHERE id='$theatre_id'");
            $stmt->bind_param("ssss", $title, $merchantID, $merchantKey, $passphrase);
            $stmt->execute();
        
            $success_message = "Theatre has been updated!";
        }
    }

    /* Remove Comment */

    if (isset($_GET["remove_comment"]) && !empty($_GET["remove_comment"])) {
        $comment_id = $_GET['remove_comment'];

        $query = "DELETE FROM comments WHERE id='$comment_id'";
        $db->query($query);

        $success_message = "Comment has been deleted!";
    }

    /* Delete Theatre */
    if (isset($_GET["delete_theatre"]) && !empty($_GET["delete_theatre"])) {
        $delete_id = $_GET["delete_theatre"];

        $query = "DELETE FROM videos WHERE TheatreID='$delete_id'";
        $db->query($query);

        $query = "DELETE FROM theatreaccess WHERE TheatreID='$delete_id'";
        $db->query($query);

        $query = "DELETE FROM theatres WHERE id='$delete_id'";
        $db->query($query);

        header('Location: ./theatres.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Admin Theatres</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/master.css">
</head>
<body class="container-fluid p-0 m-0">
    <!-- Import Admin Navigation Bar -->
    <?php include "../server/includes/admin-navigation.php"; ?>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

    <main class="mx-4">

        <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success text-center" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($page_view === "default") { ?>
            <!-- List of Theatres Section (Default) -->
            <section class="container-fluid">
                <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                    <h2 class="d-inline-block m-0 p-0">Theatres</h2>
                    <a class="btn btn-primary" href="./theatres.php?pv=add">Add Theatre</a>
                </div>

                <div class="d-flex justify-content-between align-items-end mx-2 mb-4 mobile-d-block">
                    <form class="form-inline mobile-margin-b-1" action="" method="GET">
                        <input class="form-control mr-sm-2" type="text" name="search" placeholder="Search" value="<?php if ($search_query !== "No Search Term!") { echo $search_query; } ?>" required>
                        <button class="btn btn-secondary my-2 my-sm-0 mr-2" type="submit">
                            Search
                        </button>
                        <a class="text-success mobile-d-block mobile-width-100 mobile-text-center mobile-margin-2" href="./theatres.php">Clear</a>
                    </form>
                </div>

                <?php if ($theatres->num_rows > 0) { ?>
                    <?php while($theatre = $theatres->fetch_assoc()) : ?>
                        <div class="row text-center bg-dark rounded mb-4 mx-2 px-1 py-4">
                            <div class="col-md-3">
                                <img src="<?php echo $link_prefix; ?>/media/images/logos/<?php echo file_name($theatre['Title']); ?>-logo.png" class="table-image-md mobile-margin-b-2" alt="<?php echo $theatre['Title']; ?> Logo">
                            </div>
                            <div class="col-md-3 text-left">
                                <h5 class="mobile-margin-b-2"><?php echo $theatre['Title']; ?></h5>
                            </div>
                            <div class="col-md-2">
                                <h6 class="mobile-text-left">
                                    <?php
                                        $query = "SELECT * FROM videos WHERE TheatreID =" . $theatre['id'];
                                        $theatre_videos = $db->query($query);

                                        echo number_format($theatre_videos->num_rows);
                                    ?>
                                    Videos
                                </h6>
                            </div>
                            <div class="col-md-2">
                                <h6 class="mobile-text-left mobile-margin-b-2">
                                    <?php
                                        $query = "SELECT SUM(Amount) AS Total FROM payments WHERE TheatreID = " . $theatre['id'];
                                        $theatre_payments_sum = $db->query($query);

                                        $theatre_payments_sum = $theatre_payments_sum->fetch_assoc();

                                        echo 'R' . number_format($theatre_payments_sum['Total'], 2, '.', ',');
                                    ?>
                                </h6>
                            </div>
                            <div class="col-md-2">
                                <a class="btn btn-block btn-primary" href="./theatres.php?pv=view&tid=<?php echo $theatre['id']; ?>">View & Update</a>

                                <?php if ($theatre['Status'] === "disabled") { ?>
                                    <a class="btn btn-block btn-secondary" href="./theatres.php?enable=true&tid=<?php echo $theatre['id']; ?>">Enable</a>
                                <?php } else { ?>
                                    <a class="btn btn-block btn-secondary" href="./theatres.php?disable=true&tid=<?php echo $theatre['id']; ?>">Disable</a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php } else { ?>
                    <h1 class="text-center my-5">There are no theatres to display. Try changing your search term.</h1>
                <?php } ?>
            </section>
        <?php } ?>

        <?php if ($page_view === "add") { ?>    
            <!-- Create Theatre Section -->
            <section class="mb-5">
                <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                    <h2 class="d-inline-block m-0 p-0">Add Theatre</h2>
                    <a class="btn btn-primary" href="./theatres.php">Back to Theatres</a>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="addTitle">Title*</label>
                        <input type="text" class="form-control" id="addTitle" name="addTitle" maxlength="30" value="<?php echo $title; ?>" required>
                        <small class="form-text text-muted">
                            Max 30 Characters!
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="addEmail">Account Email*</label>
                        <input type="text" class="form-control" id="addEmail" name="addEmail" value="<?php echo $email; ?>" required>
                        <small class="form-text text-muted">
                            This email must belong to an existing ArtMusic TV user.
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="addMerchantID">PayFast Merchant ID*</label>
                        <input type="text" class="form-control" id="addMerchantID" name="addMerchantID" value="<?php echo $merchantID; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="addMerchantKey">PayFast Merchant Key*</label>
                        <input type="text" class="form-control" id="addMerchantKey" name="addMerchantKey" value="<?php echo $merchantKey; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="addPassphrase">PayFast Passphrase*</label>
                        <input type="text" class="form-control" id="addPassphrase" name="addPassphrase" value="<?php echo $passphrase; ?>" required>
                    </div>
                    <hr class="border-white my-4">
                    <div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" id="addLogo" name="addLogo" required>
                        <label class="custom-file-label" for="addLogo">Logo* (.PNG)</label>
                    </div>
                    <button class="btn btn-primary" type="submit" name="addSubmit">Add Theatre</button>
                </form>
            </section>
        <?php } ?>

        <?php if ($page_view === "view") { ?>
            <!-- View & Update Theatre Section -->
            <section class="mb-5">
                <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                    <h2 class="d-inline-block m-0 p-0">View & Update Theatre</h2>
                    <a class="btn btn-primary" href="./theatres.php">Back to Theatres</a>
                </div>

                <!-- Statistics -->
                <section class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Statistics</h4>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                                <div class="card-header">Views</div>
                                <div class="card-body p-2">
                                    <h2 class="card-title">
                                        <?php
                                            $view_count = 0;

                                            $query = "SELECT * FROM views";
                                            $views = $db->query($query);

                                            if ($views->num_rows > 0) {
                                                while ($view = $views->fetch_assoc()) {
                                                    $view_video_id = $view['VideoID'];

                                                    $query = "SELECT * FROM videos WHERE id='$view_video_id'";
                                                    $result = $db->query($query);

                                                    if ($result->num_rows === 1) {
                                                        $result_array = $result->fetch_assoc();

                                                        if ($result_array['TheatreID'] === $theatre_id) {
                                                            $view_count++;
                                                        }
                                                    }
                                                }
                                            }

                                            echo $view_count;
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                                <div class="card-header">Previews</div>
                                <div class="card-body p-2">
                                    <h2 class="card-title">
                                        <?php
                                            $preview_count = 0;

                                            $query = "SELECT * FROM previews";
                                            $previews = $db->query($query);

                                            if ($previews->num_rows > 0) {
                                                while ($preview = $previews->fetch_assoc()) {
                                                    $preview_video_id = $preview['VideoID'];

                                                    $query = "SELECT * FROM videos WHERE id='$preview_video_id'";
                                                    $result = $db->query($query);

                                                    if ($result->num_rows === 1) {
                                                        $result_array = $result->fetch_assoc();

                                                        if ($result_array['TheatreID'] === $theatre_id) {
                                                            $preview_count++;
                                                        }
                                                    }
                                                }
                                            }

                                            echo $preview_count;
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                                <div class="card-header">Purchases</div>
                                <div class="card-body p-2">
                                    <h2 class="card-title">
                                        <?php
                                            $query = "SELECT * FROM payments WHERE TheatreID='$theatre_id' AND VideoID<>0 AND PayFastID<>'FREE'";
                                            $payments_count = $db->query($query);

                                            echo $payments_count->num_rows;
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                                <div class="card-header">Ratings</div>
                                <div class="card-body p-2">
                                    <h2 class="card-title">
                                        <?php
                                            $good_rating_count = 0;
                                            $bad_rating_count = 0;

                                            $query = "SELECT * FROM ratings";
                                            $ratings = $db->query($query);

                                            if ($ratings->num_rows > 0) {
                                                while ($rating = $ratings->fetch_assoc()) {
                                                    $rating_video_id = $rating['VideoID'];

                                                    $query = "SELECT * FROM videos WHERE id='$rating_video_id'";
                                                    $result = $db->query($query);

                                                    if ($result->num_rows === 1) {
                                                        $result_array = $result->fetch_assoc();

                                                        if ($result_array['TheatreID'] === $theatre_id) {
                                                            if ($rating['Rating'] === 'good') {
                                                                $good_rating_count++;
                                                            } else {
                                                                $bad_rating_count++;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        ?>
                                        <span class="text-success"><?php echo $good_rating_count; ?></span> 
                                        / 
                                        <span class="text-danger"><?php echo $bad_rating_count; ?></span>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                                <div class="card-header">Comments</div>
                                <div class="card-body p-2">
                                    <h2 class="card-title">
                                        <?php
                                            $comment_count = 0;

                                            $query = "SELECT * FROM comments";
                                            $comments = $db->query($query);

                                            if ($comments->num_rows > 0) {
                                                while ($comment = $comments->fetch_assoc()) {
                                                    $comment_video_id = $comment['VideoID'];

                                                    $query = "SELECT * FROM videos WHERE id='$comment_video_id'";
                                                    $result = $db->query($query);

                                                    if ($result->num_rows === 1) {
                                                        $result_array = $result->fetch_assoc();

                                                        if ($result_array['TheatreID'] === $theatre_id) {
                                                            $comment_count++;
                                                        }
                                                    }
                                                }
                                            }

                                            echo $comment_count;
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                                <div class="card-header">Shares</div>
                                <div class="card-body p-2">
                                    <h2 class="card-title">
                                        <?php
                                            $share_count = 0;

                                            $query = "SELECT * FROM shares";
                                            $shares = $db->query($query);

                                            if ($shares->num_rows > 0) {
                                                while ($share = $shares->fetch_assoc()) {
                                                    $share_video_id = $share['VideoID'];

                                                    $query = "SELECT * FROM videos WHERE id='$share_video_id'";
                                                    $result = $db->query($query);

                                                    if ($result->num_rows === 1) {
                                                        $result_array = $result->fetch_assoc();

                                                        if ($result_array['TheatreID'] === $theatre_id) {
                                                            $share_count++;
                                                        }
                                                    }
                                                }
                                            }

                                            echo $share_count;
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Update Theatre Information -->
                <form class="mb-5" action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="updateTitle">Title*</label>
                        <input type="text" class="form-control" id="updateTitle" name="updateTitle" maxlength="30" value="<?php echo $title; ?>" required>
                        <small class="form-text text-muted">
                            Max 30 Characters!
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="updateMerchantID">PayFast Merchant ID*</label>
                        <input type="text" class="form-control" id="updateMerchantID" name="updateMerchantID" value="<?php echo $merchantID; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="updateMerchantKey">PayFast Merchant Key*</label>
                        <input type="text" class="form-control" id="updateMerchantKey" name="updateMerchantKey" value="<?php echo $merchantKey; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="updatePassphrase">PayFast Passphrase*</label>
                        <input type="text" class="form-control" id="updatePassphrase" name="updatePassphrase" value="<?php echo $passphrase; ?>" required>
                    </div>
                    <hr class="border-white my-4">
                    <div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" id="updateLogo" name="updateLogo">
                        <label class="custom-file-label" for="updateLogo">Logo (.PNG)</label>
                    </div>
                    <button type="submit" class="btn btn-primary" name="updateSubmit">Update</button>
                </form>

                <!-- Free Access -->
                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Free Access</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone Number</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Date Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM payments WHERE TheatreID='$theatre_id' AND PayFastID='FREE'";
                                    $theatre_free_access = $db->query($query);

                                    while ($freebie = $theatre_free_access->fetch_assoc()) {
                                        $freebie_user_id = $freebie['UserID'];

                                        $query = "SELECT * FROM users WHERE id='$freebie_user_id'";
                                        $result = $db->query($query);
                                
                                        if ($result->num_rows === 1) {
                                            $freebie_user = $result->fetch_assoc();
                                        }
                                ?>
                                    <tr>
                                        <td>
                                            <?php echo $freebie_user["FirstName"] . " " . $freebie_user["LastName"]; ?>
                                        </td>
                                        <td><a href="mailto:<?php echo $freebie_user["Email"]; ?>"><?php echo $freebie_user["Email"]; ?></a></td>
                                        <td><a href="tel:<?php echo $freebie_user["PhoneNumber"]; ?>"><?php echo $freebie_user["PhoneNumber"]; ?></a></td>
                                        <td><?php echo $freebie["Description"]; ?></td>
                                        <td><?php echo $freebie["DateCreated"]; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Views</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone Number</th>
                                    <th scope="col">Video</th>
                                    <th scope="col">Payment Method</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM views";
                                    $views = $db->query($query);

                                    if ($views->num_rows > 0) {
                                        while ($view = $views->fetch_assoc()) {
                                            $view_video_id = $view['VideoID'];

                                            $query = "SELECT * FROM videos WHERE id='$view_video_id'";
                                            $result = $db->query($query);

                                            if ($result->num_rows === 1) {
                                                $result_array = $result->fetch_assoc();

                                                if ($result_array['TheatreID'] === $theatre_id) {
                                                    $view_user_id = $view['UserID'];

                                                    $query = "SELECT * FROM users WHERE id='$view_user_id'";
                                                    $result = $db->query($query);
                                            
                                                    if ($result->num_rows === 1) {
                                                        $view_user = $result->fetch_assoc();
                                                    } else {
                                                        $view_user["FirstName"] = "Unknown";
                                                        $view_user["LastName"] = "User";
                                                        $view_user["Email"] = "No Email Found";
                                                        $view_user["PhoneNumber"] = "No Phone Number Found";
                                                    }
                                ?>
                                                    <tr>
                                                        <td><?php echo $view_user["FirstName"] . " " . $view_user["LastName"]; ?></td>
                                                        <td><a href="mailto:<?php echo $view_user["Email"]; ?>"><?php echo $view_user["Email"]; ?></a></td>
                                                        <td><a href="tel:<?php echo $view_user["PhoneNumber"]; ?>"><?php echo $view_user["PhoneNumber"]; ?></a></td>
                                                        <td>
                                                            <?php
                                                                $view_video_id = $view['VideoID'];

                                                                $query = "SELECT * FROM videos WHERE id='$view_video_id'";
                                                                $result = $db->query($query);
                                                        
                                                                if ($result->num_rows === 1) {
                                                                    $view_video = $result->fetch_assoc();

                                                                    echo '<a href="' . $link_prefix . '/watch/?vid=' . $view_video_id . '">' . $view_video['Title'] . '</a>';
                                                                }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $view['ViewType']; ?></td>
                                                        <td><?php echo $view["DateCreated"]; ?></td>
                                                    </tr>
                                <?php
                                                }
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Previews</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone Number</th>
                                    <th scope="col">Video</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM previews";
                                    $previews = $db->query($query);

                                    if ($previews->num_rows > 0) {
                                        while ($preview = $previews->fetch_assoc()) {
                                            $preview_video_id = $preview['VideoID'];

                                            $query = "SELECT * FROM videos WHERE id='$preview_video_id'";
                                            $result = $db->query($query);

                                            if ($result->num_rows === 1) {
                                                $result_array = $result->fetch_assoc();

                                                if ($result_array['TheatreID'] === $theatre_id) {
                                                    $preview_user_id = $preview['UserID'];

                                                    $query = "SELECT * FROM users WHERE id='$preview_user_id'";
                                                    $result = $db->query($query);
                                            
                                                    if ($result->num_rows === 1) {
                                                        $preview_user = $result->fetch_assoc();
                                                    } else {
                                                        $preview_user["FirstName"] = "Unknown";
                                                        $preview_user["LastName"] = "User";
                                                        $preview_user["Email"] = "No Email Found";
                                                        $preview_user["PhoneNumber"] = "No Phone Number Found";
                                                    }
                                ?>
                                                    <tr>
                                                        <td><?php echo $preview_user["FirstName"] . " " . $preview_user["LastName"]; ?></td>
                                                        <td><a href="mailto:<?php echo $preview_user["Email"]; ?>"><?php echo $preview_user["Email"]; ?></a></td>
                                                        <td><a href="tel:<?php echo $preview_user["PhoneNumber"]; ?>"><?php echo $preview_user["PhoneNumber"]; ?></a></td>
                                                        <td>
                                                            <?php
                                                                $preview_video_id = $preview['VideoID'];

                                                                $query = "SELECT * FROM videos WHERE id='$preview_video_id'";
                                                                $result = $db->query($query);
                                                        
                                                                if ($result->num_rows === 1) {
                                                                    $preview_video = $result->fetch_assoc();

                                                                    echo '<a href="' . $link_prefix . '/watch/?vid=' . $preview_video_id . '">' . $preview_video['Title'] . '</a>';
                                                                }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $preview["DateCreated"]; ?></td>
                                                    </tr>
                                <?php
                                                }
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Comments</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone Number</th>
                                    <th scope="col">Video</th>
                                    <th scope="col">Comment</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM comments";
                                    $comments = $db->query($query);

                                    if ($comments->num_rows > 0) {
                                        while ($comment = $comments->fetch_assoc()) {
                                            $comment_video_id = $comment['VideoID'];

                                            $query = "SELECT * FROM videos WHERE id='$comment_video_id'";
                                            $result = $db->query($query);

                                            if ($result->num_rows === 1) {
                                                $result_array = $result->fetch_assoc();

                                                if ($result_array['TheatreID'] === $theatre_id) {
                                                    $comment_user_id = $comment['UserID'];

                                                    $query = "SELECT * FROM users WHERE id='$comment_user_id'";
                                                    $result = $db->query($query);
                                            
                                                    if ($result->num_rows === 1) {
                                                        $comment_user = $result->fetch_assoc();
                                                    } else {
                                                        $comment_user["FirstName"] = "Unknown";
                                                        $comment_user["LastName"] = "User";
                                                        $comment_user["Email"] = "No Email Found";
                                                        $comment_user["PhoneNumber"] = "No Phone Number Found";
                                                    }
                                ?>
                                                    <tr>
                                                        <td><?php echo $comment_user["FirstName"] . " " . $comment_user["LastName"]; ?></td>
                                                        <td><a href="mailto:<?php echo $comment_user["Email"]; ?>"><?php echo $comment_user["Email"]; ?></a></td>
                                                        <td><a href="tel:<?php echo $comment_user["PhoneNumber"]; ?>"><?php echo $comment_user["PhoneNumber"]; ?></a></td>
                                                        <td>
                                                            <?php
                                                                $comment_video_id = $comment['VideoID'];

                                                                $query = "SELECT * FROM videos WHERE id='$comment_video_id'";
                                                                $result = $db->query($query);
                                                        
                                                                if ($result->num_rows === 1) {
                                                                    $comment_video = $result->fetch_assoc();

                                                                    echo '<a href="' . $link_prefix . '/watch/?vid=' . $comment_video_id . '">' . $comment_video['Title'] . '</a>';
                                                                }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $comment["Message"]; ?></td>
                                                        <td><?php echo $comment["DateCreated"]; ?></td>
                                                        <td><a href="./theatres.php?pv=view&tid=<?php echo $selected_theatre['id']; ?>&remove_comment=<?php echo $comment["id"]; ?>">Remove</a></td>
                                                    </tr>
                                <?php
                                                }
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Ratings</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone Number</th>
                                    <th scope="col">Video</th>
                                    <th scope="col">Rating</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                    $query = "SELECT * FROM ratings";
                                    $ratings = $db->query($query);

                                    if ($ratings->num_rows > 0) {
                                        while ($rating = $ratings->fetch_assoc()) {
                                            $rating_video_id = $rating['VideoID'];

                                            $query = "SELECT * FROM videos WHERE id='$rating_video_id'";
                                            $result = $db->query($query);

                                            if ($result->num_rows === 1) {
                                                $result_array = $result->fetch_assoc();

                                                if ($result_array['TheatreID'] === $theatre_id) {
                                                    $rating_user_id = $rating['UserID'];

                                                    $query = "SELECT * FROM users WHERE id='$rating_user_id'";
                                                    $result = $db->query($query);
                                            
                                                    if ($result->num_rows === 1) {
                                                        $rating_user = $result->fetch_assoc();
                                                    } else {
                                                        $rating_user["FirstName"] = "Unknown";
                                                        $rating_user["LastName"] = "User";
                                                        $rating_user["Email"] = "No Email Found";
                                                        $rating_user["PhoneNumber"] = "No Phone Number Found";
                                                    }
                                ?>
                                                    <tr>
                                                        <td><?php echo $rating_user["FirstName"] . " " . $rating_user["LastName"]; ?></td>
                                                        <td><a href="mailto:<?php echo $rating_user["Email"]; ?>"><?php echo $rating_user["Email"]; ?></a></td>
                                                        <td><a href="tel:<?php echo $rating_user["PhoneNumber"]; ?>"><?php echo $rating_user["PhoneNumber"]; ?></a></td>
                                                        <td>
                                                            <?php
                                                                $rating_video_id = $rating['VideoID'];

                                                                $query = "SELECT * FROM videos WHERE id='$rating_video_id'";
                                                                $result = $db->query($query);
                                                        
                                                                if ($result->num_rows === 1) {
                                                                    $rating_video = $result->fetch_assoc();

                                                                    echo '<a href="' . $link_prefix . '/watch/?vid=' . $rating_video_id . '">' . $rating_video['Title'] . '</a>';
                                                                }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $rating["Rating"]; ?></td>
                                                        <td><?php echo $rating["DateCreated"]; ?></td>
                                                    </tr>
                                <?php
                                                }
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Delete Video Modal Trigger -->
                <button type="button" class="btn btn-block btn-outline-danger my-5" data-toggle="modal" data-target="#deleteModal">
                    Delete Theatre (Permanent)
                </button>
            </section>
        <?php } ?>
    </main>
    
    <?php if ($page_view === "view") { ?>
        <!-- Delete Theatre Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Theatre</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to permanently delete 
                        <span class="text-danger"><?php echo strtoupper($title); ?></span> 
                        and all of its information? All related videos will be 
                        deleted and members will be instructed to cancel all subscriptions!
                    </div>
                    <div class="modal-footer">
                        <a href="./theatres.php?delete_theatre=<?php echo $theatre_id?>" class="btn btn-danger">Yes, delete this Theatre</a>
                        <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../scripts/bootstrap.js"></script>

    <!-- Bootstrap Custom File -->
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init()
        })
    </script>

    <!-- Hide Alert Messages After Delay -->
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').css({"visibility":"hidden"});
            }, 5000);
        });
    </script>
</body>
</html>