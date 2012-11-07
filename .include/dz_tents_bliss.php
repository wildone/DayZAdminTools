<?php

/**************************************************

	Written by Killzone_Kid
	http://killzonekid.com
	
	@Modified-By:   Gate
	@Modified-Date: 2012/10/23

**************************************************/
	
	$cache_directory = '.cache/';
	$cache_file = $cache_directory .'dz_db_cache_tents_bliss';
	
	$now = time();
	
	if (!file_exists($cache_file)){
	
		$already_old = $now - $update_interval - 10;
		touch($cache_file, $already_old);
	}
	
	// if cache is older than set interval
	if ($force_update_cache || ($now - filemtime($cache_file)) > $update_interval) {	
		touch($cache_file);
		
		//start db query
		$filter_server_instance = ($server_instance != '')?"AND instance_deployable.instance_id = '$server_instance'\n":"";	
		$query = "
SELECT
	instance_deployable.owner_id AS oid,
	instance_deployable.unique_id AS uid,
	instance_deployable.worldspace AS pos,
	instance_deployable.last_updated AS lastupdate,
	instance_deployable.inventory,
	profile.id,
	profile.name,
	profile.unique_id AS guid

FROM
	instance_deployable
		LEFT JOIN survivor ON instance_deployable.owner_id = survivor.id
		LEFT JOIN profile ON survivor.unique_id = profile.unique_id

WHERE
	instance_deployable.deployable_id = 1
	$filter_server_instance

ORDER BY
	instance_deployable.last_updated DESC
		";
	
		if (!$link = mysql_connect($DB_hostname, $DB_username, $DB_password)) {
			echo "</script>\n<br><span style=\"color:#ffff00;font-weight:bold;\">MySQL ERROR: ".mysql_error()."\n</span><script>";
		}
		else {
			mysql_select_db($DB_database, $link);
				 
			if (!$result = mysql_query($query)) {
				echo "</script>\n<br><span style=\"color:#ffff00;font-weight:bold;\">MySQL ERROR: ".mysql_error()."\n</span><script>";
			}
			else {
				$all_UIDs = array();
				
				while($row = mysql_fetch_array($result)) {
					$oid = $row['oid'];
					$uid = $row['uid'];
					$guid = $row['guid'];
					$otype = 'TentStorage';
					$pos = preg_replace('/\\|/',',', $row['pos']);
					$inventory  = preg_replace('/\\|/',',', $row['inventory']);
					$lastupdate = $row['lastupdate'];
					$timestamp = strtotime($lastupdate);
					$actual_time = $timestamp - ($server_time_offset*60);
					$lastupdate = date("Y-m-d H:i:s",$actual_time);
					$name = addslashes(utf8_decode($row['name']));
					$name = preg_replace('/</','&lt;',$name);
					$name = preg_replace('/>/','&gt;',$name);
		
					if (!in_array($uid, $all_UIDs)) {
						$DB_return_str .= "dbData['$uid'] = ['$otype','$pos','$name','$lastupdate','$timestamp','$inventory','$guid','$oid'];\n";
						array_push($all_UIDs, $uid);
					}
				}
			}
				
			mysql_close($link);
				
			file_put_contents($cache_file, $DB_return_str);
		}
	} 

	include $cache_file;

?>