<?php

    include "../server/init.php";

    if (!isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/account/signin/');
    }

    /* Check User Access */

    if ($user_access !== "admin") {
        header('Location: ' . $link_prefix . '/');
    }

    /* Load All Theatres */

    $query = "SELECT * FROM theatres";
    $all_theatres = $db->query($query);

    /* Load All Videos */

    $query = "SELECT * FROM videos";
    $all_videos = $db->query($query);

    /* Load All Users */

    $query = "SELECT * FROM users";
    $all_users = $db->query($query);

    /* Load All Views */

    $query = "SELECT * FROM views";
    $all_views = $db->query($query);

    /* Load All Ratings */

    $query = "SELECT * FROM ratings WHERE Rating='good'";
    $all_ratings = $db->query($query);

    /* Load All Comments */

    $query = "SELECT * FROM comments";
    $all_comments = $db->query($query);


    /* Load Latest Videos */

    $query = "SELECT * FROM videos WHERE ReleaseDate < CURDATE() ORDER BY ReleaseDate DESC LIMIT 3";
    $latest_videos = $db->query($query);

    /* Load New Users */

    $query = "SELECT * FROM users ORDER BY DateCreated DESC LIMIT 3";
    $new_users = $db->query($query);

    /* Load Recent Views*/

    $query = "SELECT * FROM views ORDER BY DateCreated DESC LIMIT 3";
    $recent_views = $db->query($query);

    /* Load Latest Payments */

    $query = "SELECT * FROM payments ORDER BY DateCreated DESC LIMIT 3";
    $latest_payments = $db->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Admin Dashboard</title>
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
        <section class="jumbotron mb-5 p-3">
            <h4 class="mb-4">Overview</h4>
            <div class="row">
                <div class="col-md-2">
                    <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                        <div class="card-header">Theatres</div>
                        <div class="card-body p-2">
                            <h2 class="card-title"><?php echo number_format($all_theatres->num_rows); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                        <div class="card-header">Videos</div>
                        <div class="card-body p-2">
                            <h2 class="card-title"><?php echo number_format($all_videos->num_rows); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                        <div class="card-header">Users</div>
                        <div class="card-body p-2">
                            <h2 class="card-title"><?php echo number_format($all_users->num_rows); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                        <div class="card-header">Views</div>
                        <div class="card-body p-2">
                            <h2 class="card-title"><?php echo number_format($all_views->num_rows); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                        <div class="card-header">Likes</div>
                        <div class="card-body p-2">
                            <h2 class="card-title"><?php echo number_format($all_ratings->num_rows); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                        <div class="card-header">Comments</div>
                        <div class="card-body p-2">
                            <h2 class="card-title"><?php echo number_format($all_comments->num_rows); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="row">
            <div class="col-md-3">
                <h4>Latest Videos</h4>
                <div class="card w-100">
                    <ul class="list-group list-group-flush">
                        <?php while($video = $latest_videos->fetch_assoc()) : ?>
                            <li class="list-group-item">
                                <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $video['id']; ?>"><?php echo $video["Title"]; ?></a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <a class="btn btn-block btn-outline-primary mt-4 mb-5" href="<?php echo $link_prefix;?>/browse/latest/">All Latest Videos</a>
            </div>
            <div class="col-md-3">
                <h4>New Users</h4>
                <div class="card w-100">
                    <ul class="list-group list-group-flush">
                        <?php while($user = $new_users->fetch_assoc()) : ?>
                            <li class="list-group-item">
                                <a href="<?php echo $link_prefix; ?>/admin/users.php?uid=<?php echo $user['id']; ?>"><?php echo $user["FirstName"] . " " . $user["LastName"]; ?></a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <a class="btn btn-block btn-outline-primary mt-4 mb-5" href="<?php echo $link_prefix;?>/admin/users.php">Manage Users</a>
            </div>
            <div class="col-md-3">
                <h4>Recent Views</h4>
                <div class="card w-100">
                    <ul class="list-group list-group-flush">
                        <?php while($view = $recent_views->fetch_assoc()) : ?>
                            <?php
                                $view_user_id = $view['UserID'];
                                $view_video_id = $view['VideoID'];

                                $query = "SELECT * FROM users WHERE id='$view_user_id'";
                                $result = $db->query($query);
                        
                                if ($result->num_rows === 1) {
                                    $result_array = $result->fetch_assoc();
    
                                    $view_user_fullname = strtoupper(substr($result_array["FirstName"], 0, 1)) . " " . $result_array["LastName"];
                                } else {
                                    $view_user_fullname = "Unknown User";
                                }

                                $query = "SELECT * FROM videos WHERE id='$view_video_id'";
                                $result = $db->query($query);
                        
                                if ($result->num_rows === 1) {
                                    $result_array = $result->fetch_assoc();
    
                                    $view_video_title = substr($result_array["Title"], 0, 20) . "...";
                                }
                            ?>
                            <li class="list-group-item">
                                <a href="<?php echo $link_prefix; ?>/admin/users.php?uid=<?php echo $view_user_id; ?>"><?php echo $view_user_fullname; ?></a> 
                                - 
                                <a href="<?php echo $link_prefix; ?>/watch/?vid=<?php echo $view_video_id; ?>"><?php echo $view_video_title; ?></a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <a class="btn btn-block btn-outline-primary mt-4 mb-5" href="<?php echo $link_prefix;?>/admin/reports.php#views">View Reports</a>
            </div>
            <div class="col-md-3">
                <h4>Latest Payments</h4>
                <div class="card w-100">
                    <ul class="list-group list-group-flush">
                        <?php while($payment = $latest_payments->fetch_assoc()) : ?>
                            <?php
                                $payment_user_id = $payment['UserID'];

                                $query = "SELECT * FROM users WHERE id='$payment_user_id'";
                                $result = $db->query($query);
                        
                                if ($result->num_rows === 1) {
                                    $result_array = $result->fetch_assoc();
    
                                    $payment_user_fullname = $result_array["FirstName"] . " " . $result_array["LastName"];
                                } else {
                                    $payment_user_fullname = "Unknown User";
                                }
                            ?>
                            <li class="list-group-item">
                                R<?php echo $payment['Amount']; ?>
                                -
                                <a href="<?php echo $link_prefix; ?>/admin/users.php?uid=<?php echo $payment_user_id; ?>"><?php echo $payment_user_fullname; ?></a>  
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <a class="btn btn-block btn-outline-primary mt-4 mb-5" href="<?php echo $link_prefix;?>/admin/payments.php">View Payments</a>
            </div>
        </section>
    </main>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../scripts/bootstrap.js"></script>
</body>
</html>