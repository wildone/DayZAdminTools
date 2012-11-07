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
		<title>Killzone_Kid's Arma II DayZ Other Objects (Bliss <?php echo $server_map;?>)</title>
		<link rel="stylesheet" href="style.css" type="text/css">
		<script type="text/javascript" src=".include/functions.js"></script>
	</head>
	<body>
		| <input type="button" onclick="document.location.href='dayz_admin_tool_players_bliss.php';" value="View Players"> | <input type="button" onclick="document.location.href='dayz_admin_tool_vehicles_bliss.php';" value="View Vehicles"> | <input type="button" onclick="document.location.href='dayz_admin_tool_tents_bliss.php';" value="View Tents"> | <input type="button" onclick="document.location.href='dayz_admin_tool_objects_bliss.php';" value="View Objects"> | Killzone_Kid's Arma II DayZ Admin Tools for Bliss build 4.1
		<hr />
		<img class="<?php echo $server_map;?>" src=".maps/<?php echo $server_map.'.jpg';?>">
		<script type="text/javascript">
			dbData = new Array();
			left_pos = -170;
	
			<?php
				echo "server_map = '$server_map';\n";
				require_once(".include/dz_objects_bliss.php");
			?>
			
			markObjects(dbData);
		</script>
	</body>
</html>