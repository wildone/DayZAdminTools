<?php

	/**************************************************
	
	 Written by Killzone_Kid
	 http://killzonekid.com
	
	 @Modified-By:   Gate
	 @Modified-Date: 2012/10/23
	
	**************************************************/

	include ".include/dz_config_bliss.php";
	
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Killzone_Kid's Arma II DayZ Tents (Bliss <?php echo $server_map; ?>)</title>
		<link rel="stylesheet" href="style.css" type="text/css">
		<script type="text/javascript" src=".include/functions.js"></script>
	</head>
	<body>
		| <input id="index_players" type="button" onclick="document.location.href='dayz_admin_tool_players_bliss.php';" value="View Players"> | <input id="index_players" type="button" onclick="document.location.href='dayz_admin_tool_vehicles_bliss.php';" value="View Vehicles"> | <input id="index_players" type="button" onclick="document.location.href='dayz_admin_tool_tents_bliss.php';" value="View Tents"> | <input id="index_players" type="button" onclick="document.location.href='dayz_admin_tool_objects_bliss.php';" value="View Objects"> | Killzone_Kid's Arma II DayZ Admin Tools for Bliss build 4.1
		<hr />
		<input id="tents" type="button" onclick="document.location.href='?opt=0';" value="Tents"><input type="button" onclick="document.location.href='?opt=1';" value="Inventory"><input type="button" onclick="document.location.href='?opt=2';" value="Position"><input id="refresh" type="text" value="" readonly>
		<img class="<?php echo $server_map; ?>" src=".maps/<?php echo $server_map.'.jpg'; ?>">
		<script type="text/javascript">
			dbData = new Array();
			left_pos = -170;

			<?php
				echo "box_hide_delay = $box_hide_delay;\n";
				@include "watch_list_bliss";
			?>
			
			if (typeof(watch_list) != 'undefined' && watch_list != ''){
				wl = ' <u>Watch list</u>: <b>'+watch_list.join(', ').replace(/\(|\)/g,'')+'</b>';
			}
			else {			
				watch_list = [];
				wl = '';
			}

			document.write(' <span style="color:#ffffff;">Top-Left corner of the marker box indicates position. Right click on it to hide for '+box_hide_delay+' seconds.'+wl+'</span>');

			<?php
				$opt = 0;

				if (isset($_GET['opt']) && preg_match('/^\d+$/', $_GET['opt'])){
					$opt = $_GET['opt'];
				}

				echo "server_map = '$server_map';\n";
				@include ".include/dz_tents_bliss.php";
				echo "markTents(dbData, $opt, watch_list);\n";
			?>			
		</script>
	</body>
</html>