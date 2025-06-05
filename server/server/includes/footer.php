<footer class="bg-dark m-0 pt-4 pb-2 px-5">
    <div class="row">
        <div class="col-md-4">
            <h2>ArtMusic TV</h2>
            <p class="mb-3">
                This is an all encompassing platform taking care of all your Art Music needs.
                A modern full-featured web application that will allow users to effortlessly stream
                premium quality videos and / or music files on all major browser-enabled devices.
            </p>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h4>Get Around</h4>
                    <div>
                        <a href="<?php echo $link_prefix; ?>/browse/latest/">Latest</a>
                    </div>
                    <div>
                        <a href="<?php echo $link_prefix; ?>/browse/upcoming/">Upcoming</a>
                    </div>
                    <div>
                        <a href="<?php echo $link_prefix; ?>/browse/popular/">Popular</a>
                    </div>
                    <div>
                        <a href="<?php echo $link_prefix; ?>/browse/list/">A - Z List</a>
                    </div>
                    <div>
                        <a href="<?php echo $link_prefix; ?>/guide/">User Guide</a>
                    </div>
                    <div>
                        <a href="<?php echo $link_prefix; ?>/legal/terms.php">Terms & Conditions</a>
                    </div>
                    <div>
                        <a href="<?php echo $link_prefix; ?>/legal/privacy.php">Privacy Policy</a>
                    </div>
                    <div>
                        <a href="<?php echo $link_prefix; ?>/about/">About Us</a>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <h4>Personal Stuff</h4>

                    <?php if (!isset($_SESSION["user_id"])) { ?>
                        <div>
                            <a href="<?php echo $link_prefix; ?>/account/create/">Create Account</a>
                        </div>
                        <div>
                            <a href="<?php echo $link_prefix; ?>/account/signin/">Sign In</a>
                        </div>
                    <?php } else { ?>
                        <div>
                            <a href="<?php echo $link_prefix; ?>/account/notifications/">Notifications</a>
                        </div>
                        <div>
                            <a href="<?php echo $link_prefix; ?>/account/">Account Settings</a>
                        </div>
                        <div>
                            <a href="<?php echo $link_prefix; ?>/payments/donate/">Donate</a>
                        </div>
                        <div>
                            <a href="<?php echo $link_prefix; ?>/account/invite/">Invite a Friend</a>
                        </div>
                        <div>
                            <a href="<?php echo $link_prefix; ?>/payments/gift/">Gift Card</a>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-4 mb-3">
                    <h4>Socials</h4>
                    <div>
                        <a href="https://www.facebook.com/ArtMusicTVSa" target="_blank">Facebook</a>
                    </div>
                    <div>
                        <a href="https://www.instagram.com/artmusic.tv" target="_blank">Instagram</a>
                    </div>
                    <div>
                        <a href="<?php echo $link_prefix; ?>/contact/">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="text-center border-top mt-4 pt-4">
        &copy; <?=date('Y')?> ArtMusic TV All Rights Reserved - Powered by <a href="https://allaboutcloud.co.za" target="_blank">All About Cloud</a>
    </p>
</footer>