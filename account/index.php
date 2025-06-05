<?php

    include "../server/init.php";

    if (!isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/account/signin/');
    }

    $oldPassword= "";
    $password = "";
    $confirmPassword = "";

    $success_message = "";
    $error_message = "";

    /* Page View */

    if (isset($_GET['pv']) && !empty($_GET['pv'])) {
        $page_view = $_GET['pv'];

        if ($page_view !== "info" && $page_view !== "pass") {
            $page_view = "default";
        }
    } else {
        $page_view = "default";
    }

    /* Load User Information */

    $query = "SELECT * FROM users WHERE id='$user_id'";
    $result = $db->query($query);

    if ($result->num_rows === 1) {
        $result_array = $result->fetch_assoc();

        $firstName = $result_array["FirstName"];
        $lastName = $result_array["LastName"];
        $email = $result_array["Email"];
        $phoneNumber = $result_array["PhoneNumber"];
        $city = $result_array["City"];
        $province = $result_array["Province"];
        $country = $result_array["Country"];
    } else {
        header('Location: ' . $link_prefix . '/account/signout/');
    }

    /* Load Subscriptions */

    $query = "SELECT * FROM subscriptions WHERE UserID='$user_id'";
    $subscriptions = $db->query($query);

    /* Update Information Form Submit */

    if (isset($_POST['submitInfo'])) {

        /* Input Validation */

        if (isset($_POST['firstName']) && !empty($_POST['firstName'])) {
            $firstName = mysqli_real_escape_string($db, $_POST['firstName']);
        } else {
            $error_message = "Please provide your first name.";
        }

        if (isset($_POST['lastName']) && !empty($_POST['lastName'])) {
            $lastName = mysqli_real_escape_string($db, $_POST['lastName']);
        } else {
            $error_message = "Please provide your last name.";
        }

        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $email = mysqli_real_escape_string($db, $_POST['email']);

            $query = "SELECT * FROM users WHERE Email='$email' AND id<>'$user_id'";
            $result = $db->query($query);

            if ($result->num_rows != 0) {
                $error_message = "Email unavailable. Please provide an email that is not already in use!";
            }
        } else {
            $error_message = "Please provide a valid email address.";
        }

        if (isset($_POST['phoneNumber']) && !empty($_POST['phoneNumber'])) {
            $phoneNumber = mysqli_real_escape_string($db, $_POST['phoneNumber']);
        } else {
            $error_message = "Please provide your phone number.";
        }

        if (isset($_POST['city']) && !empty($_POST['city'])) {
            $city = mysqli_real_escape_string($db, $_POST['city']);
        } else {
            $error_message = "Please provide your city of residence.";
        }

        if (isset($_POST['province']) && !empty($_POST['province'])) {
            $province = mysqli_real_escape_string($db, $_POST['province']);
        } else {
            $error_message = "Please provide your province of residence.";
        }

        if (isset($_POST['country']) && !empty($_POST['country'])) {
            $country = mysqli_real_escape_string($db, $_POST['country']);
        } else {
            $error_message = "Please select your country of residence.";
        }

        /* Update Information */

        if (empty($error_message)) {
            $stmt = $db->prepare("UPDATE users SET FirstName=?, LastName=?, Email=?, PhoneNumber=?, City=?, Province=?, Country=? WHERE id=?");
            $stmt->bind_param("sssssssi", $firstName, $lastName, $email, $phoneNumber, $city, $province, $country, $user_id);
            $stmt->execute();

            $success_message = "Information Updated!";
            $page_view = "default";
        }
    }

    /* Update Password Form Submit */

    if (isset($_POST['submitPass'])) {

        /* Input Validation */

        if (isset($_POST['oldPassword']) && !empty($_POST['oldPassword'])) {
            $oldPassword = mysqli_real_escape_string($db, $_POST['oldPassword']);

            $query = "SELECT * FROM users WHERE id='$user_id'";
            $result = $db->query($query);

            if ($result->num_rows === 1) {
                $result_array = $result->fetch_assoc();

                if(!password_verify($oldPassword, $result_array["Password"])) {
                    $error_message = "Please provide your current account password.";
                }
            }
        } else {
            $error_message = "Please provide your current account password.";
        }

        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $password = mysqli_real_escape_string($db, $_POST['password']);
        } else {
            $error_message = "Please provide a new password.";
        }

        if (isset($_POST['confirmPassword']) && !empty($_POST['confirmPassword'])) {
            $confirmPassword = mysqli_real_escape_string($db, $_POST['confirmPassword']);
        } else {
            $error_message = "Please confirm your new password.";
        }

        if ($password === $confirmPassword) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $error_message = "Passwords do not match. Please ensure you enter the correct passwords!";
        }

        /* Update Password */

        if (empty($error_message)) {
            $stmt = $db->prepare("UPDATE users SET Password=? WHERE id=?");
            $stmt->bind_param("si", $hashed_password,  $user_id);
            $stmt->execute();

            $success_message = "Password Changed!";
            $page_view = "default";
        }
    }

    /* Delete Account */

    if (isset($_GET["delete"]) && $_GET["delete"] == "true") {
        if ($subscriptions->num_rows === 0) {
            $query = "DELETE FROM users WHERE id='$user_id'";
            $db->query($query);

            header('Location: ./signout/');
        } else {
            $error_message = "Please remove all your active subscriptions before deleting your account.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Manage your entire account on your personal settings page.">
    <meta name="keywords" content="artmusic, account settings">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Account Settings</title>
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

    <main class="mx-4">
        <!-- Page Title -->
        <h2 class="mb-5">Account Settings</h2>

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

        <!-- Personal Information Section -->
        <div class="row">
            <div class="col-md-8 mx-auto">
                <section class="jumbotron mb-5 py-4">
                    
                    <?php if ($page_view === "default") { ?>
                        <h3 class="mb-4">Personal Information</h3>
                    <?php } ?>

                    <?php if ($page_view === "info") { ?>
                        <h3 class="mb-4">Update Personal Information</h3>
                    <?php } ?>

                    <?php if ($page_view === "pass") { ?>
                        <h3 class="mb-4">Change Password</h3>
                    <?php } ?>
                    
                    <!-- Update User Information Form -->
                    <?php if ($page_view === "default" || $page_view === "info") { ?>
                        <form action="" method="POST">

                            <?php if ($page_view === "default") { ?>
                                <fieldset disabled="disabled">
                            <?php } ?>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="firstName">First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $firstName; ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="lastName">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lastName; ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="phoneNumber">Phone Number</label>
                                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo $phoneNumber; ?>" required>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control" id="city" name="city" value="<?php echo $city; ?>" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="province">Province / State</label>
                                        <input type="text" class="form-control" id="province" name="province" value="<?php echo $province; ?>" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="country">Country</label>
                                        <select class="form-control" id="country" name="country" required>
                                            <option value="" disabled selected>Choose...</option>
                                            <!-- Import Countries -->
                                            <?php include "../server/includes/countries.php"; ?>
                                        </select>
                                    </div>
                                </div>

                                <?php if ($page_view === "info") { ?>
                                    <button type="submit" class="btn btn-success" name="submitInfo">Update</button>
                                    <a class="btn btn-danger" href="./">Cancel</a>
                                <?php } ?>

                            <?php if ($page_view === "default") { ?>
                                </fieldset>
                            <?php } ?>
                        </form>
                    <?php } ?>

                    <!-- Reset Password Form -->
                    <?php if ($page_view === "pass") { ?>
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="oldPassword">Old Password</label>
                                <input type="password" class="form-control" id="oldPassword" name="oldPassword" value="<?php echo $oldPassword; ?>" required>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" value="<?php echo $confirmPassword; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-success" name="submitPass">Save</button>
                            <a class="btn btn-danger" href="./">Cancel</a>
                        </form>
                    <?php } ?>
                    
                    <?php if ($page_view === "default") { ?>
                        <a class="btn btn-success" href="./?pv=info">Update Information</a>
                        <a class="btn btn-outline-success" href="./?pv=pass">Change Password</a>
                    <?php } ?>
                    
                </section>
            </div>
        </div>

        <!-- Manage Subscriptions Section -->
        <?php if ($page_view === "default") { ?>
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <section class="jumbotron mb-5 py-4">
                        <h3 class="mb-4">Manage Subscriptions</h3>

                        <?php if ($subscriptions->num_rows > 0) { ?>
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
                                        <?php while($subscription = $subscriptions->fetch_assoc()) : ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                        $theatre_id = $subscription["TheatreID"];

                                                        $stmt = $db->prepare("SELECT * FROM theatres WHERE id=?");
                                                        $stmt->bind_param("i", $theatre_id);
                                                        $stmt->execute();
                                    
                                                        $result = $stmt->get_result();
                                    
                                                        if ($result->num_rows === 1) {
                                                            $result_array = $result->fetch_assoc();
                                                            echo $result_array["Title"];
                                                        }
                                                    ?>
                                                </td>
                                                <td>R<?php echo $subscription["Amount"]; ?></td>
                                                <td><?php echo date('d M Y H:i:s',strtotime($subscription["DateCreated"])); ?></td>
                                                <td>
                                                    <a href="<?php echo $link_prefix; ?>/payments/subscriptions/cancel.php?sid=<?php echo $subscription["id"]; ?>">Remove</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <h5 class="text-center">You don't have any subscriptions yet.</h5>
                        <?php } ?>
                    </section>
                    </div>
                </div>
        <?php } ?>

        <!-- Delete Account Modal Trigger -->
        <?php if ($page_view === "default") { ?>
            <button type="button" class="btn btn-block btn-outline-danger my-5" data-toggle="modal" data-target="#deleteModal">
                Delete Account (Permanent)
            </button>
        <?php } ?>
    </main>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to permanently delete your account and all 
                    of its information?
                </div>
                <div class="modal-footer">
                    <a href="./?delete=true" class="btn btn-danger">Yes, delete my account</a>
                    <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Footer -->
    <?php include "../server/includes/footer.php"; ?>

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