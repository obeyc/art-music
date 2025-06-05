<?php
    include "../server/init.php";

    if (!isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/account/signin/');
    }

    /* Check User Access */

    if ($user_access !== "admin") {
        header('Location: ' . $link_prefix . '/');
    }

    /* Load Views (with Filters) */

    if (isset($_GET['filterViewsOrderBy']) && !empty($_GET['filterViewsOrderBy'])) {
        $filterViewsOrderBy = $_GET['filterViewsOrderBy'];

        if ($filterViewsOrderBy === "name") {
            $query_order = "FirstName ASC";
        } else if ($filterViewsOrderBy === "date-asc") {
            $query_order = "DateCreated ASC";
        } else {
            $query_order = "DateCreated DESC";
        }
    } else {
        $query_order = "DateCreated DESC";
    }

    if (isset($_GET['filterViewsUser']) && !empty($_GET['filterViewsUser'])) {
        $filterViewsUser = $_GET['filterViewsUser'];
        $query_user = "UserID = " . $filterViewsUser;
    } else {
        $filterViewsUser = "";
        $query_user = "UserID<>0";
    }

    if (isset($_GET['filterViewsVideo']) && !empty($_GET['filterViewsVideo'])) {
        $filterViewsVideo = $_GET['filterViewsVideo'];
        $query_video = "VideoID=" . $filterViewsVideo;
    } else {
        $filterViewsVideo = "";
        $query_video = "VideoID<>0";
    }

    if (isset($_GET['filterViewsStartDate']) && !empty($_GET['filterViewsStartDate'])) {
        $filterViewsStartDate = $_GET['filterViewsStartDate'];

        if (isset($_GET['filterViewsEndDate']) && !empty($_GET['filterViewsEndDate'])) {
            $filterViewsEndDate = $_GET['filterViewsEndDate'];
            
            if ($filterViewsStartDate < $filterViewsEndDate) {
                $query_dates = "(views.DateCreated BETWEEN '$filterViewsStartDate' AND '$filterViewsEndDate')";
            } else {
                $filterViewsStartDate = 0;
                $filterViewsEndDate = 0;
                $query_dates = "1 = 1";
            }
        } else {
            $filterViewsEndDate = 0;
            $query_dates = "1 = 1";
        }
    } else {
        $filterViewsStartDate = 0;
        $query_dates = "1 = 1";
    }

    $query = "SELECT views.id, views.UserID, views.ViewType, users.FirstName, users.LastName, users.Email, users.PhoneNumber, views.VideoID, views.DateCreated FROM views INNER JOIN users ON users.id = views.UserID WHERE " . $query_dates . " AND " . $query_user . " AND " . $query_video . " ORDER BY " . $query_order;
    $views = $db->query($query);

    /* Load Previews (with Filters) */

    if (isset($_GET['filterPreviewsOrderBy']) && !empty($_GET['filterPreviewsOrderBy'])) {
        $filterPreviewsOrderBy = $_GET['filterPreviewsOrderBy'];

        if ($filterPreviewsOrderBy === "name") {
            $query_order = "FirstName ASC";
        } else if ($filterPreviewsOrderBy === "date-asc") {
            $query_order = "DateCreated ASC";
        } else {
            $query_order = "DateCreated DESC";
        }
    } else {
        $query_order = "DateCreated DESC";
    }

    if (isset($_GET['filterPreviewsUser']) && !empty($_GET['filterPreviewsUser'])) {
        $filterPreviewsUser = $_GET['filterPreviewsUser'];
        $query_user = "UserID = " . $filterPreviewsUser;
    } else {
        $filterPreviewsUser = "";
        $query_user = "UserID<>0";
    }

    if (isset($_GET['filterPreviewsVideo']) && !empty($_GET['filterPreviewsVideo'])) {
        $filterPreviewsVideo = $_GET['filterPreviewsVideo'];
        $query_video = "VideoID=" . $filterPreviewsVideo;
    } else {
        $filterPreviewsVideo = "";
        $query_video = "VideoID<>0";
    }

    if (isset($_GET['filterPreviewsStartDate']) && !empty($_GET['filterPreviewsStartDate'])) {
        $filterPreviewsStartDate = $_GET['filterPreviewsStartDate'];

        if (isset($_GET['filterPreviewsEndDate']) && !empty($_GET['filterPreviewsEndDate'])) {
            $filterPreviewsEndDate = $_GET['filterPreviewsEndDate'];
            
            if ($filterPreviewsStartDate < $filterPreviewsEndDate) {
                $query_dates = "(previews.DateCreated BETWEEN '$filterPreviewsStartDate' AND '$filterPreviewsEndDate')";
            } else {
                $filterPreviewsStartDate = 0;
                $filterPreviewsEndDate = 0;
                $query_dates = "1 = 1";
            }
        } else {
            $filterPreviewsEndDate = 0;
            $query_dates = "1 = 1";
        }
    } else {
        $filterPreviewsStartDate = 0;
        $query_dates = "1 = 1";
    }

    $query = "SELECT previews.id, previews.UserID, users.FirstName, users.LastName, users.Email, users.PhoneNumber, previews.VideoID, previews.DateCreated FROM previews INNER JOIN users ON users.id = previews.UserID WHERE " . $query_dates . " AND " . $query_user . " AND " . $query_video . " ORDER BY " . $query_order;
    $previews = $db->query($query);

    /* Load Comments (with Filters) */

    if (isset($_GET['filterCommentsOrderBy']) && !empty($_GET['filterCommentsOrderBy'])) {
        $filterCommentsOrderBy = $_GET['filterCommentsOrderBy'];

        if ($filterCommentsOrderBy === "name") {
            $query_order = "FirstName ASC";
        } else if ($filterCommentsOrderBy === "date-asc") {
            $query_order = "DateCreated ASC";
        } else {
            $query_order = "DateCreated DESC";
        }
    } else {
        $query_order = "DateCreated DESC";
    }

    if (isset($_GET['filterCommentsUser']) && !empty($_GET['filterCommentsUser'])) {
        $filterCommentsUser = $_GET['filterCommentsUser'];
        $query_user = "UserID = " . $filterCommentsUser;
    } else {
        $filterCommentsUser = "";
        $query_user = "UserID<>0";
    }

    if (isset($_GET['filterCommentsVideo']) && !empty($_GET['filterCommentsVideo'])) {
        $filterCommentsVideo = $_GET['filterCommentsVideo'];
        $query_video = "VideoID=" . $filterCommentsVideo;
    } else {
        $filterCommentsVideo = "";
        $query_video = "VideoID<>0";
    }

    if (isset($_GET['filterCommentsStartDate']) && !empty($_GET['filterCommentsStartDate'])) {
        $filterCommentsStartDate = $_GET['filterCommentsStartDate'];

        if (isset($_GET['filterCommentsEndDate']) && !empty($_GET['filterCommentsEndDate'])) {
            $filterCommentsEndDate = $_GET['filterCommentsEndDate'];
            
            if ($filterCommentsStartDate < $filterCommentsEndDate) {
                $query_dates = "(comments.DateCreated BETWEEN '$filterCommentsStartDate' AND '$filterCommentsEndDate')";
            } else {
                $filterCommentsStartDate = 0;
                $filterCommentsEndDate = 0;
                $query_dates = "1 = 1";
            }
        } else {
            $filterCommentsEndDate = 0;
            $query_dates = "1 = 1";
        }
    } else {
        $filterCommentsStartDate = 0;
        $query_dates = "1 = 1";
    }

    $query = "SELECT comments.id, comments.UserID, users.FirstName, users.LastName, users.Email, users.PhoneNumber, comments.VideoID, comments.Message, comments.DateCreated FROM comments INNER JOIN users ON users.id = comments.UserID WHERE " . $query_dates . " AND " . $query_user . " AND " . $query_video . " ORDER BY " . $query_order;
    $comments = $db->query($query);

    /* Load Ratings (with Filters) */

    if (isset($_GET['filterRatingsOrderBy']) && !empty($_GET['filterRatingsOrderBy'])) {
        $filterRatingsOrderBy = $_GET['filterRatingsOrderBy'];

        if ($filterRatingsOrderBy === "name") {
            $query_order = "FirstName ASC";
        } else if ($filterRatingsOrderBy === "date-asc") {
            $query_order = "DateCreated ASC";
        } else {
            $query_order = "DateCreated DESC";
        }
    } else {
        $query_order = "DateCreated DESC";
    }

    if (isset($_GET['filterRatingsRating']) && !empty($_GET['filterRatingsRating'])) {
        $filterRatingsRating = $_GET['filterRatingsRating'];
        $query_rating = "Rating='" . $filterRatingsRating . "'";
    } else {
        $filterRatingsRating = "";
        $query_rating = "Rating LIKE '%'";
    }

    if (isset($_GET['filterRatingsUser']) && !empty($_GET['filterRatingsUser'])) {
        $filterRatingsUser = $_GET['filterRatingsUser'];
        $query_user = "UserID = " . $filterRatingsUser;
    } else {
        $filterRatingsUser = "";
        $query_user = "UserID<>0";
    }

    if (isset($_GET['filterRatingsVideo']) && !empty($_GET['filterRatingsVideo'])) {
        $filterRatingsVideo = $_GET['filterRatingsVideo'];
        $query_video = "VideoID=" . $filterRatingsVideo;
    } else {
        $filterRatingsVideo = "";
        $query_video = "VideoID<>0";
    }

    if (isset($_GET['filterRatingsStartDate']) && !empty($_GET['filterRatingsStartDate'])) {
        $filterRatingsStartDate = $_GET['filterRatingsStartDate'];

        if (isset($_GET['filterRatingsEndDate']) && !empty($_GET['filterRatingsEndDate'])) {
            $filterRatingsEndDate = $_GET['filterRatingsEndDate'];
            
            if ($filterRatingsStartDate < $filterRatingsEndDate) {
                $query_dates = "(ratings.DateCreated BETWEEN '$filterRatingsStartDate' AND '$filterRatingsEndDate')";
            } else {
                $filterRatingsStartDate = 0;
                $filterRatingsEndDate = 0;
                $query_dates = "1 = 1";
            }
        } else {
            $filterRatingsEndDate = 0;
            $query_dates = "1 = 1";
        }
    } else {
        $filterRatingsStartDate = 0;
        $query_dates = "1 = 1";
    }

    $query = "SELECT ratings.id, ratings.UserID, users.FirstName, users.LastName, users.Email, users.PhoneNumber, ratings.VideoID, ratings.Rating, ratings.DateCreated FROM ratings INNER JOIN users ON users.id = ratings.UserID WHERE " . $query_dates . " AND " . $query_user . " AND " . $query_video . " AND " . $query_rating . " ORDER BY " . $query_order;
    $ratings = $db->query($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Admin Reports</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/master.css">
    <!-- Internal CSS -->
    <style>
        html {
            scroll-padding-top: 70px;
        }
    </style>
</head>
<body class="container-fluid p-0 m-0">
    <!-- Import Admin Navigation Bar -->
    <?php include "../server/includes/admin-navigation.php"; ?>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

    <main class="mx-4">
        <!-- Reports Section -->
        <section class="mb-5">
            <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                <h2 class="d-inline-block m-0 p-0">Reports</h2>
            </div>

            <div id="views" class="jumbotron mb-5 p-3">
                <h4 class="mb-4">Views</h5>

                <div class="d-flex justify-content-between align-items-end mb-4 mobile-d-block">
                    <form class="form-inline mobile-margin-b-1" action="" method="GET">
                        <div class="d-block w-100 mb-2">
                            <select class="form-control mr-sm-2" id="filterViewsOrderBy" name="filterViewsOrderBy">
                                <option value="date-desc" <?php if (isset($filterViewsOrderBy) && $filterViewsOrderBy === "date-desc") { echo "selected"; } ?>>Order by Date (New to Old)</option>
                                <option value="date-asc" <?php if (isset($filterViewsOrderBy) && $filterViewsOrderBy === "date-asc") { echo "selected"; } ?>>Order by Date (Old to New)</option>
                                <option value="name" <?php if (isset($filterViewsOrderBy) && $filterViewsOrderBy === "name") { echo "selected"; } ?>>Order by Name (Alphabetical)</option>
                            </select>

                            <select class="form-control mr-sm-2" id="filterViewsUser" name="filterViewsUser">
                                <option value="">All Users</option>
                                <?php
                                    $query = "SELECT * FROM users";
                                    $users = $db->query($query);
                                ?>
                                <?php while ($user = $users->fetch_assoc()) { ?>
                                    <option value="<?php echo $user['id']; ?>" <?php if (isset($filterViewsUser) && $filterViewsUser == $user['id']) { echo "selected"; } ?>><?php echo $user['FirstName'] . ' ' . $user['LastName']; ?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control mr-sm-2" id="filterViewsVideo" name="filterViewsVideo">
                                <option value="">All Videos</option>
                                <?php
                                    $query = "SELECT * FROM videos";
                                    $videos = $db->query($query);
                                ?>
                                <?php while ($video = $videos->fetch_assoc()) { ?>
                                    <option value="<?php echo $video['id']; ?>" <?php if (isset($filterViewsVideo) && $filterViewsVideo == $video['id']) { echo "selected"; } ?>><?php echo $video['Title']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group mobile-width-100 mobile-margin-b-0 mobile-margin-t-1">
                            <input type="datetime-local" class="form-control" id="filterViewsStartDate" name="filterViewsStartDate" value="<?php if (isset($filterViewsStartDate)) { echo str_replace(" ", "T", $filterViewsStartDate); } ?>">
                        </div>
                        <span class="mx-2 mobile-d-block mobile-width-100 mobile-margin-1 mobile-text-center"> to </span>
                        <div class="form-group mobile-width-100">
                            <input type="datetime-local" class="form-control" id="filterViewsEndDate" name="filterViewsEndDate" value="<?php if (isset($filterViewsEndDate)) { echo str_replace(" ", "T", $filterViewsEndDate); } ?>">
                        </div>

                        <button class="btn btn-secondary my-2 my-sm-0 mx-3 mobile-d-block mobile-width-100 mobile-margin-0" type="submit" name="submitViews">
                            Apply
                        </button>
                        <a class="text-success mobile-d-block mobile-width-100 mobile-text-center mobile-margin-2" href="./reports.php#views">Clear</a>
                    </form>

                    <button class="btn btn-outline-success ml-4 mobile-margin-0 mobile-d-block" onClick="fnExcelReport('viewsTable')">Download</button>
                </div>

                <div class="table-responsive">
                    <table id="viewsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Full Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col">Theatre</th>
                                <th scope="col">Video</th>
                                <th scope="col">Payment Method</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($view = $views->fetch_assoc()) : ?>
                                <?php
                                    $query = "SELECT * FROM videos WHERE id=" . $view['VideoID'];
                                    $result = $db->query($query);
                            
                                    if ($result->num_rows === 1) {
                                        $view_video = $result->fetch_assoc();

                                        $view_theatre_id = $view_video['TheatreID'];

                                        $query = "SELECT * FROM theatres WHERE id='$view_theatre_id'";
                                        $result = $db->query($query);
                                
                                        if ($result->num_rows === 1) {
                                            $view_theatre = $result->fetch_assoc();
                                        }
                                    }    
                                ?>
                                <tr>
                                    <td><a href="<?php echo $link_prefix; ?>/admin/users.php?pv=view&uid=<?php echo $view['UserID']; ?>"><?php echo $view['FirstName'] . ' ' . $view['LastName']; ?></a></td>
                                    <td><a href="mailto:<?php echo $view['Email']; ?>"><?php echo $view['Email']; ?></a></td>
                                    <td><a href="tel:<?php echo $view['PhoneNumber']; ?>"><?php echo $view['PhoneNumber']; ?></a></td>
                                    <td>
                                        <a href="<?php echo $link_prefix; ?>/admin/theatres.php?pv=view&tid=<?php echo $view_theatre_id; ?>"><?php echo $view_theatre['Title']; ?></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $view['VideoID']; ?>"><?php echo $view_video['Title']; ?></a>
                                    </td>
                                    <td><?php echo $view['ViewType']; ?></td>
                                    <td><?php echo $view['DateCreated']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="previews" class="jumbotron mb-5 p-3">
                <h4 class="mb-4">Previews</h5>

                <div class="d-flex justify-content-between align-items-end mb-4 mobile-d-block">
                    <form class="form-inline mobile-margin-b-1" action="" method="GET">
                        <div class="d-block w-100 mb-2">
                            <select class="form-control mr-sm-2" id="filterPreviewsOrderBy" name="filterPreviewsOrderBy">
                                <option value="date-desc" <?php if (isset($filterPreviewsOrderBy) && $filterPreviewsOrderBy === "date-desc") { echo "selected"; } ?>>Order by Date (New to Old)</option>
                                <option value="date-asc" <?php if (isset($filterPreviewsOrderBy) && $filterPreviewsOrderBy === "date-asc") { echo "selected"; } ?>>Order by Date (Old to New)</option>
                                <option value="name" <?php if (isset($filterPreviewsOrderBy) && $filterPreviewsOrderBy === "name") { echo "selected"; } ?>>Order by Name (Alphabetical)</option>
                            </select>

                            <select class="form-control mr-sm-2" id="filterPreviewsUser" name="filterPreviewsUser">
                                <option value="">All Users</option>
                                <?php
                                    $query = "SELECT * FROM users";
                                    $users = $db->query($query);
                                ?>
                                <?php while ($user = $users->fetch_assoc()) { ?>
                                    <option value="<?php echo $user['id']; ?>" <?php if (isset($filterPreviewsUser) && $filterPreviewsUser == $user['id']) { echo "selected"; } ?>><?php echo $user['FirstName'] . ' ' . $user['LastName']; ?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control mr-sm-2" id="filterPreviewsVideo" name="filterPreviewsVideo">
                                <option value="">All Videos</option>
                                <?php
                                    $query = "SELECT * FROM videos";
                                    $videos = $db->query($query);
                                ?>
                                <?php while ($video = $videos->fetch_assoc()) { ?>
                                    <option value="<?php echo $video['id']; ?>" <?php if (isset($filterPreviewsVideo) && $filterPreviewsVideo == $video['id']) { echo "selected"; } ?>><?php echo $video['Title']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group mobile-width-100 mobile-margin-b-0 mobile-margin-t-1">
                            <input type="datetime-local" class="form-control" id="filterPreviewsStartDate" name="filterPreviewsStartDate" value="<?php if (isset($filterPreviewsStartDate)) { echo str_replace(" ", "T", $filterPreviewsStartDate); } ?>">
                        </div>
                        <span class="mx-2 mobile-d-block mobile-width-100 mobile-margin-1 mobile-text-center"> to </span>
                        <div class="form-group mobile-width-100">
                            <input type="datetime-local" class="form-control" id="filterPreviewsEndDate" name="filterPreviewsEndDate" value="<?php if (isset($filterPreviewsEndDate)) { echo str_replace(" ", "T", $filterPreviewsEndDate); } ?>">
                        </div>

                        <button class="btn btn-secondary my-2 my-sm-0 mx-3 mobile-d-block mobile-width-100 mobile-margin-0" type="submit" name="submitPreviews">
                            Apply
                        </button>
                        <a class="text-success mobile-d-block mobile-width-100 mobile-text-center mobile-margin-2" href="./reports.php#previews">Clear</a>
                    </form>

                    <button class="btn btn-outline-success ml-4 mobile-margin-0 mobile-d-block" onClick="fnExcelReport('previewsTable')">Download</button>
                </div>

                <div class="table-responsive">
                    <table id="previewsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Full Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col">Theatre</th>
                                <th scope="col">Video</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($preview = $previews->fetch_assoc()) : ?>
                                <?php
                                    $query = "SELECT * FROM videos WHERE id=" . $preview['VideoID'];
                                    $result = $db->query($query);
                            
                                    if ($result->num_rows === 1) {
                                        $preview_video = $result->fetch_assoc();

                                        $preview_theatre_id = $preview_video['TheatreID'];

                                        $query = "SELECT * FROM theatres WHERE id='$preview_theatre_id'";
                                        $result = $db->query($query);
                                
                                        if ($result->num_rows === 1) {
                                            $preview_theatre = $result->fetch_assoc();
                                        }
                                    }    
                                ?>
                                <tr>
                                    <td><a href="<?php echo $link_prefix; ?>/admin/users.php?pv=view&uid=<?php echo $preview['UserID']; ?>"><?php echo $preview['FirstName'] . ' ' . $preview['LastName']; ?></a></td>
                                    <td><a href="mailto:<?php echo $preview['Email']; ?>"><?php echo $preview['Email']; ?></a></td>
                                    <td><a href="tel:<?php echo $preview['PhoneNumber']; ?>"><?php echo $preview['PhoneNumber']; ?></a></td>
                                    <td>
                                        <a href="<?php echo $link_prefix; ?>/admin/theatres.php?pv=view&tid=<?php echo $preview_theatre_id; ?>"><?php echo $preview_theatre['Title']; ?></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $preview['VideoID']; ?>"><?php echo $preview_video['Title']; ?></a>
                                    </td>
                                    <td><?php echo $preview['DateCreated']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="comments" class="jumbotron mb-5 p-3">
                <h4 class="mb-4">Comments</h5>

                <div class="d-flex justify-content-between align-items-end mb-4 mobile-d-block">
                    <form class="form-inline mobile-margin-b-1" action="" method="GET">
                        <div class="d-block w-100 mb-2">
                            <select class="form-control mr-sm-2" id="filterCommentsOrderBy" name="filterCommentsOrderBy">
                                <option value="date-desc" <?php if (isset($filterCommentsOrderBy) && $filterCommentsOrderBy === "date-desc") { echo "selected"; } ?>>Order by Date (New to Old)</option>
                                <option value="date-asc" <?php if (isset($filterCommentsOrderBy) && $filterCommentsOrderBy === "date-asc") { echo "selected"; } ?>>Order by Date (Old to New)</option>
                                <option value="name" <?php if (isset($filterCommentsOrderBy) && $filterCommentsOrderBy === "name") { echo "selected"; } ?>>Order by Name (Alphabetical)</option>
                            </select>

                            <select class="form-control mr-sm-2" id="filterCommentsUser" name="filterCommentsUser">
                                <option value="">All Users</option>
                                <?php
                                    $query = "SELECT * FROM users";
                                    $users = $db->query($query);
                                ?>
                                <?php while ($user = $users->fetch_assoc()) { ?>
                                    <option value="<?php echo $user['id']; ?>" <?php if (isset($filterCommentsUser) && $filterCommentsUser == $user['id']) { echo "selected"; } ?>><?php echo $user['FirstName'] . ' ' . $user['LastName']; ?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control mr-sm-2" id="filterCommentsVideo" name="filterCommentsVideo">
                                <option value="">All Videos</option>
                                <?php
                                    $query = "SELECT * FROM videos";
                                    $videos = $db->query($query);
                                ?>
                                <?php while ($video = $videos->fetch_assoc()) { ?>
                                    <option value="<?php echo $video['id']; ?>" <?php if (isset($filterCommentsVideo) && $filterCommentsVideo == $video['id']) { echo "selected"; } ?>><?php echo $video['Title']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group mobile-width-100 mobile-margin-b-0 mobile-margin-t-1">
                            <input type="datetime-local" class="form-control" id="filterCommentsStartDate" name="filterCommentsStartDate" value="<?php if (isset($filterCommentsStartDate)) { echo str_replace(" ", "T", $filterCommentsStartDate); } ?>">
                        </div>
                        <span class="mx-2 mobile-d-block mobile-width-100 mobile-margin-1 mobile-text-center"> to </span>
                        <div class="form-group mobile-width-100">
                            <input type="datetime-local" class="form-control" id="filterCommentsEndDate" name="filterCommentsEndDate" value="<?php if (isset($filterCommentsEndDate)) { echo str_replace(" ", "T", $filterCommentsEndDate); } ?>">
                        </div>

                        <button class="btn btn-secondary my-2 my-sm-0 mx-3 mobile-d-block mobile-width-100 mobile-margin-0" type="submit" name="submitComments">
                            Apply
                        </button>
                        <a class="text-success mobile-d-block mobile-width-100 mobile-text-center mobile-margin-2" href="./reports.php#comments">Clear</a>
                    </form>

                    <button class="btn btn-outline-success ml-4 mobile-margin-0 mobile-d-block" onClick="fnExcelReport('commentsTable')">Download</button>
                </div>

                <div class="table-responsive">
                    <table id="commentsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Full Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col">Video</th>
                                <th scope="col">Comment</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($comment = $comments->fetch_assoc()) : ?>
                                <?php
                                    $query = "SELECT * FROM videos WHERE id=" . $comment['VideoID'];
                                    $result = $db->query($query);
                            
                                    if ($result->num_rows === 1) {
                                        $comment_video = $result->fetch_assoc();
                                    }    
                                ?>
                                <tr>
                                    <td><a href="<?php echo $link_prefix; ?>/admin/users.php?pv=view&uid=<?php echo $comment['UserID']; ?>"><?php echo $comment['FirstName'] . ' ' . $comment['LastName']; ?></a></td>
                                    <td><a href="mailto:<?php echo $comment['Email']; ?>"><?php echo $comment['Email']; ?></a></td>
                                    <td><a href="tel:<?php echo $comment['PhoneNumber']; ?>"><?php echo $comment['PhoneNumber']; ?></a></td>
                                    <td>
                                        <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $comment['VideoID']; ?>"><?php echo $comment_video['Title']; ?></a>
                                    </td>
                                    <td><?php echo $comment['Message']; ?></td>
                                    <td><?php echo $comment['DateCreated']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="ratings" class="jumbotron mb-5 p-3">
                <h4 class="mb-4">Ratings</h5>

                <div class="d-flex justify-content-between align-items-end mb-4 mobile-d-block">
                    <form class="form-inline mobile-margin-b-1" action="" method="GET">
                        <div class="d-block w-100 mb-2">
                            <select class="form-control mr-sm-2" id="filterRatingsOrderBy" name="filterRatingsOrderBy">
                                <option value="date-desc" <?php if (isset($filterRatingsOrderBy) && $filterRatingsOrderBy === "date-desc") { echo "selected"; } ?>>Order by Date (New to Old)</option>
                                <option value="date-asc" <?php if (isset($filterRatingsOrderBy) && $filterRatingsOrderBy === "date-asc") { echo "selected"; } ?>>Order by Date (Old to New)</option>
                                <option value="name" <?php if (isset($filterRatingsOrderBy) && $filterRatingsOrderBy === "name") { echo "selected"; } ?>>Order by Name (Alphabetical)</option>
                            </select>

                            <select class="form-control mr-sm-2" id="filterRatingsRating" name="filterRatingsRating">
                                <option value="">All Ratings</option>
                                <option value="good" <?php if (isset($filterRatingsRating) && $filterRatingsRating === "good") { echo "selected"; } ?>>Only Good Ratings</option>
                                <option value="bad" <?php if (isset($filterRatingsRating) && $filterRatingsRating === "bad") { echo "selected"; } ?>>Only Bad Ratings</option>
                            </select>

                            <select class="form-control mr-sm-2" id="filterRatingsUser" name="filterRatingsUser">
                                <option value="">All Users</option>
                                <?php
                                    $query = "SELECT * FROM users";
                                    $users = $db->query($query);
                                ?>
                                <?php while ($user = $users->fetch_assoc()) { ?>
                                    <option value="<?php echo $user['id']; ?>" <?php if (isset($filterRatingsUser) && $filterRatingsUser == $user['id']) { echo "selected"; } ?>><?php echo $user['FirstName'] . ' ' . $user['LastName']; ?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control mr-sm-2" id="filterRatingsVideo" name="filterRatingsVideo">
                                <option value="">All Videos</option>
                                <?php
                                    $query = "SELECT * FROM videos";
                                    $videos = $db->query($query);
                                ?>
                                <?php while ($video = $videos->fetch_assoc()) { ?>
                                    <option value="<?php echo $video['id']; ?>" <?php if (isset($filterRatingsVideo) && $filterRatingsVideo == $video['id']) { echo "selected"; } ?>><?php echo $video['Title']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group mobile-width-100 mobile-margin-b-0 mobile-margin-t-1">
                            <input type="datetime-local" class="form-control" id="filterRatingsStartDate" name="filterRatingsStartDate" value="<?php if (isset($filterRatingsStartDate)) { echo str_replace(" ", "T", $filterRatingsStartDate); } ?>">
                        </div>
                        <span class="mx-2 mobile-d-block mobile-width-100 mobile-margin-1 mobile-text-center"> to </span>
                        <div class="form-group mobile-width-100">
                            <input type="datetime-local" class="form-control" id="filterRatingsEndDate" name="filterRatingsEndDate" value="<?php if (isset($filterRatingsEndDate)) { echo str_replace(" ", "T", $filterRatingsEndDate); } ?>">
                        </div>

                        <button class="btn btn-secondary my-2 my-sm-0 mx-3 mobile-d-block mobile-width-100 mobile-margin-0" type="submit" name="submitRatings">
                            Apply
                        </button>
                        <a class="text-success mobile-d-block mobile-width-100 mobile-text-center mobile-margin-2" href="./reports.php#ratings">Clear</a>
                    </form>

                    <button class="btn btn-outline-success ml-4 mobile-margin-0 mobile-d-block" onClick="fnExcelReport('ratingsTable')">Download</button>
                </div>

                <div class="table-responsive">
                    <table id="ratingsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Full Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col">Theatre</th>
                                <th scope="col">Video</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rating = $ratings->fetch_assoc()) : ?>
                                <?php
                                    $query = "SELECT * FROM videos WHERE id=" . $rating['VideoID'];
                                    $result = $db->query($query);
                            
                                    if ($result->num_rows === 1) {
                                        $rating_video = $result->fetch_assoc();

                                        $rating_theatre_id = $rating_video['TheatreID'];

                                        $query = "SELECT * FROM theatres WHERE id='$rating_theatre_id'";
                                        $result = $db->query($query);
                                
                                        if ($result->num_rows === 1) {
                                            $rating_theatre = $result->fetch_assoc();
                                        }
                                    }    
                                ?>
                                <tr>
                                    <td><a href="<?php echo $link_prefix; ?>/admin/users.php?pv=view&uid=<?php echo $rating['UserID']; ?>"><?php echo $rating['FirstName'] . ' ' . $rating['LastName']; ?></a></td>
                                    <td><a href="mailto:<?php echo $rating['Email']; ?>"><?php echo $rating['Email']; ?></a></td>
                                    <td><a href="tel:<?php echo $rating['PhoneNumber']; ?>"><?php echo $rating['PhoneNumber']; ?></a></td>
                                    <td>
                                        <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $rating['VideoID']; ?>"><?php echo $rating_video['Title']; ?></a>
                                    </td>
                                    <td><?php echo ucfirst($rating['Rating']); ?></td>
                                    <td><?php echo $rating['DateCreated']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../scripts/bootstrap.js"></script>

    <!-- Export Table to Excel -->
    <script type="text/javascript">
        function fnExcelReport(table) {
            var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
            var textRange; 
            var j = 0;
            tab = document.getElementById(table);

            for (j = 0; j < tab.rows.length; j++) {     
                tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
            }

            tab_text=tab_text+"</table>";
            tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");
            tab_text= tab_text.replace(/<img[^>]*>/gi,"");
            tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, "");

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE "); 

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
                txtArea1.document.open("txt/html","replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus(); 
                sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls");
            } else {
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));  
            }

            return (sa);
        }
    </script>

    <!-- Scroll to Table after submitting -->
    <script type="text/javascript">
        window.location.hash = "<?php echo $internal_link_target; ?>";
    </script>
</body>
</html>