<?php
    include "../../server/init.php";

    if (isset($_GET["search"]) && !empty($_GET["search"])) {
        $search_query = mysqli_real_escape_string($db, $_GET['search']);
    } else {
        $search_query = "No Search Term!";
    }

    /* Pagination */

    $query = "SELECT * FROM videos WHERE Title LIKE '%$search_query%' OR ShortDescription LIKE '%$search_query%' OR LongDescription LIKE '%$search_query%' OR Starring LIKE '%$search_query%'";
    $all_videos = $db->query($query);
    
    $page_total = ceil($all_videos->num_rows/12);

    if (isset($_GET['p']) && !empty($_GET['p'])) {
        if (is_numeric($_GET['p'])) {
            $page = $_GET['p'];
        } else {
            $page = 1;
        }
    } else {
        $page = 1;
    }

    if ($page < 1) {
        $page = 1;
    }

    if ($page > $page_total) {
        $page = $page_total;
    }

    $start = ($page - 1)*12;

    /* Load Searched Videos */

    $query = "SELECT * FROM videos WHERE Title LIKE '%$search_query%' OR ShortDescription LIKE '%$search_query%' OR LongDescription LIKE '%$search_query%' OR Starring LIKE '%$search_query%' ORDER BY ReleaseDate DESC LIMIT $start, 12";
    $search_videos = $db->query($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="description" content="Search all the videos on ArtMusic TV">
    <meta name="keywords" content="artmusic tv, search videos">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Search Results</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../../styles/master.css">
    <link rel="stylesheet" href="../../styles/custom.css">
</head>
<body class="container-fluid p-0 m-0">
    <!-- Import Navigation Bar -->
    <?php include "../../server/includes/navigation.php"; ?>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

    <main class="mx-4">
        <!-- Page Title -->
        <h2 class="mb-2">Search: "<?php echo $search_query; ?>"</h2>

        <h5 class="mb-5 text-muted"><strong><?php echo $all_videos->num_rows; ?></strong> results</h5>

        <?php if ($page_total == 0) { ?>
            <h1 class="text-center">No Results Found :(</h1>
            <p class="text-center mb-5">
                We could not match any videos to your search, please try searching for 
                something else.
            </p>
            <div class="row">
                <div class="col-md-4"><!-- Spacer --></div>
                <div class="col-md-4">
                    <form class="mb-5 pb-5" action="./" method="GET">
                        <div class="form-group">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Search" value="<?php echo $search_query; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-block btn-primary">Search</button>
                    </form>
                </div>
                <div class="col-md-4"><!-- Spacer --></div>
            </div>
        <?php } else { ?>
            <!-- Searched Videos Section -->
            <?php while($video = $search_videos->fetch_assoc()) : ?>
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
                                <div class="pt-2 pb-4">
                                    <?php if ($releaseDate > $date) { ?>
                                        <small class="float-none">
                                            <?php echo $video["ReleaseDate"]; ?>
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
        
            <!-- Pagination -->
            <div class="mb-5">
                
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if ($page - 1 < 1) { echo "disabled"; } ?>">
                        <a class="page-link" href="./?search=<?php echo $search_query; ?>&p=<?php echo $page - 1; ?>">Prev</a>
                    </li>
                    <li class="px-4 pt-1">
                        <h4 class="text-center">
                            <?php echo $page; ?> of <?php echo $page_total; ?>
                        </h4>
                    </li>
                    <li class="page-item <?php if ($page + 1 > $page_total) { echo "disabled"; } ?>">
                        <a class="page-link" href="./?search=<?php echo $search_query; ?>&p=<?php echo $page + 1; ?>">Next</a>
                    </li>
                </ul>
            </div>
        <?php } ?>
    </main>

    <!-- Import Footer -->
    <?php include "../../server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../../scripts/bootstrap.js"></script>

    <!-- Favourites API Calls -->
    <script>
        $(document).ready(function() {
            let user = "<?php echo $user_id; ?>";

            $(".addToFavourites").on('click', function (event) {
                event.preventDefault();

                let video = event.target.id;

                $.ajax({
                    url: "../../server/api/addFavourite.php",
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
                    url: "../../server/api/removeFavourite.php",
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