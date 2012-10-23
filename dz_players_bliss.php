<?php
// Written by Killzone_Kid
// http://killzonekid.com

$cache_file_players_bliss = 'dz_db_cache_players_bliss';

$now = time();

if (!file_exists($cache_file_players_bliss)){

	$already_old = $now - $update_interval - 10;
	touch($cache_file_players_bliss, $already_old);
}

// if cache is older than set interval
if (($now-filemtime($cache_file_players_bliss)) > $update_interval){	

	touch($cache_file_players_bliss);
	$db_pull_limit = $DB_max_query_players_results;
	$GQ_return_str = "<span class=\"gameq\"><b>Querying $DB_max_query_players_results records</b></span>";
	
	if ($GQ){
	
		$GQ_return_str = '<span class="gameq"><b>The server is empty or OFFLINE</b></span>';
		
		//get player number from the server
		require $GQ_path;
		
		$GQ_id = 'DayZ';
		$GQ_type = 'armedassault2oa';
		$GQ_host = $server_ip.':'.$server_port;
		
		$servers = array(
			
			array(
				
				'id' => $GQ_id,
				'type' => $GQ_type,
				'host' => $GQ_host
			)
		);
		$gq = new GameQ();
		$gq->addServers($servers);
		$gq->setOption('timeout', 5);
		$gq->setFilter('normalise');
		$results = $gq->requestData();
			
		//debug
		//echo var_dump($results);
		
		if ($results[$GQ_id]['gq_online'] && count($results[$GQ_id]['numplayers']) > 0){
		
			$player_names = array();
			$players_total = count($results[$GQ_id]['players']);
			
			for ($i=0; $i<$players_total; $i++){

				array_push($player_names, $results[$GQ_id]['players'][$i]['player']);
			}
			
			$GQ_return_str = implode(', ', $player_names).'.';
			$GQ_return_str = preg_replace('/</','&lt;',$GQ_return_str);
			$GQ_return_str = preg_replace('/>/','&gt;',$GQ_return_str);
			$GQ_return_str = '<span class="gameq"><b>Players on the server ['.$results[$GQ_id]['gq_numplayers'].'/'.$results[$GQ_id]['gq_maxplayers'].']:</b> '.$GQ_return_str.'</span>';

			$db_pull_limit = $players_total;
			
		} 
	}
	
//start db query		
$query = <<<END

SELECT s1.unique_id AS uid, s1.pos, s1.is_dead AS death, s1.last_update AS lastupdate, s1.medical, s1.inventory, s1.backpack, s1.model, s1.state, profile.humanity, profile.name
FROM survivor s1
LEFT JOIN profile ON s1.unique_id = profile.unique_id
LEFT JOIN survivor s2 ON s1.unique_id = s2.unique_id AND s1.last_update < s2.last_update
WHERE s2.last_update IS NULL
ORDER BY s1.last_update DESC
LIMIT $db_pull_limit

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
			$lastupdated = false;
				
			while($row = mysql_fetch_array($result)){
				
				$uid = $row['uid'];
				$name = addslashes(utf8_decode($row['name']));
				$name = preg_replace('/</','&lt;',$name);
				$name = preg_replace('/>/','&gt;',$name);
				$pos = preg_replace('/\\|/',',', $row['pos']);
				$backpack = preg_replace('/\\|/',',', $row['backpack']);
				$inventory  = preg_replace('/\\|/',',', $row['inventory']);
				$medical = preg_replace('/\\|/',',', $row['medical']);
				//bug work around
				$medical = preg_replace('/,any,/',',[],', $row['medical']);
				$state = preg_replace('/\\|/',',', $row['state']);
				$humanity = $row['humanity'];
				$death = $row['death'];
				$lastupdate = $row['lastupdate'];
				$timestamp = strtotime($lastupdate);
				$actual_time = $timestamp - ($server_time_offset*60);
				$lastupdate = date("Y-m-d H:i:s",$actual_time);
				$model = $row['model'];
				$model = preg_replace('/\'/','',$model);
				$model = preg_replace('/\"/','',$model);
				
				if(!$lastupdated){
						
					$DB_return_str .= "last_updated = $timestamp;\n";
					$lastupdated = true;
				}
						
				if (!in_array($uid, $all_UIDs)) {
						
					$DB_return_str .= "dbData['$uid'] = ['$name','$pos','$death','$lastupdate','$timestamp','$medical','$humanity','$inventory','$backpack','$model','$state'];\n";
					array_push($all_UIDs, $uid);
				}
			}
				
			$DB_return_str .= "server_query = '".addslashes($GQ_return_str)."';\n";
			$DB_return_str .= "readyToUpdateIn($update_interval-(t_now - t_up));\n";
		}
			
		mysql_close($link);
			
		file_put_contents ($cache_file_players_bliss, $DB_return_str);
	}
} 

function mySqlError(){

	echo "</script>\n<br><span style=\"color:#ffff00;font-weight:bold;\">MySQL ERROR: ".mysql_error()."\n</span><script>";
}

include $cache_file_players_bliss;

?>