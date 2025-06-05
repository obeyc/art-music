<?php

    include "../server/init.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Stream the latest and greatest musicle performace South Africa has to offer.">
    <meta name="keywords" content="artmusic tv, south africa, music, classical, theatre">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - About Us</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/master.css">
    <link rel="stylesheet" href="../styles/custom.css">
</head>
<body class="container-fluid p-0 m-0">
    
    <!-- Import Navigation Bar -->
    <?php include "../server/includes/navigation.php"; ?>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

    <section class="mx-4 mb-5 pb-5">
        <h1 class="mb-5 display-4 mt-5">About Us</h1>

        <p class="text-justify">
            Welcome to ArtMusic.tv
            <br></br>

            This is an all encompassing platform taking care of all your Art Music needs. A 
            modern full-featured web application that will allow users to effortlessly stream 
            premium quality videos and / or music files on all major browser-enabled devices.
            <br><br> 

            Tickets can be purchased for individual products or patrons can acquire monthly subscriptions from 
            individual suppliers. 
        </p>
        <ol>
            <li>You can buy tickets to watch any of the concerts / programmes on the site offered by selected venues or institutions, hosted on the site. </li>
            <li>Approved theatres or institutions can buy their own section on Artmusic. Such a theatre or institution becomes responsible for allowing an authorized user to securely manage videos and settings of the said theatre. </li>
            <li>Users can purchase streaming of all of the 34 CD’s on the Salon Music label, via a subscription of R50 per month. </li>
        </ol>
        <p class="text-justify">
            This is a One-Stop Art Music site where you can access stylish entertainment. The broad term, 
            Art Music implies everything from Solo instrumental Recitals, Chamber, Choral & Symphonic 
            Music. Classical and Modern Ballet, Opera and Operetta, World Music and Jazz. Albeit that 
            productions are featured by a broad spectrum of impresarios, artists or institutions on Artmusic.com 
            can will monitor products to assure quality. 
        </p>
    </section>

    <!-- Import Footer -->
    <?php include "../server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../scripts/bootstrap.js"></script>
</body>
</html>