<?php
	
/**************************************************

	Written by Killzone_Kid
	http://killzonekid.com
	
	@Modified-By:   Gate
	@Modified-Date: 2012/10/23

**************************************************/
	
	$server_ip = '127.0.0.1';								// IP of your server
	$server_port = '2302';									// Port your server runs on
	$server_instance = 1;									// the instance of the server you want to monitor
	$server_map = 'Chernarus';								// currently 2 values accepted 'Chernarus, Lingor'
	$server_time_offset = 0;								// time offset minutes between server/database time and time displayed
	$update_interval = 30;									// limit access to database to not more than once every 30 seconds
	$box_hide_delay = 10;									// Right clicked boxes hide for 10 seconds
	$last_update_cutoff_min = 5;							// do not display players on map if their last update was more than 5 minutes ago (probably logged off)
	
	$DB_hostname = '127.0.0.1:3306';						// Server:Port of your database
	$DB_username = 'dayz';									// Username
	$DB_password = 'dayz';									// Password
	$DB_database = 'dayz';									// Database Name
	$DB_max_query_players_results = 100;					// Maximum number of results to ask for
	
	$force_update_cache = true;								// override caching, and always call the database (for testing purpose only!)
	
	$cache_directory = '.cache/';							// directory for cache files to go,  requires read/write access
?>