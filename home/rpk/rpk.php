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

//kalkulasi(1040,1024);

// sql to create table
$tabel_pvp = "CREATE TABLE IF NOT EXISTS pvp (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
killer INT(11) NOT NULL,
killed INT(11) NOT NULL,
jumlah INT(11),
time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$tabel_rank = "CREATE TABLE IF NOT EXISTS rank (
roleid INT(11) NOT NULL PRIMARY KEY,
userid INT(11) NOT NULL,
rolename VARCHAR(64) NOT NULL,
roleclass INT(11) NOT NULL,
kills INT(11) NOT NULL,
deads INT(11) NOT NULL,
point INT(11) NOT NULL,
pointconvert INT(11) NOT NULL,
pointconverttime INT(11) NOT NULL,
ip VARCHAR(32) NOT NULL,
time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$tabel_ipakun = "CREATE TABLE IF NOT EXISTS ipakun (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
account varchar(64) NOT NULL,
userid INT(11) NOT NULL,
ip VARCHAR(64) NOT NULL,
time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$tabel_convert = "CREATE TABLE IF NOT EXISTS convertpoint (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
userid INT(11) NOT NULL,
roleid INT(11) NOT NULL,
point INT(11) NOT NULL,
tanggal INT(11) NOT NULL,
creatime datetime NOT NULL,
time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";


if ($conn->query($tabel_pvp) === TRUE) {
    print "Service PVP Rank Runing \n";
} else {
    print "Error : " . $conn->error;
}

if ($conn->query($tabel_rank) === TRUE) {
    print "";
} else {
    print "Error : " . $conn->error;
}

if ($conn->query($tabel_ipakun) === TRUE) {
    print "";
} else {
    print "Error : " . $conn->error;
}

if ($conn->query($tabel_convert) === TRUE) {
    print "";
} else {
    print "Error : " . $conn->error;
}



// Last read position
$last = 0;
$cek = "cek.txt";
if (file_exists($cek)){
	$cekfile = fopen($cek,"r");
	$last = fgets($cekfile);
}
else{
	$cekfile = fopen($cek,"w");
	fwrite($cekfile, $last);
}
fclose($cekfile);

// Path to the updating log file
$file = '/home/logservice/logs/world2.formatlog';

while (true) {
    // PHP caches file information in order to provide faster
    // performance; In order to read the new filesize, we need
    // to flush this cache.
    clearstatcache(false, $file);

    // Get the current size of file
    $current = filesize($file);

    // Reseted the file?
    if ($current < $last) {
        $last = $current;
    }
    // If there's new content in the file
    elseif ($current > $last) {
        // Open the file in read mode
        $fp = fopen($file, 'r');
		
        // Set the file position indicator to the last position
        fseek($fp, $last);

        // While not reached the end of the file
        while (! feof($fp)) {
            // Read a line and print
            //print fgets($fp);
			$str = fgets($fp);
			login($str);
			buatakun($str);
			pvp($str);
        }

        // Store the current position of the file for the next pass
        $last = ftell($fp);
		
		$cekfile = fopen($cek,"w");
		fwrite($cekfile,$last);
		fclose($cekfile);

        // Close the file pointer
        fclose($fp);
    }
	sleep(1);
}


function pvp($str){
	if (strpos($str,":die:")>0 and strpos($str,":type=2")>0 ){
		$a = strpos($str,"roleid=")+7;
		$b = strpos($str,":type=");
		$korban = substr($str,$a,$b-$a);
		$a = strpos($str,"attacker=")+9;
    	$b = strpos(substr($str,$a),"\n");
    	$penyerang = substr($str,$a,$b);
		kalkulasi($korban, $penyerang);
	}
}

function login($str){
	if (strpos($str,"formatlog:login:")>0){
		$a = strpos($str,"account=")+8;
		$b = strpos($str,":userid=");
		$account = substr($str,$a,$b-$a);
		$a = strpos($str,"userid=")+7;
		$b = strpos($str,":sid");
		$userid = substr($str,$a,$b-$a);
		$a = strpos($str,"peer=")+5;
    	$b = strpos(substr($str,$a),"\n");
    	$ip = substr($str,$a,$b);
		//catat_ip($account, $userid, $ip);
		setroles($userid, $ip);
	}
}

function buatakun($str){
	if (strpos($str,":createrole-success:")>0){
		$a = strpos($str,"account=")+8;
		$b = strpos($str,":roleid=");
		$account = substr($str,$a,$b-$a);
		$a = strpos($str,"userid=")+7;
		$b = strpos($str,":account=");
		$userid = substr($str,$a,$b-$a);
		$a = strpos($str,"roleid=")+7;
		$b = strpos($str,":IP=");
		$roleid = substr($str,$a,$b-$a);
		$a = strpos($str,":IP=")+4;
    	$b = strpos(substr($str,$a),"\n");
    	$ip = substr($str,$a,$b);
		//catat_ip($account, $userid, $ip);
		setroles($userid, $ip);
	}
}


function chat($txt){
	$ChatBroadCast = new WritePacket();
	$ChatBroadCast -> WriteUByte(9); 	//0:Umum, 1:Shout, 2:Party, 3:Guild, 7:Trade, 9:Sistem
	$ChatBroadCast -> WriteUByte(0); 				//Emotion
	$ChatBroadCast -> WriteUInt32(0);		//Roleid	but if offline then need to use 0
	$ChatBroadCast -> WriteUString($txt); 	//Text
	$ChatBroadCast -> WriteOctets(""); 				//Data
	$ChatBroadCast -> Pack(0x78); 					//Opcode
	$ChatBroadCast -> Send("localhost", 29300);
	echo $txt;
}

function catat_ip($account, $userid, $ip){
	$s = mysqli_query($GLOBALS['conn'],"SELECT * FROM rank WHERE userid='$userid'");
	// Kalau belum ada, simpan data user tersebut ke database
 	if(mysqli_num_rows($s) == 0){
    	mysqli_query($GLOBALS['conn'],"INSERT INTO ipakun (account, ip, userid) VALUES('$account','$ip','$userid')");
		print "First Login Account : " . $account .", User ID : ". $userid . ", IP :". $ip . "\n";
	}
 	// Jika sudah ada, update
	else{
    	mysqli_query($GLOBALS['conn'],"UPDATE rank set ip='$ip' where userid='$userid'");
		print "UPDATE ipakun set account='$account', ip='$ip' where userid='$userid' \n";
	}
}


function kalkulasi($idkorban,$idpenyerang){
	$pointmin = 500;
	$pointmax = 9000;
	$korban = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'],"SELECT * FROM rank where roleid='$idkorban'"));
	$penyerang = mysqli_fetch_assoc(mysqli_query($GLOBALS['conn'],"SELECT * FROM rank where roleid='$idpenyerang'"));
	if($korban['ip']==$penyerang['ip']){
		chat($penyerang['rolename'] . " Mendapat Peringatan Karena Percobaan Spam Kills Char Sendiri Atau Teman Disekitarnya");
	}
	else{
	$a = $korban['point']+9000; 
	$b = $penyerang['point']+9000;
	$hasil = round($korban['point']*100*$a/($b*($b+$b-$a)));
	//chat("Point : " . $hasil);
	if ($korban['point']-$hasil<500){
		$hasil = $korban['point']-500;
	}
	if($penyerang['point']+$hasil>9500){
		$hasil = $penyerang['point']+$hasil -9500;
	}
	
	$rate=round(($korban['kills']/($korban['kills']+$korban['deads']+1)*100),2);
	$txtkorban= $korban['rolename'] . " K:" . $korban['kills'] . " D:" . ($korban['deads']+1) . " Rate:" . $rate . "% Point:" . $korban['point'] . "-" . $hasil;
	$rate=round((($penyerang['kills']+1)/($penyerang['kills']+$penyerang['deads']+1)*100),2);
	$txtpenyerang= $penyerang['rolename'] . " K:" . ($penyerang['kills']+1) . " D:" . $penyerang['deads'] . " Rate:" . $rate . "% Point:" . $penyerang['point']. "+" . $hasil;
	
	chat($korban['rolename'] . " Telah Dibunuh Oleh " . $penyerang['rolename']);
	chat($txtkorban);
	chat($txtpenyerang);
	
	$roleid = $korban['roleid'];
	$point = $korban['point']-$hasil;
	$kills = $korban['kills'];
	$deads = $korban['deads']+1;
	mysqli_query($GLOBALS['conn'],"update rank set point='$point', kills='$kills', deads='$deads' where roleid='$roleid';");
	
	$roleid = $penyerang['roleid'];
	$point = $penyerang['point']+$hasil;
	$kills = $penyerang['kills']+1;
	$deads = $penyerang['deads'];
	mysqli_query($GLOBALS['conn'],"update rank set point='$point', kills='$kills', deads='$deads' where roleid='$roleid';");
}}


function setroles($id,$ip){
	//mysqli_query($GLOBALS['conn'],"DELETE FROM roles where accountid='$id'")
	$CharCount=0;
	$GetUserRolesArg = new WritePacket();
	$GetUserRolesArg -> WriteUInt32(-1); // always
	$GetUserRolesArg -> WriteUInt32($id); // userid
	$GetUserRolesArg -> Pack(0xD49);//0xD49
	if ($GetUserRolesArg -> Send("localhost", 29400)){ // send to gamedbd
		//return;
		$GetUserRolesRes = new ReadPacket($GetUserRolesArg); // reading packet from stream
		$GetUserRolesRes -> ReadPacketInfo(); // read opcode and length
		$GetUserRolesRes -> ReadUInt32(); // always
		$GetUserRolesRes -> ReadUInt32(); // retcode
		$CharCount = $GetUserRolesRes -> ReadCUInt32();
													
		for ($i = 0; $i < $CharCount; $i++){
			$roleid = $GetUserRolesRes -> ReadUInt32();
			$rolename = $GetUserRolesRes -> ReadUString();
			
			$GetRoleBase = new WritePacket();
			$GetRoleBase -> WriteUInt32(-1); // always
			$GetRoleBase -> WriteUInt32($roleid); // userid
			$GetRoleBase -> Pack(0x1F43); // opcode  

			if (!$GetRoleBase -> Send("localhost", 29400)) // send to gamedbd
			return;
			
			$GetRoleBase_Re = new ReadPacket($GetRoleBase); // reading packet from stream
			$packetinfo = $GetRoleBase_Re -> ReadPacketInfo(); // read opcode and length
			$GetRoleBase_Re -> ReadUInt32(); // always
			$GetRoleBase_Re -> ReadUInt32(); // retcode
			$GetRoleBase_Re -> ReadUByte();
			$GetRoleBase_Re -> ReadUInt32();
			$GetRoleBase_Re -> ReadUString();
			$GetRoleBase_Re -> ReadUInt32();
			$roleCls = $GetRoleBase_Re -> ReadUInt32();
			$GetRoleBase_Re -> ReadUByte();
			$GetRoleBase_Re -> ReadOctets();
			$GetRoleBase_Re -> ReadOctets();
			$GetRoleBase_Re -> ReadUInt32();
			$GetRoleBase_Re -> ReadUByte();
			$roleDelTime = $GetRoleBase_Re -> ReadUInt32();
			$GetRoleBase_Re -> ReadUInt32();
			$roleLastLogin = $GetRoleBase_Re -> ReadUInt32();
			$forbidcount = $GetRoleBase_Re -> ReadCUInt32();
			for ($x = 0; $x < $forbidcount; $x++){
				$GetRoleBase_Re -> ReadUByte();
				$GetRoleBase_Re -> ReadUInt32();
				$GetRoleBase_Re -> ReadUInt32();
				$GetRoleBase_Re -> ReadUString();
			}
			$GetRoleBase_Re -> ReadOctets();
			$GetRoleBase_Re -> ReadUInt32();
			$GetRoleBase_Re -> ReadUInt32();
			$GetRoleBase_Re -> ReadOctets();
			$GetRoleBase_Re -> ReadUByte();
			$GetRoleBase_Re -> ReadUByte();
			$GetRoleBase_Re -> ReadUByte();
			$GetRoleBase_Re -> ReadUByte();
			$roleLevel = $GetRoleBase_Re -> ReadUInt32();
			$roleCulti = $GetRoleBase_Re -> ReadUInt32();
			$roleClass = $roleCls;
			$rolePath="";
			
			//$role_arr[$i]=array("roleid" => $roleid, "rolename" => $rolename, "roleclass" => $roleClass, "rolelevel" => $roleLevel, "rolepath" => $rolePath, "roledel" => $roleDelTime, "roleban" => $forbidcount, "roleculti" => $roleCulti);
			
			$s = mysqli_query($GLOBALS['conn'],"SELECT * FROM roles WHERE role_id='$roleid'");
			// Kalau belum ada, simpan data user tersebut ke database
 			if(mysqli_num_rows($s)==0){
    			mysqli_query($GLOBALS['conn'],"INSERT INTO roles set account_id='$id', role_id='$roleid', role_name='$rolename', role_level='$roleLevel', role_occupation='$roleCls'");
				echo "First Login User ID : ". $id . ", IP :". $ip . "\n";
			}
			
			$s = mysqli_query($GLOBALS['conn'],"SELECT * FROM rank WHERE roleid='$roleid'");
			// Kalau belum ada, simpan data user tersebut ke database
 			if(mysqli_num_rows($s)==0){
    			mysqli_query($GLOBALS['conn'],"insert into rank set roleid='$roleid', userid='$id', rolename='$rolename', roleclass='$roleCls', point='1000', ip='$ip';");
				echo "First Login User ID : ". $id . ", IP :". $ip . "\n";
			}
			else{
				mysqli_query($GLOBALS['conn'],"update rank set userid='$id', rolename='$rolename', roleclass='$roleCls', ip='$ip' where roleid='$roleid';");
				echo "Update User ID : ". $id . ", IP :". $ip . "\n";
			}
			
			//mysqli_query($GLOBALS['conn'],"REPLACE INTO rank set roleid='$roleid', userid='$id', rolename='$rolename', roleclass='$roleCls', point='1000', ip='$ip';");
			
			
		}
	}
	
}


?>

