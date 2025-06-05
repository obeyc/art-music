<?php
    include "../../server/init.php";

    if (isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/');
    }

    $email = "";
    $password = "";

    $error_message = "";

    /* Sign In Form Submit */

    if (isset($_POST['submit'])) {

        /* Input Validation */

        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $email = mysqli_real_escape_string($db, $_POST['email']);

            if (isset($_POST['password']) && !empty($_POST['password'])) {
                $password = mysqli_real_escape_string($db, $_POST['password']);

                /* User Authentication */

                $query = "SELECT * FROM users WHERE Email='$email'";
                $result = $db->query($query);

                if ($result->num_rows === 1) {
                    $result_array = $result->fetch_assoc();

                    if(password_verify($password, $result_array["Password"])) {
                        $_SESSION["user_id"] = $result_array['id'];
                        
                        $new_session_id = session_id();
                        //setcookie("user_session_id", $new_session_id, time() + (86400 * 30), "/");
                        $_SESSION["user_session_id"] = $new_session_id;
                        $stmt = $db->prepare("UPDATE users SET SessionID=? WHERE id=?");
                        $stmt->bind_param("si", $new_session_id, $result_array['id']);
                        $stmt->execute();

                        header('Location: ' . $link_prefix . '/');
                    } else {
                        $error_message = "Invalid Password. Try Again!";
                    }
                } else {
                    $error_message = "Invalid Email. Try Again!";
                }
            } else {
                $error_message = "Please provide your account password.";
            }
        } else {
            $error_message = "Please provide your account email.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Sign in to your ArtMusic TV account and enjoy everything we have to offer.">
    <meta name="keywords" content="artmusic, sign in">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Sign In</title>
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
            <div class="col-md-4"><!-- Spacer --></div>
            <div class="col-md-4">

                <?php if (isset($_GET['msg']) && $_GET['msg'] === "success") : ?>
                    <div class="alert alert-success text-center" role="alert">
                        Account Created Successfully!
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)) : ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <div class="jumbotron py-4 mb-5">
                    <!-- Page Title -->
                    <h2 class="mb-5">Sign In</h2>

                    <!-- Sign In Form -->
                    <form action="./" method="POST">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success" name="submit">Sign In</button>
                    </form>
                    <div class="text-right">
                        <a class="d-inline-block mt-3" href="../password/forgot.php">Forgot Password</a>
                        <br>
                        <a class="d-inline-block mt-1" href="../create/">Create Account</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4"><!-- Spacer --></div>
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