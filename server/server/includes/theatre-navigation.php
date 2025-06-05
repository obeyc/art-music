<nav class="navbar navbar-expand-xl navbar-light bg-white fixed-top">
    <a class="navbar-brand" href="<?php echo $link_prefix;?>/theatre/"><?php echo $user_theatre['Title']; ?> Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navigation">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $link_prefix;?>/theatre/videos.php">Videos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $link_prefix;?>/theatre/members.php">Members</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Reports
                </a>
                <div class="dropdown-menu" aria-labelledby="reportsDropdown">
                    <a class="dropdown-item" href="<?php echo $link_prefix;?>/theatre/reports.php#views">Views</a>
                    <a class="dropdown-item" href="<?php echo $link_prefix;?>/theatre/reports.php#previews">Previews</a>
                    <a class="dropdown-item" href="<?php echo $link_prefix;?>/theatre/reports.php#comments">Comments</a>
                    <a class="dropdown-item" href="<?php echo $link_prefix;?>/theatre/reports.php#ratings">Ratings</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $link_prefix;?>/theatre/payments.php">Payments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $link_prefix;?>/theatre/settings.php">Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn btn-success text-white ml-2 mobile-margin-0" href="<?php echo $link_prefix;?>/">Back to ArtMusic TV</a>
            </li>
        </ul>
    </div>
</nav>