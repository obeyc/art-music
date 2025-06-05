<?php
    include "../server/init.php";

    if (!isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/account/signin/');
    }

    /* Check User Access */

    if ($user_access !== "admin") {
        header('Location: ' . $link_prefix . '/');
    }

    /* Load Payments (with Filters) */

    if (isset($_GET['filterOrderBy']) && !empty($_GET['filterOrderBy'])) {
        $filterOrderBy = $_GET['filterOrderBy'];

        if ($filterOrderBy === "amount") {
            $query_order = "Amount DESC";
        } else {
            $query_order = "DateCreated DESC";
        }
    } else {
        $query_order = "DateCreated DESC";
    }

    if (isset($_GET['filterUser']) && !empty($_GET['filterUser'])) {
        $filterUser = $_GET['filterUser'];
        $query_user = "UserID = " . $filterUser;
    } else {
        $filterUser = "";
        $query_user = "UserID<>0";
    }

    if (isset($_GET['filterTheatre']) && !empty($_GET['filterTheatre'])) {
        $filterTheatre = $_GET['filterTheatre'];
        $query_theatre = "TheatreID=" . $filterTheatre;
    } else {
        $filterTheatre = "";
        $query_theatre = "TheatreID<>0";
    }

    if (isset($_GET['filterStartDate']) && !empty($_GET['filterStartDate'])) {
        $filterStartDate = $_GET['filterStartDate'];

        if (isset($_GET['filterEndDate']) && !empty($_GET['filterEndDate'])) {
            $filterEndDate = $_GET['filterEndDate'];
            
            if ($filterStartDate < $filterEndDate) {
                $query_dates = "(DateCreated BETWEEN '$filterStartDate' AND '$filterEndDate')";
            } else {
                $filterStartDate = 0;
                $filterEndDate = 0;
                $query_dates = "1 = 1";
            }
        } else {
            $filterEndDate = 0;
            $query_dates = "1 = 1";
        }
    } else {
        $filterStartDate = 0;
        $query_dates = "1 = 1";
    }

    $query = "SELECT * FROM payments WHERE " . $query_dates . " AND " . $query_user . " AND " . $query_theatre . " ORDER BY " . $query_order;
    $payments = $db->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Admin Payments</title>
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
        <!-- List of Payments Section -->
        <section class="container-fluid">
            <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                <h2 class="d-inline-block m-0 p-0">Payments</h2>
            </div>

            <div class="d-flex justify-content-between align-items-end mb-4 mobile-d-block">
                <form class="form-inline mobile-margin-b-1" action="" method="GET">
                    <div class="d-block w-100 mb-2">
                        <select class="form-control mr-sm-2" id="filterOrderBy" name="filterOrderBy">
                            <option value="date" <?php if (isset($filterOrderBy) && $filterOrderBy === "date") { echo "selected"; } ?>>Order by Date (New to Old)</option>
                            <option value="amount" <?php if (isset($filterOrderBy) && $filterOrderBy === "amount") { echo "selected"; } ?>>Order by Amount (Low to High)</option>
                        </select>

                        <select class="form-control mr-sm-2" id="filterUser" name="filterUser">
                            <option value="">All Users</option>
                            <?php
                                $query = "SELECT * FROM users";
                                $users = $db->query($query);
                            ?>
                            <?php while ($user = $users->fetch_assoc()) { ?>
                                <option value="<?php echo $user['id']; ?>" <?php if (isset($filterUser) && $filterUser == $user['id']) { echo "selected"; } ?>><?php echo $user['FirstName'] . ' ' . $user['LastName']; ?></option>
                            <?php } ?>
                        </select>

                        <select class="form-control mr-sm-2" id="filterTheatre" name="filterTheatre">
                            <option value="">All Theatres</option>
                            <?php
                                $query = "SELECT * FROM theatres";
                                $theatres = $db->query($query);
                            ?>
                            <?php while ($theatre = $theatres->fetch_assoc()) { ?>
                                <option value="<?php echo $theatre['id']; ?>" <?php if (isset($filterTheatre) && $filterTheatre == $theatre['id']) { echo "selected"; } ?>><?php echo $theatre['Title']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group mobile-width-100 mobile-margin-b-0 mobile-margin-t-1">
                        <input type="datetime-local" class="form-control" id="filterStartDate" name="filterStartDate" value="<?php if (isset($filterStartDate)) { echo str_replace(" ", "T", $filterStartDate); } ?>">
                    </div>
                    <span class="mx-2 mobile-d-block mobile-width-100 mobile-margin-1 mobile-text-center"> to </span>
                    <div class="form-group mobile-width-100">
                        <input type="datetime-local" class="form-control" id="filterEndDate" name="filterEndDate" value="<?php if (isset($filterEndDate)) { echo str_replace(" ", "T", $filterEndDate); } ?>">
                    </div>

                    <button class="btn btn-secondary my-2 my-sm-0 mx-3 mobile-d-block mobile-width-100 mobile-margin-0" type="submit">
                        Apply
                    </button>
                    <a class="text-success mobile-d-block mobile-width-100 mobile-text-center mobile-margin-2" href="./payments.php">Clear</a>
                </form>
                <button class="btn btn-outline-success ml-4 mobile-margin-0 mobile-d-block mobile-width-100" onClick="fnExcelReport()">Download</button>
            </div>
            <div class="table-responsive mb-5">
                <table id="paymentsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Full Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Theatre</th>
                            <th scope="col">Description</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Fee</th>
                            <th scope="col">Gateway ID</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments->fetch_assoc()) : ?>
                            <?php
                                $query = "SELECT * FROM users WHERE id=" . $payment['UserID'];
                                $result = $db->query($query);
                        
                                if ($result->num_rows === 1) {
                                    $payment_user = $result->fetch_assoc();
                                } else {
                                    $payment_user['id'] = 0;
                                    $payment_user['FirstName'] = "Unknown";
                                    $payment_user['LastName'] = "User";
                                    $payment_user['Email'] = "No Email";
                                    $payment_user['PhoneNumber'] = "No Phone Number";
                                }

                                $query = "SELECT * FROM theatres WHERE id=" . $payment['TheatreID'];
                                $result = $db->query($query);
                        
                                if ($result->num_rows === 1) {
                                    $payment_theatre = $result->fetch_assoc();
                                } else {
                                    $payment_theatre['id'] = 0;
                                    $payment_theatre['Title'] = "Unknown Theatre";
                                }
                            ?>
                            <tr>
                                <td><a href="<?php echo $link_prefix; ?>/admin/users.php?pv=view&uid=<?php echo $payment_user['id']; ?>"><?php echo $payment_user['FirstName'] . ' ' . $payment_user['LastName']; ?></a></td>
                                <td><a href="mailto:<?php echo $payment_user['Email']; ?>"><?php echo $payment_user['Email']; ?></a></td>
                                <td><a href="tel:<?php echo $payment_user['PhoneNumber']; ?>"><?php echo $payment_user['PhoneNumber']; ?></a></td>
                                <td><a href="<?php echo $link_prefix; ?>/admin/theatres.php?pv=view&tid=<?php echo $payment_theatre['id']; ?>"><?php echo $payment_theatre['Title']; ?></a></td>
                                <td><?php echo $payment['Description']; ?></td>
                                <td>R<?php echo number_format($payment['Amount'], 2, '.', ','); ?></td>
                                <td>R<?php echo number_format($payment['Fee'], 2, '.', ','); ?></td>
                                <td><?php echo $payment['PayFastID']; ?></td>
                                <td><?php echo $payment['DateCreated']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../scripts/bootstrap.js"></script>

    <!-- Export Table to Excel -->
    <script>
        function fnExcelReport() {
            var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
            var textRange; 
            var j = 0;
            tab = document.getElementById('paymentsTable');

            for (j = 0; j < tab.rows.length; j++) {     
                tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
                //tab_text=tab_text+"</tr>";
            }

            tab_text=tab_text+"</table>";
            tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");
            tab_text= tab_text.replace(/<img[^>]*>/gi,"");
            tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, "");

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE "); 

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
                txtArea1.document.open("txt/html","replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus(); 
                sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls");
            } else {
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));  
            }

            return (sa);
        }
    </script>
</body>
</html>