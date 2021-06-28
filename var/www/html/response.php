<?php
//Deklarasi Variable
$myfile="responselog.txt";
$a=null;
$b=null;
$waktu=date("d-m-Y H:i:s");

if (!($_GET or $_POST)){echo "Tidak Ada Request!!!";}
else{}
//Cek Metode Get
if($_GET){
	$a=json_encode($_GET);
	$text=$waktu."\nGET: ".$a."\n";
	tulisdata($text);
	echo bacadata();
}
//Cek Metode Post
if($_POST){
	$b=json_encode($_POST);
	$text=$waktu."\nPOST: ".$b."\n";
	tulisdata($text);
	echo bacadata();
}


function tulisdata($txt){
	$datafile = fopen($GLOBALS["myfile"], "a") or die("Unable to open file!");
	fwrite($datafile, $txt);
	fclose($datafile);
}

function bacadata(){
	$data = fopen($GLOBALS["myfile"], "r") or die("Unable to open file!");
	$txt='';
	while(! feof($data)) {
  		$line = fgets($data);
  		$txt.=$line. "<br>";
	}
	return $txt;
}
?>
