<?php

/**************************************************

	Written by Killzone_Kid
	http://killzonekid.com
	
	@Modified-By:   Gate
	@Modified-Date: 2012/10/23

**************************************************/

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
			
			if (isset($_GET['guid']) && preg_match ('/^\w+$/',$_GET['guid'])) {
				$guid = $_GET['guid'];
			}
			else {
				echo "Invalid GUID</body></html>";
				exit;
			}
		
			$cache_file = '../'. $cache_directory .'player_history.js';
			$now = time();
			
			if (!file_exists($cache_file)) {
				$already_old = $now - $update_interval - 10;
				touch($cache_file, $already_old);
			}
			
			// if cache is older than set interval
			if (($now - filemtime($cache_file)) > $update_interval) {	
				touch($cache_file);
				
				//start db query			
				$query = "
			
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
				";
			
				if (!$link = mysql_connect($DB_hostname, $DB_username, $DB_password)){
					mySqlError();
				}
				else {
					mysql_select_db($DB_database, $link);
						 
					if (!$result = mysql_query($query)) {
						mySqlError();
					}
					else {
						$output = "<b>Player history for GUID [$guid]:</b><br><br>";
						$previous_time = '';
						
						while ($row = mysql_fetch_array($result)) {
							$interval = 0;
							$updated = $row['updated'];
							
							switch ($row['dead']) {
								case '0':
									$dead = '<b>STILL ALIVE</b>';
									break;
								case '1':
									$dead = '<span style="color:red;text-decoration:line-through"><span style="color:black;font-weight:bold;">PRONOUNCED DEAD</span></span>';
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
									$start = '<span style="color:blue;font-weight:bold;">NEW CHARACTER CREATED</span>';
									break;
								default:
									$start = $row['start'];
							}
							
							$unix_time = strtotime($updated);

							if ($previous_time != '') {
								$interval = $previous_time - $unix_time;
							}
							
							$height = round($interval / 1000);
							$output .= "<div style=\"font-size:8pt;padding-top:".$height."px;\">$updated - $dead$login$start</div>";
							$previous_time = $unix_time;
						}
					}
						
					mysql_close($link);
					
					echo $output;
				}
			}
			else {
				echo "Wait at least $update_interval seconds between the requests<br><br>";
			} 
			
			function mySqlError(){
				echo "MySQL ERROR: ".mysql_error()."<br>";
			}
		
		?>
	</body>
</html>