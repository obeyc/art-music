<?php
    include "../../server/init.php";

    if (!isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/account/signin/');
    }

    $email = "";
    $selected_video = "";

    $error_message = "";

    if (isset($_POST['submit'])) {
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $email = mysqli_real_escape_string($db, $_POST['email']);

            $query = "SELECT * FROM users WHERE Email='$email'";
            $result = $db->query($query);

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();

                if ($user['id'] === $user_id) {
                    $error_message = "You can't send yourself a gift, please provide a different email.";
                }
            } else {
                $error_message = "Please provide an email address of a valid ArtMusic user.";
            }
        } else {
            $error_message = "Please provide an email address of a valid ArtMusic user.";
        }

        if (isset($_POST['video']) && !empty($_POST['video'])) {
            $selected_video = mysqli_real_escape_string($db, $_POST['video']);
        } else {
            $error_message = "Please select a video to gift.";
        }

        if (empty($error_message)) {
            header('Location: ./checkout.php?vid=' . $selected_video . '&uid=' . $user['id']);
        }
    }

    $query = "SELECT * FROM videos WHERE ReleaseDate < CURDATE() ORDER BY ReleaseDate DESC";
    $all_videos = $db->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Purchase a gift card for a friend!">
    <meta name="keywords" content="artmusic, gift card">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Gift Card</title>
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
        <h2 class="mb-5">Gift Card</h2>
        <div class="row">            
            <div class="col-md-4 mx-auto">
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
                
                <div class="jumbotron py-4 mb-5">
                    <!-- Page Title 
                    <h2 class="mb-5">Gift Card</h2>-->

                    <!-- Gift Card Form -->
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="email">Email (Your Friend)</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                        </div>
                        <div class="form-group">
                            <select class="form-control" id="video" name="video" required>
                                <option value="">Select a Video</option>
                                <?php while ($video = $all_videos->fetch_assoc()) { ?>
                                    <option value="<?php echo $video['id']; ?>" <?php if (isset($selected_video) && $selected_video == $video['id']) { echo "selected"; } ?>><?php echo $video['Title'] . " (R" . $video['Price'] . ")"; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success" name="submit">Send Gift</button>
                    </form>
                </div>
            </div>            
        </div>
    </main>

    <!-- Import Footer -->
    <?php include "../../server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../../scripts/bootstrap.js"></script>
    
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