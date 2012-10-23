<?php
header('Cache-Control: private');
// Written by Killzone_Kid
// http://killzonekid.com

include "dz_config_bliss.php";

if (!file_exists('watch_list_bliss')){

	touch('watch_list_bliss');
}

//limit to 100 items
$updated_wl_array = array();
$is_submit = false;

for ($i=0; $i<100; $i++){

	if (isset($_POST['item'.$i])){
		
		$is_submit = true;
		
		if (preg_match('/^\w+$/',$_POST['item'.$i])){
		
			array_push($updated_wl_array, $_POST['item'.$i]);
		}
	
	} else {
	
		break;
	}
}

$updated_wl_array2 = array_unique ($updated_wl_array);

if ($is_submit){

	if (count($updated_wl_array) > 0){

		$updated_list = "watch_list = ['(".implode(")','(",$updated_wl_array2).")'];\n";

	} else {

		$updated_list = "";
	}
		
	file_put_contents('watch_list_bliss', $updated_list);
	sleep (2);
}


?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Killzone_Kid's Arma II DayZ Watch List Editor (Bliss <?php echo $server_map;?>)</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="functions.js"></script>
</head>
<body>
<input id="back" type="button" onclick="document.location.href='index.html';" value="Index"><br><br>
<form action="" method="POST">

<?php
$wl = file_get_contents('watch_list_bliss'); //watch_list = ['(GPS)','(NVGoggles)','(Binocular_Vector)'];
$wl = preg_replace ("/^.*?watch_list = \['\(/","",$wl);
$wl = preg_replace ("/\)'(,)'\(/","$1 ",$wl);
$wl = preg_replace ("/\)'\];.*$/","",$wl);

echo '<span style="color:#ffffff;"><u>Watch list</u>: <b>'.$wl.'</b><br></span><br><br>';

$wl_array = ($wl != '')?$wl_array = explode(", ",$wl): array();

for ($i=0; $i<count($wl_array); $i++){

	echo 'Item '.$i.' <input type="text" name="item'.$i.'" value="'.$wl_array[$i].'"><br>';
}
 
$f = $i;
for ($i=$f; $i<$f+5; $i++){

	echo 'Item '.$i.' <input type="text" name="item'.$i.'" value=""><br>';
}
echo '<br><input type="submit" value="Update Watch List">';
?>
</form>
</body>
</html>
