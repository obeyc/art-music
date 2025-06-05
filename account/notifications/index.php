<?php
    include "../../server/init.php";

    if (!isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/account/signin/');
    }

    /* Load New Notifications */

    $query = "SELECT * FROM notifications WHERE UserID='$user_id' AND Status='0' ORDER BY DateCreated DESC LIMIT 20 ";
    $new_notifications = $db->query($query);

    /* Load Old Notifications */

    $query = "SELECT * FROM notifications WHERE UserID='$user_id' AND Status='1' ORDER BY DateCreated DESC LIMIT 20 ";
    $old_notifications = $db->query($query);

    /* Mark Notification As Read */

    if (isset($_GET["nid"]) && !empty($_GET["nid"])) {
        $notification_id = $_GET['nid'];
        $read_status = 1;

        $query = "UPDATE notifications SET Status='$read_status' WHERE id='$notification_id'";
        $db->query($query);

        header('Location: ./');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Manage your notifications from ArtMusic TV.">
    <meta name="keywords" content="artmusic, notifications">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Notifications</title>
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
        <section class="container-fluid">
            <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                <h2 class="d-inline-block m-0 p-0">New Notifications</h2>
            </div>
            <?php if ($new_notifications->num_rows > 0) { ?>
                <div class="table-responsive mb-5">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Description</th>
                                <th scope="col">Theatre</th>
                                <th scope="col">Date</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($notification = $new_notifications->fetch_assoc()) : ?>
                                <tr>
                                    <td><a href="<?php echo $notification["Link"]; ?>"><?php echo $notification["Description"]; ?></a></td>
                                    <td>
                                        <?php
                                            $theatre_id = $notification["TheatreID"];

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
                                    </td>
                                    <td><?php echo date('d M Y H:i:s',strtotime($notification["DateCreated"])); ?></td>
                                    <td><a class="btn btn-primary" href="./?nid=<?php echo $notification["id"]; ?>">Mark as Read</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <h5 class="text-center mb-5">You don't have any new notifications.</h5>
            <?php } ?>

            <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                <h2 class="d-inline-block m-0 p-0">Notifications</h2>
            </div>
            <?php if ($old_notifications->num_rows > 0) { ?>
                <div class="table-responsive mb-5">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Description</th>
                                <th scope="col">Theatre</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($notification = $old_notifications->fetch_assoc()) : ?>
                                <tr>
                                    <td><a href="<?php echo $notification["Link"]; ?>"><?php echo $notification["Description"]; ?></a></td>
                                    <td>
                                        <?php
                                            $theatre_id = $notification["TheatreID"];

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
                                    </td>
                                    <td><?php echo date('d M Y H:i:s',strtotime($notification["DateCreated"])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <h5 class="text-center mb-5">You don't have any notifications.</h5>
            <?php } ?>
        </section>
    </main>

    <!-- Import Footer -->
    <?php include "../../server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../../scripts/bootstrap.js"></script>
</body>
</html>