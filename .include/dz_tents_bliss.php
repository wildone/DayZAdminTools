<?php
	
	// Written by Killzone_Kid
	// http://killzonekid.com
	//
	// @Modified-By:   Gate
	// @Modified-Date: 2012/10/23
	
	$cache_directory = '.cache/';
	$cache_file_tents_bliss = $cache_directory .'dz_db_cache_tents_bliss';
	
	$now = time();
	
	if (!file_exists($cache_file_tents_bliss)){
		$already_old = $now - $update_interval - 10;
		touch($cache_file_tents_bliss, $already_old);
	}
	
	// if cache is older than set interval
	if (true || ($now-filemtime($cache_file_tents_bliss)) > $update_interval) {	
		touch($cache_file_tents_bliss);
		
		//start db query
		//$filter_server_instance = ($server_instance != '')?"AND id.instance = '$server_instance'\n":"";
		$filter_server_instance = '';	
		$query = <<<END
			SELECT
				instance_deployable.unique_id,
				instance_deployable.worldspace,
				instance_deployable.last_updated,
				instance_deployable.inventory,
				profile.name as owner_name,
				profile.unique_id as owner_unique_id
			FROM
				instance_deployable
				LEFT JOIN deployable ON deployable.id = instance_deployable.deployable_id
				LEFT JOIN survivor   ON instance_deployable.owner_id = survivor.id
				LEFT JOIN profile    ON survivor.unique_id = profile.unique_id
			WHERE
				instance_deployable.deployable_id = 1 $filter_server_instance
			ORDER BY instance_deployable.id ASC
		END;
	
		if (!$link = mysql_connect($DB_hostname, $DB_username, $DB_password)) {
			echo "</script>\n<br><span style=\"color:#ffff00;font-weight:bold;\">MySQL ERROR: ".mysql_error()."\n</span><script>";
		}
		else {
			mysql_select_db($DB_database, $link);
				 
			if (!$result = mysql_query($query)){
				echo "</script>\n<br><span style=\"color:#ffff00;font-weight:bold;\">MySQL ERROR: ".mysql_error()."\n</span><script>";
			}
			else {
				$all_UIDs = array();
				$DB_return_str = "t_up = $now;\n";
				
				while ($row = mysql_fetch_array($result)) {
					$unique_id    = $row['unique_id'];
					$owner_id     = $row['owner_id'];
					$owner_name   = htmlentities(addslashes(utf8_decode($row['owner_name'])));
					$worldspace   = preg_replace('/\\|/',',', $row['worldspace']);
					$inventory    = preg_replace('/\\|/',',', $row['inventory']);
					$last_updated = $row['last_updated'];
					$timestamp    = strtotime($last_updated);
					$last_updated = date("Y-m-d H:i:s", ($timestamp - ($server_time_offset * 60)));
		
					if (!in_array($unique_id, $all_UIDs)) {
						$DB_return_str .= "dbData['$unique_id'] = ['$worldspace','$last_updated','$timestamp','$owner_id','$owner_name','$inventory'];\n";
						array_push($all_UIDs, $unique_id);
					}
				}
					
				$DB_return_str .= "document.getElementById('tents').value += ' (".count($all_UIDs).")';\n";
				$DB_return_str .= "server_query = '';\n";
				$DB_return_str .= "readyToUpdateIn($update_interval-(t_now - t_up));\n";
			}
				
			mysql_close($link);
				
			file_put_contents ($cache_file_tents_bliss, $DB_return_str);
		}
	} 
	
	include $cache_file_tents_bliss;
	
?>