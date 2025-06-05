<?php

    include "../server/init.php";

    if (!isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/account/signin/');
    }

    /* Check User Access */

    if ($user_access !== "admin") {
        header('Location: ' . $link_prefix . '/');
    }

    $category_title = "";
    $email = "";

    $success_message = "";
    $error_message = "";

    /* Create Category */

    if (isset($_POST['submitCategory'])) {
        if (isset($_POST['category']) && !empty($_POST['category'])) {
            $category_title = $_POST['category'];

            $query = "SELECT * FROM categories WHERE Title='$category_title'";
            $result = $db->query($query);

            if ($result->num_rows == 0) {
                $query = "INSERT INTO categories (Title) VALUES ('$category_title')";
                $db->query($query);

                $success_message = "Category has been added!";
            } else {
                $error_message = "Category already exists!";
            }
        } else {
            $error_message = "Please provide a valid category title!";
        }
    }

    /* Remove Category */

    if (isset($_GET['remove_c']) && !empty($_GET['remove_c'])) {
        $category_id = $_GET['remove_c'];

        $query = "SELECT * FROM videos WHERE CategoryID='$category_id'";
        $result = $db->query($query);

        if ($result->num_rows === 0) {
            $query = "DELETE FROM categories WHERE id=" . $category_id;
            $db->query($query);

            $success_message = "Category has been deleted!";
        } else {
            $error_message = "Please remove this category from all videos before deleting it.";
        }
    }

    /* Load Categories */

    $query = "SELECT * FROM categories ORDER BY Title DESC";
    $categories_list = $db->query($query);

    /* Grant Admin Access */

    if (isset($_POST['submitAccess'])) {
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $email = $_POST['email'];

            $query = "SELECT * FROM users WHERE Email='$email'";
            $result = $db->query($query);

            if ($result->num_rows === 1) {
                $access_user = $result->fetch_assoc();

                $query = "INSERT INTO adminaccess (UserID, GrantedBy) VALUES (" . $access_user['id'] . ", $user_id)";
                $db->query($query);

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

        $query = "SELECT * FROM users WHERE id='$remove_id'";
        $result = $db->query($query);

        if ($result->num_rows === 1) {
            $query = "DELETE FROM adminaccess WHERE UserID=" . $remove_id;
            $db->query($query);

            $success_message = "User's admin access has been removed!";
        }
    }

    /* Load Admin Access */

    $query = "SELECT *, users.FirstName, users.LastName, users.Email, users.PhoneNumber FROM adminaccess INNER JOIN users ON users.id = adminaccess.UserID WHERE UserID<>'$user_id'";
    $admin_access_list = $db->query($query);

    /* Update Maintenance Mode */

    if (isset($_POST['submitMaintenance'])) {
        if (isset($_POST['maintenance']) && $_POST['maintenance'] == "on") {
            $query = "UPDATE settings SET Value='on' WHERE Name='maintenance'";
            $db->query($query);

            $success_message = "Maintenance Mode has bee turned ON!";
        } else {
            $query = "UPDATE settings SET Value='off' WHERE Name='maintenance'";
            $db->query($query);

            $success_message = "Maintenance Mode has bee turned OFF!";
        }
    }

    /* Load Maintenance Mode */

	$query = "SELECT * FROM settings WHERE Name='maintenance'";
    $result = $db->query($query);

    if ($result->num_rows === 1) {
      	$result_array = $result->fetch_assoc();
		$maintenance_mode = $result_array['Value'];
	} else {
        $maintenance_mode = "off";
    }

    /* Upload Banner Images */

    if (isset($_POST["updateSlide1"])) {
        if ($_FILES["updateSlideImage1"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['updateSlideImage1']['name'], PATHINFO_EXTENSION));
        
            $target_dir = "../media/images/banners/";
            $target_file = $target_dir . 'banner-1.' . $imageFileType;
        
            $uploadOk = 1;
        
            if ($imageFileType != "jpg") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES["updateSlideImage1"]["tmp_name"], $target_file);
                $success_message = "Slide #1 has been updated!";
            } else {
                $error_message = "File Upload Error. Try Again!";
            }
        }
    }

    if (isset($_POST["updateSlide2"])) {
        if ($_FILES["updateSlideImage2"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['updateSlideImage2']['name'], PATHINFO_EXTENSION));
        
            $target_dir = "../media/images/banners/";
            $target_file = $target_dir . 'banner-2.' . $imageFileType;
        
            $uploadOk = 1;
        
            if ($imageFileType != "jpg") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES["updateSlideImage2"]["tmp_name"], $target_file);
                $success_message = "Slide #2 has been updated!";
            } else {
                $error_message = "File Upload Error. Try Again!";
            }
        }
    }

    if (isset($_POST["updateSlide3"])) {
        if ($_FILES["updateSlideImage3"]["error"] != 4) {

            $imageFileType = strtolower(pathinfo($_FILES['updateSlideImage3']['name'], PATHINFO_EXTENSION));
        
            $target_dir = "../media/images/banners/";
            $target_file = $target_dir . 'banner-3.' . $imageFileType;
        
            $uploadOk = 1;
        
            if ($imageFileType != "jpg") {
                $uploadOk = 0;
            }
        
            if ($uploadOk == 1) {
                move_uploaded_file($_FILES["updateSlideImage3"]["tmp_name"], $target_file);
                $success_message = "Slide #3 has been updated!";
            } else {
                $error_message = "File Upload Error. Try Again!";
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Admin Settings</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/master.css">
</head>
<body class="container-fluid p-0 m-0">

    <!-- Import Admin Navigation Bar -->
    <?php include "../server/includes/admin-navigation.php"; ?>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

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
            <h4 class="mb-4">Category Manager</h4>
            <form action="./settings.php" method="POST">
                <div class="form-group">
                    <label for="category">New Catagory</label>
                    <input type="text" class="form-control" id="category" name="category" value="<?php echo $category_title; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary mb-5" name="submitCategory">Save</button>
            </form>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Videos in Category</th>
                            <th scope="col">Date Created</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $categories_list->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $c['Title']; ?></td>
                                <td>
                                    <?php
                                        $query = "SELECT * FROM videos WHERE CategoryID=" . $c['id'];
                                        $result = $db->query($query);

                                        echo $result->num_rows;
                                    ?>
                                </td>
                                <td><?php echo $c['DateCreated']; ?></td>
                                <td>
                                    <a href="./settings.php?remove_c=<?php echo $c['id']; ?>">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
        
        <section class="jumbotron mb-5 p-3">
            <h4 class="mb-4">Admin Manager</h4>
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
                                <td><a href="<?php echo $link_prefix; ?>/admin/users.php?pv=view&uid=<?php echo $access['UserID']; ?>"><?php echo $access['FirstName'] . ' ' . $access['LastName']; ?></a></td>
                                <td><a href="mailto:<?php echo $access['Email']; ?>"><?php echo $access['Email']; ?></a></td>
                                <td><a href="tel:<?php echo $access['PhoneNumber']; ?>"><?php echo $access['PhoneNumber']; ?></a></td>
                                <td><?php echo $access['DateCreated']; ?></td>
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
            <h4 class="mb-4">Maintenance Mode</h4>
            <form action="./settings.php" method="POST">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="maintenance" name="maintenance" value="on" <?php if ($maintenance_mode === "on") { echo "checked"; }?>>
                    <label class="custom-control-label" for="maintenance">Website Maintenance Mode</label>
                </div>
                <button type="submit" class="btn btn-primary mt-3" name="submitMaintenance">Save</button>
            </form>
        </section>

        <section class="jumbotron mb-5 p-3">
            <h4 class="mb-4">Slides Manager</h4>
            <div class="row">
                <div class="col-md-4 p-3">
                    <h5>Slide #1</h5>
                    <img src="<?php echo $link_prefix; ?>/media/images/banners/banner-1.jpg" class="mb-4 img-fluid">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="updateSlideImage1" name="updateSlideImage1" required>
                            <label class="custom-file-label" for="updateSlideImage1">Upload Slide Image (.JPG)</label>
                        </div>
                        <button type="submit" class="mt-2 btn btn-primary" name="updateSlide1">Update</button>
                    </form>
                </div>
                <div class="col-md-4 p-3">
                    <h5>Slide #2</h5>
                    <img src="<?php echo $link_prefix; ?>/media/images/banners/banner-2.jpg" class="mb-4 img-fluid">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="updateSlideImage2" name="updateSlideImage2" required>
                            <label class="custom-file-label" for="updateSlideImage2">Upload Slide Image (.JPG)</label>
                        </div>
                        <button type="submit" class="mt-2 btn btn-primary" name="updateSlide2">Update</button>
                    </form>
                </div>
                <div class="col-md-4 p-3">
                    <h5>Slide #3</h5>
                    <img src="<?php echo $link_prefix; ?>/media/images/banners/banner-3.jpg" class="mb-4 img-fluid">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="updateSlideImage3" name="updateSlideImage3" required>
                            <label class="custom-file-label" for="updateSlideImage3">Upload Slide Image (.JPG)</label>
                        </div>
                        <button type="submit" class="mt-2 btn btn-primary" name="updateSlide3">Update</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../scripts/bootstrap.js"></script>

    <!-- Bootstrap Custom File -->
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init()
        })
    </script>

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