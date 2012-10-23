<?php
// Written by Killzone_Kid
// http://killzonekid.com

include "dz_config_bliss.php";

echo 'Connecting to the database server...';
if (!$link = mysql_connect($DB_hostname, $DB_username, $DB_password)){

	echo "Failed<br>";
	echo "Error: ".mysql_error();
	exit;
	
} else {

	echo "OK<br>";
}

echo 'Selecting database...';
if (!mysql_select_db($DB_database, $link)){
	
	echo "Failed<br>";
	echo "Error: ".mysql_error();
	exit;

}  else {

	echo "OK<br>";
}
mysql_close($link);

if ($GQ) {

	echo "Connecting to the game server...<pre>";
	
	$GQ_id = 'DayZ';
	$GQ_type = 'armedassault2oa';
	$GQ_host = $server_ip.':'.$server_port;
	
	require $GQ_path;
	$servers = array(
			
		array(
				
			'id' => $GQ_id,
			'type' => $GQ_type,
			'host' => $GQ_host,
		)
	);
	
	$gq = new GameQ();
	$gq->addServers($servers);
	$gq->setOption('timeout', 5);
	$gq->setFilter('normalise');
	$results = $gq->requestData();

	echo var_dump($results)."</pre>";

} else {

	echo "GameQ is not used";
}
?>