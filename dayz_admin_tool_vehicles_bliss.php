<?php
// Written by Killzone_Kid
// http://killzonekid.com
include "dz_config_bliss.php";
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Killzone_Kid's Arma II DayZ Vehicles (Bliss <?php echo $server_map;?>)</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="functions.js"></script>
</head>
<body>
<input id="back" type="button" onclick="document.location.href='index.html';" value="Index">
<input id="refresh" type="text" value="" readonly>
<input id="vehicles" type="button" onclick="document.location.href='?';" value="Vehicles">
<input type="button" onclick="document.location.href='?opt=1';" value="Damages">
<input type="button" onclick="document.location.href='?opt=2';" value="Inventory">
<input type="button" onclick="document.location.href='?opt=3';" value="Position">
<img class="<?php echo $server_map;?>" src="<?php echo $server_map.'.jpg';?>">

<script type="text/javascript">
left_pos = -170;
dbData = new Array();
partNameEnglish = new Array();
partNameEnglish['motor'] = 'engine';
partNameEnglish['palivo'] = 'fuel_tank';
partNameEnglish['karoserie'] = 'hull';
partNameEnglish['sklo predni L'] = 'windscreen_left';
partNameEnglish['sklo predni P'] = 'windscreen_right';
partNameEnglish['elektronika'] = 'electronics';
partNameEnglish['mala vrtule'] = 'tail_rotor';
partNameEnglish['velka vrtule'] = 'main_rotor';
partNameEnglish['wheel_1_1_steering'] = 'left_front_wheel';
partNameEnglish['wheel_1_2_steering'] = 'left_back_wheel';
partNameEnglish['wheel_2_1_steering'] = 'right_front_wheel';
partNameEnglish['wheel_2_2_steering'] = 'right_back_wheel';
partNameEnglish['Pravy zadni tlumic'] = 'right_rear_damper';
partNameEnglish['Levy zadni tlumic'] = 'left_rear_damper';
partNameEnglish['Pravy predni tlumic'] = 'right_front_damper';
partNameEnglish['Levy predni tlumic'] = 'left_front_damper';
partNameEnglish['Pravy prostredni tlumic'] = 'right_middle_damper';
partNameEnglish['Levy prostredni tlumic'] = 'left_middle_damper';
partNameEnglish['Pravy dalsi tlumic'] = 'right_additional_damper';
partNameEnglish['Levy dalsi tlumic'] = 'left_additional_damper';
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
document.write(' <span style="color:#ffffff;">Top-Left corner of the marker box indicates position. Right click on it to hide for '+box_hide_delay+' seconds.'+wl+'<br><div id="filter"></div></span>');
<?php
$opt = 0;
if (isset($_GET['opt']) && preg_match('/^\d+$/',$_GET['opt'])){

	$opt = $_GET['opt'];
}
echo "server_map = '$server_map';\n";
echo "t_now = ".time().";\n";
@include "dz_vehicles_bliss.php";
echo "markVehicles(dbData,$opt,watch_list,document.location.search.replace(/^\?/,''));\n";
?>
document.write('<br><b>'+((typeof(server_query) == 'undefined')?'[Errors]':server_query)+'</b>');
</script>

</body>
</html>
