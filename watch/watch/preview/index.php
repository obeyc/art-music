<?php
    include "../../server/init.php";

    if (isset($_GET['vid']) && !empty($_GET['vid'])) {
        if (is_numeric($_GET['vid'])) {
            $video_id = $_GET['vid'];

            $query = "SELECT * FROM videos WHERE id=$video_id";
            $video = $db->query($query);

            if ($video->num_rows === 1) {
                $video = $video->fetch_assoc();
                $price_rd = ($video['Price']=="zero") ? "0" : $video['Price'];
                $theatre_id = $video["TheatreID"];

                $stmt = $db->prepare("SELECT * FROM theatres WHERE id=?");
                $stmt->bind_param("i", $theatre_id);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $result_array = $result->fetch_assoc();
                    $membership_rd = ($result_array["MembershipFee"]=="zero") ? "0" : $result_array["MembershipFee"];
                    if($membership_rd!=0) {
                        $theatrenameprice_rd_text = $result_array["Title"] . " (R" . $membership_rd . " pm)";
                    } else {
                        $theatrenameprice_rd_text = $result_array["Title"]." (Register and get <b>FREE</b> access)";
                    }
                } else {
                    $theatrenameprice_rd_text = "Unknown Theatre";
                }
            } else {
                header('Location: ' . $link_prefix . '/');
            }
        } else {
            header('Location: ' . $link_prefix . '/');
        }
    } else {
        header('Location: ' . $link_prefix . '/');
    }

    $query = "INSERT INTO previews (UserID, VideoID) VALUES ('$user_id', '$video_id')";
    $db->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Watch preview video and purchase access or subscription">
    <meta name="keywords" content="artmusic tv, preview video, purchase, subscription">
    <meta name="author" content="All About Cloud South Africa">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Preview Video</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../../styles/master.css">
    <!-- Video JS CDN -->
    <link href="https://vjs.zencdn.net/7.8.4/video-js.css" rel="stylesheet" />
    <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
    <link href="https://unpkg.com/@videojs/themes@1/dist/fantasy/index.css" rel="stylesheet">
    <style>
        .vjs-theme-fantasy {
            --vjs-theme-fantasy--primary: white !important;
        }
    </style>
</head>
<body class="container-fluid p-0 m-0">
    <!-- Import Navigation Bar -->
    <?php include "../../server/includes/navigation.php"; ?>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

    <section class="row mx-4 p-4 mobile-margin-0 mobile-padding-1">
        <div class="col-md-2">
            <!-- Spacer -->
        </div>
        <div class="col-md-8">
            <div class="mb-2 mobile-margin-b-2">
                <h4 class="text-center">
                    Preview: <?php echo $video['Title']; ?>
                </h4>
                <h6 class="text-center mb-4">
                    <?php
                        $releaseDate = new DateTime($video['ReleaseDate']);
                        $date = new DateTime();

                        if ($releaseDate > $date) {
                            echo "Available From: " . $video['ReleaseDate'];
                        } else {
                            echo "Available Now!";
                        }
                    ?>
                </h6>
            </div>

            <video
                id="preview-video"
                class="video-js vjs-theme-fantasy w-100 mb-3"
                controls
                preload="auto"
                data-setup="{}"
            >
                <source src="<?php echo $link_prefix . '/media/videos/preview/' . file_name($video['Title'] . '-preview.mp4')?>" type="video/mp4" />
                <p class="vjs-no-js">
                    To view this video please enable JavaScript, and consider upgrading to a
                    web browser that
                    <a href="https://videojs.com/html5-video-support/" target="_blank"
                        >supports HTML5 video</a
                    >
                </p>
            </video>
            <?php
            if($price_rd!=0) {
            ?>
            <a class="btn btn-block btn-success" href="<?php echo $link_prefix; ?>/payments/checkout.php?vid=<?php echo $video_id; ?>">Purchase 3 Days Access (R<?php echo $price_rd; ?>)</a>
            
            <?php } ?>
            <a class="btn btn-block btn-success" href="<?php echo $link_prefix; ?>/payments/subscriptions/register.php?vid=<?php echo $video_id; ?>">
                Subscribe to <?=$theatrenameprice_rd_text?>
                
            </a>
            <p class="my-4">
                Starring: <?php echo $video['Starring']; ?>
                <br><br>

                <?php echo nl2br($video['LongDescription']); ?>
            </p>
        </div>
        <div class="col-md-2">
            <!-- Spacer -->
        </div>
    </section>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

    <!-- Import Footer -->
    <?php include "../../server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../../scripts/bootstrap.js"></script>

    <!-- Video JS CDN -->
    <script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
</body>
</html>