<?php
include "../server/init.php";

if (!isset($_SESSION["user_id"])) {
    header('Location: ' . $link_prefix . '/account/signin/');
}

/* Check User Access */

if ($user_access !== "theatre") {
    header('Location: ' . $link_prefix . '/');
}

/* Check User Theatre */
$query = "SELECT * FROM theatres WHERE Status='enabled' AND id=" . $user_theatre;
$result = $db->query($query);

if ($result->num_rows === 1) {
    $user_theatre = $result->fetch_assoc();
} else {
    header('Location: ' . $link_prefix . '/');
}

$email = "";

$success_message = "";
$error_message = "";

/* Grant Free Access*/

if (isset($_POST['submitFreeAccess'])) {
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = $_POST['email'];

        $query = "SELECT * FROM users WHERE Email='$email'";
        $result = $db->query($query);

        if ($result->num_rows === 1) {
            $access_user = $result->fetch_assoc();

            $query = "SELECT * FROM subscriptions WHERE UserID=" . $access_user['id'] . " AND TheatreID=" . $user_theatre['id'];
            $result = $db->query($query);

            if ($result->num_rows === 0) {
                $query = "INSERT INTO subscriptions (UserID, TheatreID, Amount, Token) VALUES (" . $access_user['id'] . ", " . $user_theatre['id'] . ", 0, '')";
                $db->query($query);

                $email = "";
                $success_message = "User has been granted free access!";
            } else {
                $error_message = "User already has an active subscription!";
            }

        } else {
            $error_message = "Email was not found. Please provide an exising user's email.";
        }
    } else {
        $error_message = "Please provide a valid email addess.";
    }
}

/* Remove Admin Access*/

if (isset($_GET['remove_free_access']) && !empty($_GET['remove_free_access'])) {
    $remove_id = $_GET['remove_free_access'];

    $query = "DELETE FROM subscriptions WHERE id=" . $remove_id;
    $db->query($query);

    $success_message = "Users free access has been removed!";
}

/* Load FREE Access */

$query = "SELECT subscriptions.*, users.FirstName, users.LastName, users.Email, users.PhoneNumber FROM subscriptions INNER JOIN users ON users.id = subscriptions.UserID WHERE subscriptions.Amount='0' AND subscriptions.Token=''";
$free_access_list = $db->query($query);

/* Grant Admin Access*/

if (isset($_POST['submitAccess'])) {
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = $_POST['email'];

        $query = "SELECT * FROM users WHERE Email='$email'";
        $result = $db->query($query);

        if ($result->num_rows === 1) {
            $access_user = $result->fetch_assoc();

            $query = "INSERT INTO theatreaccess (UserID, TheatreID) VALUES (" . $access_user['id'] . ", " . $user_theatre['id'] . ")";
            $db->query($query);

            $email = "";
            $success_message = "User has been granted admin access!";
        } else {
            $error_message = "Email was not found. Please provide an exising user's email.";
        }
    } else {
        $error_message = "Please provide a valid email addess.";
    }
}

/* Remove Admin Access*/

if (isset($_GET['remove_access']) && !empty($_GET['remove_access'])) {
    $remove_id = $_GET['remove_access'];

    $query = "DELETE FROM theatreaccess WHERE id=" . $remove_id;
    $db->query($query);

    $success_message = "Users admin access has been removed!";
}

/* Load Admin Access */
$query = "SELECT theatreaccess.*, users.FirstName, users.LastName, users.Email, users.PhoneNumber FROM theatreaccess INNER JOIN users ON users.id = theatreaccess.UserID WHERE theatreaccess.UserID<>'$user_id' AND theatreaccess.TheatreID=" . $user_theatre['id'];
$admin_access_list = $db->query($query);

/* Update PayFast */
if (isset($_POST['submitPayfast'])) {
    $merchant_id = $_POST['merchant-id'];
    $merchant_key = $_POST['merchant-key'];
    $passphrase = $_POST['passphrase'];

    $query = "UPDATE theatres SET MerchantID='$merchant_id', MerchantKey='$merchant_key', Passphrase='$passphrase' WHERE id=" . $user_theatre['id'];
    $db->query($query);

    $success_message = "Payfast details has been updated!";
}

/* Load PayFast */

$query = "SELECT * FROM theatres WHERE id=" . $user_theatre['id'];
$result = $db->query($query);

if ($result->num_rows === 1) {
    $result_array = $result->fetch_assoc();

    $payfast_merchant_id = $result_array['MerchantID'];
    $payfast_merchant_key = $result_array['MerchantKey'];
    $payfast_passphrase = $result_array['Passphrase'];
}

/* Update Membership Fee */

if (isset($_POST['submitMembership'])) {
    $membership_fee = $_POST['membership-fee'];

    $query = "UPDATE theatres SET MembershipFee='$membership_fee' WHERE id=" . $user_theatre['id'];
    $db->query($query);

    $success_message = "Membership Fee has been updated!";
}

/* Load Membership Fee */

$query = "SELECT * FROM theatres WHERE id=" . $user_theatre['id'];
$result = $db->query($query);

if ($result->num_rows === 1) {
    $result_array = $result->fetch_assoc();
    $membership_fee = $result_array['MembershipFee'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Theatre Settings</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/master.css">
    <link rel="stylesheet" href="../styles/custom.css">
</head>

<body class="container-fluid p-0 m-0">
    <!-- Import Theatre Navigation Bar -->
    <?php include "../server/includes/theatre-navigation.php"; ?>

    <div class="mt-5 pt-5">
        <!-- Spacer -->
    </div>

    <main class="mx-4">
        <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
            <h2 class="d-inline-block m-0 p-0">Settings</h2>
        </div>

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

        <section class="jumbotron mb-5 p-3">
            <h4 class="mb-4">Give Free Full Access</h4>
            <form action="./settings.php" method="POST">
                <div class="form-group">
                    <label for="email">Account Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary mb-5" name="submitFreeAccess">Give</button>
            </form>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th scope="col">Full Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Date Received</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($access = $free_access_list->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $access['FirstName'] . ' ' . $access['LastName']; ?></td>
                                <td><a href="mailto:<?php echo $access['Email']; ?>"><?php echo $access['Email']; ?></a></td>
                                <td><a href="tel:<?php echo $access['PhoneNumber']; ?>"><?php echo $access['PhoneNumber']; ?></a></td>
                                <td><?php echo date('d M Y H:i:s',strtotime($access['DateCreated'])); ?></td>
                                <td>
                                    <a href="./settings.php?remove_free_access=<?php echo $access['id']; ?>">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="jumbotron mb-5 p-3">
            <h4 class="mb-4">Theatre Admin Manager</h4>
            <form action="./settings.php" method="POST">
                <div class="form-group">
                    <label for="email">Account Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary mb-5" name="submitAccess">Grant Access</button>
            </form>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th scope="col">Full Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Date Received</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($access = $admin_access_list->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $access['FirstName'] . ' ' . $access['LastName']; ?></td>
                                <td><a href="mailto:<?php echo $access['Email']; ?>"><?php echo $access['Email']; ?></a></td>
                                <td><a href="tel:<?php echo $access['PhoneNumber']; ?>"><?php echo $access['PhoneNumber']; ?></a></td>
                                <td><?php echo date('d M Y H:i:s',strtotime($access['DateCreated'])); ?></td>
                                <td>
                                    <a href="./settings.php?remove_access=<?php echo $access['id']; ?>">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="jumbotron mb-5 p-3">
            <h4 class="mb-4">PayFast</h4>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="merchant-id">Merchant ID</label>
                    <input type="text" class="form-control" id="merchant-id" name="merchant-id" value="<?php echo $payfast_merchant_id; ?>" required>
                </div>
                <div class="form-group">
                    <label for="merchant-key">Merchant Key</label>
                    <input type="text" class="form-control" id="merchant-key" name="merchant-key" value="<?php echo $payfast_merchant_key; ?>" required>
                </div>
                <div class="form-group">
                    <label for="passphrase">Passphrase</label>
                    <input type="text" class="form-control" id="passphrase" name="passphrase" value="<?php echo $payfast_passphrase; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" name="submitPayfast">Save</button>
            </form>
        </section>
        <section class="jumbotron mb-5 p-3">
            <h4 class="mb-4">Pricing</h4>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="membership-fee">Monthly Membership Fee (R)</label>
                    <input type="text" class="form-control" id="membership-fee" name="membership-fee" value="<?php echo $membership_fee; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" name="submitMembership">Save</button>
            </form>
        </section>
    </main>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../scripts/bootstrap.js"></script>

    <!-- Hide Alert Messages After Delay -->
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').css({
                    "visibility": "hidden"
                });
            }, 5000);
        });
    </script>
</body>

</html>