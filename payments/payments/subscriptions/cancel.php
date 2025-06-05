<?php

    include "../../server/init.php";

	if (!isset($_SESSION["user_id"])) {
		header('Location: ' . $link_prefix . '/account/signin/');
    }
    
    if (isset($_GET["sid"]) && !empty($_GET["sid"])) {
        $subscription_id = mysqli_real_escape_string($db, $_GET['sid']);

		$stmt = $db->prepare("SELECT * FROM subscriptions WHERE id=?");
		$stmt->bind_param("i", $subscription_id);
		$stmt->execute();

		$result = $stmt->get_result();

		if ($result->num_rows === 1) {
            $subscription = $result->fetch_assoc();

            $query = "SELECT * FROM theatres WHERE id=" . $subscription['TheatreID'];
			$result = $db->query($query);

			if ($result->num_rows === 1) {
                $theatre = $result->fetch_assoc();
            } else {
                header('Location: ' . $link_prefix . '/');
            }
        } else {
            header('Location: ' . $link_prefix . '/');
        }
    } else {
        header('Location: ' . $link_prefix . '/account');
    }

    if (isset($_GET['cancel']) && $_GET['cancel'] == 1) {

        $pfData = array(
            'token' => $subscription['Token'],
            'api_action' => 'cancel',
            'timeframe' => 'daily',
            'merchant-id' => $theatre['MerchantID'],
            'version' => 'v1',
            'amount' => $subscription['Amount'] * 100,
            'item_name' => $theatre['Title'] . ' Subscription',
            'item_description' => $theatre['Title'] . ' Subscription',
            'cycles' => 0,
            'frequency' => "3",
            'run_date' => date('Y-m-d', strtotime('+1 months')),
            'passphrase' => $theatre['Passphrase'],
            'from' => '',
            'to' => ''
        );

        date_default_timezone_set('GMT');

        $timestamp = date('Y-m-d') . 'T' . date('H:i:s') . '+02:00';
        $pfData['timestamp'] = $timestamp;

        ksort($pfData);

        $pfParamString = '';
        foreach ($pfData as $key => $val) {
            if ($pfData['timeframe'] != 'custom timeframe') {
                if (!empty($val) && $key != 'api_action' && $key != 'submit' && $key != 'token' && $key != 'timeframe' && $key != 'from' && $key != 'to') {
                    $pfParamString .= $key . '=' . urlencode(trim($val)) . '&';
                }
            } else {
                if (!empty($val) && $key != 'api_action' && $key != 'submit' && $key != 'token' && $key != 'timeframe') {
                    $pfParamString .= $key . '=' . urlencode(trim($val)) . '&';
                }
            }
        }

        if ($pfData['api_action'] == 'history') {
            if ($pfData['timeframe'] == 'custom timeframe') {
                $pfParamString = 'format=csv&' . $pfParamString;
            }
        }

        $pfParamString = substr($pfParamString, 0, -1);
        $signature = md5($pfParamString);

        $action = '';
        if ($pfData['api_action']) {
            $action = $pfData['api_action'];
        }

        function setMethod($action)
        {
            switch ($action) {
                case 'fetch':
                case 'ping':
                case 'history':
                    return 'GET';
                    break;
                case 'pause':
                case 'unpause':
                case 'cancel':
                    return 'PUT';
                    break;
                case 'update':
                    return 'PATCH';
                    break;
                case 'adhoc':
                    return 'POST';
                    break;
                default:
                    break;
            }
        }
        $method = setMethod($action);

        $token = ($pfData['token'] ? $pfData['token'] . '/' : '');

        $payload = '';
        $exclude = array('api_action', 'submit', 'token', 'passphrase', 'version', 'merchant-id', 'timestamp', 'from', 'to', 'timeframe');
        foreach ($pfData as $key => $val) {
            if (!empty($val) && !in_array($key, $exclude)) {
                $payload .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }

        $payload = substr($payload, 0, -1);
        $timeframe = $pfData['timeframe'];
        $from = $pfData['from'];
        $to = $pfData['to'];

        if ($action != 'ping' & $action != 'history') {
            $ch = curl_init('https://api.payfast.co.za/subscriptions/' . $token . $action);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'version: ' . $pfData['version'],
                'merchant-id: ' . $pfData['merchant-id'],
                'signature: ' . $signature,
                'timestamp: ' . $timestamp,
            ));
        } elseif ($action == 'history') {
            if ($timeframe != 'custom timeframe') {
                $ch = curl_init('https://api.payfast.co.za/transactions/' . $action . '/' . $timeframe);
            } else {
                $ch = curl_init('https://api.payfast.co.za/transactions/' . $action . '&from=' . $from . '&to=' . $to . '&format=csv');
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'version: ' . $pfData['version'],
                'merchant-id: ' . $pfData['merchant-id'],
                'timestamp: ' . $timestamp,
                'signature: ' . $signature,
            ));
        } else {
            $ch = curl_init('https://api.payfast.co.za/' . $action);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'version: ' . $pfData['version'],
                'merchant-id: ' . $pfData['merchant-id'],
                'signature: ' . $signature,
                'timestamp: ' . $timestamp,
            ));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        $decoded_response = json_decode($response, true);

        if ($decoded_response['code'] == 200 && $decoded_response['status'] == "success") {
            $query = "DELETE FROM subscriptions WHERE id='$subscription_id'";
            $db->query($query);

            header('Location: ' . $link_prefix . '/account');
        } else {
            $error_message = "Unexpected error on payment gateway. Please try again later!";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Cancel your monthly subscription.">
    <meta name="keywords" content="artmusic tv, cancel">
    <meta name="author" content="Dominic Mostert">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtMusic TV - Subscription Cancelation</title>
    <link rel="icon" href="<?php echo $link_prefix; ?>/media/images/Icon.png">
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../styles/bootstrap.min.css">
    <link rel="stylesheet" href="../../styles/master.css">
</head>
<body class="container-fluid p-0 m-0">

    <!-- Import Navigation Bar -->
	<?php include "../../server/includes/navigation.php"; ?>

    <div class="mt-5 pt-5"><!-- Spacer --></div>

    <div class="row mx-0 p-0 my-5">
        <div class="col-md-4"><!-- Spacer --></div>
        <div class="col-md-4">
            <h2 class="text-center">Are you sure?</h2>

            <p class="text-center mb-5">
                If you cancel your subscription to <?php echo $theatre['Title']; ?>, you will no longer be able to 
                enjoy full access to available videos. If you cancel please be patient
                and do not close the page until you have be redirected to the 
                ArtMusic TV Dashboard.
            </p>

            <a class="btn btn-block btn-danger" href="./cancel.php?sid=<?php echo $subscription_id; ?>&cancel=1">Cancel Subscription</a>
            <a class="btn btn-block btn-primary" href="<?php echo $link_prefix; ?>/account/">Back to Account Settings</a>
        </div>
        <div class="col-md-4"><!-- Spacer --></div>
    </div>

    <!-- Import Footer -->
    <?php include "../../server/includes/footer.php"; ?>

    <!-- JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="../../scripts/bootstrap.js"></script>
</body>
</html>
