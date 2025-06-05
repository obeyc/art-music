<?php
    include "../../server/init.php";

    $firstName = "";
    $lastName = "";
    $email = "";
    $phoneNumber = "";
    $city = "";
    $province = "";
    $country = "";
    $password = "";
    $confirmPassword = "";

    $error_message = "";

    /* Create Account Form Submit */

    if (isset($_POST['submit'])) {

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

            $query = "SELECT * FROM users WHERE Email='$email'";
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

        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $password = mysqli_real_escape_string($db, $_POST['password']);
        } else {
            $error_message = "Please provide a password.";
        }

        if (isset($_POST['confirmPassword']) && !empty($_POST['confirmPassword'])) {
            $confirmPassword = mysqli_real_escape_string($db, $_POST['confirmPassword']);
        } else {
            $error_message = "Please confirm your password.";
        }

        if ($password === $confirmPassword) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $error_message = "Passwords do not match. Please ensure you enter the correct passwords!";
        }

        /* Create Account */

        $default_session_id = "";
        $default_status = "enabled";
        $default_reset_code = "";
    
        if (empty($error_message)) {
            $stmt = $db->prepare("INSERT INTO users (FirstName, LastName, Email, Password, PhoneNumber, City, Province, Country, SessionID, Status, ResetCode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssss", $firstName, $lastName, $email, $hashed_password, $phoneNumber, $city, $province, $country, $default_session_id, $default_status, $default_reset_code);
            $stmt->execute();

            session_unset();
            session_destroy();
            setcookie("user_session_id", "", time() - 3600, "/");
        
            header('Location: ../signin/?msg=success');
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Create your ArtMusic TV account and start watching today!">
    <meta name="keywords" content="artmusic, create account">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Create Account</title>
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

    <div class="container">
        <div class="row">
            <div class="col-md-2"><!-- Spacer --></div>
            <div class="col-md-8">

                <?php if (!empty($error_message)) : ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="jumbotron py-4 mb-5">
                    <!-- Page Title -->
                    <h2 class="mb-5">Create Account</h2>

                    <!-- Create Account Form -->
                    <form action="" method="POST">
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
                                    <?php include "../../server/includes/countries.php"; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" value="<?php echo $confirmPassword; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success" name="submit">Create Account</button>
                    </form>
                    <p class="text-right mt-3">
                        Already have an account? <a href="../signin/">Sign In</a>
                    </p>
                </div>
            </div>
            <div class="col-md-2"><!-- Spacer --></div>
        </div>
    </div>

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