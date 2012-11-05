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
		<title>Killzone_Kid's Arma II DayZ Vehicles (Bliss <?php echo $server_map;?>)</title>
		<link rel="stylesheet" href="style.css" type="text/css">
		<script type="text/javascript" src=".include/functions.js"></script>
	</head>
	<body onload="JavaScript:timedRefresh(31000);">
		| <input id="index_players" type="button" onclick="document.location.href='dayz_admin_tool_players_bliss.php';" value="View Players"> | <input id="index_players" type="button" onclick="document.location.href='dayz_admin_tool_vehicles_bliss.php';" value="View Vehicles"> | <input id="index_players" type="button" onclick="document.location.href='dayz_admin_tool_tents_bliss.php';" value="View Tents" disabled="disabled"> | <input id="index_players" type="button" onclick="document.location.href='dayz_admin_tool_objects_bliss.php';" value="View Objects" disabled="disabled"> | Killzone_Kid's Arma II DayZ Admin Tools for Bliss build 4.1
		<hr /> 
		&nbsp;&nbsp;<input id="vehicles" type="button" onclick="document.location.href='?';" value="Vehicles"><input type="button" onclick="document.location.href='?opt=1';" value="Damages"><input type="button" onclick="document.location.href='?opt=2';" value="Inventory"><input type="button" onclick="document.location.href='?opt=3';" value="Position"> <input id="refresh" type="text" value="" readonly>
		<img class="<?php echo $server_map;?>" src="<?php echo '.maps/'.$server_map.'.jpg';?>">
		
		<script type="text/javascript">
			left_pos = -170;
			dbData = new Array();
			partNameEnglish = new Array();
			partNameEnglish['motor']                   = 'Engine';
			partNameEnglish['palivo']                  = 'Fuel System';
			partNameEnglish['karoserie']               = 'Hull';
			partNameEnglish['sklo predni L']           = 'Windscreen Left';
			partNameEnglish['sklo predni P']           = 'Windscreen Right';
			partNameEnglish['elektronika']             = 'Electrical';
			partNameEnglish['mala vrtule']             = 'Tail Rotor';
			partNameEnglish['velka vrtule']            = 'Main Rotor';
			partNameEnglish['wheel_1_1_steering']      = 'Left Front Wheel';
			partNameEnglish['wheel_1_2_steering']      = 'Left Back Wheel';
			partNameEnglish['wheel_2_1_steering']      = 'Right Front Wheel';
			partNameEnglish['wheel_2_2_steering']      = 'Right Back Wheel';
			partNameEnglish['Pravy zadni tlumic']      = 'Right Rear Damper';
			partNameEnglish['Levy zadni tlumic']       = 'Left Rear Damper';
			partNameEnglish['Pravy predni tlumic']     = 'Right Front Damper';
			partNameEnglish['Levy predni tlumic']      = 'Left Front Damper';
			partNameEnglish['Pravy prostredni tlumic'] = 'Right Middle Damper';
			partNameEnglish['Levy prostredni tlumic']  = 'Left Middle Damper';
			partNameEnglish['Pravy dalsi tlumic']      = 'Right Additional Damper';
			partNameEnglish['Levy dalsi tlumic']       = 'Left Additional Damper';
		
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
			
			document.write(' <span style="color:#ffffff;">Top-Left corner of the marker box indicates position. Right click on it to hide for '+box_hide_delay+' seconds.'+wl+'<br><div id="filter"></div></span>');
			
			<?php
				$opt = 0;
		
				if (isset($_GET['opt']) && preg_match('/^\d+$/',$_GET['opt'])){
					$opt = $_GET['opt'];
				}
		
				echo "server_map = '$server_map';\n";
				echo "t_now = ".time().";\n";
				@include ".include/dz_vehicles_bliss.php";
				echo "markVehicles(dbData,$opt,watch_list,document.location.search.replace(/^\?/,''));\n";
			?>
		
			document.write('<br><b>'+((typeof(server_query) == 'undefined')?'[Errors]':server_query)+'</b>');
		
		</script>
	</body>
</html>