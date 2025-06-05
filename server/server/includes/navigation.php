<?php

/* Load Categories */

$query = "SELECT * FROM categories";
$nav_categories = $db->query($query);

/* Load Theatres */

$query = "SELECT * FROM theatres";
$nav_theatres = $db->query($query);

?>
<nav class="navbar navbar-expand-xl navbar-dark bg-primary fixed-top">
    <a class="navbar-brand" href="<?php echo $link_prefix; ?>/">
        <img src="<?php echo $link_prefix; ?>/media/images/Logo-new-Transparent.png" width="300" alt="ArtMusic TV Logo" loading="lazy">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <!-- TODO: Link Active Style -->
    <div class="collapse navbar-collapse" id="navigation">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $link_prefix; ?>/browse/latest/">Latest</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $link_prefix; ?>/browse/upcoming/">Upcoming</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $link_prefix; ?>/browse/popular/">Popular</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $link_prefix; ?>/browse/list/">A - Z List</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $link_prefix; ?>/browse/purchased/">Purchased</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Categories
                </a>
                <div class="dropdown-menu" aria-labelledby="categoriesDropdown">
                    <a class="dropdown-item" href="<?php echo $link_prefix; ?>/browse/category/?cid=0">All Categories</a>
                    <?php while ($category = $nav_categories->fetch_assoc()) : ?>
                        <a class="dropdown-item" href="<?php echo $link_prefix; ?>/browse/category/?cid=<?php echo $category['id']; ?>"><?php echo $category['Title']; ?></a>
                    <?php endwhile; ?>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="theatresDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Theatres
                </a>
                <div class="dropdown-menu" aria-labelledby="theatresDropdown">
                    <a class="dropdown-item" href="<?php echo $link_prefix; ?>/browse/theatre/?tid=0">All Theatres</a>
                    <?php while ($t = $nav_theatres->fetch_assoc()) : ?>
                        <a class="dropdown-item" href="<?php echo $link_prefix; ?>/browse/theatre/?tid=<?php echo $t['id']; ?>"><?php echo $t['Title']; ?></a>
                    <?php endwhile; ?>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $link_prefix; ?>/browse/favourites/">My Favourites</a>
            </li>
        </ul>
        <form class="form-inline mx-auto my-2 my-lg-0" action="<?php echo $link_prefix; ?>/browse/search/" method="GET">
            <input class="form-control mr-sm-2" type="text" name="search" placeholder="Search" value="<?php if (isset($_GET["search"]) && !empty($_GET["search"])) {
                                                                                                            echo $_GET['search'];
                                                                                                        } ?>" required>
            <button class="btn btn-secondary my-2 my-sm-0" type="submit">
                Search
            </button>
        </form>
        <ul class="navbar-nav mr-3">
            <?php if (isset($_SESSION["user_id"])) { ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php
                        $query = "SELECT * FROM users WHERE id='$user_id'";
                        $result = $db->query($query);

                        if ($result->num_rows === 1) {
                            $result_array = $result->fetch_assoc();

                            echo $result_array["FirstName"] . " " . $result_array["LastName"];
                        }
                        ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="accountDropdown">

                        <?php if ($user_access === "admin") { ?>
                            <a class="dropdown-item" href="<?php echo $link_prefix; ?>/admin/">Admin Dashboard</a>
                            <div class="dropdown-divider"></div>
                        <?php } ?>

                        <?php if ($user_access === "theatre") { ?>
                            <a class="dropdown-item" href="<?php echo $link_prefix; ?>/theatre/">Theatre Dashboard</a>
                            <div class="dropdown-divider"></div>
                        <?php } ?>

                        <a class="dropdown-item" href="<?php echo $link_prefix; ?>/account/notifications/">Notifications</a>
                        <a class="dropdown-item" href="<?php echo $link_prefix; ?>/account/payments/">Payment History</a>
                        <a class="dropdown-item" href="<?php echo $link_prefix; ?>/account/">Account Settings</a>
                        <a class="dropdown-item" href="<?php echo $link_prefix; ?>/payments/donate/">Donate</a>
                        <a class="dropdown-item" href="<?php echo $link_prefix; ?>/account/invite/">Invite a Friend</a>
                        <a class="dropdown-item" href="<?php echo $link_prefix; ?>/payments/gift/">Gift Cards</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo $link_prefix; ?>/account/signout/">Sign Out</a>
                    </div>
                </li>
            <?php } else { ?>
                <li class="nav-item mr-2 my-1">
                    <a class="nav-link" href="<?php echo $link_prefix; ?>/account/create/">Create Account</a>
                </li>
                <li class="nav-item mr-2 my-1">
                    <a class="nav-link btn btn-success text-white" href="<?php echo $link_prefix; ?>/account/signin/">Sign In</a>
                </li>
            <?php } ?>
        </ul>
    </div>
</nav>