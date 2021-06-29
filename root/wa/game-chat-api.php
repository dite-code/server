<?php
include("packet_class.php");

function chat($from, $content){
	$txt = "^ffdd99".$from.": ^ffdd00".$content;
	$ChatBroadCast = new WritePacket();
	$ChatBroadCast -> WriteUByte(9); 	//0:Umum, 1:Shout, 2:Party, 3:Guild, 7:Trade, 9:Sistem
	$ChatBroadCast -> WriteUByte(0); 				//Emotion
	$ChatBroadCast -> WriteUInt32(0);		//Roleid	but if offline then need to use 0
	$ChatBroadCast -> WriteUString($txt); 	//Text
	$ChatBroadCast -> WriteOctets(""); 				//Data
	$ChatBroadCast -> Pack(0x78); 					//Opcode
	$ChatBroadCast -> Send("localhost", 29300);
}

function chatcustom($from, $content, $channel=9){
	$ChatBroadCast = new WritePacket();
	$ChatBroadCast -> WriteUByte($channel); 	//0:Umum, 1:Shout, 2:Party, 3:Guild, 7:Trade, 9:Sistem
	$ChatBroadCast -> WriteUByte(0); 				//Emotion
	$ChatBroadCast -> WriteUInt32($from);		//Roleid	but if offline then need to use 0
	$ChatBroadCast -> WriteUString($content); 	//Text
	$ChatBroadCast -> WriteOctets(""); 				//Data
	$ChatBroadCast -> Pack(0x78); 					//Opcode
	$ChatBroadCast -> Send("localhost", 29300);
}

if (count($argv)>1){
	if(count($argv)>=4){$channel=$argv[3]}
	else{$channel=9}
	chatcustom($argv[1], $argv[2], $channel)

}
?>
