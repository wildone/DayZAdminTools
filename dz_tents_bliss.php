<?php
// Written by Killzone_Kid
// http://killzonekid.com

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

SELECT objects.otype, objects.oid, objects.uid, objects.pos, objects.lastupdate, objects.inventory, profile.id, profile.name, profile.unique_id AS guid
FROM objects
LEFT JOIN profile
ON objects.oid = profile.id
WHERE objects.otype = 'TentStorage'
$filter_server_instance
ORDER BY objects.lastupdate DESC

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