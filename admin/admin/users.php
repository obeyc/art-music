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

        if ($page_view !== "view") {
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

    if (isset($_GET["order"]) && !empty($_GET["order"])) {
        $order_query = mysqli_real_escape_string($db, $_GET['order']);

        if ($order_query === "name") {
            $order_query = "FirstName";
        } else {
            $order_query = "DateCreated";
        }
    } else {
        $order_query = "DateCreated";
    }

    if ($search_query === "No Search Term!") {

        /* Load Users List */

        $query = "SELECT * FROM users ORDER BY $order_query";
        $users = $db->query($query);
    } else {

        /* Load Users List by Search Term */

        $query = "SELECT * FROM users WHERE LOWER(FirstName) LIKE '%$search_query%' OR LOWER(LastName) LIKE '%$search_query%' OR LOWER(Email) LIKE '%$search_query%' ORDER BY $order_query";
        $users = $db->query($query);
    }

    $firstName = "";
    $lastName = "";
    $email = "";
    $phoneNumber = "";
    $city = "";
    $province = "";
    $country = "";

    $success_message = "";
    $error_message = "";

    /* Load Selected User */

    if ($page_view === "view") {
        if (isset($_GET['uid']) && !empty($_GET['uid'])) {
            $selected_user_id = $_GET['uid'];

            $query = "SELECT * FROM users WHERE id='$selected_user_id'";
            $result = $db->query($query);

            if ($result->num_rows === 1) {
                $selected_user = $result->fetch_assoc();

                $firstName = $selected_user['FirstName'];
                $lastName = $selected_user['LastName'];
                $email = $selected_user['Email'];
                $phoneNumber = $selected_user['PhoneNumber'];
                $city = $selected_user['City'];
                $province = $selected_user['Province'];
                $country = $selected_user['Country'];
            } else {
                header('Location: ./users.php');
            }
        } else {
            header('Location: ./users.php');
        }
    }

    /* Remove Comment */

    if (isset($_GET["remove_comment"]) && !empty($_GET["remove_comment"])) {
        $comment_id = $_GET['remove_comment'];

        $query = "DELETE FROM comments WHERE id='$comment_id'";
        $db->query($query);

        $success_message = "Comment has been deleted!";
    }

    /* Delete User */

    if (isset($_GET["deleteUser"]) && !empty($_GET["deleteUser"])) {
        $delete_user_id = $_GET['deleteUser'];

        $query = "SELECT * FROM subscriptions WHERE UserID='$delete_user_id'";
        $subscriptions = $db->query($query);

        if ($subscriptions->num_rows === 0) {
            $query = "DELETE FROM users WHERE id='$delete_user_id'";
            $db->query($query);

            header('Location: ./users.php');
        } else {
            $error_message = "Please remove all the user's active subscriptions before deleting the user.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Admin Users</title>
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
            <!-- List of Users Section (Default) -->
            <section class="container-fluid">
                <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                    <h2 class="d-inline-block m-0 p-0">Users</h2>
                </div>

                <div class="d-flex justify-content-between align-items-end mx-2 mb-4 mobile-d-block">
                    <form class="form-inline mobile-margin-b-1" action="" method="GET">
                        <input class="form-control mr-sm-2" type="text" name="search" placeholder="Search" value="<?php if ($search_query !== "No Search Term!") { echo $search_query; } ?>">
                        <select class="form-control mr-sm-2" id="order" name="order">
                            <option value="date" <?php if ($order_query === "DateCreated") { echo "selected"; } ?>>Date Joined</option>
                            <option value="name" <?php if ($order_query === "FirstName") { echo "selected"; } ?>>First Name</option>
                        </select>
                        <button class="btn btn-secondary mobile-d-block mobile-width-100 my-2 my-sm-0 mr-2" type="submit">
                            Filter
                        </button>
                        <a class="text-success mobile-d-block mobile-width-100 mobile-text-center" href="./users.php">Clear</a>
                    </form>
                </div>

                <div class="table-responsive mb-5">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Full Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col">Date Joined</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $users->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo $user['FirstName'] . ' ' . $user['LastName']; ?></td>
                                    <td><a href="mailto:<?php echo $user['Email']; ?>"><?php echo $user['Email']; ?></a></td>
                                    <td><a href="tel:<?php echo $user['PhoneNumber']; ?>"><?php echo $user['PhoneNumber']; ?></a></td>
                                    <td><?php echo $user['DateCreated']; ?></td>
                                    <td>
                                        <a class="btn btn-block btn-primary" href="./users.php?pv=view&uid=<?php echo $user['id']; ?>">View User</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php } ?>

        <?php if ($page_view === "view") { ?>
            <!-- View User Section -->
            <section class="mb-5">
                <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                    <h2 class="d-inline-block m-0 p-0">View User</h2>
                    <a class="btn btn-primary" href="./users.php">Back to Users</a>
                </div>

                <!-- Overview -->
                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Overview</h4>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                                <div class="card-header">Views</div>
                                <div class="card-body p-2">
                                    <h2 class="card-title">
                                        <?php
                                            $query = "SELECT * FROM views WHERE UserID='$selected_user_id'";
                                            $views = $db->query($query);

                                            echo $views->num_rows;
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
                                            $query = "SELECT * FROM previews WHERE UserID='$selected_user_id'";
                                            $previews = $db->query($query);

                                            echo $previews->num_rows;
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
                                            $query = "SELECT * FROM payments WHERE UserID='$selected_user_id' AND VideoID<>0 AND PayFastID<>'FREE'";
                                            $payments = $db->query($query);

                                            echo $payments->num_rows;
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
                                                $query = "SELECT * FROM ratings WHERE UserID='$selected_user_id' AND Rating='good'";
                                                $good_ratings = $db->query($query);

                                                echo $good_ratings->num_rows;
                                            ?>
                                        </span> 
                                        / 
                                        <span class="text-danger">
                                            <?php
                                                $query = "SELECT * FROM ratings WHERE UserID='$selected_user_id' AND Rating='bad'";
                                                $bad_ratings = $db->query($query);

                                                echo $bad_ratings->num_rows;
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
                                            $query = "SELECT * FROM comments WHERE UserID='$selected_user_id'";
                                            $comments = $db->query($query);

                                            echo $comments->num_rows;
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card text-center text-white bg-dark border-secondary mb-3" style="max-width: 18rem;">
                                <div class="card-header">Amount Spent</div>
                                <div class="card-body p-2">
                                    <h2 class="card-title">
                                        <?php
                                            $query = "SELECT SUM(Amount) AS Total FROM payments WHERE UserID='$selected_user_id'";
                                            $amount_spent = $db->query($query);

                                            $amount_spent = $amount_spent->fetch_assoc();

                                            echo "R" . number_format($amount_spent['Total'], 2, '.', ',');
                                        ?>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-center my-4">
                        Joined
                        <?php
                            $joinedDate = new DateTime($selected_user['DateCreated']);
                            echo $joinedDate->format('d M Y H:i');
                        ?>
                    </p>
                </div>

                <!-- User Information -->
                <form class="mb-5" action="" method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="firstName">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $firstName; ?>" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lastName; ?>" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo $phoneNumber; ?>" disabled>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" value="<?php echo $city; ?>" disabled>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="province">Province / State</label>
                            <input type="text" class="form-control" id="province" name="province" value="<?php echo $province; ?>" disabled>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="country">Country</label>
                            <select class="form-control" id="country" name="country" disabled>
                                <option value="" disabled selected>Choose...</option>
                                <!-- Import Countries -->
                                <?php include "../server/includes/countries.php"; ?>
                            </select>
                        </div>
                    </div>
                    <a class="btn btn-block btn-outline-success" href="mailto:<?php echo $email; ?>">Send an Email</a>
                </form>

                <!-- User Subscriptions Section -->
                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">User Subscriptions</h4>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Theatre</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Date of Payment</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM subscriptions WHERE UserID='$selected_user_id'";
                                    $subscriptions = $db->query($query);

                                    while ($subscription = $subscriptions->fetch_assoc()) {
                                        $subscription_theatre_id = $subscription['TheatreID'];

                                        $query = "SELECT * FROM theatres WHERE id='$subscription_theatre_id'";
                                        $result = $db->query($query);
                                
                                        if ($result->num_rows === 1) {
                                            $subscription_theatre = $result->fetch_assoc();
                                        }
                                ?>
                                        <tr>
                                            <td><a href="./theatres.php?pv=view&tid=<?php echo $subscription_theatre_id; ?>"><?php echo $subscription_theatre['Title']; ?></td>
                                            <td>R<?php echo number_format($subscription['Amount'], 2, '.', ','); ?></td>
                                            <td><?php echo $subscription['DateCreated']; ?></td>
                                            <td>
                                                <a href="<?php echo $link_prefix; ?>/payments/subscriptions/cancel.php?sid=<?php echo $subscription["id"]; ?>">Remove</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Views</h4>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Video Title</th>
                                    <th scope="col">Theatre</th>
                                    <th scope="col">Payment Method</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM views WHERE UserID='$selected_user_id'";
                                    $views = $db->query($query);

                                    while ($view = $views->fetch_assoc()) {
                                        $view_video_id = $view['VideoID'];

                                        $query = "SELECT * FROM videos WHERE id='$view_video_id'";
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
                                            <td>
                                                <a href="<?php echo $link_prefix?>/watch/?vid=<?php echo $view_video_id?>"><?php echo $view_video['Title']?></a>
                                            </td>
                                            <td>
                                                <a href="./theatres.php?pv=view&tid=<?php echo $view_theatre_id; ?>"><?php echo $view_theatre['Title']; ?>
                                            </td>
                                            <td><?php echo $view['ViewType']; ?></td>
                                            <td><?php echo $view['DateCreated']; ?></td>
                                        </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Previews</h4>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Video Title</th>
                                    <th scope="col">Theatre</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM previews WHERE UserID='$selected_user_id'";
                                    $previews = $db->query($query);

                                    while ($preview = $previews->fetch_assoc()) {
                                        $preview_video_id = $preview['VideoID'];

                                        $query = "SELECT * FROM videos WHERE id='$preview_video_id'";
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
                                            <td>
                                                <a href="<?php echo $link_prefix?>/watch/?vid=<?php echo $preview_video_id?>"><?php echo $preview_video['Title']?></a>
                                            </td>
                                            <td>
                                                <a href="./theatres.php?pv=view&tid=<?php echo $preview_theatre_id; ?>"><?php echo $preview_theatre['Title']; ?>
                                            </td>
                                            <td><?php echo $preview['DateCreated']; ?></td>
                                        </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Comments</h4>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Video Title</th>
                                    <th scope="col">Comment</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM comments WHERE UserID='$selected_user_id'";
                                    $comments = $db->query($query);

                                    while ($comment = $comments->fetch_assoc()) {
                                        $comment_video_id = $comment['VideoID'];

                                        $query = "SELECT * FROM videos WHERE id='$comment_video_id'";
                                        $result = $db->query($query);
                                
                                        if ($result->num_rows === 1) {
                                            $comment_video = $result->fetch_assoc();
                                        }
                                ?>
                                        <tr>
                                            <td><a href="<?php echo $link_prefix?>/watch/?vid=<?php echo $comment_video_id?>"><?php echo $comment_video['Title']?></a></td>
                                            <td><?php echo $comment['Message']; ?></td>
                                            <td><?php echo $comment['DateCreated']; ?></td>
                                            <td>
                                                <a href="./users.php?pv=view&uid=<?php echo $selected_user_id; ?>&remove_comment=<?php echo $comment["id"]; ?>">Remove</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="jumbotron mb-5 p-3">
                    <h4 class="mb-4">Ratings</h4>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">Video Title</th>
                                    <th scope="col">Rating</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM ratings WHERE UserID='$selected_user_id'";
                                    $ratings = $db->query($query);

                                    while ($rating = $ratings->fetch_assoc()) {
                                        $rating_video_id = $rating['VideoID'];

                                        $query = "SELECT * FROM videos WHERE id='$rating_video_id'";
                                        $result = $db->query($query);
                                
                                        if ($result->num_rows === 1) {
                                            $rating_video = $result->fetch_assoc();
                                        }
                                ?>
                                        <tr>
                                            <td><a href="<?php echo $link_prefix?>/watch/?vid=<?php echo $rating_video_id?>"><?php echo $rating_video['Title']?></a></td>
                                            <td><?php echo ucfirst($rating['Rating']); ?></td>
                                            <td><?php echo $rating['DateCreated']; ?></td>
                                        </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Delete User Modal Trigger -->
            <button type="button" class="btn btn-block btn-outline-danger my-5" data-toggle="modal" data-target="#deleteModal">
                    Delete User (Permanent)
            </button>
        <?php } ?>
    </main>
    
    <?php if ($page_view === "view") { ?>
        <!-- Delete User Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to permanently delete this User and all 
                        of his/her information?
                    </div>
                    <div class="modal-footer">
                        <a href="./users.php?pv=view&uid=<?php echo $selected_user_id; ?>&deleteUser=<?php echo $selected_user_id; ?>" class="btn btn-danger">Yes, delete this User</a>
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