<?php
// Written by Killzone_Kid
// http://killzonekid.com
include "dz_config_bliss.php";
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Killzone_Kid's Arma II DayZ Other Objects (Bliss <?php echo $server_map;?>)</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="functions.js"></script>
</head>

<body>
<input id="back" type="button" onclick="document.location.href='index.html';" value="Index">
<input id="refresh" type="text" value="" readonly>
<input id="objects" type="button" onclick="document.location.href='?';" value="Objects">
<img class="<?php echo $server_map;?>" src="<?php echo $server_map.'.jpg';?>">

<script type="text/javascript">
left_pos = -170;
dbData = new Array();
<?php
echo "box_hide_delay = $box_hide_delay;\n";
?>
document.write(' <span style="color:#ffffff;">Top-Left corner of the marker box indicates position. Right click on it to hide for '+box_hide_delay+' seconds.<br><div id="filter"></div></span>');
<?php
echo "server_map = '$server_map';\n";
echo "t_now = ".time().";\n";
@include "dz_objects_bliss.php";
echo "markObjects(dbData,document.location.search.replace(/^\?/,''));\n";
?>
document.write('<br><b>'+((typeof(server_query) == 'undefined')?'[Errors]':server_query)+'</b>');
</script>

</body>
</html>
