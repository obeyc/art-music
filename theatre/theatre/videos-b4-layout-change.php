<?php
    include "../server/init.php";

    require '../vendor/autoload.php';
    use Vimeo\Vimeo;

    //$client = new Vimeo("{client_id}", "{client_secret}", "{access_token}");
    $client = new Vimeo("92aee3ed57c154519a678d2b8776fa340a89e7e3", "eHbqh6B/CUVj7ut51AmZlqbDu8WnDFlHKfSjUdOdnLwpA2+CdhmzN05R0tKa6U0Aq7RKT+8W0xH51/GqT24UPB/++L3AxioKNRnBWMHGb7DPPdKCfl5W1a4yy9z4tYYX", "43dfff1e1d75387ae5285011ca07fafd");

    if (!isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/account/signin/');
    }

    /* Check User Access */

    if ($user_access !== "theatre") {
        header('Location: ' . $link_prefix . '/');
    }

    /* Check User Theatre */
    $query = "SELECT * FROM theatres WHERE Status='enabled' AND id=" . $user_theatre;
    $result = $db->query($query);

    if ($result->num_rows === 1) {
        $user_theatre = $result->fetch_assoc();
    } else {
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

        /* Load Videos List */

        $query = "SELECT * FROM videos WHERE TheatreID=" . $user_theatre['id'] . " ORDER BY id DESC";
        $videos = $db->query($query);
    } else {

        /* Load Videos List by Search Term */

        $query = "SELECT * FROM videos WHERE TheatreID=" . $user_theatre['id'] . " AND LOWER(Title) LIKE '%$search_query%' ORDER BY Title";
        $videos = $db->query($query);
    }

    /* Load Categories */

    $query = "SELECT * FROM categories";
    $add_categories = $db->query($query);

    $title = "";
    $shortDescription = "";
    $longDescription = "";
    $length = "";
    $price = "";
    $category = "";
    $starring = "";
    $releaseDate = "";
    $videoType = "";

    $success_message = "";
    $error_message = "";

    /* Create Video Form Submit */

    if (isset($_POST['addSubmit'])) {

        /* Input Validation */

        if (isset($_POST['addTitle']) && !empty($_POST['addTitle'])) {
            $title = mysqli_real_escape_string($db, $_POST['addTitle']);

            $query = "SELECT * FROM videos WHERE Title='$title'";
            $result = $db->query($query);

            if ($result->num_rows != 0) {
                $error_message = "Title unavailable. Please provide a title that is not already in use!";
            }
        } else {
            $error_message = "Please provide a title.";
        }

        if (isset($_POST['addShortDescription']) && !empty($_POST['addShortDescription'])) {
            $shortDescription = mysqli_real_escape_string($db, $_POST['addShortDescription']);
        } else {
            $error_message = "Please provide a Short Description.";
        }

        if (isset($_POST['addLongDescription']) && !empty($_POST['addLongDescription'])) {
            $longDescription = $_POST['addLongDescription'];
        } else {
            $error_message = "Please provide a Long Description.";
        }

        if (isset($_POST['addLength']) && !empty($_POST['addLength'])) {
            $length = mysqli_real_escape_string($db, $_POST['addLength']);
        } else {
            $error_message = "Please provide a video Length in mm:ss format.";
        }

        if (isset($_POST['addPrice']) && !empty($_POST['addPrice'])) {
            $price = mysqli_real_escape_string($db, $_POST['addPrice']);
        } else {
            $error_message = "Please provide a Price.";
        }

        if (isset($_POST['addCategory']) && !empty($_POST['addCategory'])) {
            $category = mysqli_real_escape_string($db, $_POST['addCategory']);
        } else {
            $error_message = "Please select a Category.";
        }

        if (isset($_POST['addStarring']) && !empty($_POST['addStarring'])) {
            $starring = mysqli_real_escape_string($db, $_POST['addStarring']);
        } else {
            $error_message = "Please provide a list of people Starring in the video.";
        }

        if (isset($_POST['addReleaseDate']) && !empty($_POST['addReleaseDate'])) {
            $releaseDate = mysqli_real_escape_string($db, $_POST['addReleaseDate']);
        } else {
            $error_message = "Please provide a Release Date";
        }

        if (isset($_POST['addType']) && !empty($_POST['addType'])) {
            $videoType = mysqli_real_escape_string($db, $_POST['addType']);
        } else {
            $error_message = "Please select a Video Type.";
        }

        if ($_FILES["addThumb"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['addThumb']['name'],PATHINFO_EXTENSION));
        
            $target_dir = "../media/images/thumbs/";
            $target_file = $target_dir . file_name($title) . '-thumb.' . $imageFileType;
        
            $uploadOk = 1;
        
            if ($imageFileType != "png") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES["addThumb"]["tmp_name"], $target_file);
            } else {
                $error_message = "Thumbnail File Upload Error. Try Again!";
            }
        } else {
            $error_message = "Please provide a video thumbnail file (.PNG).";
        }

        if ($_FILES["addPreview"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['addPreview']['name'],PATHINFO_EXTENSION));
        
            $target_dir = "../media/videos/preview/";
            $target_file = $target_dir . file_name($title) . '-preview.' . $imageFileType;
        
            $uploadOk = 1;
        
            if ($imageFileType != "mp4") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES["addPreview"]["tmp_name"], $target_file);
            } else {
                $error_message = "Preview File Upload Error. Try Again!";
            }
        } else {
            $error_message = "Please provide a video preview file (.mp4).";
        }

        /* Upload Video to Vimeo */

        if ($_FILES["addVideo"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['addVideo']['name'],PATHINFO_EXTENSION));
        
            $uploadOk = 1;
        
            if ($imageFileType != "mp4") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                $file_name = $_FILES["addVideo"]["tmp_name"];
                try {
                $uri = $client->upload($file_name, array(
                    "name" => $title,
                    "description" => $shortDescription,
                    "privacy"=> array( "view" => "disable" )
                ));

                $vimeoLink = "https://player.vimeo.com" . str_replace('s', '', $uri);
                } catch(Exception $e) {
                     $error_message = "Error at Vimoe. Try Again later!".$e;
                }
            } else {
                $error_message = "Video File Upload Error. Try Again!";
            }
        } else {
            $error_message = "Please provide a video file file (.mp4).";
        }

        /* Create Video */

        $default_popularity = 0;
    
        if (empty($error_message)) {
            
            $db = new mysqli($servname, $username, $password, $database);
            
            $stmt = $db->prepare("INSERT INTO videos (Title, ShortDescription, LongDescription, Length, Price, TheatreID, CategoryID, Starring, ReleaseDate, VimeoLink, VideoType, Popularity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssiissssi", $title, $shortDescription, $longDescription, $length, $price, $user_theatre['id'], $category, $starring, $releaseDate, $vimeoLink, $videoType, $default_popularity);
            $stmt->execute();

            /* Create Notification: New Video */

            $query = "SELECT * FROM videos WHERE Title='$title'";
            $notification_result = $db->query($query);

            $notification_video = $notification_result->fetch_assoc();

            $notification_description = "New Video: " . $title;
            $notification_link = "https://artmusic.tv/watch/?vid=" . $notification_video['id'];
            $notification_theatre = $user_theatre['id'];
            $notification_status = 0;

            $query = "SELECT * FROM users";
            $notification_users = $db->query($query);

            while ($notification_u = $notification_users->fetch_assoc()) {
                $stmt = $db->prepare("INSERT INTO notifications (UserID, Description, Link, TheatreID, Status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issii", $notification_u['id'], $notification_description, $notification_link, $notification_theatre, $notification_status);
                $stmt->execute();
            }

            /*

            $default_email_sent = 0;

            $query = "SELECT * FROM users";
            $notification_users = $db->query($query);

            while ($notification_u = $notification_users->fetch_assoc()) {
                $stmt = $db->prepare("INSERT INTO emails (UserID, VideoID, VideoTitle, EmailSent) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iisi", $notification_u['id'], $notification_video['id'], $title, $default_email_sent);
                $stmt->execute();
            }
            
            */

            $title = "";
            $shortDescription = "";
            $longDescription = "";
            $length = "";
            $price = "";
            $category = "";
            $starring = "";
            $releaseDate = "";
            $videoType = "";
        
            $success_message = "Video has been added!";
        }
    }

    /* Load Selected Video */

    if ($page_view === "view") {
        if (isset($_GET['vid']) && !empty($_GET['vid'])) {
            $video_id = $_GET['vid'];

            $query = "SELECT * FROM videos WHERE id='$video_id'";
            $result = $db->query($query);

            if ($result->num_rows === 1) {
                $selected_video = $result->fetch_assoc();

                $title = $selected_video['Title'];
                $shortDescription = $selected_video['ShortDescription'];
                $longDescription = $selected_video['LongDescription'];
                $length = $selected_video['Length'];
                $price = $selected_video['Price'];
                $category = $selected_video['CategoryID'];
                $starring = $selected_video['Starring'];
                $releaseDate = $selected_video['ReleaseDate'];
                $videoType = $selected_video['VideoType'];
            } else {
                header('Location: ./videos.php');
            }
        } else {
            header('Location: ./videos.php');
        }
    }

    /* Update Video Form Submit */

    if (isset($_POST['updateSubmit'])) {

        /* Input Validation */

        if (isset($_POST['updateShortDescription']) && !empty($_POST['updateShortDescription'])) {
            $shortDescription = utf8_decode($_POST['updateShortDescription']);
        } else {
            $error_message = "Please provide a Short Description.";
        }

        if (isset($_POST['updateLongDescription']) && !empty($_POST['updateLongDescription'])) {
            $longDescription = $_POST['updateLongDescription'];
        } else {
            $error_message = "Please provide a Long Description.";
        }

        if (isset($_POST['updateLength']) && !empty($_POST['updateLength'])) {
            $length = mysqli_real_escape_string($db, $_POST['updateLength']);
        } else {
            $error_message = "Please provide a video Length in mm:ss format.";
        }

        if (isset($_POST['updatePrice']) && !empty($_POST['updatePrice'])) {
            $price = mysqli_real_escape_string($db, $_POST['updatePrice']);
        } else {
            $error_message = "Please provide a Price.";
        }

        if (isset($_POST['updateCategory']) && !empty($_POST['updateCategory'])) {
            $category = mysqli_real_escape_string($db, $_POST['updateCategory']);
        } else {
            $error_message = "Please select a Category.";
        }

        if (isset($_POST['updateStarring']) && !empty($_POST['updateStarring'])) {
            $starring = mysqli_real_escape_string($db, $_POST['updateStarring']);
        } else {
            $error_message = "Please provide a list of people Starring in the video.";
        }

        if (isset($_POST['updateReleaseDate']) && !empty($_POST['updateReleaseDate'])) {
            $releaseDate = mysqli_real_escape_string($db, $_POST['updateReleaseDate']);
        } else {
            $error_message = "Please provide a Release Date";
        }

        if (isset($_POST['updateType']) && !empty($_POST['updateType'])) {
            $videoType = mysqli_real_escape_string($db, $_POST['updateType']);
        } else {
            $error_message = "Please select a Video Type.";
        }

        if ($_FILES["updateThumb"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['updateThumb']['name'],PATHINFO_EXTENSION));
        
            $target_dir = "../media/images/thumbs/";
            $target_file = $target_dir . file_name($title) . '-thumb.' . $imageFileType;
        
            $uploadOk = 1;
        
            if ($imageFileType != "png") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES["updateThumb"]["tmp_name"], $target_file);
            } else {
                $error_message = "Thumbnail File Upload Error. Try Again!";
            }
        }

        if ($_FILES["updatePreview"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['updatePreview']['name'],PATHINFO_EXTENSION));
        
            $target_dir = "../media/videos/preview/";
            $target_file = $target_dir . file_name($title) . '-preview.' . $imageFileType;
        
            $uploadOk = 1;
        
            if ($imageFileType != "mp4") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES["updatePreview"]["tmp_name"], $target_file);
            } else {
                $error_message = "Preview File Upload Error. Try Again!";
            }
        }

        /* Upload Video to Vimeo */

        if ($_FILES["updateVideo"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['updateVideo']['name'],PATHINFO_EXTENSION));
        
            $uploadOk = 1;
        
            if ($imageFileType != "mp4") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                $file_name = $_FILES["updateVideo"]["tmp_name"];
                $uri = $client->upload($file_name, array(
                    "name" => $title,
                    "description" => $shortDescription
                ));

                $vimeoLink = "https://player.vimeo.com" . str_replace('s', '', $uri);
            } else {
                $error_message = "Video File Upload Error. Try Again!";
            }
        } else {
            $vimeoLink = $selected_video['VimeoLink'];
        }

        /* Update Video */
    
        if (empty($error_message)) {
            $db = new mysqli($servname, $username, $password, $database);
            
            $stmt = $db->prepare("UPDATE videos SET ShortDescription=?, LongDescription=?, Length=?, Price=?, CategoryID=?, Starring=?, ReleaseDate=?, VimeoLink=?, VideoType=? WHERE id=?");
            $stmt->bind_param("ssssissssi", $shortDescription, $longDescription, $length, $price, $category, $starring, $releaseDate, $vimeoLink, $videoType, $selected_video['id']);
            $stmt->execute();
        
            $success_message = "Video has been updated!";
        }
    }

    $email = "";

    /* Give FREE Access to Video */
    if (isset($_POST['submitAccess'])) {
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $email = $_POST['email'];

            $query = "SELECT * FROM users WHERE Email='$email'";
            $result = $db->query($query);

            if ($result->num_rows === 1) {
                $access_user = $result->fetch_assoc();

                $query = "INSERT INTO payments (UserID, TheatreID, VideoID, Description, Amount, Fee, PayFastID) VALUES (" . $access_user['id'] . ", " . $user_theatre['id'] . ", " . $selected_video['id'] . ", 'Free Access: " . remove_special_char($selected_video['Title']) . "', '0', '0', 'FREE')";
                $db->query($query);

                $email = "";
                $success_message = "User has been given free access!";
            } else {
                $error_message = "Email was not found. Please provide an exising user's email.";
            }
        } else {
            $error_message = "Please provide a valid email addess.";
        }
    }

    /* Remove Admin Access */

    if (isset($_GET['remove_access']) && !empty($_GET['remove_access'])) {
        $remove_id = $_GET['remove_access'];
        
        $query = "DELETE FROM payments WHERE id=" . $remove_id;
        $db->query($query);

        $success_message = "Users free access has been removed!";
    }

    /* Load FREE Access */

    if ($page_view === "view") {
        $query = "SELECT payments.*, users.FirstName, users.LastName, users.Email, users.PhoneNumber FROM payments INNER JOIN users ON users.id = payments.UserID WHERE payments.PayFastID='FREE' AND payments.VideoID=" . $selected_video['id'];
        $free_access_list = $db->query($query);
    }

    /* Remove Comment */

    if (isset($_GET["remove_comment"]) && !empty($_GET["remove_comment"])) {
        $comment_id = $_GET['remove_comment'];

        $query = "DELETE FROM comments WHERE id='$comment_id'";
        $db->query($query);

        $success_message = "Comment has been deleted!";
    }

    /* Delete Video */

    if (isset($_GET["delete_video"]) && !empty($_GET["delete_video"])) {
        $delete_id = $_GET["delete_video"];

        $query = "DELETE FROM videos WHERE id='$delete_id'";
        $db->query($query);

        $query = "DELETE FROM favourites WHERE VideoID='$delete_id'";
        $db->query($query);

        header('Location: ./videos.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="All About Cloud South Africa">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Theatre Videos</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/master.css">
</head>
<body class="container-fluid p-0 m-0">
    <!-- Import Theatre Navigation Bar -->
    <?php include "../server/includes/theatre-navigation.php"; ?>

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
            <!-- List of Videos Section (Default) -->
            <section class="container-fluid">
                <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                    <h2 class="d-inline-block m-0 p-0">Videos</h2>
                    <a class="btn btn-primary" href="./videos.php?pv=add">Upload Video</a>
                </div>

                <div class="d-flex justify-content-between align-items-end mx-2 mb-4 mobile-d-block">
                    <form class="form-inline mobile-margin-b-1" action="" method="GET">
                        <input class="form-control mr-sm-2" type="text" name="search" placeholder="Search" value="<?php if ($search_query !== "No Search Term!") { echo $search_query; } ?>" required>
                        <button class="btn btn-secondary my-2 my-sm-0 mr-2" type="submit">
                            Search
                        </button>
                        <a class="text-success mobile-d-block mobile-width-100 mobile-text-center mobile-margin-2" href="./videos.php">Clear</a>
                    </form>
                </div>

                <?php if ($videos->num_rows > 0) { ?>
                    <?php while($video = $videos->fetch_assoc()) : ?>
                        <div class="row text-center bg-dark rounded mb-4 mx-2 px-1 py-4">
                            <div class="col-md-2">
                                <img src="<?php echo $link_prefix; ?>/media/images/thumbs/<?php echo file_name($video['Title']); ?>-thumb.png" class="table-image-sm mobile-margin-b-2" alt="<?php echo $video['Title']; ?>">
                            </div>
                            <div class="col-md-3 text-left">
                                <h5 class="mobile-margin-b-2"><?php echo $video['Title']; ?></h5>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-2">
                                <h6 class="mobile-text-left">
                                    <?php
                                        $query = "SELECT * FROM views WHERE VideoID=" . $video['id'];
                                        $video_views = $db->query($query);

                                        echo $video_views->num_rows;
                                    ?>
                                    views
                                </h6>
                            </div>
                            <div class="col-md-2">
                                <h6 class="mobile-text-left mobile-margin-b-2"><?php echo $video['ReleaseDate']; ?></h6>
                            </div>
                            <div class="col-md-2">
                                <a class="btn btn-block btn-primary" href="./videos.php?pv=view&vid=<?php echo $video['id']; ?>">View & Update</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php } else { ?>
                    <h1 class="text-center my-5">There are no videos to display. Try changing your search term.</h1>
                <?php } ?>
            </section>
        <?php } ?>

        <?php if ($page_view === "add") { ?>
            <!-- Upload Video Section -->
            <section class="mb-5 container">
                <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                    <h2 class="d-inline-block m-0 p-0">Upload Video</h2>
                    <a class="btn btn-primary" href="./videos.php">Back to Videos</a>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="addTitle">Title*</label>
                            <input type="text" class="form-control" id="addTitle" name="addTitle" maxlength="60" value="<?php echo $title; ?>" required>
                            <small class="form-text text-muted">
                                Max 60 Characters! (can't be changed after uploading)
                            </small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="addCategory">Category</label>
                            <select id="addCategory" name="addCategory" class="form-control">
                                <option value="none">No Category</option>
                                <?php while($category = $add_categories->fetch_assoc()) : ?>
                                    <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $category) { echo "selected"; } ?>><?php echo $category['Title']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="addShortDescription">Short Description*</label>
                            <textarea class="form-control" id="addShortDescription" name="addShortDescription" rows="6" maxlength="125" required><?php echo $shortDescription; ?></textarea>
                            <small class="form-text text-muted">
                                Max 125 Characters!
                            </small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="addLongDescription">Long Description*</label>
                            <textarea class="form-control" id="addLongDescription" name="addLongDescription" rows="6" required><?php echo $longDescription; ?></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="addLength">Length* (mm:ss)</label>
                            <input type="text" class="form-control" id="addLength" name="addLength" value="<?php echo $length; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="addPrice">Price*</label>
                            <input type="text" class="form-control" id="addPrice" name="addPrice" value="<?php echo $price; ?>" required>
                        </div>
                    </div>
                    <div class="form-row">                        
                        <div class="form-group col-md-6">
                            <label for="addStarring">Starring</label>
                            <input type="text" class="form-control" id="addStarring" name="addStarring" value="<?php echo $starring; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="addReleaseDate">Release Date*</label>
                            <input type="datetime-local" class="form-control" id="addReleaseDate" name="addReleaseDate" value="<?php echo str_replace(" ", "T", $releaseDate); ?>" required>
                        </div>
                    </div>

                    <hr class="border-white my-4">
                    <div class="form-group">
                        <label for="addType">Video Type</label>
                        <select id="addType" name="addType" class="form-control" required>
                            <option value="standard" <?php if ($videoType === "standard") { echo "selected"; } ?>>Standard Video</option>
                            <option value="exclusive" <?php if ($videoType === "exclusive") { echo "selected"; } ?>>Exclusive Video (only selected users have access)</option>
                        </select>
                    </div>
                    <hr class="border-white my-4">
                    <div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" id="addThumb" name="addThumb" required>
                        <label class="custom-file-label" for="addThumb">Thumbnail Image* (.PNG)</label>
                    </div>
                    <div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" id="addPreview" name="addPreview" required>
                        <label class="custom-file-label" for="addPreview">Preview Video File* (.MP4)</label>
                    </div>
                    <div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" id="addVideo" name="addVideo" required>
                        <label class="custom-file-label" for="addVideo">Full Video File* (.MP4)</label>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary" name="addSubmit">Upload</button>
                    </div>
                </form>
            </section>
        <?php } ?>

        <?php if ($page_view === "view") { ?>
            <!-- View & Update Video Section -->
            <section class="mb-5">
                <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                    <h2 class="d-inline-block m-0 p-0">View & Update Video</h2>
                    <a class="btn btn-primary" href="./videos.php">Back to Videos</a>
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
                                            $query = "SELECT * FROM views WHERE VideoID=" . $selected_video['id'];
                                            $result = $db->query($query);

                                            echo $result->num_rows;
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
                                            $query = "SELECT * FROM previews WHERE VideoID=" . $selected_video['id'];
                                            $result = $db->query($query);

                                            echo $result->num_rows;
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
                                            $query = "SELECT * FROM payments WHERE PayFastID<>'FREE' AND VideoID=" . $selected_video['id'];
                                            $result = $db->query($query);

                                            echo $result->num_rows;
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
                                        <span class="text-success">
                                            <?php
                                                $query = "SELECT * FROM ratings WHERE Rating='good' AND VideoID=" . $selected_video['id'];
                                                $result = $db->query($query);

                                                echo $result->num_rows;
                                            ?>
                                        </span> 
                                        / 
                                        <span class="text-danger">
                                            <?php
                                                $query = "SELECT * FROM ratings WHERE Rating='bad' AND VideoID=" . $selected_video['id'];
                                                $result = $db->query($query);

                                                echo $result->num_rows;
                                            ?>
                                        </span>
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
                                            $query = "SELECT * FROM comments WHERE VideoID=" . $selected_video['id'];
                                            $result = $db->query($query);

                                            echo $result->num_rows;
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
                                            $query = "SELECT * FROM shares WHERE VideoID=" . $selected_video['id'];
                                            $result = $db->query($query);

                                            echo $result->num_rows;
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Video Card Preview -->
                <div class="card mb-3">
                    <div class="row no-gutters">
                        <div class="col-md-4">          
                            <span class="badge badge-success position-absolute rounded-0 p-1">
                                <?php echo $selected_video['Length']; ?>
                            </span>
                            <img src="<?php echo $link_prefix; ?>/media/images/thumbs/<?php echo file_name($selected_video['Title']); ?>-thumb.png" class="card-img rounded-0 card-image-lg" alt="<?php echo $selected_video["Title"]; ?> Thumbnail">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <?php echo $selected_video['Title']; ?>
                                </h5>
                                <div class="mb-4">
                                    <p class="d-inline-block text-muted m-0 p-0">
                                        <?php echo $user_theatre['Title']; ?>
                                    </p>
                                </div>
                                <p class="card-text">
                                    <?php echo $selected_video['ShortDescription']; ?>
                                </p>
                                <div class="border-top pt-2 pb-4 text-right">
                                    <span class="card-link text-success">Watch</span>
                                    <span class="card-link text-success">Add to Favourites</span>
                                </div>
                                <div class="pt-2 pb-4">
                                    <small class="float-left">
                                        <?php
                                            $query = "SELECT * FROM views WHERE VideoID=" . $selected_video['id'];
                                            $result = $db->query($query);

                                            echo $result->num_rows;
                                        ?>
                                        Views
                                        - 
                                        <?php
                                            $query = "SELECT * FROM comments WHERE VideoID=" . $selected_video['id'];
                                            $result = $db->query($query);

                                            echo $result->num_rows;
                                        ?>
                                        Comments
                                    </small>
                                    <small class="float-right">
                                        <?php echo time_elapsed_string($selected_video["ReleaseDate"]); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Copy Video Link -->
                <button class="btn btn-block btn-outline-success copy-link-btn mt-4 mb-5" data-clipboard-text="https://artmusic.tv/watch?vid=<?php echo $selected_video['id']; ?>">
                    Copy Video Link to Clipboard
                </button>

                <!-- Update Video Information -->
                <form class="mb-5" action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="updateShortDescription">Short Description*</label>
                        <textarea class="form-control" id="updateShortDescription" name="updateShortDescription" rows="2" maxlength="125" required><?php echo $shortDescription; ?></textarea>
                        <small class="form-text text-muted">
                            Max 125 Characters!
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="updateLongDescription">Long Description*</label>
                        <textarea class="form-control" id="updateLongDescription" name="updateLongDescription" rows="6" required><?php echo $longDescription; ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="updateLength">Length* (mm:ss)</label>
                            <input type="text" class="form-control" id="updateLength" name="updateLength" value="<?php echo $length; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="updatePrice">Price*</label>
                            <input type="text" class="form-control" id="updatePrice" name="updatePrice" value="<?php echo $price; ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="updateCategory">Category</label>
                        <select id="updateCategory" name="updateCategory" class="form-control">
                            <option value="none">No Category</option>
                            <?php while($c = $add_categories->fetch_assoc()) : ?>
                                <option value="<?php echo $c['id']; ?>" <?php if ($c['id'] == $category) { echo "selected"; } ?>><?php echo $c['Title']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="updateStarring">Starring</label>
                        <input type="text" class="form-control" id="updateStarring" name="updateStarring" value="<?php echo $starring; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="updateReleaseDate">Release Date*</label>
                        <input type="datetime-local" class="form-control" id="updateReleaseDate" name="updateReleaseDate" value="<?php echo str_replace(" ", "T", $releaseDate); ?>" required>
                    </div>
                    <hr class="border-white my-4">
                    <div class="form-group">
                        <label for="updateType">Video Type</label>
                        <select id="updateType" name="updateType" class="form-control" required>
                            <option value="standard" <?php if ($videoType === "standard") { echo "selected"; } ?>>Standard Video</option>
                            <option value="exclusive" <?php if ($videoType === "exclusive") { echo "selected"; } ?>>Exclusive Video (only selected users have access)</option>
                        </select>
                    </div>
                    <hr class="border-white my-4">
                    <div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" id="updateThumb" name="updateThumb">
                        <label class="custom-file-label" for="updateThumb">Thumbnail Image* (.PNG)</label>
                    </div>
                    <div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" id="updatePreview" name="updatePreview">
                        <label class="custom-file-label" for="updatePreview">Preview Video File* (.MP4)</label>
                    </div>
                    <div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" id="updateVideo" name="updateVideo">
                        <label class="custom-file-label" for="updateVideo">Full Video File* (.MP4)</label>
                    </div>
                    <button type="submit" class="btn btn-primary" name="updateSubmit">Update</button>
                </form>

                <!-- Manage Free Access -->
                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Give Free Access</h5>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="email">Account Email*</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary mb-5" name="submitAccess">Give</button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone Number</th>
                                    <th scope="col">Date Received</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($access = $free_access_list->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?php echo $access['FirstName'] . ' ' . $access['LastName']; ?></td>
                                        <td><a href="mailto:<?php echo $access['Email']; ?>"><?php echo $access['Email']; ?></a></td>
                                        <td><a href="tel:<?php echo $access['PhoneNumber']; ?>"><?php echo $access['PhoneNumber']; ?></a></td>
                                        <td><?php echo $access['DateCreated']; ?></td>
                                        <td>
                                            <a href="./videos.php?pv=view&vid=<?php echo $selected_video['id']; ?>&remove_access=<?php echo $access['id']; ?>">Remove</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
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
                                    <th scope="col">Payment Method</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM views WHERE VideoID=" . $selected_video['id'] . " ORDER BY DateCreated DESC";
                                    $views = $db->query($query);

                                    while ($view = $views->fetch_assoc()) {
                                        $view_user_id = $view['UserID'];

                                        $query = "SELECT * FROM users WHERE id=" . $view_user_id;
                                        $result = $db->query($query);

                                        if ($result->num_rows === 1) {
                                            $view_user = $result->fetch_assoc();
                                        } else {
                                            $view_user['FirstName'] = "Unknown";
                                            $view_user['LastName'] = "User";
                                            $view_user['Email'] = "No Email Found!";
                                            $view_user['PhoneNumber'] = "No Phone Number Found";
                                        }
                                ?>
                                    <tr>
                                        <td><?php echo $view_user['FirstName'] . " " . $view_user['LastName']; ?></td>
                                        <td><a href="mailto:<?php echo $view_user['Email']; ?>"><?php echo $view_user['Email']; ?></a></td>
                                        <td><a href="tel:<?php echo $view_user['PhoneNumber']; ?>"><?php echo $view_user['PhoneNumber']; ?></a></td>
                                        <td><?php echo $view['ViewType']; ?></td>
                                        <td><?php echo $view['DateCreated']; ?></td>
                                    </tr>
                                <?php } ?>
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
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM previews WHERE VideoID=" . $selected_video['id'] . " ORDER BY DateCreated DESC";
                                    $previews = $db->query($query);

                                    while ($preview = $previews->fetch_assoc()) {
                                        $preview_user_id = $preview['UserID'];

                                        $query = "SELECT * FROM users WHERE id=" . $preview_user_id;
                                        $result = $db->query($query);

                                        if ($result->num_rows === 1) {
                                            $preview_user = $result->fetch_assoc();
                                        } else {
                                            $preview_user['FirstName'] = "Unknown";
                                            $preview_user['LastName'] = "User";
                                            $preview_user['Email'] = "No Email Found!";
                                            $preview_user['PhoneNumber'] = "No Phone Number Found";
                                        }
                                ?>
                                    <tr>
                                        <td><?php echo $preview_user['FirstName'] . " " . $preview_user['LastName']; ?></td>
                                        <td><a href="mailto:<?php echo $preview_user['Email']; ?>"><?php echo $preview_user['Email']; ?></a></td>
                                        <td><a href="tel:<?php echo $preview_user['PhoneNumber']; ?>"><?php echo $preview_user['PhoneNumber']; ?></a></td>
                                        <td><?php echo $preview['DateCreated']; ?></td>
                                    </tr>
                                <?php } ?>
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
                                    <th scope="col">Comment</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                    $query = "SELECT * FROM comments WHERE VideoID=" . $selected_video['id'] . " ORDER BY DateCreated DESC";
                                    $comments = $db->query($query);

                                    while ($comment = $comments->fetch_assoc()) {
                                        $comment_user_id = $comment['UserID'];

                                        $query = "SELECT * FROM users WHERE id=" . $comment_user_id;
                                        $result = $db->query($query);

                                        if ($result->num_rows === 1) {
                                            $comment_user = $result->fetch_assoc();
                                        } else {
                                            $comment_user['FirstName'] = "Unknown";
                                            $comment_user['LastName'] = "User";
                                            $comment_user['Email'] = "No Email Found!";
                                            $comment_user['PhoneNumber'] = "No Phone Number Found";
                                        }
                                ?>
                                    <tr>
                                        <td><?php echo $comment_user['FirstName'] . " " . $comment_user['LastName']; ?></td>
                                        <td><a href="mailto:<?php echo $comment_user['Email']; ?>"><?php echo $comment_user['Email']; ?></a></td>
                                        <td><?php echo $comment['Message']; ?></td>
                                        <td><a href="tel:<?php echo $comment_user['PhoneNumber']; ?>"><?php echo $comment_user['PhoneNumber']; ?></a></td>
                                        <td><?php echo $comment['DateCreated']; ?></td>
                                        <td><a href="./videos.php?pv=view&vid=<?php echo $selected_video['id']; ?>&remove_comment=<?php echo $comment['id']; ?>">Remove</a></td>
                                    </tr>
                                <?php } ?>
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
                                    <th scope="col">Rating</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM ratings WHERE VideoID=" . $selected_video['id'] . " ORDER BY DateCreated DESC";
                                    $ratings = $db->query($query);

                                    while ($rating = $ratings->fetch_assoc()) {
                                        $rating_user_id = $rating['UserID'];

                                        $query = "SELECT * FROM users WHERE id=" . $rating_user_id;
                                        $result = $db->query($query);

                                        if ($result->num_rows === 1) {
                                            $rating_user = $result->fetch_assoc();
                                        } else {
                                            $rating_user['FirstName'] = "Unknown";
                                            $rating_user['LastName'] = "User";
                                            $rating_user['Email'] = "No Email Found!";
                                            $rating_user['PhoneNumber'] = "No Phone Number Found";
                                        }
                                ?>
                                    <tr>
                                        <td><?php echo $rating_user['FirstName'] . " " . $rating_user['LastName']; ?></td>
                                        <td><a href="mailto:<?php echo $rating_user['Email']; ?>"><?php echo $rating_user['Email']; ?></a></td>
                                        <td><a href="tel:<?php echo $rating_user['PhoneNumber']; ?>"><?php echo $rating_user['PhoneNumber']; ?></a></td>
                                        <td><?php echo ucfirst($rating['Rating']); ?></td>
                                        <td><?php echo $rating['DateCreated']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Delete Video Modal Trigger -->
                <button type="button" class="btn btn-block btn-outline-danger my-5" data-toggle="modal" data-target="#deleteModal">
                    Delete Video (Permanent)
                </button>
            </section>
        <?php } ?>
    </main>
    
    <?php if ($page_view === "view") { ?>
        <!-- Delete Video Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Video</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    Are you sure you want to permanently delete 
                        <span class="text-danger"><?php echo strtoupper($selected_video['Title']); ?></span> 
                        and all of its information? All related reports will be 
                        deleted and payments will be canceled.
                    </div>
                    <div class="modal-footer">
                        <a href="./videos.php?delete_video=<?php echo $selected_video['id']; ?>" class="btn btn-danger">Yes, delete this video</a>
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

    <!-- Clipboard JS -->
    <script src="../server/clipboard/dist/clipboard.min.js"></script>
    <script>
        const clipboard = new ClipboardJS('.copy-link-btn');

        clipboard.on('success', function(e) {
            $(".copy-link-btn").html('Copied!');
            setTimeout(function() {
                $(".copy-link-btn").html('Copy Video Link to Clipboard');
            }, 5000);

            e.clearSelection();
        });
    </script>

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
            /*setTimeout(function() {
                $('.alert').css({"visibility":"hidden"});
            }, 15000);*/
        });
    </script>
</body>
</html>