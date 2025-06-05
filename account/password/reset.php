<?php

    include "../../server/init.php";

    $password = "";
    $confirmPassword = "";

    $error_message = "";

    /*  Validate Reset Code */

    if (isset($_GET["r_c"]) && !empty($_GET["r_c"])) {
        $reset_code = mysqli_real_escape_string($db, $_GET['r_c']);

        $stmt = $db->prepare("SELECT * FROM users WHERE ResetCode=?");
        $stmt->bind_param("s", $reset_code);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $result_array = $result->fetch_assoc();
            $user_reset_id = $result_array["id"];
        } else {
            header('Location: forgot.php');
        }
    } else {
        header('Location: forgot.php');
    }

    /* Reset Password Form Submit */

    if (isset($_POST['submit'])) {

        /* Input Validation */

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

        /* Reset Password */

        if (empty($error_message)) {
            $default_reset_code = "";

            $stmt = $db->prepare("UPDATE users SET Password=?, ResetCode=? WHERE id=?");
            $stmt->bind_param("ssi", $hashed_password, $default_reset_code, $user_reset_id);
            $stmt->execute();
        
            header('Location: ../signin/');
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Reset your account password for ArtMusic TV.">
    <meta name="keywords" content="artmusic, reset password">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Reset Password</title>
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
            <div class="col-md-3"><!-- Spacer --></div>
            <div class="col-md-6">

                <?php if (!empty($error_message)) : ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <div class="jumbotron py-4 mb-5">
                    <!-- Page Title -->
                    <h2 class="mb-3">Reset Password</h2>
                    
                    <!-- Reset Password Form -->
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" value="<?php echo $confirmPassword; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success" name="submit">Reset</button>
                    </form>
                </div>
            </div>
            <div class="col-md-3"><!-- Spacer --></div>
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