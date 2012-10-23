<?php

// Written by Killzone_Kid
// http://killzonekid.com
//
// @Modified-By:   Gate
// @Modified-Date: 2012/10/23

$cache_file_vehicles_bliss = 'dz_db_cache_vehicles_bliss';

$now = time();

if (!file_exists($cache_file_vehicles_bliss)){
	$already_old = $now - $update_interval - 10;
	touch($cache_file_vehicles_bliss, $already_old);
}

// if cache is older than set interval
if (($now-filemtime($cache_file_vehicles_bliss)) > $update_interval){	
	touch($cache_file_vehicles_bliss);

//start db query
$filter_server_instance = ($server_instance != '')?"AND objects.instance = '$server_instance'\n":"";		
$query = <<<END

SELECT
	iv.id, iv.vehicle_id as uid, iv.parts as health, iv.damage, iv.fuel, iv.worldspace as pos, iv.inventory, iv.last_updated as lastupdate, vehicle.class_name as otype
FROM
	(instance_vehicle iv
	LEFT JOIN `vehicle` ON (iv.vehicle_id = vehicle.id))
WHERE
	iv.damage < 1
$filter_server_instance
ORDER BY
	iv.last_updated DESC

END;

	if (!$link = mysql_connect($DB_hostname, $DB_username, $DB_password)){
		mySqlError();
	}
	else {
		mysql_select_db($DB_database, $link);
			 
		if (!$result = mysql_query($query)){
			mySqlError();
		}
		else {
			$DB_return_str = "t_up = $now;\n";
			$all_UIDs = array();
			
			while($row = mysql_fetch_array($result)){
				$uid			= $row['uid'];
				$otype			= $row['otype'];
				$lastupdate		= $row['lastupdate'];
				$timestamp		= strtotime($lastupdate);
				$actual_time	= $timestamp - ($server_time_offset*60);
				$lastupdate		= date("Y-m-d H:i:s",$actual_time);
				$fuel			= $row['fuel'];
				$pos			= preg_replace('/\\|/',',', $row['pos']);
				$damage			= preg_replace('/\\|/',',', $row['damage']);
				$health			= preg_replace('/\\|/',',', $row['health']);
				$inventory		= preg_replace('/\\|/',',', $row['inventory']);
				
				if (!in_array($uid, $all_UIDs)) {
					$DB_return_str .= "dbData['$uid'] = ['$otype','$pos','$damage','$lastupdate','$timestamp','$fuel','$health','$inventory'];\n";
					array_push($all_UIDs, $uid);
				}
			}
				
			//$DB_return_str .= "document.getElementById('vehicles').value += ' (".count($all_UIDs).")';\n";
			$DB_return_str .= "server_query = '';\n";
			$DB_return_str .= "readyToUpdateIn($update_interval-(t_now - t_up));\n";
		}
			
		mysql_close($link);
			
		file_put_contents ($cache_file_vehicles_bliss, $DB_return_str);
	}
}

function mySqlError(){

	echo "</script>\n<br><span style=\"color:#ffff00;font-weight:bold;\">MySQL ERROR: ".mysql_error()."\n</span><script>";
}

include $cache_file_vehicles_bliss;
?>