<?php

/**************************************************

	Written by Killzone_Kid
	http://killzonekid.com
	
	@Modified-By:   Gate
	@Modified-Date: 2012/10/23

**************************************************/
	
	$cache_directory = '.cache/';
	$cache_file_players_bliss = $cache_directory .'dz_db_cache_players_bliss';
	$now = time();
	
	if (!file_exists($cache_file_players_bliss)) {
		$already_old = ($now - $update_interval - 10);
		touch($cache_file_players_bliss, $already_old);
	}
	
	// check to see if cache is out of date
	if (true || ($now - filemtime($cache_file_players_bliss)) > $update_interval) {	
		touch($cache_file_players_bliss);
		
		$db_pull_limit = $DB_max_query_players_results;
		$GQ_return_str = "<span class=\"gameq\"><b>Querying maximum of $DB_max_query_players_results records</b></span>";
		
		$query = "
SELECT
	survivor.unique_id,
	survivor.worldspace,
	survivor.is_dead,
	survivor.last_updated,
	survivor.medical,
	survivor.inventory,
	survivor.backpack,
	survivor.model,
	survivor.state,
	profile.name,
	profile.humanity

FROM
	(survivor
		LEFT JOIN profile ON
			(survivor.unique_id = profile.unique_id))

WHERE
	survivor.is_dead <> 1
	AND
	survivor.worldspace <> '[]'
	AND
	survivor.last_updated > (now() - interval $last_update_cutoff_min minute)

ORDER BY
	survivor.last_updated
	DESC
	
LIMIT
	$DB_max_query_players_results;

";
	
		if (!$link = mysql_connect($DB_hostname, $DB_username, $DB_password)) {
			echo "</script>\n<br><span style=\"color:#ffff00;font-weight:bold;\">MySQL ERROR: ".mysql_error()."\n</span><script>";
		}
		else {
			mysql_select_db($DB_database, $link);
				 
			if (!$result = mysql_query($query)){
				echo "</script>\n<br><span style=\"color:#ffff00;font-weight:bold;\">MySQL ERROR: ".mysql_error()."\n</span><script>";
			}
			else {
				$DB_return_str = "t_up = $now;\n";
				$all_UIDs = array();
				$lastupdated = false;
					
				while ($row = mysql_fetch_array($result)) {
					$unique_id		= $row['unique_id'];
					$name			= htmlentities(addslashes(utf8_decode($row['name'])));
					$worldspace		= $row['worldspace'];
					$backpack		= preg_replace('/\\|/', ',', $row['backpack']);
					$inventory		= preg_replace('/\\|/', ',', $row['inventory']);
					$medical		= preg_replace('/\\|/', ',', $row['medical']);
					$medical		= preg_replace('/,any,/', ',[],', $row['medical']);
					$state			= preg_replace('/\\|/', ',', $row['state']);
					$humanity		= $row['humanity'];
					$is_dead		= ($row['is_dead'] != 0) ? true : false;
					$timestamp		= strtotime($row['last_updated']);
					$last_update	= date("Y-m-d H:i:s", ($timestamp - ($server_time_offset * 60)));
					$model			= preg_replace(array('/\'/','/\"/'), '', $row['model']);
	                         	
					// Gate - Human readable position, corrected for Z being inverted. 				
					$pos_text = '? x ?';
					$regexp = '/\[.+,\[(.+),(.+),.+\]\]/U';
					if (preg_match($regexp, $worldspace, $matches) !== false) {
						$pos_text = number_format($matches[1] / 100, 2) .' x '. number_format(153 - ($matches[2] / 100), 2);
					}
					
					if (!$lastupdated) {
						$DB_return_str .= "last_updated = $timestamp;\n";
						$lastupdated = true;
					}
							
					if (!in_array($unique_id, $all_UIDs)) {
						$DB_return_str .= "dbData['$unique_id'] = ['$name','$worldspace','$is_dead','$last_update','$timestamp','$medical','$humanity','$inventory','$backpack','$model','$state','$pos_text'];\n";
						array_push($all_UIDs, $unique_id);
					}
				}
					
				$DB_return_str .= "server_query = '". addslashes($GQ_return_str)."';\n";
				$DB_return_str .= "readyToUpdateIn($update_interval - (t_now - t_up));\n";
			}
				
			mysql_close($link);
				
			file_put_contents($cache_file_players_bliss, $DB_return_str);
		}
	}
	
	include $cache_file_players_bliss;

?>