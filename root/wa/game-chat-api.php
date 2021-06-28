<?php
include("packet_class.php");

function chat($from, $content){
	$txt = "^ffdd99".$from.": ^ffdd00".$content.$data['rolename'];
	$ChatBroadCast = new WritePacket();
	$ChatBroadCast -> WriteUByte(9); 	//0:Umum, 1:Shout, 2:Party, 3:Guild, 7:Trade, 9:Sistem
	$ChatBroadCast -> WriteUByte(0); 				//Emotion
	$ChatBroadCast -> WriteUInt32(0);		//Roleid	but if offline then need to use 0
	$ChatBroadCast -> WriteUString($txt); 	//Text
	$ChatBroadCast -> WriteOctets(""); 				//Data
	$ChatBroadCast -> Pack(0x78); 					//Opcode
	$ChatBroadCast -> Send("localhost", 29300);
}

?>
