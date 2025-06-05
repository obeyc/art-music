<?php

	date_default_timezone_set('Africa/Johannesburg');
	session_start();
ini_set('display_errors','1');
	/* Database Connection */

	$development_mode = false; // TODO: change to FALSE on Live

	if ($development_mode === true) {
		$servname = "localhost";
		$username = "root";
		$password = "";
		$database = "artmusic";

		$link_prefix = "/artmusic";
	} else {
		if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
			$location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . $location);
			exit;
		}

		$servname = "localhost";
		$username = "artmusic_dominic";
		$password = "q?!KFh,nahkt";
		$database = "artmusic_db";

		$link_prefix = "";
	}

	$db = new mysqli($servname, $username, $password, $database);

	if ($db->connect_error) {
		die("Database Connection Failed: " . $db->connect_error);
	}

	$db->set_charset("utf8");
	ini_set('default_charset','utf-8');
    
	/* User Authentication */

	$user_id = 0;

	if (isset($_SESSION["user_id"])) {
		$user_id = $_SESSION["user_id"];
		$user_access = "viewer";

		$query = "SELECT * FROM adminaccess WHERE UserID='$user_id'";
		$result = $db->query($query);

		if ($result->num_rows === 1) {
			$user_access = "admin";
		} else {
			$query = "SELECT * FROM theatreaccess WHERE UserID='$user_id'";
			$result = $db->query($query);

			if ($result->num_rows === 1) {
				$result_array = $result->fetch_assoc();

				$user_access = "theatre";
				$user_theatre = $result_array['TheatreID'];
			}
		}
	} else {
		if (isset($_SESSION['user_session_id'])) {
			$user_session_id = $_SESSION['user_session_id'];
	
			$query = "SELECT * FROM users WHERE SessionID='$user_session_id'";
			$result = $db->query($query);
	
			if ($result->num_rows === 1) {
				$result_array = $result->fetch_assoc();

				$user_id = $result_array['id'];
				$user_access = "viewer";

				$query = "SELECT * FROM adminaccess WHERE UserID='$user_id'";
				$result = $db->query($query);

				if ($result->num_rows === 1) {
					$user_access = "admin";
				} else {
					$query = "SELECT * FROM theatreaccess WHERE UserID='$user_id'";
					$result = $db->query($query);

					if ($result->num_rows === 1) {
						$result_array = $result->fetch_assoc();

						$user_access = "theatre";
						$user_theatre = $result_array['TheatreID'];
					}
				}

				$_SESSION["user_id"] = $user_id;
			} else {
				//setcookie("user_session_id", "", time() - 3600, "/");
				unset($_SESSION['user_session_id']);
			}
		}
	}	

	// echo "SELECT * FROM theatreaccess WHERE UserID='$user_id'";

	// echo "<br>user theatre:".$user_theatre;

	// echo "<br>user access:".$user_access;

	/* Maintenance Mode */

	$query = "SELECT * FROM settings WHERE Name='maintenance'";
    $result = $db->query($query);

    if ($result->num_rows === 1) {
      	$result_array = $result->fetch_assoc();
		$maintenance_mode = $result_array['Value'];

		if ($maintenance_mode === 'on' && $user_access !== 'admin') {
			header('Location: ' . $link_prefix . '/maintenance');
		}
	}

	/* Global Functions */

	function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$dif = $now->diff($ago);

		$dif->w = floor($dif->d / 7);
		$dif->d -= $dif->w * 7;

		$string = array(
			'y' => 'Year',
			'm' => 'Month',
			'w' => 'Week',
			'd' => 'Day',
			'h' => 'Hour',
			'i' => 'Minute',
			's' => 'Second',
		);

		foreach ($string as $k => &$v) {
			if ($dif->$k) {
				$v = $dif->$k . ' ' . $v . ($dif->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) {
			$string = array_slice($string, 0, 1);
		}
		
		return $string ? implode(', ', $string) . ' Ago' : 'Just Now';
	}

	function file_name ($video_title) {
		return str_replace(' ', '-', strtolower($video_title));
	}
	
	function remove_special_char($str) { 
        $res = str_replace( array( '\'', '"', ',' , ';', '<', '>' ), ' ', $str); 
       
        return $res; 
    } 

?>