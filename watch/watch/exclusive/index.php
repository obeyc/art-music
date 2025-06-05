<?php
    include "../../server/init.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Watch preview video and purchase access or subscription">
    <meta name="keywords" content="artmusic tv, preview video, purchase, subscription">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Exclusive Video</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../../styles/master.css">
</head>
<body class="container-fluid p-0 m-0">
    <!-- Import Navigation Bar -->
    <?php include "../../server/includes/navigation.php"; ?>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

    <section class="row mx-4 p-4 mobile-margin-0 mobile-padding-1">
        <div class="col-md-2">
            <!-- Spacer -->
        </div>
        <div class="col-md-8 text-center">

            <h1 class="mt-5 display-2">Exclusive Members Only</h1>
            <p class="mb-5">
                Sorry but you unfortunately don't have access to this video. This is an 
                exclusive video and only the owner of this video can grant you access.
            </p>

            <p>
                If you would like to learn more about exclusive videos or would like to 
                request access to this video please send us a contact request.
            </p>
            <a href="../../contact/" class="btn btn-primary">Contact Us</a>

            <hr class="my-5 bg-white" />

            <h2 class="mb-4">Browse Other Videos on ArtMusic TV</h2>
            
            <a href="../../browse/latest/" class="btn btn-primary">Latest Videos</a>
            <a href="../../browse/upcoming/" class="btn btn-primary">Upcoming Videos</a>
            <a href="../../browse/popular/" class="btn btn-primary">Popular Videos</a>

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
</body>
</html>