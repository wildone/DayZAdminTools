<?php

// Written by Killzone_Kid
// http://killzonekid.com
//
// @Modified-By:   Gate
// @Modified-Date: 2012/10/23

$cache_file_players_bliss = 'dz_db_cache_players_bliss';
$now = time();

if (!file_exists($cache_file_players_bliss)) {
	$already_old = ($now - $update_interval - 10);
	
	touch($cache_file_players_bliss, $already_old);
}

// if cache is older than set interval
if (($now - filemtime($cache_file_players_bliss)) > $update_interval) {	
	touch($cache_file_players_bliss);
	
	$db_pull_limit = $DB_max_query_players_results;
	$GQ_return_str = "<span class=\"gameq\"><b>Querying $DB_max_query_players_results records</b></span>";
	
	if ($GQ) {
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
		
		if ($results[$GQ_id]['gq_online'] && count($results[$GQ_id]['numplayers']) > 0) {
			$player_names = array();
			$players_total = count($results[$GQ_id]['players']);
			
			for ($i = 0; $i < $players_total; $i++) {
				array_push($player_names, $results[$GQ_id]['players'][$i]['player']);
			}
			
			$GQ_return_str = implode(', ', $player_names).'.';
			$GQ_return_str = preg_replace('/</','&lt;',$GQ_return_str);
			$GQ_return_str = preg_replace('/>/','&gt;',$GQ_return_str);
			$GQ_return_str = '<span class="gameq"><b>Players on the server ['.$results[$GQ_id]['gq_numplayers'].'/'.$results[$GQ_id]['gq_maxplayers'].']:</b> '.$GQ_return_str.'</span>';

			$db_pull_limit = $players_total;
		} 
	}
	
/* Query built/tested using MySQL Workbench v5.2
   Gate 2012/10/21 */

$query = <<<END

SELECT
	survivor.unique_id      AS uid,
	survivor.worldspace     AS pos,
	survivor.is_dead        AS death,
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
	survivor.last_updated > now() - interval 5 minute

ORDER BY
	survivor.last_updated
	DESC
	
LIMIT
	$db_pull_limit

END;

	if (!$link = mysql_connect($DB_hostname, $DB_username, $DB_password)) {
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
			$lastupdated = false;
				
			while ($row = mysql_fetch_array($result)) {
				$uid         = $row['uid'];
				$name        = addslashes(utf8_decode($row['name']));
				$name        = preg_replace('/</','&lt;',$name);
				$name        = preg_replace('/>/','&gt;',$name);
				$pos         = $row['pos'];
		
				$backpack    = preg_replace('/\\|/', ',', $row['backpack']);
				$inventory   = preg_replace('/\\|/', ',', $row['inventory']);
				$medical     = preg_replace('/\\|/', ',', $row['medical']);
				$medical     = preg_replace('/,any,/', ',[],', $row['medical']);
				$state       = preg_replace('/\\|/', ',', $row['state']);
				
				$humanity    = $row['humanity'];
				$death       = $row['death'];
				$lastupdate  = $row['last_updated'];
				$timestamp   = strtotime($lastupdate);
				$actual_time = $timestamp - ($server_time_offset*60);
				$lastupdate  = date("Y-m-d H:i:s", $actual_time);
				$model       = $row['model'];
				$model       = preg_replace('/\'/','',$model);
				$model       = preg_replace('/\"/','',$model);

				// Gate - Human readable position, corrected for Z being inverted. 				
				$pos_text = '? x ?';
				$regexp = '/\[.+,\[(.+),(.+),.+\]\]/U';
				if (preg_match($regexp, $pos, $matches) !== false) {
					$pos_text = number_format($matches[1] / 100, 2) .' x '. number_format(153 - ($matches[2] / 100), 2);
				}
				
				if (!$lastupdated) {
					$DB_return_str .= "last_updated = $timestamp;\n";
					$lastupdated = true;
				}
						
				if (!in_array($uid, $all_UIDs)) {
					$DB_return_str .= "dbData['$uid'] = ['$name','$pos','$death','$lastupdate','$timestamp','$medical','$humanity','$inventory','$backpack','$model','$state','$pos_text'];\n";
					array_push($all_UIDs, $uid);
				}
			}
				
			$DB_return_str .= "server_query = '".addslashes($GQ_return_str)."';\n";
			$DB_return_str .= "readyToUpdateIn($update_interval-(t_now - t_up));\n";
		}
			
		mysql_close($link);
			
		file_put_contents($cache_file_players_bliss, $DB_return_str);
	}
} 

function mySqlError() {
	echo "</script>\n<br><span style=\"color:#ffff00;font-weight:bold;\">MySQL ERROR: ".mysql_error()."\n</span><script>";
}

include $cache_file_players_bliss;

?>