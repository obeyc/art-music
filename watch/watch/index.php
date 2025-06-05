<?php
include "../server/init.php";

if (!isset($_SESSION["user_id"])) {
    header('Location: ' . $link_prefix . '/account/signin/');
}

if (isset($_GET['vid']) && !empty($_GET['vid'])) {
    if (is_numeric($_GET['vid'])) {
        $video_id = $_GET['vid'];

        $query = "SELECT * FROM videos WHERE id=$video_id";
        $video = $db->query($query);

        if ($video->num_rows === 1) {
            $video = $video->fetch_assoc();

            /* Check Video Release Date */
            $releaseDate = new DateTime($video['ReleaseDate']);
            $date = new DateTime();

            // if ($releaseDate > $date) {
               
            // }
            // if($video_id==328) {
            //      header('Location: ' . $link_prefix . '/watch/?vid=' . $video_id);
            // }
        } else {
            header('Location: ' . $link_prefix . '/');
        }
    } else {
        header('Location: ' . $link_prefix . '/');
    }
} else {
    header('Location: ' . $link_prefix . '/');
}

/* Check Video Release Date */

$releaseDate = new DateTime($video['ReleaseDate']);
$date = new DateTime();


/* Check User Access */

$allow_access = true;

/* Admin Access */

if ($user_access === "viewer") {
    $allow_access = false;
} else if ($user_access === "theatre" && $user_theatre===$video['TheatreID']) { 
    $allow_access = false;
}


/* Free Access */

if ($video['VideoType'] == "standard" && $video['Price'] == "zero") {
    $allow_access = false;

    $query = "SELECT * FROM views WHERE UserID=$user_id AND VideoID=$video_id AND DateCreated > DATE_SUB(NOW(), interval 1 hour);";
    $result = $db->query($query);

    if ($result->num_rows === 0) {
        $query = "INSERT INTO views (UserID, VideoID, ViewType) VALUES ('$user_id', '$video_id', 'FREE')";
        $db->query($query);

        $query = "UPDATE videos SET Popularity = Popularity + 1 WHERE id='$video_id'";
        $db->query($query);
    }
}

if($video_id==328) {
    $allow_access=false;
}


if (!($allow_access) && $releaseDate < $date) {

    if ($video['VideoType'] == "standard") {
        /* Check User Subscription */

        $query = "SELECT * FROM subscriptions WHERE UserID=$user_id AND TheatreID=" . $video['TheatreID'];
        $result = $db->query($query);

        if ($result->num_rows === 1) {

            /* Check Last View */

            $query = "SELECT * FROM views WHERE UserID=$user_id AND VideoID=$video_id AND DateCreated > DATE_SUB(NOW(), interval 1 hour);";
            $result = $db->query($query);

            if ($result->num_rows === 0) {
                $query = "INSERT INTO views (UserID, VideoID, ViewType) VALUES ('$user_id', '$video_id', 'Subscription')";
                $db->query($query);

                $query = "UPDATE videos SET Popularity = Popularity + 1 WHERE id='$video_id'";
                $db->query($query);
            }
        } else {

            /* Check User Payment - with Expiration Date */

            $query = "SELECT * FROM payments WHERE UserID=$user_id AND VideoID=$video_id AND ExpirationDate > CURDATE()";
            $result = $db->query($query);

            if ($result->num_rows === 1) {

                /* Check Last View */

                $query = "SELECT * FROM views WHERE UserID=$user_id AND VideoID=$video_id AND DateCreated > DATE_SUB(NOW(), interval 1 hour);";
                $result = $db->query($query);

                if ($result->num_rows === 0) {
                    $query = "INSERT INTO views (UserID, VideoID, ViewType) VALUES ('$user_id', '$video_id', 'Once Off')";
                    $db->query($query);

                    $query = "UPDATE videos SET Popularity = Popularity + 1 WHERE id='$video_id'";
                    $db->query($query);
                }
            } else {

                /* Check User Payment - without Expiration Date */
                if ($user_access === "viewer") {
                    $query = "SELECT * FROM payments WHERE UserID=$user_id AND VideoID=$video_id AND ExpirationDate IS NULL";
                    $result = $db->query($query);

                    if ($result->num_rows === 1) {
                        $result_array = $result->fetch_assoc();

                        $payment_id = $result_array['id'];

                        /* Update Expiration Date */

                        $query = "UPDATE payments SET ExpirationDate = NOW() + INTERVAL 3 DAY WHERE id='$payment_id'";
                        $db->query($query);

                        /* Check Last View */

                        $query = "SELECT * FROM views WHERE UserID=$user_id AND VideoID=$video_id AND DateCreated > DATE_SUB(NOW(), interval 1 hour);";
                        $result = $db->query($query);

                        if ($result->num_rows === 0) {
                            $query = "INSERT INTO views (UserID, VideoID, ViewType) VALUES ('$user_id', '$video_id', 'Once Off')";
                            $db->query($query);

                            $query = "UPDATE videos SET Popularity = Popularity + 1 WHERE id='$video_id'";
                            $db->query($query);
                        }
                    } else {
                        if($video['TheatreID']!=7) {
                            header('Location: ' . $link_prefix . '/watch/preview/?vid=' . $video_id);
                        }
                    }
                }
            }
        }
    } else {

        /* Check User Payment - with FREE PayFast ID */

        $query = "SELECT * FROM payments WHERE UserID=$user_id AND VideoID=$video_id AND PayFastID='FREE'";
        $result = $db->query($query);

        if ($result->num_rows === 1) {
            /* Check Last View */

            $query = "SELECT * FROM views WHERE UserID=$user_id AND VideoID=$video_id AND DateCreated > DATE_SUB(NOW(), interval 1 hour);";
            $result = $db->query($query);

            if ($result->num_rows === 0) {
                $query = "INSERT INTO views (UserID, VideoID, ViewType) VALUES ('$user_id', '$video_id', 'Once Off')";
                $db->query($query);

                $query = "UPDATE videos SET Popularity = Popularity + 1 WHERE id='$video_id'";
                $db->query($query);
            }
        } else {
            header('Location: ' . $link_prefix . '/watch/exclusive/');
        }
    }
} else {
   header('Location: ' . $link_prefix . '/watch/preview/?vid=' . $video_id);
   exit;
}

/* Load Video Rating by User */

$query = "SELECT * FROM ratings WHERE UserID='$user_id' AND VideoID='$video_id'";
$result = $db->query($query);

if ($result->num_rows === 1) {
    $result = $result->fetch_assoc();
    $user_rating = $result['Rating'];
} else {
    $user_rating = "";
}

/* Load Video Comments */

$query = "SELECT * FROM comments WHERE VideoID='$video_id' ORDER BY DateCreated DESC";
$comments = $db->query($query);

/* Load Recommended Videos */

$query = "SELECT * FROM videos WHERE id<>$video_id ORDER BY rand() DESC LIMIT 3";
$recommended_videos = $db->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="description" content="Watch Video: <?php echo $video['Title']; ?>">
    <meta name="keywords" content="artmusic tv, watch video">
    <meta name="author" content="All About Cloud South Africa">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Watch <?php echo $video['Title']; ?></title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/master.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="../styles/fontawesome/css/all.css">
    <!-- Internal CSS -->
    <style>
        .deleteComment {
            cursor: pointer;
        }
    </style>
</head>

<body class="container-fluid p-0 m-0">
    <!-- Import Navigation Bar -->
    <?php include "../server/includes/navigation.php"; ?>

    <div class="mt-5 pt-5">
        <!-- Spacer -->
    </div>

    <section class="row mx-4 p-4 mobile-margin-0 mobile-padding-1">
        <div class="col-md-8">
            <div class="border-bottom d-flex justify-content-between mb-2 mobile-margin-b-2">
                <h5 class="d-inline m-0 p-0">
                    <?php echo $video['Title']; ?>
                </h5>
                <small>
                    <span class="mobile-d-inline-block">
                        <?php
                        $query = "SELECT * FROM views WHERE VideoID='$video_id'";
                        $video_views = $db->query($query);

                        echo $video_views->num_rows . " Views";
                        ?>
                    </span>
                    <span class="mobile-hidden">-</span>
                    <span class="mobile-d-inline-block"><?php echo time_elapsed_string($video["ReleaseDate"]); ?></span>
                </small>
            </div>
            <iframe src="<?php echo $video['VimeoLink']; ?>" class="video-player mb-1 mobile-height-auto mobile-width-100" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
            <div class="mb-4 mobile-text-center">
                <span class="d-inline-block float-left mobile-no-float mobile-margin-2">
                    <a class="btn btn-primary <?php if (isset($user_rating) && !empty($user_rating)) {
                                                    echo "disabled";
                                                } ?>" id="rateGood">
                        <i class="fas fa-thumbs-up  mr-2"></i>
                        <span id="goodRatingCount">
                            <?php
                            $query = "SELECT * FROM ratings WHERE VideoID='$video_id' AND Rating='good' ORDER BY DateCreated DESC";
                            $ratings = $db->query($query);

                            echo $ratings->num_rows;
                            ?>
                        </span>
                    </a>
                    <a class="btn btn-secondary mr-2 <?php if (isset($user_rating) && !empty($user_rating)) {
                                                            echo "disabled";
                                                        } ?>" id="rateBad">
                        <i class="fas fa-thumbs-down mr-2"></i>
                        <span id="badRatingCount">
                            <?php
                            $query = "SELECT * FROM ratings WHERE VideoID='$video_id' AND Rating='bad' ORDER BY DateCreated DESC";
                            $ratings = $db->query($query);

                            echo $ratings->num_rows;
                            ?>
                        </span>
                    </a>
                    <?php if (isset($user_rating) && !empty($user_rating)) { ?>
                        <span class="mobile-d-block">You rated this video as <?php echo strtoupper($user_rating); ?>.</span>
                    <?php } ?>
                </span>
                <span class="d-inline-block float-right mobile-no-float">
                    <a class="ml-3 mobile-margin-1 a2a_dd addToShares" id="<?php echo $video['id']; ?>" href="https://www.addtoany.com/share"><i class="fas fa-share"></i> Share</a>
                    <?php
                    $query = "SELECT * FROM favourites WHERE UserID='$user_id' AND VideoID=" . $video['id'];
                    $favs = $db->query($query);

                    if ($favs->num_rows === 1) {
                        $favourite = $favs->fetch_assoc();
                    ?>
                        <a href="#" class="ml-3 mobile-margin-1 removeFromFavourites <?php echo $favourite['id']; ?>" id="<?php echo $favourite['id']; ?>">Remove From Favourites</a>
                    <?php } else { ?>
                        <a href="#" class="ml-3 mobile-margin-1 addToFavourites <?php echo $video['id']; ?>" id="<?php echo $video['id']; ?>"><i class="fas fa-plus"></i> Add to Favourites</a>
                    <?php } ?>
                    <a class="ml-3 mobile-margin-1" href="<?php echo $link_prefix; ?>/payments/donate/?vid=<?php echo $video_id; ?>"><i class="fas fa-hand-holding-usd"></i> Donate</a>

                    <!-- <button is='google-cast-button' class="btn btn-primary ml-3 mobile-margin-1"></button> -->
                    <!-- <button class="button">Loading</button> -->
                </span>
            </div>

            <p class="border-bottom mt-5 mb-4 pb-4">
                Starring: <?php echo $video['Starring']; ?>
                <br><br>

                <?php echo nl2br($video['LongDescription']); ?>
            </p>

            <div>
                <div class="form-group mb-2">
                    <textarea class="form-control" id="commentMessage" rows="4" placeholder="Write a comment..."></textarea>
                </div>
                <button class="btn btn-success" id="postComment">Post</button>
            </div>

            <div class="my-5" id="comments-box">
                <?php while ($comment = $comments->fetch_assoc()) : ?>
                    <div class="card mb-4" id="comment-<?php echo $comment["id"]; ?>">
                        <div class="card-header m-0 p-3">
                            <?php
                            $comment_user_id = $comment["UserID"];

                            $stmt = $db->prepare("SELECT * FROM users WHERE id=?");
                            $stmt->bind_param("i", $comment_user_id);
                            $stmt->execute();

                            $result = $stmt->get_result();

                            if ($result->num_rows === 1) {
                                $comment_user = $result->fetch_assoc();
                                echo $comment_user["FirstName"] . " " . $comment_user["LastName"];
                            }
                            ?>
                            <?php if ($comment_user_id === $user_id) { ?>
                                <span class="float-right">
                                    <a class="text-danger deleteComment" id="<?php echo $comment["id"]; ?>">
                                        Remove
                                    </a>
                                </span>
                            <?php } ?>
                        </div>
                        <div class="card-body m-0 p-3">
                            <p class="card-text">
                                <?php echo $comment["Message"]; ?>
                            </p>
                        </div>
                        <div class="card-footer bg-dark border-0 text-right text-muted m-0 p-3">
                            <?php
                            $commentDate = new DateTime($comment['DateCreated']);
                            echo $commentDate->format('d M Y H:i');
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="col-md-4">
            <h5 class="border-bottom">Recommended</h5>

            <?php while ($video = $recommended_videos->fetch_assoc()) : ?>
                <?php
                $releaseDate = new DateTime($video['ReleaseDate']);
                $date = new DateTime();

                if ($releaseDate > $date) {
                    $video_cta = "Pre-Order";
                } else {
                    $video_cta = "Watch";

                    $recommended_video_id = $video["id"];

                    $query = "SELECT * FROM views WHERE VideoID='$recommended_video_id'";
                    $video_views = $db->query($query);

                    $query = "SELECT * FROM comments WHERE VideoID='$recommended_video_id'";
                    $video_comments = $db->query($query);
                }
                ?>
                <div class="card mb-2">
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
    </section>

    <div class="mt-5 pt-5">
        <!-- Spacer -->
    </div>

    <!-- Import Footer -->
    <?php include "../server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../scripts/bootstrap.js"></script>

    <script type="text/javascript" src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js"></script>
    <script>
        var initializeCastApi = function() {
        console.log('initializeCastApi');

        var sessionRequest = new chrome.cast.SessionRequest(
            chrome.cast.media.DEFAULT_MEDIA_RECEIVER_APP_ID);
        var apiConfig = new chrome.cast.ApiConfig(
            sessionRequest, sessionListener, receiverListener);
        chrome.cast.initialize(apiConfig, onInitSuccess, onError);
        };

        if (!chrome.cast || !chrome.cast.isAvailable) {
        setTimeout(initializeCastApi, 1000);
        }

        function onInitSuccess() {
        console.log('onInitSuccess');
        }

        function onError(e) {
        console.log('onError', e);
        }

        function sessionListener(e) {
        console.log('sessionListener', e);
        }

        function receiverListener(availability) {
        console.log('receiverListener', availability);

        if(availability === chrome.cast.ReceiverAvailability.AVAILABLE) {
            $(".button").removeAttr("disabled").text("Start");
        }
        }

        function onSessionRequestSuccess(session) {
        console.log('onSessionRequestSuccess', session);

        var mediaInfo = new chrome.cast.media.MediaInfo(
            "https://player.vimeo.com/video/580631900",
            "video/mp4");
        var request = new chrome.cast.media.LoadRequest(mediaInfo);
        session.loadMedia(request, onMediaLoadSuccess, onError);
        }

        function onMediaLoadSuccess(e) {
        console.log('onMediaLoadSuccess', e);
        }

        $(".button").click(function() {
        chrome.cast.requestSession(onSessionRequestSuccess, onError);
        });

    </script>

    <!-- <script src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>
    <script>
    // window.__onGCastApiAvailable = function(isAvailable){
    // if(! isAvailable){
    //     return false;
    // }

    window['__onGCastApiAvailable'] = function(isAvailable) {
        if (isAvailable) {
            initializeCastApi();
        }
    };

    initializeCastApi = function() {
        cast.framework.CastContext.getInstance().setOptions({
            receiverApplicationId: chrome.cast.media.DEFAULT_MEDIA_RECEIVER_APP_ID,
            autoJoinPolicy: chrome.cast.AutoJoinPolicy.ORIGIN_SCOPED
        });
    };

    var castSession = cast.framework.CastContext.getInstance().getCurrentSession();
    var currentMediaURL = 'https://player.vimeo.com/video/580631900';
    var contentType = 'video/mp4';

    var mediaInfo = new chrome.cast.media.MediaInfo(currentMediaURL, contentType);
    var request = new chrome.cast.media.LoadRequest(mediaInfo);
    castSession.loadMedia(request).then(
        function() { console.log('Load succeed'); },
        function(errorCode) { console.log('Error code: ' + errorCode); 
    });

    var player = new cast.framework.RemotePlayer();
    var playerController = new cast.framework.RemotePlayerController(player);

    // var castContext = cast.framework.CastContext.getInstance();

    // castContext.setOptions({
    //     autoJoinPolicy: chrome.cast.AutoJoinPolicy.ORIGIN_SCOPED,
    //     receiverApplicationId: chrome.cast.media.DEFAULT_MEDIA_RECEIVER_APP_ID
    // });

    // var stateChanged = cast.framework.CastContextEventType.CAST_STATE_CHANGED;
    // castContext.addEventListener(stateChanged, function(event){
    //     var castSession = castContext.getCurrentSession();
    //     var media = new chrome.cast.media.MediaInfo('<?php echo $video['VimeoLink']; ?>', 'video/mp4');
    //     var request = new chrome.cast.media.LoadRequest(media);

    //     castSession && castSession
    //         .loadMedia(request)
    //         .then(function(){
    //             console.log('Success');
    //         })
    //         .catch(function(error){
    //             console.log('Error: ' + error);
    //         });
    // });

    
//};
    </script>  -->

    <!-- Share Button -->
    <script>
        var a2a_config = a2a_config || {};
        a2a_config.onclick = 1;
        a2a_config.num_services = 4;
    </script>
    <script async src="https://static.addtoany.com/menu/page.js"></script>

    <!-- Rate API Calls -->
    <script>
        $(document).ready(function() {

            let user = "<?php echo $user_id; ?>";
            let video = "<?php echo $video_id; ?>";

            $("#rateGood").on('click', function() {
                let rating = "good";

                $.ajax({
                    url: "../server/api/addRating.php",
                    type: "POST",
                    data: {
                        user: user,
                        video: video,
                        rating: rating
                    },
                    success: function(res) {
                        switch (res) {
                            case "Invalid Parameter":
                                alert("We could not process your rating. Try Again!");
                                break;
                            case "Done":
                                $('#goodRatingCount').text(parseInt($('#goodRatingCount').text()) + 1);

                                $("#rateGood").addClass("disabled");
                                $("#rateBad").addClass("disabled");
                        }
                    },
                    error: function(err) {
                        alert("Server Error: We couldn't establish a connection to the server.");
                        console.log("Server Error Details: " + err.statusText);
                    }
                })
            })

            $("#rateBad").on('click', function() {
                let rating = "bad";

                $.ajax({
                    url: "../server/api/addRating.php",
                    type: "POST",
                    data: {
                        user: user,
                        video: video,
                        rating: rating
                    },
                    success: function(res) {
                        switch (res) {
                            case "Invalid Parameter":
                                alert("We could not process your rating. Try Again!");
                                break;
                            case "Done":
                                $('#badRatingCount').text(parseInt($('#badRatingCount').text()) + 1);

                                $("#rateGood").addClass("disabled");
                                $("#rateBad").addClass("disabled");
                        }
                    },
                    error: function(err) {
                        alert("Server Error: We couldn't establish a connection to the server.");
                        console.log("Server Error Details: " + err.statusText);
                    }
                })
            })
        });
    </script>

    <!-- Comment API Calls -->
    <script>
        $(document).ready(function() {
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ];

            let user = "<?php echo $user_id; ?>";
            let video = "<?php echo $video_id; ?>";

            <?php
            $query = "SELECT * FROM users WHERE id='$user_id'";
            $result = $db->query($query);

            if ($result->num_rows === 1) {
                $result_array = $result->fetch_assoc();

                $fullName = $result_array["FirstName"] . " " . $result_array["LastName"];
            }
            ?>

            let fullName = "<?php echo $fullName; ?>";

            $("#postComment").on('click', function() {
                let message = $("#commentMessage").val();

                if (message !== "") {
                    $.ajax({
                        url: "../server/api/addComment.php",
                        type: "POST",
                        data: {
                            user: user,
                            video: video,
                            message: message
                        },
                        success: function(res) {
                            switch (res) {
                                case "Invalid Parameter":
                                    alert("Comment could not be saved. Try Again!");
                                    break;
                                case "Done":
                                    $("#commentMessage").val("");

                                    var d = new Date();
                                    var dformat = [d.getDate(), monthNames[d.getMonth()], d.getFullYear()].join(' ') + ' ' + [("0" + d.getHours()).slice(-2), ("0" + d.getMinutes()).slice(-2)].join(':');

                                    $("#comments-box").prepend(`
                                        <div class="card mb-4">
                                            <div class="card-header m-0 p-3">
                                                ` + fullName + `
                                            </div>
                                            <div class="card-body m-0 p-3">
                                                <p class="card-text">
                                                    ` + message + `
                                                </p>
                                            </div>
                                            <div class="card-footer bg-dark border-0 text-right text-muted m-0 p-3">
                                                ` + dformat + `
                                            </div>
                                        </div>
                                    `);
                            }
                        },
                        error: function(err) {
                            alert("Server Error: We couldn't establish a connection to the server.");
                            console.log("Server Error Details: " + err.statusText);
                        }
                    })
                }
            })

            $(".deleteComment").on('click', function(event) {
                let comment = event.target.id;

                $.ajax({
                    url: "../server/api/removeComment.php",
                    type: "POST",
                    data: {
                        comment: comment
                    },
                    success: function(res) {
                        switch (res) {
                            case "Invalid Parameter":
                                alert("Comment could not be removed. Try Again!");
                                break;
                            case "Done":
                                $("#comment-" + comment).remove();
                        }
                    },
                    error: function(err) {
                        alert("Server Error: We couldn't establish a connection to the server.");
                        console.log("Server Error Details: " + err.statusText);
                    }
                })
            })
        });
    </script>

    <!-- Favourites API Calls -->
    <script>
        $(document).ready(function() {
            let user = "<?php echo $user_id; ?>";

            $(".addToFavourites").on('click', function(event) {
                event.preventDefault();

                let video = event.target.id;

                $.ajax({
                    url: "../server/api/addFavourite.php",
                    type: "POST",
                    data: {
                        user: user,
                        video: video,
                    },
                    success: function(res) {
                        switch (res) {
                            case "Invalid Parameter":
                                alert("Video could not be added to favourites. Try Again!");
                                break;
                            case "Done":
                                $("." + video).replaceWith("<span class='ml-3 mobile-margin-1'>Added to Favourites!</span>");
                                break;
                            default:
                                alert("Server Error: We couldn't establish a connection to the server.");
                        }
                    },
                    error: function(err) {
                        alert("Server Error: We couldn't establish a connection to the server.");
                        console.log("Server Error Details: " + err.statusText);
                    }
                })

            })

            $(".removeFromFavourites").on('click', function(event) {
                event.preventDefault();

                let favourite = event.target.id;

                $.ajax({
                    url: "../server/api/removeFavourite.php",
                    type: "POST",
                    data: {
                        favourite: favourite
                    },
                    success: function(res) {
                        switch (res) {
                            case "Invalid Parameter":
                                alert("Video could not be removed from favourites. Try Again!");
                                break;
                            case "Done":
                                $("." + favourite).replaceWith("<span class='ml-3 mobile-margin-1'>Removed from Favourites!</span>");
                                break;
                            default:
                                alert("Server Error: We couldn't establish a connection to the server.");
                        }
                    },
                    error: function(err) {
                        alert("Server Error: We couldn't establish a connection to the server.");
                        console.log("Server Error Details: " + err.statusText);
                    }
                })
            })
        });
    </script>

    <!-- Shares API Calls -->
    <script>
        $(document).ready(function() {
            let user = "<?php echo $user_id; ?>";

            $(".addToShares").on('click', function(event) {
                event.preventDefault();

                let video = event.target.id;

                $.ajax({
                    url: "../server/api/addShare.php",
                    type: "POST",
                    data: {
                        user: user,
                        video: video,
                    },
                    success: function(res) {
                        switch (res) {
                            case "Invalid Parameter":
                                alert("Video could not be shared. Try Again!");
                                break;
                            case "Done":
                                break;
                            default:
                                alert("Server Error: We couldn't establish a connection to the server.");
                        }
                    },
                    error: function(err) {
                        alert("Server Error: We couldn't establish a connection to the server.");
                        console.log("Server Error Details: " + err.statusText);
                    }
                })

            })
        });
    </script>
</body>

</html>