<?php

// Written by Killzone_Kid
// http://killzonekid.com
//
// @Modified-By:   Gate
// @Modified-Date: 2012/10/23

include "dz_config_bliss.php";
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Killzone_Kid's Arma II DayZ Current Players (Bliss <?php echo $server_map;?>)</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="functions.js"></script>
</head>

<body>
<input id="back" type="button" onclick="document.location.href='index.html';" value="Index">
<input id="refresh" type="text" value="" readonly>
<input id="players" type="button" onclick="document.location.href='?opt=0';" value="Players">
<input type="button" onclick="document.location.href='?opt=1';" value="Health">
<input type="button" onclick="document.location.href='?opt=2';" value="Inventory">
<input type="button" onclick="document.location.href='?opt=3';" value="Position">
<img class="<?php echo $server_map;?>" src="<?php echo $server_map.'.jpg';?>">

<script type="text/javascript">
left_pos = -170;
dbData = new Array();
<?php
echo "box_hide_delay = $box_hide_delay;\n";
@include "watch_list_bliss"; //watch_list = ['(GPS)','(NVGoggles)','(Binocular_Vector)'];
?>
if (typeof(watch_list) != 'undefined' && watch_list != ''){

	wl = ' <u>Watch list</u>: <b>'+watch_list.join(', ').replace(/\(|\)/g,'')+'</b>';
	
} else {

	watch_list = [];
	wl = '';
}
document.write(' <span style="color:#ffffff;">Top-Left corner of the marker box indicates position. Right click on it to hide for '+box_hide_delay+' seconds.'+wl+'</span>');
<?php
$opt = 0;
if (isset($_GET['opt']) && preg_match('/^\d+$/',$_GET['opt'])){

	$opt = $_GET['opt'];
}
echo "last_update_cutoff_min = $last_update_cutoff_min;\n";
echo "server_map = '$server_map';\n";
echo "t_now = ".time().";\n";
@include "dz_players_bliss.php";
echo "markPlayers(dbData,$opt,watch_list,last_update_cutoff_min);\n";
?>
document.write('<br><b>'+((typeof(server_query) == 'undefined')?'[Errors]':server_query)+'</b>');
</script>

</body>
</html>
