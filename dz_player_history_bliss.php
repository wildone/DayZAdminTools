<?php

// Written by Killzone_Kid
// http://killzonekid.com
//
// @Modified-By:   Gate
// @Modified-Date: 2012/10/23

include "dz_config_bliss.php";
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Killzone_Kid's Arma II DayZ Player History (Bliss <?php echo $server_map;?>)</title>
<link rel="stylesheet" href="style.css" type="text/css">
</head>

<body>
<input id="back" type="button" onclick="window.history.back();" value="< Back"><br><br>

<?php
$update_interval = 5;
if (isset($_GET['guid']) && preg_match ('/^\w+$/',$_GET['guid'])){

	$guid = $_GET['guid'];

} else {

	echo "Invalid GUID</body></html>";
	exit;
}

$cache_file_player_history_bliss = 'dz_db_cache_player_history_bliss';

$now = time();

if (!file_exists($cache_file_player_history_bliss)){

	$already_old = $now - $update_interval - 10;
	touch($cache_file_player_history_bliss, $already_old);
}

// if cache is older than set interval
if (($now-filemtime($cache_file_player_history_bliss)) > $update_interval){	

	touch($cache_file_player_history_bliss);
	
//start db query			
$query = <<<END

SELECT
	survivor.last_updated AS updated,
	survivor.is_dead AS dead,
	'' AS login,
	'' AS start

FROM
	survivor

WHERE
	survivor.unique_id = '$guid'
	
	UNION ALL
		SELECT log_entry.created, '', log_entry.log_code_id, ''
	FROM
		log_entry
	WHERE
		log_entry.unique_id = '$guid'
		
	UNION ALL
		SELECT survivor.start_time, '', '', '1'
	FROM
		survivor
	WHERE
		survivor.unique_id = '$guid'
		
ORDER BY updated DESC

END;

	if (!$link = mysql_connect($DB_hostname, $DB_username, $DB_password)){

		mySqlError();
			
	} else {

		mysql_select_db($DB_database, $link);
			 
		if (!$result = mysql_query($query)){
			
			mySqlError();
			 
		} else {
		
			$output = "<b>Player history for GUID [$guid]:</b><br><br>";
			$previous_time = '';
			
			while($row = mysql_fetch_array($result)){
				
				$interval = 0;
				$updated = $row['updated'];
				
				switch ($row['dead']) {
					case '0':
						$dead = '<b>STILL ALIVE</b>';
						break;
					case '1':
						$dead = 'PRONOUNCED DEAD';
						break;
					default:
						$dead = $row['dead'];
				}

				switch ($row['login']) {
					case '1':
						$login = 'LOGGED IN';
						break;
					case '2':
						$login = 'LOGGED OUT';
						break;
					case '3':
						$login = 'BANNED';
						break;
					case '4':
						$login = 'CONNECTED';
						break;
					case '5':
						$login = 'DISCONNECTED';
						break;
					default:
						$login = $row['login'];
				}
				
				switch ($row['start']) {
					case '1':
						$start = 'NEW CHARACTER CREATED';
						break;
					default:
						$start = $row['start'];
				}
				
				$unix_time = strtotime($updated);
				if ($previous_time != ''){
					$interval = $previous_time - $unix_time;
				}
				
				$height = round($interval/300);
				$output .= "<div style=\"padding-top:".$height."px;\">$updated - $dead$login$start</div>";
				$previous_time = $unix_time;
			}
		}
			
		mysql_close($link);
		
		echo $output;
	}
	
} else {

	echo "Wait at least $update_interval seconds between the requests<br><br>";

} 

function mySqlError(){

	echo "MySQL ERROR: ".mysql_error()."<br>";
	
}

?>
</body>
</html>



