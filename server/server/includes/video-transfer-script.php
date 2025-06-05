<?php
    // Connect to ArtMusic Database
    $servname = "localhost";
    $username = "root";
    $password = "";
    $database = "artmusic";

    //$artmusic_db = new mysqli($servname, $username, $password, $database);

	if ($artmusic_db->connect_error) {
		die("ArtMusic Database Connection Failed: " . $artmusic_db->connect_error);
    }
    
    // Connect to Brooklyn Theatre Database
    $servname = "localhost";
    $username = "root";
    $password = "";
    $database = "brooklyntheatre";

    //$brooklyntheatre_db = new mysqli($servname, $username, $password, $database);

	if ($brooklyntheatre_db->connect_error) {
		die("Brooklyn Theatre Database Connection Failed: " . $brooklyntheatre_db->connect_error);
    }
    
    // Fetch all Videos is Brooklyn Theatre Database
    $query = "SELECT * FROM shows";
    $all_videos = $brooklyntheatre_db->query($query);

    // Loop over all videos
    while($video = $all_videos->fetch_assoc()) {
        // Populate Required Fields
        $title = substr($video['Title'], 0, 60);
        $shortDescription = substr($video['ShortDescription'], 0, 125);
        $longDescription = $video['LongDescription'];
        $length = $video['Length'];
        $price = $video['Price'];
        $theatreID = 1;
        $categoryID = 0;
        $starring = $video['Starring'];
        $releaseDate = $video['ReleaseDate'];
        $vimeoLink = $video['VimeoLink'];
        $popularity = $video['Views'];

        // Insert new video into ArtMusic Database
        $query = "INSERT INTO videos(Title, ShortDescription, LongDescription, Length, Price, TheatreID, CategoryID, Starring, ReleaseDate, VimeoLink, Popularity) VALUES ('$title', '$shortDescription', '$longDescription', '$length', '$price', '$theatreID', '$categoryID', '$starring', '$releaseDate', '$vimeoLink', '$popularity')";
        $artmusic_db->query($query);
    }

    echo "Completed!";
?>