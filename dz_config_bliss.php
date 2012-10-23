<?php
// Written by Killzone_Kid
// http://killzonekid.com

//server details
//To get correct external IP address for your server go to http://arma2.swec.se/server/list
//search fo your server and copy ipaddress:port fom there
$server_ip = '127.0.0.1'; //change as nescessary
$server_port = '2302'; //change as nescessary

//if you run multiple servers make a separate ToolZ install for each one
//in each separate install indicate server instance
//$server_instance = '1'; for example
//if you dont run multiple servers, leave it blank
$server_instance = ''; //leave it unchanged this is not fully functional yet

//server map
//currently 2 values accepted
//$server_map = 'Chernarus';
//$server_map = 'Lingor';
$server_map = 'Chernarus'; //change as nescessary

//time offset minutes between server/database time and time displayed
//if your server is set 1 hour behind from your local time $server_time_offset = -60;
//if your server is set 1 hour ahead from your local time $server_time_offset = 60;
$server_time_offset = 0;

//limit access to database to not more than once every 30 seconds
$update_interval = 30;

//Right clicked boxes hide for 10 seconds
$box_hide_delay = 10;

//do not display players on map if their last update was more than 5 minutes ago (probably logged off)
$last_update_cutoff_min = 5;

//db connect details
$DB_hostname = '127.0.0.1:3306'; //change as nescessary
$DB_username = 'dayz'; //change as nescessary
$DB_password = 'dayz'; //change as nescessary
$DB_database = 'dayz'; //change as nescessary
$DB_max_query_players_results = 100;

//GameQ is to determine actual player count on the server to better shape DB query
//Set $GQ = true; if you want to use it
$GQ = false; //change as nescessary
$GQ_path = './GameQ/GameQ.php';

?>