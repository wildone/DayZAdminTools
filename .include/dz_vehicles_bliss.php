<?php
	
/**************************************************

	Written by Killzone_Kid
	http://killzonekid.com
	
	@Modified-By:   Gate
	@Modified-Date: 2012/10/23

**************************************************/
	
	$cache_directory = '.cache/';
	$cache_file_vehicles_bliss = $cache_directory .'dz_db_cache_vehicles_bliss';
	
	$now = time();
	
	if (!file_exists($cache_file_vehicles_bliss)){
		$already_old = $now - $update_interval - 10;
		touch($cache_file_vehicles_bliss, $already_old);
	}
	
	// if cache is older than set interval
	if (true || ($now-filemtime($cache_file_vehicles_bliss)) > $update_interval){	
		touch($cache_file_vehicles_bliss);
	
	//start db query
	$filter_server_instance = ($server_instance != '') ? "AND iv.instance_id = '$server_instance'\n":"";
			
	$query = <<<END
SELECT
	iv.id,
	iv.worldspace,
	iv.inventory,
	iv.fuel,
	iv.damage,
	iv.parts,
	iv.last_updated,
	wv.vehicle_id,
	v.class_name
FROM
	instance_vehicle AS iv
	JOIN world_vehicle AS wv ON (iv.world_vehicle_id = wv.id)
	JOIN vehicle AS v ON (wv.id = v.id)
WHERE iv.damage < 1.0 $filter_server_instance
ORDER BY iv.id ASC
END;
	
		if (!$link = mysql_connect($DB_hostname, $DB_username, $DB_password)){
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
				
				while($row = mysql_fetch_array($result)){
					$id				= $row['id'];
					$class_name		= $row['class_name'];
					
					$last_updated	= $row['last_updated'];
					$timestamp		= strtotime($last_updated);
					$last_updated	= date("Y-m-d H:i:s", ($timestamp - ($server_time_offset * 60)));
					
					$fuel			= $row['fuel'];
					$worldspace		= preg_replace('/\\|/',',', $row['worldspace']);
					$damage			= preg_replace('/\\|/',',', $row['damage']);
					$parts			= preg_replace('/\\|/',',', $row['parts']);
					$inventory		= preg_replace('/\\|/',',', $row['inventory']);
					
					// Gate - Human readable position, corrected for Z being inverted. 				
					$pos_text = '? x ?';
					$regexp = '/\[.+,\[(.+),(.+),.+\]\]/U';
					if (preg_match($regexp, $worldspace, $matches) !== false) {
						$pos_text = number_format($matches[1] / 100, 2) .' x '. number_format(153 - ($matches[2] / 100), 2);
					}
					
					if (!in_array($id, $all_UIDs)) {
						$DB_return_str .= "dbData['$id'] = ['$class_name','$worldspace','$damage','$last_updated','$timestamp','$fuel','$parts','$inventory','$pos_text'];\n";
						array_push($all_UIDs, $id);
					}
				}
					
				$DB_return_str .= "server_query = '';\n";
				$DB_return_str .= "readyToUpdateIn($update_interval-(t_now - t_up));\n";
			}
				
			mysql_close($link);
				
			file_put_contents ($cache_file_vehicles_bliss, $DB_return_str);
		}
	}

	include $cache_file_vehicles_bliss;
?>