<?php

// Written by Killzone_Kid
// http://killzonekid.com
//
// @Modified-By:   Gate
// @Modified-Date: 2012/10/23

$cache_file_tents_bliss = 'dz_db_cache_tents_bliss';

$now = time();

if (!file_exists($cache_file_tents_bliss)){
	$already_old = $now - $update_interval - 10;
	touch($cache_file_tents_bliss, $already_old);
}

// if cache is older than set interval
if (($now-filemtime($cache_file_tents_bliss)) > $update_interval){	
	touch($cache_file_tents_bliss);
	
//start db query
$filter_server_instance = ($server_instance != '')?"AND objects.instance = '$server_instance'\n":"";	
$query = <<<END

SELECT
	instance_deployable.id,
	instance_deployable.deployable_id as otype,
	instance_deployable.owner_id as oid,
	instance_deployable.unique_id as uid,
	instance_deployable.worldspace as pos,
	instance_deployable.last_updated as lastupdate,
	instance_deployable.inventory,
	deployable.class_name AS otype,
	survivor.id,
	survivor.unique_id,
	profile.id,
	profile.name,
	profile.unique_id AS guid
FROM
	instance_deployable
	LEFT JOIN deployable ON deployable.id = instance_deployable.deployable_id
	LEFT JOIN survivor ON instance_deployable.owner_id = survivor.id
	LEFT JOIN profile ON survivor.unique_id = profile.unique_id
WHERE
	instance_deployable.deployable_id = 1

$filter_server_instance
ORDER BY instance_deployable.last_updated DESC

END;

	if (!$link = mysql_connect($DB_hostname, $DB_username, $DB_password)){

		mySqlError();
			
	} else {

		mysql_select_db($DB_database, $link);
			 
		if (!$result = mysql_query($query)){
			
			mySqlError();
			 
		} else {
		
			$DB_return_str = "t_up = $now;\n";
			$all_UIDs = array();
			
			while($row = mysql_fetch_array($result)){
				
				$oid = $row['oid'];
				$uid = $row['uid'];
				$guid = $row['guid'];
				$otype = $row['otype'];
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
				
			$DB_return_str .= "document.getElementById('tents').value += ' (".count($all_UIDs).")';\n";
			$DB_return_str .= "server_query = '';\n";
			$DB_return_str .= "readyToUpdateIn($update_interval-(t_now - t_up));\n";
		}
			
		mysql_close($link);
			
		file_put_contents ($cache_file_tents_bliss, $DB_return_str);
	}
} 

function mySqlError(){

	echo "</script>\n<br><span style=\"color:#ffff00;font-weight:bold;\">MySQL ERROR: ".mysql_error()."\n</span><script>";
}

include $cache_file_tents_bliss;
?>