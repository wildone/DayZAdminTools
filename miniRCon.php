<?php
// Written by Killzone_Kid
// http://killzonekid.com
header('Cache-Control: private');
include "dz_config_bliss.php";
$pass = (isset($_POST['pass']))?$_POST['pass']:'';
$msg = (isset($_POST['msg']))?$_POST['msg']:'';
$kick = (isset($_POST['kick']))?$_POST['kick']:'';
$submit = 'LOG IN';
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Killzone_Kid's Mini RCon</title>
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>

<input id="back" type="button" onclick="document.location.href='index.html';" value="Index">
<br><h2>KK's Mini RCon</h2> 
<form id="form" action="" method="POST">

<?php

if ($kick != ''){

	Kick ($kick);
}


if ($msg != ''){

	Say ('-1', $msg);
} 

if ($pass != ''){

	//list players
	$players = RCon('players',$pass,$server_ip,$server_port);
	
	if ($players != ''){

		$players = preg_replace ('/^(.*?)(Players on server:)/','<b><u>$2</u></b><br>',$players);
		$players = preg_replace ('/^(\d+)(\s)/m','[<a style="color:#ffff00;" href="javascript:void(0);" onclick="kickPlayer(\'$1\');">Kick</a>] $1$2', $players);
		$players = preg_replace ('/\\n/','<br>',$players);
	
		echo '<br><div style="font-family:monospace;">'.$players.'</div>';
		echo '<br><br><b>Send Admin message to ALL:</b><br>';
		echo '<textarea name="msg"></textarea><br>';
		
		$submit = 'UPDATE';
	}
}

function Say ($n, $msg){


	global $pass;
	global $server_ip;
	global $server_port;
	
	if ($msg != ''){
	
		$text = ' >>> '.$msg;
		$response = RCon('Say '.$n.$text,$pass,$server_ip,$server_port);
		if ($response != ''){

			echo '<b>### Message sent:</b> '.preg_replace('/\\n/','<br>',$text).'<br>';
			
		} else {
		
			echo '<b>### No message sent!</b><br>';
		}
	}
}

function Kick ($n){

	global $pass;
	global $server_ip;
	global $server_port;
	
	$response = RCon('kick '.$n,$pass,$server_ip,$server_port);
	
	if ($response != ''){

		echo '<b>### Player #'.$n.'was kicked</b><br>';
			
	} else {
		
		echo '<b>### Failed to kick Player #'.$n.'</b><br>';
	}
}

function RCon ($text,$pass,$ip,$port){

	//Made by The-Killer (killer@righttorule.com)
	//modified by KK

	$msgseq = 0;
	
	//Generate CRC32 for pass and msg
	$authCRC = crc32(chr(255).chr(00).trim($pass));
	$authCRC = sprintf("%x", $authCRC);
	$msgCRC = crc32(chr(255).chr(01).chr(hexdec(sprintf('%01b',$msgseq))).$text);
	$msgCRC = sprintf("%x", $msgCRC);

	//Reverse the CRCs and put into array
	$authCRC = array(substr($authCRC,-2,2),substr($authCRC,-4,2),substr($authCRC,-6,2),substr($authCRC,0,2));
	$msgCRC = array(substr($msgCRC,-2,2),substr($msgCRC,-4,2),substr($msgCRC,-6,2),substr($msgCRC,0,2));

	//Socket comm
	$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

	if(!$sock) {
		
		echo "Socket create failed: ".socket_last_error()."<br>\n";
		return '';
		
	} else {
			
		//echo "Got Socket!\n";
	}
	

	//header
	$loginmsg = "BE".chr(hexdec($authCRC[0])).chr(hexdec($authCRC[1])).chr(hexdec($authCRC[2])).chr(hexdec($authCRC[3]));

	//Add payload
	$loginmsg .= chr(hexdec('ff')).chr(hexdec('00')).$pass;
	$len = strlen($loginmsg);

	//echo "Attempting Login\n";

	$sent = socket_sendto($sock, $loginmsg, $len, 0, $ip, $port);

	if ($sent == false) {

		echo "failed to send login ".socket_last_error()."<br>\n";
		return '';
		
	} else {

		//echo "Login sent: ".$sent." bytes\n";

	}

	$recv = socket_recvfrom($sock, $buf, 64, 0, $ip, $port);
	
	if($recv == false){

		echo "failed to recv ".socket_last_error()."<br>\n";
		return '';
		
	} else {

		//echo "Recieved: ".$recv." bytes\n\n";
	}

	//var_dump($buf);

	if (ord($buf[strlen($buf)-1]) == 1){

		//echo "Login Successful!\n";

	} else if(ord($buf[strlen($buf)-1]) == 0){

		echo "Login Failed!<br>\n";
		return '';
		
	} else {

		echo "Unknown result from login!<br>\n";
		return '';
	}

	$recv = socket_recvfrom($sock, $buf, 64, 0, $ip, $port);

	if($recv == false){

		echo "failed to recv ".socket_last_error()."<br>\n";
		return '';
		
	} else {

		//echo "Recieved: ".$recv." bytes\n\n";
	}

	//var_dump($buf);
	//echo substr($buf,9)."\n";

	//Send a heartbeat packet
	$statusmsg = "BE".chr(hexdec("7d")).chr(hexdec("8f")).chr(hexdec("ef")).chr(hexdec("73"));
	$statusmsg .= chr(hexdec('ff')).chr(hexdec('02')).chr(hexdec('00'));
	$len = strlen($statusmsg);
	
	$sent = socket_sendto($sock, $statusmsg, $len, 0, $ip, $port);
	if($sent == false){

		echo "failed to send msg ".socket_last_error()."<br>\n";
		return '';

	} else {

		//echo $text."\n";
	}
	
	//header
	$saymsg = "BE".chr(hexdec($msgCRC[0])).chr(hexdec($msgCRC[1])).chr(hexdec($msgCRC[2])).chr(hexdec($msgCRC[3]));

	//msg
	$saymsg .= chr(hexdec('ff')).chr(hexdec('01')).chr(hexdec(sprintf('%01b',$msgseq))).$text;
	$len = strlen($saymsg);

	$sent = socket_sendto($sock, $saymsg, $len, 0, $ip, $port);
	
	if($sent == false){

		echo "failed to send msg ".socket_last_error()."<br>\n";
		return '';

	} else {

		//echo $text."\n";
	}
	
	$msgseq++;
	
	$recv = socket_recvfrom($sock, $buf, 65535, 0, $ip, $port);
	if($recv == false){

		echo "failed to recv ".socket_last_error()."<br>\n";
		return '';
		
	} else {

		//echo "Recieved: ".$recv." bytes\n\n";
	}
	
	//var_dump($buf);
	socket_close($sock);
	
	return $buf;
}

?>


<br><br><br>
Password: <input id="pw" type="password" name="pass" value="<?php echo $pass;?>">
<input id="kick" name="kick" type="hidden" value="">
<br><br>
<input type="submit" value="<?php echo $submit;?>">
</form>

<script type="text/javascript">

function kickPlayer(n){


	if (confirm('Kick Player #'+n+'?')){
	
		document.getElementById('kick').value = n;
		document.getElementById('form').submit();

	} else {
	
		return false;
	}
}

</script>
</body>
</html>