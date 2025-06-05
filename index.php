<?php

    include "server/init.php";

    /* Load Latest Videos */

    $query = "SELECT * FROM videos WHERE ReleaseDate < NOW() ORDER BY ReleaseDate DESC LIMIT 3";
    $latest_videos = $db->query($query);

    /* Load Upcoming Videos */

    $query = "SELECT * FROM videos WHERE ReleaseDate > NOW() ORDER BY ReleaseDate ASC LIMIT 3";
    $upcoming_videos = $db->query($query);

    /* Load Popular Videos */

    $query = "SELECT * FROM videos WHERE ReleaseDate < CURDATE() ORDER BY Popularity DESC LIMIT 3";
    $popular_videos = $db->query($query);

    /* Load Recommended Videos */

    $query = "SELECT * FROM videos ORDER BY rand() DESC LIMIT 3";
    $recommended_videos = $db->query($query);

    /* Load Favourite Videos List */

    $query = "SELECT * FROM favourites WHERE UserID='$user_id' ORDER BY DateCreated DESC LIMIT 3";
    $favourite_videos_list = $db->query($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Stream the latest and greatest musical performance South Africa has to offer.">
    <meta name="keywords" content="artmusic tv, south africa, music, classical, theatre">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="styles/bootstrap.min.css">
    <link rel="stylesheet" href="styles/master.css">
    <link rel="stylesheet" href="styles/custom.css">
</head>
<body class="container-fluid p-0 m-0">
    
    <!-- Import Navigation Bar -->
    <?php include "server/includes/navigation.php"; ?>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

    <!-- Welcome Section -->
    <div class="jumbotron mx-4">
        <div class="row">
            <div class="col-md-6 col-lg-4">
                <h3>Welcome to ArtMusic TV</h3>
                <p class="mt-4 mb-4">
                    This is an all encompassing platform taking care of all your Art Music needs. 
                    A modern full-featured web application that will allow users to effortlessly stream 
                    premium quality videos and / or music files on all major browser-enabled devices. 
                    For more information visit our 
                    <a href="<?php echo $link_prefix; ?>/guide/">User Guide</a>.
                </p>
                <?php if (!isset($_SESSION["user_id"])) { ?>
                    <div class="mb-4">
                        <a class="btn btn-outline-success" href="<?php echo $link_prefix; ?>/account/create/">Create Account</a>
                        <a class="btn btn-success" href="<?php echo $link_prefix; ?>/account/signin/">Sign In</a>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-6 col-lg-8">
                <div id="welcomeCarousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="d-block w-100 rounded" src="<?php echo $link_prefix; ?>/media/images/banners/banner-1.jpg" alt="ArtMusic TV Banner #1">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100 rounded" src="<?php echo $link_prefix; ?>/media/images/banners/banner-2.jpg" alt="ArtMusic TV Banner #2">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100 rounded" src="<?php echo $link_prefix; ?>/media/images/banners/banner-3.jpg" alt="ArtMusic TV Banner #3">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#welcomeCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#welcomeCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Videos Section -->
    <?php if ($latest_videos->num_rows === 3) { ?>
        <section class="border-bottom mx-4 mb-5 pb-5">
            <h2>Latest</h2>

            <div class="card-deck">
                <?php while($video = $latest_videos->fetch_assoc()) : ?>
                    <?php
                        $releaseDate = new DateTime($video['ReleaseDate']);
                        $date = new DateTime();

                        if ($releaseDate > $date) {
                            $video_cta = "Pre-Order";
                        } else {
                            $video_cta = "Watch";

                            $video_id = $video["id"];

                            $query = "SELECT * FROM views WHERE VideoID='$video_id'";
                            $video_views = $db->query($query);

                            $query = "SELECT * FROM comments WHERE VideoID='$video_id'";
                            $video_comments = $db->query($query);
                        }
                    ?>
                    <div class="card">
                        <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>">
                            <span class="badge badge-success position-absolute rounded-0 p-1">
                                <?php echo $video["Length"]; ?>
                            </span>
                            <img src="<?php echo $link_prefix; ?>/media/images/thumbs/<?php echo file_name($video['Title']); ?>-thumb.png" class="card-img-top card-image-sm" alt="<?php echo $video["Title"]; ?> Thumbnail">
                        </a>
                        <div class="card-body">
                            <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>" class="d-inline-block">
                                <h5 class="card-title">
                                    <?php echo $video["Title"]; ?>
                                </h5>
                            </a>
                            <div class="mb-4">
                                <a class="d-inline-block text-muted" href="<?php echo $link_prefix; ?>/browse/theatre/?tid=<?php echo $video["TheatreID"]; ?>">
                                    <?php
                                        $theatre_id = $video["TheatreID"];

                                        $stmt = $db->prepare("SELECT * FROM theatres WHERE id=?");
                                        $stmt->bind_param("i", $theatre_id);
                                        $stmt->execute();
                    
                                        $result = $stmt->get_result();
                    
                                        if ($result->num_rows === 1) {
                                            $result_array = $result->fetch_assoc();
                                            echo $result_array["Title"];
                                        } else {
                                            echo "Unknown Theatre";
                                        }
                                    ?>
                                </a>
                            </div>
                            <p class="card-text">
                                <?php echo $video["ShortDescription"]; ?>
                            </p>
                        </div>
                        <div class="card-footer bg-dark m-0 p-0">
                            <div class="border-top mx-3 pt-2 pb-4 text-right">
                                <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>" class="card-link"><?php echo $video_cta; ?></a>
                                <?php
                                    $query = "SELECT * FROM favourites WHERE UserID='$user_id' AND VideoID=" . $video['id'];
                                    $favs = $db->query($query);

                                    if ($favs->num_rows === 1) {
                                        $favourite = $favs->fetch_assoc();
                                ?>
                                    <a href="#" class="card-link removeFromFavourites <?php echo $favourite['id']; ?>" id="<?php echo $favourite['id']; ?>">Remove From Favourites</a>
                                <?php } else { ?>
                                    <a href="#" class="card-link addToFavourites <?php echo $video['id']; ?>" id="<?php echo $video['id']; ?>">Add to Favourites</a>
                                <?php } ?>
                            </div>
                            <?php if ($releaseDate > $date) { ?>
                                <div class="bg-secondary pt-2 pb-2 px-3 text-center">
                                    <small class="float-none">
                                        <?php echo $video["ReleaseDate"]; ?>
                                    </small>
                                </div>
                            <?php } else { ?>
                                <div class="bg-secondary pt-2 pb-4 px-3 rrv_popular">
                                    <small class="float-left">
                                        <?php echo $video_views->num_rows; ?> Views - <?php echo $video_comments->num_rows; ?> Comments
                                    </small>
                                    <small class="float-right">
                                        <?php echo time_elapsed_string($video["ReleaseDate"]); ?>
                                    </small>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <a class="btn btn-block btn-outline-primary mt-3" href="<?php echo $link_prefix; ?>/browse/latest/">Explore Latest</a>
        </section>
    <?php } ?>

    <!-- Upcoming Videos Section -->
    <?php if ($upcoming_videos->num_rows === 3) { ?>
        <section class="border-bottom mx-4 mb-5 pb-5">
            <h2>Upcoming</h2>

            <div class="card-deck">
                <?php while($video = $upcoming_videos->fetch_assoc()) : ?>
                    <?php
                        $releaseDate = new DateTime($video['ReleaseDate']);
                        $date = new DateTime();

                        if ($releaseDate > $date) {
                            $video_cta = "Pre-Order";
                        } else {
                            $video_cta = "Watch";

                            $video_id = $video["id"];

                            $query = "SELECT * FROM views WHERE VideoID='$video_id'";
                            $video_views = $db->query($query);

                            $query = "SELECT * FROM comments WHERE VideoID='$video_id'";
                            $video_comments = $db->query($query);
                        }
                    ?>
                    <div class="card">
                        <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>">
                            <span class="badge badge-success position-absolute rounded-0 p-1">
                                <?php echo $video["Length"]; ?>
                            </span>
                            <img src="<?php echo $link_prefix; ?>/media/images/thumbs/<?php echo file_name($video['Title']); ?>-thumb.png" class="card-img-top card-image-sm" alt="<?php echo $video["Title"]; ?> Thumbnail">
                        </a>
                        <div class="card-body">
                            <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>" class="d-inline-block">
                                <h5 class="card-title">
                                    <?php echo $video["Title"]; ?>
                                </h5>
                            </a>
                            <div class="mb-4">
                                <a class="d-inline-block text-muted" href="<?php echo $link_prefix; ?>/browse/theatre/?tid=<?php echo $video["TheatreID"]; ?>">
                                    <?php
                                        $theatre_id = $video["TheatreID"];

                                        $stmt = $db->prepare("SELECT * FROM theatres WHERE id=?");
                                        $stmt->bind_param("i", $theatre_id);
                                        $stmt->execute();
                    
                                        $result = $stmt->get_result();
                    
                                        if ($result->num_rows === 1) {
                                            $result_array = $result->fetch_assoc();
                                            echo $result_array["Title"];
                                        } else {
                                            echo "Unknown Theatre";
                                        }
                                    ?>
                                </a>
                            </div>
                            <p class="card-text">
                                <?php echo $video["ShortDescription"]; ?>
                            </p>
                        </div>
                        <div class="card-footer bg-dark m-0 p-0">
                            <div class="border-top mx-3 pt-2 pb-4 text-right">
                                <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>" class="card-link"><?php echo $video_cta; ?></a>
                                <?php
                                    $query = "SELECT * FROM favourites WHERE UserID='$user_id' AND VideoID=" . $video['id'];
                                    $favs = $db->query($query);

                                    if ($favs->num_rows === 1) {
                                        $favourite = $favs->fetch_assoc();
                                ?>
                                    <a href="#" class="card-link removeFromFavourites <?php echo $favourite['id']; ?>" id="<?php echo $favourite['id']; ?>">Remove From Favourites</a>
                                <?php } else { ?>
                                    <a href="#" class="card-link addToFavourites <?php echo $video['id']; ?>" id="<?php echo $video['id']; ?>">Add to Favourites</a>
                                <?php } ?>
                            </div>
                            <?php if ($releaseDate > $date) { ?>
                                <div class="bg-secondary pt-2 pb-2 px-3 text-center rrv_upcoming-time">
                                    <small class="float-none">
                                        <?php echo date('d M Y H:i',strtotime($video["ReleaseDate"])); ?>
                                    </small>
                                </div>
                            <?php } else { ?>
                                <div class="bg-secondary pt-2 pb-4 px-3">
                                    <small class="float-left">
                                        <?php echo $video_views->num_rows; ?> Views - <?php echo $video_comments->num_rows; ?> Comments
                                    </small>
                                    <small class="float-right">
                                        <?php echo time_elapsed_string($video["ReleaseDate"]); ?>
                                    </small>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <a class="btn btn-block btn-outline-primary mt-3" href="<?php echo $link_prefix; ?>/browse/upcoming/">Explore Upcoming</a>
        </section>
    <?php } ?>

    <!-- Popular Videos Section -->
    <?php if ($popular_videos->num_rows === 3) { ?>
        <section class="border-bottom mx-4 mb-5 pb-5">
            <h2>Popular</h2>

            <div class="card-deck">
                <?php while($video = $popular_videos->fetch_assoc()) : ?>
                    <?php
                        $releaseDate = new DateTime($video['ReleaseDate']);
                        $date = new DateTime();

                        if ($releaseDate > $date) {
                            $video_cta = "Pre-Order";
                        } else {
                            $video_cta = "Watch";

                            $video_id = $video["id"];

                            $query = "SELECT * FROM views WHERE VideoID='$video_id'";
                            $video_views = $db->query($query);

                            $query = "SELECT * FROM comments WHERE VideoID='$video_id'";
                            $video_comments = $db->query($query);
                        }
                    ?>
                    <div class="card">
                        <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>">
                            <span class="badge badge-success position-absolute rounded-0 p-1">
                                <?php echo $video["Length"]; ?>
                            </span>
                            <img src="<?php echo $link_prefix; ?>/media/images/thumbs/<?php echo file_name($video['Title']); ?>-thumb.png" class="card-img-top card-image-sm" alt="<?php echo $video["Title"]; ?> Thumbnail">
                        </a>
                        <div class="card-body">
                            <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>" class="d-inline-block">
                                <h5 class="card-title">
                                    <?php echo $video["Title"]; ?>
                                </h5>
                            </a>
                            <div class="mb-4">
                                <a class="d-inline-block text-muted" href="<?php echo $link_prefix; ?>/browse/theatre/?tid=<?php echo $video["TheatreID"]; ?>">
                                    <?php
                                        $theatre_id = $video["TheatreID"];

                                        $stmt = $db->prepare("SELECT * FROM theatres WHERE id=?");
                                        $stmt->bind_param("i", $theatre_id);
                                        $stmt->execute();
                    
                                        $result = $stmt->get_result();
                    
                                        if ($result->num_rows === 1) {
                                            $result_array = $result->fetch_assoc();
                                            echo $result_array["Title"];
                                        } else {
                                            echo "Unknown Theatre";
                                        }
                                    ?>
                                </a>
                            </div>
                            <p class="card-text">
                                <?php echo $video["ShortDescription"]; ?>
                            </p>
                        </div>
                        <div class="card-footer bg-dark m-0 p-0">
                            <div class="border-top mx-3 pt-2 pb-4 text-right">
                                <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>" class="card-link"><?php echo $video_cta; ?></a>
                                <?php
                                    $query = "SELECT * FROM favourites WHERE UserID='$user_id' AND VideoID=" . $video['id'];
                                    $favs = $db->query($query);

                                    if ($favs->num_rows === 1) {
                                        $favourite = $favs->fetch_assoc();
                                ?>
                                    <a href="#" class="card-link removeFromFavourites <?php echo $favourite['id']; ?>" id="<?php echo $favourite['id']; ?>">Remove From Favourites</a>
                                <?php } else { ?>
                                    <a href="#" class="card-link addToFavourites <?php echo $video['id']; ?>" id="<?php echo $video['id']; ?>">Add to Favourites</a>
                                <?php } ?>
                            </div>
                            <?php if ($releaseDate > $date) { ?>
                                <div class="bg-secondary pt-2 pb-2 px-3 text-center rrv_upcoming-time">
                                    <small class="float-none">
                                        <?php echo date('d M Y H:i',strtotime($video["ReleaseDate"])); ?>
                                    </small>
                                </div>
                            <?php } else { ?>
                                <div class="bg-secondary pt-2 pb-4 px-3 rrv_popular">
                                    <small class="float-left">
                                        <?php echo $video_views->num_rows; ?> Views - <?php echo $video_comments->num_rows; ?> Comments
                                    </small>
                                    <small class="float-right">
                                        <?php echo time_elapsed_string($video["ReleaseDate"]); ?>
                                    </small>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <a class="btn btn-block btn-outline-primary mt-3" href="<?php echo $link_prefix; ?>/browse/popular/">Explore Popular</a>
        </section>
    <?php } ?>

    <!-- Recommended Section -->
    <?php if ($recommended_videos->num_rows === 3) { ?>
        <section class="border-bottom mx-4 mb-5 pb-5">
            <h2>Recommended For You</h2>

            <?php while($video = $recommended_videos->fetch_assoc()) : ?>
                <?php
                    $releaseDate = new DateTime($video['ReleaseDate']);
                    $date = new DateTime();

                    if ($releaseDate > $date) {
                        $video_cta = "Pre-Order";
                    } else {
                        $video_cta = "Watch";

                        $video_id = $video["id"];

                        $query = "SELECT * FROM views WHERE VideoID='$video_id'";
                        $video_views = $db->query($query);

                        $query = "SELECT * FROM comments WHERE VideoID='$video_id'";
                        $video_comments = $db->query($query);
                    }
                ?>
                <div class="card mb-3">
                    <div class="row no-gutters">
                        <div class="col-md-4">
                            <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>">       
                                <span class="badge badge-success position-absolute rounded-0 p-1">
                                    <?php echo $video["Length"]; ?>
                                </span>
                                <img src="<?php echo $link_prefix; ?>/media/images/thumbs/<?php echo file_name($video['Title']); ?>-thumb.png" class="card-img rounded-0 card-image-lg" alt="<?php echo $video["Title"]; ?> Thumbnail">
                            </a> 
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>" class="d-inline-block">
                                    <h5 class="card-title">
                                        <?php echo $video["Title"]; ?>
                                    </h5>
                                </a>
                                <div class="mb-4">
                                    <a class="d-inline-block text-muted" href="<?php echo $link_prefix; ?>/browse/theatre/?tid=<?php echo $video["TheatreID"]; ?>">
                                        <?php
                                            $theatre_id = $video["TheatreID"];

                                            $stmt = $db->prepare("SELECT * FROM theatres WHERE id=?");
                                            $stmt->bind_param("i", $theatre_id);
                                            $stmt->execute();
                        
                                            $result = $stmt->get_result();
                        
                                            if ($result->num_rows === 1) {
                                                $result_array = $result->fetch_assoc();
                                                echo $result_array["Title"];
                                            } else {
                                                echo "Unknown Theatre";
                                            }
                                        ?>
                                    </a>
                                </div>
                                <p class="card-text">
                                    <?php echo $video["ShortDescription"]; ?>
                                </p>
                                <div class="border-top pt-2 pb-4 text-right">
                                    <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>" class="card-link"><?php echo $video_cta; ?></a>
                                    <?php
                                        $query = "SELECT * FROM favourites WHERE UserID='$user_id' AND VideoID=" . $video['id'];
                                        $favs = $db->query($query);

                                        if ($favs->num_rows === 1) {
                                            $favourite = $favs->fetch_assoc();
                                    ?>
                                        <a href="#" class="card-link removeFromFavourites <?php echo $favourite['id']; ?>" id="<?php echo $favourite['id']; ?>">Remove From Favourites</a>
                                    <?php } else { ?>
                                        <a href="#" class="card-link addToFavourites <?php echo $video['id']; ?>" id="<?php echo $video['id']; ?>">Add to Favourites</a>
                                    <?php } ?>
                                </div>
                                <div class="pt-2 pb-4 rrv_upcoming-time">
                                    <?php if ($releaseDate > $date) { ?>
                                        <small class="float-none">
                                            <?php echo date('d M Y H:i',strtotime($video["ReleaseDate"])); ?>
                                        </small>
                                    <?php } else { ?>
                                        <small class="float-left">
                                            <?php echo $video_views->num_rows; ?> Views - <?php echo $video_comments->num_rows; ?> Comments
                                        </small>
                                        <small class="float-right">
                                            <?php echo time_elapsed_string($video["ReleaseDate"]); ?>
                                        </small>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </section>
    <?php } ?>

    <!-- Favouirite Videos Section -->
    <?php if ($favourite_videos_list->num_rows === 3) { ?>
        <section class="border-bottom mx-4 mb-5 pb-5">
            <h2>Favourites</h2>

            <div class="card-deck">
                <?php while($favourite_list_item = $favourite_videos_list->fetch_assoc()) : ?>
                    <?php
                        $video_id = $favourite_list_item['VideoID'];

                        $query = "SELECT * FROM videos WHERE id='$video_id'";
                        $result = $db->query($query);

                        $video = $result->fetch_assoc();
                        
                        $releaseDate = new DateTime($video['ReleaseDate']);
                        $date = new DateTime();

                        if ($releaseDate > $date) {
                            $video_cta = "Pre-Order";
                        } else {
                            $video_cta = "Watch";

                            $query = "SELECT * FROM views WHERE VideoID='$video_id'";
                            $video_views = $db->query($query);

                            $query = "SELECT * FROM comments WHERE VideoID='$video_id'";
                            $video_comments = $db->query($query);
                        }
                    ?>
                    <div class="card">
                        <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>">
                            <span class="badge badge-success position-absolute rounded-0 p-1">
                                <?php echo $video["Length"]; ?>
                            </span>
                            <img src="<?php echo $link_prefix; ?>/media/images/thumbs/<?php echo file_name($video['Title']); ?>-thumb.png" class="card-img-top card-image-sm" alt="<?php echo $video["Title"]; ?> Thumbnail">
                        </a>
                        <div class="card-body">
                            <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>" class="d-inline-block">
                                <h5 class="card-title">
                                    <?php echo $video["Title"]; ?>
                                </h5>
                            </a>
                            <div class="mb-4">
                                <a class="d-inline-block text-muted" href="<?php echo $link_prefix; ?>/browse/theatre/?tid=<?php echo $video["TheatreID"]; ?>">
                                    <?php
                                        $theatre_id = $video["TheatreID"];

                                        $stmt = $db->prepare("SELECT * FROM theatres WHERE id=?");
                                        $stmt->bind_param("i", $theatre_id);
                                        $stmt->execute();
                    
                                        $result = $stmt->get_result();
                    
                                        if ($result->num_rows === 1) {
                                            $result_array = $result->fetch_assoc();
                                            echo $result_array["Title"];
                                        } else {
                                            echo "Unknown Theatre";
                                        }
                                    ?>
                                </a>
                            </div>
                            <p class="card-text">
                                <?php echo $video["ShortDescription"]; ?>
                            </p>
                        </div>
                        <div class="card-footer bg-dark m-0 p-0">
                            <div class="border-top mx-3 pt-2 pb-4 text-right">
                                <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>" class="card-link"><?php echo $video_cta; ?></a>
                                <?php
                                    $query = "SELECT * FROM favourites WHERE UserID='$user_id' AND VideoID=" . $video['id'];
                                    $favs = $db->query($query);

                                    if ($favs->num_rows === 1) {
                                        $favourite = $favs->fetch_assoc();
                                ?>
                                    <a href="#" class="card-link removeFromFavourites <?php echo $favourite['id']; ?>" id="<?php echo $favourite['id']; ?>">Remove From Favourites</a>
                                <?php } else { ?>
                                    <a href="#" class="card-link addToFavourites <?php echo $video['id']; ?>" id="<?php echo $video['id']; ?>">Add to Favourites</a>
                                <?php } ?>
                            </div>
                            <?php if ($releaseDate > $date) { ?>
                                <div class="bg-secondary pt-2 pb-2 px-3 text-center rrv_upcoming-time">
                                    <small class="float-none">
                                        <?php echo date('d M Y H:i',strtotime($video["ReleaseDate"])); ?>
                                    </small>
                                </div>
                            <?php } else { ?>
                                <div class="bg-secondary pt-2 pb-4 px-3 rrv_popular">
                                    <small class="float-left">
                                        <?php echo $video_views->num_rows; ?> Views - <?php echo $video_comments->num_rows; ?> Comments
                                    </small>
                                    <small class="float-right">
                                        <?php echo time_elapsed_string($video["ReleaseDate"]); ?>
                                    </small>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <a class="btn btn-block btn-outline-primary mt-3" href="<?php echo $link_prefix; ?>/browse/favourites/">Explore Favourites</a>
        </section>
    <?php } ?>
    <section class="border-bottom mx-4 mb-5 pb-5">
        <h2>Newsletter Signup</h2>
         <p>
           <iframe src="https://cdn.forms-content.sg-form.com/212c700d-5bb9-11eb-b49a-92b4cfec75f3" style="height:390px;width:100%;border:0;"/></iframe>
        </p>
    </section>

    <!-- Import Footer -->
    <?php include "server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="scripts/bootstrap.js"></script>

    <!-- Favourites API Calls -->
    <script>
        $(document).ready(function() {
            let user = "<?php echo $user_id; ?>";

            $(".addToFavourites").on('click', function (event) {
                event.preventDefault();

                let video = event.target.id;

                $.ajax({
                    url: "./server/api/addFavourite.php",
                    type: "POST",
                    data: {
                        user: user,
                        video: video,
                    },
                    success: function (res) {
                        switch (res) {
                            case "Invalid Parameter":
                                alert("Video could not be added to favourites. Try Again!");
                                break;
                            case "Done":
                                $("." + video).replaceWith("<span class='card-link'>Added to Favourites!</span>");
                                break;
                            default:
                                alert("Server Error: We couldn't establish a connection to the server.");
                        }
                    },
                    error: function (err) {
                        alert("Server Error: We couldn't establish a connection to the server.");
                        console.log("Server Error Details: " + err.statusText);
                    }
                })
                
            })

            $(".removeFromFavourites").on('click', function (event) {
                event.preventDefault();

                let favourite = event.target.id;

                $.ajax({
                    url: "./server/api/removeFavourite.php",
                    type: "POST",
                    data: {
                        favourite: favourite
                    },
                    success: function (res) {
                        switch (res) {
                            case "Invalid Parameter":
                                alert("Video could not be removed from favourites. Try Again!");
                                break;
                            case "Done":
                                $("." + favourite).replaceWith("<span class='card-link'>Removed from Favourites!</span>");
                                break;
                            default:
                                alert("Server Error: We couldn't establish a connection to the server.");
                        }
                    },
                    error: function (err) {
                        alert("Server Error: We couldn't establish a connection to the server.");
                        console.log("Server Error Details: " + err.statusText);
                    }
                })
            })
        });
    </script>
</body>
</html>