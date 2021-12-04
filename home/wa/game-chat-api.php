<?php
include("packet_class.php");
$servername = "localhost";
$username = "root";
$password = "Ed2931993@";
$dbname = "pw";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function chat($from, $content){
//	$data = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'],"select * from users where mobilenumber='$from'"));
	if($data = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'],"SELECT * FROM users inner join rank on users.id=rank.userid where users.mobilenumber='$from' order by rank.time desc"))){
		$txt = "^ffdd99".$data['rolename'].": ^ffdd00".$content;
		$ChatBroadCast = new WritePacket();
		$ChatBroadCast -> WriteUByte(9); 	//0:Umum, 1:Shout, 2:Party, 3:Guild, 7:Trade, 9:Sistem
		$ChatBroadCast -> WriteUByte(0); 				//Emotion
		$ChatBroadCast -> WriteUInt32(0);		//Roleid	but if offline then need to use 0
		$ChatBroadCast -> WriteUString($txt); 	//Text
		$ChatBroadCast -> WriteOctets(""); 				//Data
		$ChatBroadCast -> Pack(0x78); 					//Opcode
		$ChatBroadCast -> Send("localhost", 29300);
	}
}
//echo $argv[1];
//var_dump($argv);
chat($argv[1],base64_decode($argv[2]));

?>
