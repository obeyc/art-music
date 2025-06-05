<?php
    include "../../server/init.php";

    if (!isset($_SESSION["user_id"])) {
        header('Location: ' . $link_prefix . '/account/signin/');
    }

    /* Load Payments (filters) */

    if (isset($_GET['filterOrderBy']) && !empty($_GET['filterOrderBy'])) {
        $filterOrderBy = $_GET['filterOrderBy'];

        if ($filterOrderBy === "amount") {
            $filterOrderBy = "Amount";
        } else {
            $filterOrderBy = "DateCreated";
        }
    } else {
        $filterOrderBy = "DateCreated";
    }

    $query = "SELECT * FROM payments WHERE UserID='$user_id' ORDER BY " . $filterOrderBy . " DESC";

    if (isset($_GET['filterStartDate']) && !empty($_GET['filterStartDate'])) {
        $filterStartDate = $_GET['filterStartDate'];

        if (isset($_GET['filterEndDate']) && !empty($_GET['filterEndDate'])) {
            $filterEndDate = $_GET['filterEndDate'];
            
            if ($filterStartDate < $filterEndDate) {
                $query = "SELECT * FROM payments WHERE UserID='$user_id' AND (DateCreated BETWEEN '$filterStartDate' AND '$filterEndDate') ORDER BY " . $filterOrderBy . " DESC";
            } else {
                $filterStartDate = 0;
                $filterEndDate = 0;
            }
        } else {
            $filterEndDate = 0;
        }
    } else {
        $filterStartDate = 0;
    }

    $payments = $db->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Manage and download your payment history.">
    <meta name="keywords" content="artmusic, payments">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Payment History</title>
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

    <main class="mx-4">
        <!-- List of Payments Section -->
        <section class="container-fluid">
            <div class="border-bottom d-flex justify-content-between align-items-end mb-5 pb-2">
                <h2 class="d-inline-block m-0 p-0">Payments</h2>
            </div>

            <div class="d-flex justify-content-between align-items-end mb-4 mobile-d-block">
                <form class="form-inline mobile-margin-b-1" action="" method="GET">
                    <select class="form-control mr-sm-2" id="filterOrderBy" name="filterOrderBy">
                        <option value="date" <?php if (isset($filterOrderBy) && $filterOrderBy === "DateCreated") { echo "selected"; } ?>>Order By Date</option>
                        <option value="amount" <?php if (isset($filterOrderBy) && $filterOrderBy === "Amount") { echo "selected"; } ?>>Order By Amount</option>
                    </select>

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
                    <a class="text-success mobile-d-block mobile-width-100 mobile-text-center mobile-margin-2" href="./">Clear</a>
                </form>
                <button class="btn btn-outline-success ml-4 mobile-margin-0 mobile-d-block mobile-width-100" onClick="fnExcelReport()">Download</button>
            </div>

            <?php if ($payments->num_rows > 0) { ?>
                <div class="table-responsive mb-5">
                    <table class="table table-striped" id="paymentsTable">
                        <thead>
                            <tr>
                                <th scope="col">Theatre</th>
                                <th scope="col">Description</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($payment = $payments->fetch_assoc()) : ?>
                                <tr>
                                    <td>
                                        <?php
                                            $theatre_id = $payment["TheatreID"];

                                            $stmt = $db->prepare("SELECT * FROM theatres WHERE id=?");
                                            $stmt->bind_param("i", $theatre_id);
                                            $stmt->execute();
                        
                                            $result = $stmt->get_result();
                        
                                            if ($result->num_rows === 1) {
                                                $result_array = $result->fetch_assoc();
                                                echo $result_array["Title"];
                                            } else {
                                                echo "Unknown Theatre";
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo $payment["Description"]; ?></td>
                                    <td><?php echo $payment["Amount"]; ?></td>
                                    <td><?php echo date('d M Y H:i:s',strtotime($payment["DateCreated"])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <h5 class="text-center my-5">You don't have any payments yet.</h5>
            <?php } ?>
        </section>
    </main>

    <!-- Import Footer -->
    <?php include "../../server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../../scripts/bootstrap.js"></script>

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