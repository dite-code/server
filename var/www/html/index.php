<?php
	// Start the session
	session_start();
	
	//include PHP library
	include_once(dirname(__FILE__) . '/Midtrans.php'); 
	\Midtrans\Config::$serverKey = 'Mid-server-bGt9EGInTS4dhg9fH81FLbL3';
	\Midtrans\Config::$isProduction = true;   // false = sandbox
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require 'mail/Exception.php';
	require 'mail/PHPMailer.php';
	require 'mail/SMTP.php';
	
	//Global variable
	$title="PW Private Server | PW NESIA | PW Private Incast";
	$page="dashboard";
	$serverip="127.0.0.1";
	$serverport="29000";
	$openbeta="1 November 2020";
	$nextmaintenance="8 November 2020";
	$ip=$_SERVER['REMOTE_ADDR'];
	$tanggal=date("Ymd");
	$waktu=time();
	
	//Function Koneksi Database
	$conn = new mysqli("localhost", "root", "camelia", "pw");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	// Mencek berdasarkan IPnya, apakah user sudah pernah mengakses hari ini
	$s = mysqli_query($conn,"SELECT * FROM pengunjung WHERE ip='$ip' AND tanggal='$tanggal'");
	
	// Kalau belum ada, simpan data user tersebut ke database
	if(mysqli_num_rows($s) == 0){
		mysqli_query($conn,"INSERT INTO pengunjung(ip, tanggal, jumlah, online) VALUES('$ip','$tanggal','1','$waktu')");
	}
	// Jika sudah ada, update
	else{
		mysqli_query($conn,"UPDATE pengunjung SET jumlah=jumlah+1, online='$waktu' WHERE ip='$ip' AND tanggal='$tanggal'");
	}
	
	
	//Input Variable
	extract($_GET);
	extract($_POST);
	
	function sendwa ($no, $isi){
		$isi = urlencode($isi);
		
		$ch = curl_init(); 
		// persiapkan curl
		$ch = curl_init(); 
		
		// set url 
		curl_setopt($ch, CURLOPT_URL, 'pw-nesia.com:8088/?no='.$no.'&isi='.$isi);
		
		// return the transfer as a string 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		
		// $output contains the output string 
		$output = curl_exec($ch); 
		
		// tutup curl 
		curl_close($ch);      
		
		// menampilkan hasil curl
		echo $output;
		if($output=="Sukses"){
			return 1;
		}
	}
	
	function sendmail($to, $name, $bodi){
		date_default_timezone_set('Asia/Jakarta');
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 587;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->Username = "gmpwnesia@gmail.com";
		$mail->Password = "ed2931993";
		$mail->setFrom('gmpwnesia@gmail.com', 'PW Nesia');
		$mail->addAddress($to, $name);
		$mail->Subject = 'Verifikasi E-mail';
		//$mail->msgHTML(file_get_contents('mailcontents.php?id=1024'), dirname(__FILE__));
		$mail->isHTML(true); 
		$mail->Body = $bodi;
		if (!$mail->send()) {
			$result = $mail->ErrorInfo;
			} else {
			$result = 1;
		}
		return $result;
	}
	
	function resendmail($to, $name, $bodi){
		date_default_timezone_set('Asia/Jakarta');
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 587;
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->Username = "gmnesia@gmail.com";
		$mail->Password = "ed2931993";
		$mail->setFrom('gmpwnesia@gmail.com', 'PW Nesia');
		$mail->addAddress($to, $name);
		$mail->Subject = 'Verifikasi E-mail';
		//$mail->msgHTML(file_get_contents('mailcontents.php?id=1024'), dirname(__FILE__));
		$mail->isHTML(true); 
		$mail->Body = $bodi;
		if (!$mail->send()) {
			$result = $mail->ErrorInfo;
			} else {
			$result = 1;
		}
		return $result;
	}
	
	function ubahnohp($nohp){
		$nohp = str_replace(" ","",$nohp);
		$nohp = str_replace("(","",$nohp);
		$nohp = str_replace(")","",$nohp);
		$nohp = str_replace(".","",$nohp);
		if(!preg_match('/[^+0-9]/',trim($nohp))){
			if(substr(trim($nohp), 0, 2)=='62'){
				$nohp = trim($nohp);
			}
			elseif(substr(trim($nohp), 0, 3)=='+62'){
				$nohp = '62'.substr(trim($nohp), 3);
			}
			elseif(substr(trim($nohp), 0, 1)=='0'){
				$nohp = '62'.substr(trim($nohp), 1);
			}
		}
		return $nohp;
	}
	
	//cek username
	if ($aksi=='cekuser'){
		$nohp = ubahnohp($nohp);
		if(mysqli_num_rows(mysqli_query($conn,"select * from users where mobilenumber='$nohp'"))>0){
			echo "2";
		}
		else if(mysqli_num_rows(mysqli_query($conn,"select * from users where name='$username'"))>0){
			echo "1";
		}
		else {
			echo"0";
		}
	}
	
	//Daftar
	else if ($aksi=='daftar'){
		$Login = StrToLower(Trim($username));
		$Pass = StrToLower(Trim($password));
		$Email = Trim($email);
		$Salt = base64_encode(md5($Login.$Pass, true));			
		$kodeverify= rand(100000,999999);
		$nohp = ubahnohp($nohp);
		
		mysqli_query($conn,"call adduser('$Login', '0', '0', '$kode', '$nama', '0', '$Email', '$nohp', '0', '0', '0', '0', '0', '0', '', '$kodeverify', '$Salt')");
		
		$query = mysqli_query($conn,"select * from users where name='$Login' and passwd2='$Salt'");
		$data = mysqli_fetch_assoc($query);
		$_SESSION['username']=$data['name'];
		$_SESSION['userid']=$data['ID'];
		$userid=$data['ID'];
		mysqli_query($conn,"call usecash($userid,1,0,1,0,1000,1,@error)");
		
		$bodi="Kode Verifikasi Anda Adalah : *$kodeverify*.";
		//$kirim = sendmail($Email, $nama, $bodi);
		$kirim = sendwa($nohp, $bodi); 
		if ($kirim=="1"){
			//Redirect ke dasboard
			echo "<script>window.location.assign('?page=dashboard');</script>";
		}
		else {
			echo "<script>alert('Gagal Mengirim WA!!');window.location.assign('?page=dashboard');</script>";
		}
		echo $kirim;
	}
	//Login
	else if ($aksi=='login'){
		$Login = StrToLower(Trim($username));
		$Pass = StrToLower(Trim($password));
		$Salt = base64_encode(md5($Login.$Pass, true));			
		$query = mysqli_query($conn,"select * from users where name='$Login' and passwd2='$Salt'");
		$data = mysqli_fetch_assoc($query);
		$_SESSION['username']=$data['name'];
		$_SESSION['userid']=$data['ID'];
		//Redirect ke dasboard
		echo "<script>window.location.assign('?page=dashboard');</script>";
	}
	//Logout
	else if ($aksi=='logout'){
		// remove all session variables
		session_unset();
		
		// destroy the session
		session_destroy();
		
		//Redirect ke dasboard
		echo "<script>window.location.assign('?page=dashboard');</script>";
	}
	//convert
	else if ($aksi=='convert'){
		$tabel_convert = "CREATE TABLE IF NOT EXISTS convertpoint (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		userid INT(11) NOT NULL,
		roleid INT(11) NOT NULL,
		point INT(11) NOT NULL,
		tanggal INT(11) NOT NULL,
		creatime datetime NOT NULL,
		time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		)";
		if ($conn->query($tabel_convert) === TRUE) {
			print "";
			} else {
			print "Error : " . $conn->error;
		}
		$query=mysqli_query($conn,"select * from rank where roleid='$roleid'");
		$data=mysqli_fetch_assoc($query);
		$userid=$data['userid'];
		$sisapoint=$data['point']-$point;
		$pointconvert=$data['pointconvert']+$point;
		$t=time();
		$cash=10*$point;
		if($sisapoint>=1000){
			mysqli_query($conn,"update rank set point='$sisapoint',pointconvert='$pointconvert', pointconverttime='$t' where roleid='$roleid'");
			mysqli_query($conn,"call usecash($userid,1,0,1,0,$cash,1,@error)");
			$query = mysqli_query($conn,"select * from usecashnow where userid='$userid' and cash='$cash'");
			while ($data = mysqli_fetch_assoc($query)){
				$creatime = $data['creatime'];
				if (mysqli_num_rows(mysqli_query($conn,"select * from convertpoint where creatime='$creatime'"))>0){
					echo"";
					}else {
					mysqli_query($conn,"insert into convertpoint set userid='$userid', roleid='$roleid', point='$point', tanggal='$t', creatime='$creatime'");
				}
			}
			//Redirect ke Tukar Point
			echo "<script>window.location.assign('?page=tukar point');</script>";
		}
		echo "<script>alert('Penukaran Gagal !!!');window.location.assign('?page=tukar point');</script>";
	}
	//Donasi
	else if ($aksi=='donasi'){
		$tabel_donasi = "CREATE TABLE IF NOT EXISTS donasi (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		userid INT(11) NOT NULL,
		idorder VARCHAR(30) NOT NULL,
		point INT(11) NOT NULL,
		harga INT(15) NOT NULL,
		status INT(11) NOT NULL,
		tanggal INT(11) NOT NULL,
		creatime datetime NOT NULL,
		time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		)";
		if ($conn->query($tabel_donasi) === TRUE) {
			print "";
			} else {
			print "<script>alert('" . $conn->error."');<script>";
		}
		$t=time();
		$query=mysqli_query($conn,"select * from donasi where userid='$userid'");
		$jmlhorder=0;
		if (mysqli_num_rows($query)){$jmlhorder=mysqli_num_rows($query);}
		$jmlhorder++;
		$idorder="N".$userid."-".$jmlhorder;
		mysqli_query($conn,"insert into donasi set userid='$userid', idorder='$idorder', point='$point', harga='$harga', status='0', tanggal='$t'");
		echo "<script>window.location.assign('?page=donasi');</script>";
	}
	//Bayar
	else if ($aksi=='bayar'){
		$userid=$_SESSION['userid'];
		$query=mysqli_query($conn,"select * from donasi where userid='$userid'");
		while ($data = mysqli_fetch_assoc($query)){
			echo "tes<br>";
			$order_status_obj = \Midtrans\Transaction::status($data['idorder']);
			$status = $order_status_obj->transaction_status;
			if($status=="settlement"){echo "ada";}
			else { echo "gagal";}
		}
		//mysqli_query($conn,"update donasi set userid='$userid', idorder='$idorder', point='$point', harga='$harga', status='0', tanggal='$t'");
		//echo "<script>window.location.assign('?page=donasi');</script>";
	}
	else if ($aksi=='finish' || $aksi=='unfinish' || $aksi=='error'){
		$userid=$_SESSION['userid'];
		//	GET: {"aksi":"finish","order_id":"T-0021","status_code":"200","transaction_status":"settlement"}
		if ($transaction_status=="settlement"){
			$query = mysqli_query($conn,"select * from donasi where idorder='$order_id'");
			$data = mysqli_fetch_assoc($query);
			$cash = $data["point"];
			mysqli_query($conn,"call usecash($userid,1,0,1,0,$cash,1,@error)");
			$query = mysqli_query($conn,"select * from usecashnow where userid='$userid' and cash='$cash'");
			while ($data = mysqli_fetch_assoc($query)){
				$creatime = $data['creatime'];
				if (mysqli_num_rows(mysqli_query($conn,"select * from donasi where creatime='$creatime'"))>0){
					echo"";
					}else {
					mysqli_query($conn,"update donasi set status='3', creatime='$creatime' where idorder='$order_id'");
				}
			}
		}
		else if ($transaction_status=="pending"){
			mysqli_query($conn,"update donasi set status='1' where idorder='$order_id'");
		}
		else if ($transaction_status=="cancel"){
			mysqli_query($conn,"update donasi set status='2' where idorder='$order_id'");
		}
		//Redirect ke Donasi
		echo "<script>window.location.assign('?page=donasi');</script>";
	}
	else if ($aksi=='verify'){
		$userid=$_SESSION['userid'];
		$query = mysqli_query($conn,"select * from users where ID='$userid' and qq='$kodeverify'");
		if(mysqli_num_rows($query)>0){
			$data= mysqli_fetch_assoc($query);
			$passwd2 =$data["passwd2"];
			$query = mysqli_query($conn,"update users set passwd='$passwd2' where ID='$userid'");
			//Redirect ke Dashboard
		} 
		echo "<script>window.location.assign('?page=dashboard');</script>";
	}
	else if ($aksi=='kirimulang'){
		$userid=$_SESSION['userid'];
		$query = mysqli_query($conn,"select * from users where ID='$userid'");
		$data= mysqli_fetch_assoc($query);
		$email = $data["email"];
		$nohp = ubahnohp($data["mobilenumber"]);
		$nama =  $data["truename"];
		$kodeverify= rand(100000,999999);
		$bodi="*PW NESIA*\nKode Verifikasi Anda Adalah : *$kodeverify*.";
		//$kirim = resendmail($email, $nama, $bodi);
		$kirim = sendwa($nohp, $bodi);
		if ($kirim=="1"){
			$query = mysqli_query($conn,"update users set qq='$kodeverify' where ID='$userid'");
			//Redirect ke dasboard
			echo "<script>alert('Kode Verifikasi di Kirim');window.location.assign('?page=dashboard');</script>";
		}
		else{
			echo $kirim;
		}
	}
	else if ($aksi=='prosesgantinomor'){
		$userid=$_SESSION['userid'];
		$nohp = ubahnohp($nohp);
		if(mysqli_num_rows(mysqli_query($conn,"select * from users where mobilenumber='$nohp'"))>0){
			echo "<script>alert('No. HP Sudah Terdaftar');window.location.assign('?page=gantinohp');</script>";
		}
		else {
			$simpan = mysqli_query($conn,"update users set mobilenumber='$nohp' where ID='$userid'");
			if ($simpan){
				$query = mysqli_query($conn,"select * from users where ID='$userid'");
				$data= mysqli_fetch_assoc($query);
				$email = $data["email"];
				$nohp = ubahnohp($data["mobilenumber"]);
				$nama =  $data["truename"];
				$kodeverify= rand(100000,999999);
				$bodi="*PW NESIA*\nKode Verifikasi Anda Adalah : *$kodeverify*.";
				//$kirim = resendmail($email, $nama, $bodi);
				$kirim = sendwa($nohp, $bodi);
				if ($kirim=="1"){
					$query = mysqli_query($conn,"update users set qq='$kodeverify' where ID='$userid'");
					//Redirect ke dasboard
					echo "<script>alert('Kode Verifikasi di Kirim');window.location.assign('?page=dashboard');</script>";
				}
				else{
					echo $kirim;
				}
			}	
		}	
	}
	else if ($aksi=='gantinohp'){
		echo "<script>window.location.assign('?page=gantinohp');</script>";
	}
	else{
		if (isset($_SESSION['userid'])){
			$userid=$_SESSION['userid'];
			$query = mysqli_query($conn,"select * from users where ID='$userid'");
			$data = mysqli_fetch_assoc($query);
			if($page=="gantinohp"){
				$page=="gantinohp";
			}
			else if ($data['passwd']=='0'){
				$page="verifikasi";
			}
		}
		
		//Shell Commands
		$proses = shell_exec('ps -A w');
		
		//Info Player
		$playercount = mysqli_num_rows(mysqli_query($conn,"select * from users"));
		$playeronline = mysqli_num_rows(mysqli_query($conn,"select * from point where zoneid<>''"));
		
		//Function Cek Map Online or Offline
		function cekmap($proses, $maps){
			if (strpos($proses,'./gs '.$maps)!==false){$status = "<div class='text-success'> ONLINE </div>";}
			else{$status = "<div class='text-danger'> OFFLINE </div>";}
			return $status;
		}
		
		//Function Cek Event On or Off
		function cekproses($proses, $data){
			if (strpos($proses,$data)!==false){$status = "<div class='text-success'> ON </div>";}
			else{$status = "<div class='text-danger'> OFF </div>";}
			return $status;
		}
		
		function timeleft($seconds){
			$days = floor($seconds / 86400);
			$seconds %= 86400;
			
			$hours = floor($seconds / 3600);	
			$seconds %= 3600;
			
			$minutes = floor($seconds / 60);
			$seconds %= 60;
			
			$txt="";
			if ($days>0){
				$txt.="$days Hari ";
			}
			if ($hours>0){
				$txt.="$hours Jam ";
			}
			if ($minutes>0){
				$txt.="$minutes Menit ";
			}
			return $txt;
			}
			
			function getjob($a){
			switch ($a) {
			case 0:
			return "WR";
			break;
			case 1:
			return "MG";
			break;
			case 2:
			return "PS";
			break;
			case 3:
			return "FX";
			break;
			case 4:
			return "BS";
			break;
			case 5:
			return "AS";
			break;
			case 6:
			return "AR";
			break;
			case 7:
			return "PR";
			break;
			case 8:
			return "SK";
			break;
			case 9:
			return "MS";
			break;
			case 10:
			return "DB";
			break;
			case 11:
			return "SB";
			break;
			default:
			"";
			}
			}
			?>
			<!doctype html>
			<html lang="en">
			<head>
			<!-- Global site tag (gtag.js) - Google Analytics -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=UA-178669683-1"></script>
			<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());
			gtag('config', 'UA-178669683-1');
			</script>
			
			<meta name="description" content="PW Private Nesia, Perfect World Private Server Incast. PW Private 2020">
			
			<meta charset="utf-8" />
			<link rel="icon" type="image/png" href="assets/img/favicon.ico">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			
			<title><?php echo $title; ?></title>
			
			<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
			<meta name="viewport" content="width=device-width" />
			
			
			<!-- Bootstrap core CSS     -->
			<link href="assets/css/bootstrap.min.css" rel="stylesheet" />
			
			<!-- Animation library for notifications   -->
			<link href="assets/css/animate.min.css" rel="stylesheet"/>
			
			<!--  Light Bootstrap Table core CSS    -->
			<link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>
			
			<!--     Fonts and icons     -->
			<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
			<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
			<link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
			
			</head>
			<body>
			
			<div class="wrapper">
			
			<!-------------------------------------------------------------------------------- Start Sidebar ------------------------------------------------------------------------------------------>
			<div class="sidebar" data-color="azure" data-image="assets/img/sidebar.jpg">
			
			<!--
			
			Tip 1: you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple"
			Tip 2: you can also add an image using data-image tag
			
			-->
			
			<div class="sidebar-wrapper">
			<div class="logo">
			<a href="" class="simple-text">
			PW NESIA
			</a>
			</div>
			
			<ul class="nav">
			<li <?php if($page=="dashboard"){echo "class='active'";} ?> >
			<a href="?page=dashboard">
			<i class="pe-7s-home"></i>
			<p>Dashboard</p>
			</a>
			</li>
			<li <?php if($page=="guide"){echo "class='active'";} ?> >
			<a href="?page=guide">
			<i class="pe-7s-display1"></i>
			<p>Guide</p>
			</a>
			</li>
			<li <?php if($page=="download"){echo "class='active'";} ?> >
			<a href="?page=download">
			<i class="pe-7s-cloud-download"></i>
			<p>Download</p>
			</a>
			</li>
			<li <?php if($page=="rank"){echo "class='active'";} ?> >
			<a href="?page=rank">
			<i class="pe-7s-graph3"></i>
			<p>Rank</p>
			</a>
			</li>
			<?php if(isset($_SESSION['username'])) { ?>
			<li <?php if($page=="tukar point"){echo "class='active'";} ?> >
			<a href="?page=tukar point">
			<i class="pe-7s-gift"></i>
			<p>Tukar Point</p>
			</a>
			</li>
			<li <?php if($page=="donasi"){echo "class='active'";} ?> >
			<a href="?page=donasi">
			<i class="pe-7s-diamond"></i>
			<p>Donasi</p>
			</a>
			</li>
			<li <?php if($page=="promosi"){echo "class='active'";} ?> >
			<a href="?page=promosi">
			<i class="pe-7s-users"></i>
			<p>Promosi</p>
			</a>
			</li>
			<?php } ?>
			</ul>
			</div>
			</div>
			<!-------------------------------------------------------------------------------- End Sidebar ------------------------------------------------------------------------------------------>
			
			<!-------------------------------------------------------------------------------- Start Main Panel ------------------------------------------------------------------------------------------>
			<div class="main-panel">
			<nav class="navbar navbar-default navbar-fixed">
			<div class="container-fluid">
			<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#"><?php echo strtoupper($page); ?></a>
			</div>
			<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav navbar-right">
			<?php
			if(!isset($_SESSION['username'])) {
			?>
			<li>
			<a href="?page=daftar">
			<p>Daftar</p>
			</a>
			</li>
			<li>
			<a href="?page=login">
			<p>Login</p>
			</a>
			</li>
			
			<?php
			}
			else{
			?>
			<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<p>
			<?php echo $_SESSION['username'];?>
			<b class="caret"></b>
			</p>
			</a>
			<ul class="dropdown-menu">
			<li><a href="?page=ganti password">Ganti Password</a></li>
			<li class="divider"></li>
			<li><a href="?aksi=logout">Logout</a></li>
			</ul>
			</li>
			<?php
			}
			?>
			</ul>
			</div>
			</div>
			</nav>
			<!-------------------------------------------------------------------------------- End Main Panel ------------------------------------------------------------------------------------------>
			<!-------------------------------------------------------------------------------- Start Content ------------------------------------------------------------------------------------------>
			<div class="content" >
			<div class="container-fluid">
			<div class="row" >
			<div class="col-md-8">
			<div class="card">
			<!-------------------------------------------------------------------------------- Start Daftar ------------------------------------------------------------------------------------------>						
			<?php if($page=="daftar"){ ?>
			<div class="header">
			<h4 class="title text-center">DAFTAR</h4>
			<p class="category text-center">Silahkan Isi Formulir Registrasi</p>
			</div>
			<div class="content">
			<form id="form-daftar">
			<input type="hidden" name="aksi" value="daftar">
			<div class="row">
			<div class="col-md-4">
			<div class="form-group">
			<label>Username</label>
			<input type="text" class="form-control" placeholder="Username" name="username" id="username">
			</div>
			</div>
			<div class="col-md-4">
			<div class="form-group">
			<label>Password</label>
			<input type="password" class="form-control" placeholder="Password" name="password" id="password">
			</div>
			</div>
			<div class="col-md-4">
			<div class="form-group">
			<label>Re-Password</label>
			<input type="password" class="form-control" placeholder="Re-Password" name="password1" id="password1">
			</div>
			</div>    
			</div>
			<div class="row">
			<div class="col-md-7">
			<div class="form-group">
			<label>E-Mail</label>
			<input type="text" class="form-control" placeholder="example@domain.com" name="email" id="email">
			</div>
			</div>
			<div class="col-md-5">
			<div class="form-group">
			<label>No. Handphone</label>
			<input type="text" class="form-control" placeholder="No. Handphone" name="nohp" id="nohp">
			</div>
			</div>
			</div>
			<div class="row">
			<div class="col-md-6">
			<div class="form-group">
			<label>Nama Asli</label>
			<input type="text" class="form-control" placeholder="Nama Alsi" name="nama" id="nama">
			</div>
			</div>
			<div class="col-md-6">
			<div class="form-group">
			<label>Kode Invite dari Player Lain</label>
			<input type="text" class="form-control" placeholder="Contoh: GM0001" name="kode" id="kode">
			</div>
			</div>    
			</div>
			
			
			<button type="button" class="btn btn-info btn-fill pull-right" onClick="klikdaftar()" id="daftar">Daftar</button>
			<div class="clearfix"></div>
			</form>
			</div>
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Daftar ------------------------------------------------------------------------------------------>							
			<!-------------------------------------------------------------------------------- Start Login ------------------------------------------------------------------------------------------>						
			<?php if($page=="login"){ 
			if(isset($_SESSION['username'])) {
			echo "<script>window.location.assign('?page=dashboard');</script>";
			}
			?>
			<div class="header">
			<h4 class="title text-center">Login</h4>
			<p class="category text-center">Silahkan Isi Formulir Login</p>
			</div>
			<div class="content">
			<form id="form-login" method="post">
			<input type="hidden" name="aksi" value="login">
			<div class="row">
			<div class="col-md-12">
			<div class="form-group">
			<label>Username</label>
			<input type="text" class="form-control" placeholder="Username" name="username">
			</div>
			</div>
			</div>
			<div class="row">
			<div class="col-md-12">
			<div class="form-group">
			<label>Password</label>
			<input type="password" class="form-control" placeholder="Password" name="password">
			</div>
			</div>
			</div>
			
			
			<button type="submit" class="btn btn-info btn-fill" id="login">Login</button>
			<div class="clearfix"></div>
			</form>
			</div>
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Login ------------------------------------------------------------------------------------------>							
			<!-------------------------------------------------------------------------------- Start Verifikasi ------------------------------------------------------------------------------------------>						
			<?php if($page=="verifikasi"){ ?>
			<div class="header">
			<h4 class="title">Verifikasi No. WhatsApp</h4>
			<p class="category">Welcome to Perfect World Nesia</p>
			</div>
			<div class="content">								
			<ul>
			<li>Sebelum Melakukan Verifikasi Anda tidak akan dapat login kedalam Game.</li>
			<li>Kode Verifikasi Dikirim WhatsApp Anda.</li>
			</ul>
			<form id="form-convert" method="post">
			<input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>">
			<div class="row">
			<div class="col-md-4">
			<div class="form-group">
			<label>Kode Verifikasi</label>
			<input type="text" class="form-control" name="kodeverify">
			</div>
			</div>
			<div class="col-md-4">
			<div class="form-group">
			<label>&nbsp;</label>
			<br><button type="submit" class="btn btn-info btn-fill" name="aksi" value="verify">Verifikasi</button>
			</div>
			</div>
			</div>
			
			<button type="submit" class="btn btn-info btn-fill" name="aksi" value="kirimulang">Kirim Ulang Kode</button>
			<button type="bu" class="btn btn-info btn-fill" name="aksi" value="gantinohp">Ganti No. HP</button>
			<div class="clearfix"></div>
			</form>
			</div>
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Verifikasi------------------------------------------------------------------------------------------>
			<!-------------------------------------------------------------------------------- Start Ganti Nomor HP------------------------------------------------------------------------------------------>						
			<?php if($page=="gantinohp"){ ?>
			<div class="header">
			<h4 class="title">Ganti No. Handphone</h4>
			<p class="category">Pastikan No. HP Dapat Menerima WA</p>
			</div>
			<div class="content">								
			<form id="form-convert" method="post">
			<input type="hidden" name="userid" value="<?php echo $_SESSION['userid'];?>">
			<div class="row">
			<div class="col-md-4">
			<div class="form-group">
			<label>Nomor Handphone</label>
			<input type="text" class="form-control" name="nohp">
			</div>
			</div>
			<div class="col-md-4">
			<div class="form-group">
			<label>&nbsp;</label>
			<br><button type="submit" class="btn btn-info btn-fill" name="aksi" value="prosesgantinomor">Kirim</button>
			</div>
			</div>
			</div>
			
			
			<div class="clearfix"></div>
			</form>
			</div>
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Ganti Nomor HP------------------------------------------------------------------------------------------>
			<!-------------------------------------------------------------------------------- Start Dashboard ------------------------------------------------------------------------------------------>						
			<?php if($page=="dashboard"){ ?>
			<div class="header">
			<h4 class="title">PERFECT WORLD NESIA</h4>
			<p class="category">Welcome to Perfect World Nesia</p>
			</div>
			<div class="content">
			<img src="assets/img/bg.jpg" alt="..." width="100%"/> 
			<p class="description text-left">
			Perfect World Nesia dibuat berdasarkan PWI 100%
			</p>
			
			<p class="description text-left">
			lalu apa yang membuat berbeda dengan PWI?
			</p>
			<ul>
			<li>Awal Pendaftaran Mendapat Koin 1000 Silver(10 Gold) Didapat Setelah Melakukan Verifikasi No,WhatsApp.</li>
			<li>Semua Karakter lahir di Desa Topeng.</li>
			<li>EXP 10x Sampai Lvl 100</li>
			<li>Free Wibawa Peri 3.</li>
			<li>Free Reputasi 200k.</li>
			<li>Satu-satunya Zona Aman Hanya Didesa Topeng (Sebagai Ganti Save farming Server Menjadi PVE).</li>
			<li>Tidak Ada Amulet Dan Hiero.(Sebagai Ganti Obat Tersedia di Ahli Obat)</li>
			<li>Sistem Live Rank RPK (Dihitung Berdasarkan Point RPK).</li>
			<li>Point RPK Bisa Ditukar Menjadi Koin Melalui WEBSITE (1 x Seminggu).</li>
			<li>Custom Publik Quest Agar Tidak Ada Monopoli WB(Masih Dalam Pengembangan).</li>
			</ul>
			</div>
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Dashboard ------------------------------------------------------------------------------------------>
			<!-------------------------------------------------------------------------------- Start Guide ------------------------------------------------------------------------------------------>						
			<?php if($page=="guide"){ ?>
			<div class="header">
			<h4 class="title">PANDUAN</h4>
			<p class="category">Welcome to Perfect World Nesia</p>
			</div>
			<div class="content">								
			<p class="description text-left">
			Leveling
			</p>
			<ul>
			<li>Lvl 1-100 Beli Bahan Labirin Jadikan Exp (Bahan Beli di IM Gunakan Dengan Bijak)</li>
			<li>Lvl 100++ PV</li>
			</ul>
			<p class="description text-left">
			Equipment 
			</p>
			<ul>
			<li>EQ Awal Lab 85 (Bahan Beli di IM).</li>
			<li>Refine Orb +9 free(Bahan Beli di IM).</li>
			<li>Colokan Dari WB Normal Lalu Upgrade Di NPC Alchemis.</li>
			<li>R8 Ambil di NPC Ranking Seperti Biasa.</li>
			<li>Bahan WS IM/LB PQ.</li>
			<li>Bahan Utama dan Upgrade Didapat Melalui IM/LB PQ.</li>
			<li>War Avatar Didapat Melalui FSC/IM.</li>
			<li>StartChart Didapat Melalui Bounty Hunter 100.</li>
			<li>IP(Item Pembantu) Di Dapat Melalui Titah Dewa.</li>
			<li>Suplay Token Tukar Dengan TOL</li>
			<li>R9 G3 Menyusul.</li>
			<li>G17 Menyusul.</li>
			<li>AVA S+ Menyusul.</li>
			</ul>
			<p class="description text-left">
			Farming
			</p>
			<ul>
			<li>RPK (Tukar Point RPK Menjadi Koin Melalui Website)</li>
			<li>Bounty Hunter</li>
			<li>Public Quest</li>
			<li>Summon WB</li>
			<li>FS/FSJ</li>
			
			</ul>
			
			</div>
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Guide ------------------------------------------------------------------------------------------>
			<!-------------------------------------------------------------------------------- Start Download ------------------------------------------------------------------------------------------>						
			<?php if($page=="download"){ ?>
			<div class="header">
			<h4 class="title">Download Client</h4>
			<p class="category">Welcome to Perfect World Nesia</p>
			</div>
			<div class="content">								
			<p class="description text-left">
			Equipment
			</p>
			<button type="submit" class="btn btn-info btn-fill" name="aksi" value="verify">Download</button>
			<ul>
			<li>Semua Karakter lahir di Desa Topeng.</li>
			<li>EXP 10x Sampai Lvl 100</li>
			<li>Free Wibawa Peri 3.</li>
			<li>Free Reputasi 200k.</li>
			<li>Satu-satunya Zona Aman Hanya Didesa Topeng (Sebagai Ganti Save farming Server Menjadi PVE).</li>
			<li>Sistem Live Rank RPK (Dihitung Berdasarkan Point RPK).</li>
			<li>Point RPK Bisa Ditukar Menjadi Koin Melalui WEBSITE (1 x Seminggu).</li>
			<li>Custom Publik Quest Agar Tidak Ada Monopoli WB(Masih Dalam Pengembangan).</li>
			
			</ul>
			</div>
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Download ------------------------------------------------------------------------------------------>
			<!-------------------------------------------------------------------------------- Start Rank ------------------------------------------------------------------------------------------>						
			<?php if($page=="rank"){ 
			$query = mysqli_query($conn,"select * from rank where point>1000 order by point DESC limit 10");
			?>
			<div class="header">
			<h4 class="title">LIVE RANK</h4>
			<p class="category">Welcome to Perfect World Nesia</p>
			</div>
			<div class="content table-responsive table-full-width">
			<p class="description text-center">
			Top 10
			</p>
			<table class="table table-hover table-striped">
			<thead>
			<th>Rank</th>
			<th>Nick</th>
			<th>Job</th>
			<th>KIll/Death</th>
			<th>Point</th>
			</thead>
			<tbody>
			<?php 
			$no=1;
			while ($data = mysqli_fetch_assoc($query)){
			$rate=round($data["kills"]/($data["kills"]+$data["deads"])*100);
			
			?>
			<tr>
			<td><?php echo $no; ?></td>
			<td><?php echo $data["rolename"];?></td>
			<td><?php echo getjob($data["roleclass"]);?></td>
			<td><?php echo $data["kills"]."/".$data["deads"]." ".$rate."%";?></td>
			<td><?php echo $data["point"];?></td>
			</tr>
			<?php $no++;} ?>
			
			</tbody>
			</table>
			<br><hr><br>
			<?php 
			if(isset($_SESSION['userid'])) {
			$userid=$_SESSION['userid'];
			$query = mysqli_query($conn,"select * from rank order by point DESC");
			?>
			
			<p class="description text-center">
			Status Rank Karakter
			</p>
			<table class="table table-hover table-striped">
			<thead>
			<th>Rank</th>
			<th>Nick</th>
			<th>Job</th>
			<th>KIll/Death</th>
			<th>Point</th>
			</thead>
			<tbody>
			<?php 
			$no=1;
			while ($data = mysqli_fetch_assoc($query)){
			if($_SESSION['userid']==$data['userid']){
			$rate=round($data["kills"]/($data["kills"]+$data["deads"])*100);
			?>
			<tr>
			<td><?php echo $no; ?></td>
			<td><?php echo $data["rolename"];?></td>
			<td><?php echo getjob($data["roleclass"]);?></td>
			<td><?php echo $data["kills"]."/".$data["deads"]." ".$rate."%";?></td>
			<td><?php echo $data["point"];?></td>
			</tr>
			<?php
			}
			$no++;
			} ?>
			
			</tbody>
			</table>
			
			<?php }	?>
			</div>
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Rank------------------------------------------------------------------------------------------>
			<!-------------------------------------------------------------------------------- Start Tukar Point ------------------------------------------------------------------------------------------>						
			<?php if($page=="tukar point"){ 
			if(!isset($_SESSION['username'])) {
			echo "<script>window.location.assign('?page=login');</script>";
			}
			?>
			<div class="header">
			<h4 class="title">Tukar Point</h4>
			<p class="category">Welcome to Perfect World Nesia</p>
			</div>
			<div class="content">				
			<?php 
			$no=1;
			$userid=$_SESSION['userid'];
			$t=time();
			$cdtime=60*60*24*7;
			$query = mysqli_query($conn,"select * from rank where userid='$userid' and point>1000");
			
			
			?>
			<p class="description text-center"><b>
			Syarat Dan Kondisi Penukaran
			</b></p>
			<ul>
			<li>Penukaran Point RPK Menyebabkan turun nya Live Rank Anda.</li>
			<li>Penukaran Point RPK hanya bisa dilakukan 1x seminggu/1 Karakter.</li>
			<li>Hanya Karakter Dengan Point RPK 1000+ yang dapat di tukar.</li>
			<li>Point RPK tersisa Min 1000 Point.</li>
			<li>10 Point RPK = 1 Gold (atau 100 Silver).</li>
			<li>jadi tukarlah dengan bijak.</li>
			</ul>
			<?php if (mysqli_num_rows($query)>0){?>
			<table class="table table-hover table-striped">
			<thead>
			<th>No.</th>
			<th>Nick</th>
			<th>Job</th>
			<th>Point Tersedia</th>
			<th>Aksi</th>
			</thead>
			<tbody>
			<?php 
			while ($data = mysqli_fetch_assoc($query)){
			?>
			<tr>
			<td><?php echo $no; ?></td>
			<td><?php echo $data["rolename"];?></td>
			<td><?php echo getjob($data["roleclass"]);?></td>
			<td><?php echo $data["point"]-1000;?></td>
			<?php 
			$last=$t-$data['pointconverttime'];
			$cd=$cdtime-$last;
			
			if($data['pointconverttime']==0 || $cd<=0){ ?>
			<td><a href="?page=convert&roleid=<?php echo $data['roleid'];?>"><button>Tukar</button></a></td>
			<?php } else{ ?>
			<td><?php echo timeleft($cd);?></td>
			<?php }?>
			</tr>
			<?php $no++; } ?>
			
			</tbody>
			</table>
			<?php
			} 
			$no=1;
			$query = mysqli_query($conn,"select * from convertpoint where userid='$userid'");
			if(mysqli_num_rows($query)>0){
			?>
			<hr>
			<p class="description text-center"><b>
			History Penukaran
			</b></p>
			
			<table class="table table-hover table-striped">
			<thead>
			<th>No.</th>
			<th>Nick</th>
			<th>Job</th>
			<th>Point</th>
			<th>Tanggal</th>
			<th>Status</th>
			</thead>
			<tbody>
			<?php 
			$query = mysqli_query($conn,"select rank.rolename as rolename, rank.roleclass as roleclass, convertpoint.point as point, usecashnow.creatime as creatime from convertpoint inner join usecashnow on usecashnow.creatime=convertpoint.creatime inner join rank on rank.roleid=convertpoint.roleid where convertpoint.userid='$userid' order by convertpoint.id Desc limit 9");
			while ($data = mysqli_fetch_assoc($query)){
			?>
			<tr>
			<td><?php echo $no; ?></td>
			<td><?php echo $data["rolename"];?></td>
			<td><?php echo getjob($data["roleclass"]);?></td>
			<td><?php echo $data["point"];?></td>
			<td><?php echo $data["creatime"];?></td>
			<td>Proses</td>
			</tr>
			<?php $no++; } 
			$query = mysqli_query($conn,"select rank.rolename as rolename, rank.roleclass as roleclass, convertpoint.point as point, usecashlog.creatime as creatime, usecashlog.fintime as fintime from convertpoint inner join usecashlog on usecashlog.creatime=convertpoint.creatime inner join rank on rank.roleid=convertpoint.roleid where convertpoint.userid='$userid' order by convertpoint.id Desc limit ".(11-$no));
			while ($data = mysqli_fetch_assoc($query)){
			?>
			<tr>
			<td><?php echo $no; ?></td>
			<td><?php echo $data["rolename"];?></td>
			<td><?php echo getjob($data["roleclass"]);?></td>
			<td><?php echo $data["point"];?></td>
			<td><?php echo $data["creatime"];?></td>
			<td><?php echo $data["fintime"];?></td>
			</tr>
			<?php $no++; } 
			
			?>
			
			</tbody>
			</table>
			<?php } ?>
			</div>
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Tukar Point ------------------------------------------------------------------------------------------>
			<!-------------------------------------------------------------------------------- Start Convert ------------------------------------------------------------------------------------------>						
			<?php 
			if($page=="convert"){ 
			if(!isset($_SESSION['username'])) {
			echo "<script>window.location.assign('?page=login');</script>";
			}
			$query = mysqli_query($conn,"select * from rank where roleid='$roleid'");
			$data = mysqli_fetch_assoc($query);
			?>
			<div class="header">
			<h4 class="title">Tukar Point</h4>
			<p class="category">Welcome to Perfect World Nesia</p>
			</div>
			<div class="content">
			<form id="form-convert" method="post">
			<input type="hidden" name="aksi" value="convert">
			<div class="row">
			<div class="col-md-4">
			<div class="form-group">
			<label>ID</label>
			<input type="text" class="form-control" disabled name="roleid" value="<?php echo $roleid;?>">
			</div>
			</div>
			<div class="col-md-8">
			<div class="form-group">
			<label>Nick</label>
			<input type="text" class="form-control" disabled name="rolename" value="<?php echo $data['rolename'];?>">
			</div>
			</div>
			</div>
			<div class="row">
			<div class="col-md-4">
			<div class="form-group">
			<label>Jumlah Point Yang di Tukar (<b>Max:<?php echo $data['point']-1000;?>)</b></label>
			<input type="number" class="form-control" min="1" max="<?php echo $data['point']-1000;?>"name="point">
			</div>
			</div>
			</div>
			
			
			<button type="submit" class="btn btn-info btn-fill" id="tukar">Tukar</button>
			<div class="clearfix"></div>
			</form>
			</div>
			
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Convert ------------------------------------------------------------------------------------------>
			<!-------------------------------------------------------------------------------- Start Donasi ------------------------------------------------------------------------------------------>						
			<?php 
			$userid=$_SESSION['userid'];
			if($page=="donasi"){ 
			if(!isset($_SESSION['username'])) {
			echo "<script>window.location.assign('?page=login');</script>";
			}
			?>
			<div class="header">
			<h4 class="title">Donasi</h4>
			<p class="category">Welcome to Perfect World Nesia</p>
			</div>
			<div class="content">
			<p class="description text-center"><b>
			Syarat Dan Kondisi Donasi
			</b></p>
			<ul>
			<li>Donasi GoPay Min Rp. 1.000,- (Selesaikan Dalam 15 Menit Setelah Klik Bayar).</li>
			<li>Donasi Transfer Bank Min Rp. 10.000,- (Selesaikan Dalam 1 hari Setelah Klik Bayar).</li>
			<li>Jangan Merefresh Web Sewaktu Menu Pembayaran Sedang Aktif.</li>
			<li>Jika Pembayaran Pending Tapi Sudah Bayar atau Transfer Refresh Web Untuk Melihat Update.</li>
			<li>Pemeriksaan Pembayaran Dilakukan otomatis.</li>
			<li>Setelah Klik Bayar Tapi Belum Transfer dan Lupa Nomor Rekening, Lakukanlah Pembelian Baru, Karena Nomor Rekening Berubah Setiap Transaksi.</li>
			<li>Jika Terjadi Kendala Hubungi GM Melalui Game Saat GM Online.</li>
			</ul>
			<form id="form-convert" method="post">
			<input type="hidden" name="aksi" value="donasi">
			<input type="hidden" name="userid" value="<?php echo $userid;?>">
			<input type="hidden" name="harga" id="harga">
			<div class="row">
			<div class="col-md-4">
			<div class="form-group">
			<label>Jumlah Gold</b></label>
			<input type="number" class="form-control" id="pointdonasi" name="point" onkeyup="hitungrupiah()" >
			</div>
			</div>
			<div class="col-md-8">
			<div class="form-group">
			<label>Jumlah Rp.</label>
			<input type="text" class="form-control" name="rp" disabled id="jumlahrp">
			</div>
			</div>
			</div>
			<button type="submit" class="btn btn-info btn-fill" id="donasi">Donasi</button>
			<div class="clearfix"></div>
			</form>
			<?php 
			$query=mysqli_query($conn,"select * from donasi where userid='$userid' order by id DESC limit 10");
			if (mysqli_num_rows($query)>0){ ?>
			<p class="description text-center"><b>
			History Donasi
			</b></p>
			
			<table class="table table-hover table-striped">
			<thead>
			<th>No.</th>
			<th>ID Donasi</th>
			<th>Point</th>
			<th>Harga</th>
			<th>Pembayaran</th>
			<th>Status</th>
			</thead>
			<tbody>
			<?php 
			$no=1;
			while ($data = mysqli_fetch_assoc($query)){
			?>
			<tr>
			<td><?php echo $no; ?></td>
			<td><?php echo $data["idorder"]; ?></td>
			<td><?php echo $data["point"]; ?></td>
			<td><?php echo "Rp. ". number_format($data["harga"]) .".-"; ?></td>
			<?php 
			if ($data['status']=="0"){
			?>
			<td><button id="pay-button" class="btn btn-info btn-fill"  onClick="gettoken('<?php echo $data["idorder"]; ?>','<?php echo  $data["harga"]; ?>')">Bayar</button></td>
			<?php 
			} else if($data['status']=="1"){
			$order_id=$data["idorder"];
			$order_status_obj = \Midtrans\Transaction::status($data['idorder']);
			$status = $order_status_obj->transaction_status;
			if($status=="pending"){
			echo "<td>Pending</td>";
			}
			else if ($status=="cancel") {
			echo "<td>Dibatalkan</td>";
			mysqli_query($conn,"update donasi set status='2' where idorder='$order_id'");
			}
			else if ($status=="settlement") { 
			echo "<td>Selesai</td>";	
			mysqli_query($conn,"update donasi set status='3' where idorder='$order_id'");
			echo "<script>window.location.assign('?aksi=finish&order_id=$order_id&transaction_status=settlement');</script>";
			}	
			}else if($data['status']=="3"){
			?>
			<td>Selesai</td>
			<?php 	
			}
			if ($data['status']<="1"){
			?>
			<td>Pending</td>
			<?php 
			} else if($data['status']=="2"){
			?>
			<td>Dibatalkan</td>
			<?php 	
			} else if($data['status']=="3"){
			$creatime = $data["creatime"];
			$cekquery = mysqli_query($conn,"select * from usecashlog where userid='$userid' and creatime='$creatime'");
			if(mysqli_num_rows($cekquery)>0){
			$cekdata = mysqli_fetch_assoc($cekquery);
			echo "<td>". $cekdata["fintime"] ."</td>";
			}
			else{ ?>
			<td>Proses(5-10Mnt)</td>
			<?php
			}
			}
			?>
			</tr>
			<?php $no++; } ?>
			
			</tbody>
			</table>
			<?php } ?>
			</div>
			<?php } ?>
			<!-------------------------------------------------------------------------------- End Donasi ------------------------------------------------------------------------------------------>
			
			</div>
			</div>
			
			<!-------------------------------------------------------------------------------- Start Status Server------------------------------------------------------------------------------------------>
			<div class="col-md-4">
			<div class="card">
			<div class="header">
			<h4 class="title">SERVER STATUS</h4>
			<!-- <p class="category">Created using Roboto Font Family</p> -->
			</div>
			<div class="content">
			<div class="typo-line">
			<?php 
			$timeout = "1";
			$fp = @fsockopen($serverip, $serverport, $errno, $errstr, $timeout);
			if (!$fp) {
			$statusserver = "<div class='text-danger'> Maintenance </div>";
			
			} 
			else {
			$statusserver= "<div class='text-success'> ONLINE </div>";
			fclose($fp);
			}
			?>
			<table width="100%">
			<tr><td width="40%">Server</td><td>:</td><td><?php echo $statusserver; ?></td></tr>
			<tr><td>Open Beta</td><td>:</td><td><?php echo $openbeta; ?></td></tr>
			<tr><td>Next Maintenance</td><td>:</td><td><?php echo $nextmaintenance; ?></td></tr>
			<tr><td>Player Registered</td><td>:</td><td><?php echo $playercount; ?> Player</td></tr>
			<tr><td>Player Online</td><td>:</td><td><?php echo $playeronline; ?> Player</td></tr>
			
			</table>
			
			</div>
			</div>
			</div>
			<div class="card">
			<div class="header">
			<h4 class="title">MAP STATUS</h4>
			<!-- <p class="category">Created using Roboto Font Family</p> -->
			</div>
			<div class="content">
			<div class="typo-line">
			<table width="100%">
			<tr><td width="60%">World</td><td>:</td><td><?php echo cekmap($proses, "gs01"); ?></td></tr>
			<tr><td>Warsong City</td><td>:</td><td><?php echo cekmap($proses, "is25"); ?></td></tr>
			<tr><td>Morai</td><td>:</td><td><?php echo cekmap($proses, "is37"); ?></td></tr>
			<tr><td>Titah Dewa</td><td>:</td><td><?php echo cekmap($proses, "is28"); ?></td></tr>
			<tr><td>Phoenix Valley</td><td>:</td><td><?php echo cekmap($proses, "is38"); ?></td></tr>
			<tr><td>Endless Universe 1</td><td>:</td><td><?php echo cekmap($proses, "is38"); ?></td></tr>
			<tr><td>Endless Universe 2</td><td>:</td><td><?php echo cekmap($proses, "is41"); ?></td></tr>
			<tr><td>Primal World</td><td>:</td><td><?php echo cekmap($proses, "is63"); ?></td></tr>
			<tr><td>Flowsilver Palace</td><td>:</td><td><?php echo cekmap($proses, "is66"); ?></td></tr>
			<tr><td>Primal World (Story Mode)</td><td>:</td><td><?php echo cekmap($proses, "is68"); ?></td></tr>
			
			</table>
			
			</div>
			</div>
			</div>
			<div class="card">
			<div class="header">
			<h4 class="title">EVENT STATUS</h4>
			<!-- <p class="category">Created using Roboto Font Family</p> -->
			</div>
			<div class="content">
			<div class="typo-line">
			<table width="100%">
			<tr><td width="40%">Live Rank RPK</td><td>:</td><td><?php echo cekproses($proses, "php rpk.php"); ?></td></tr>
			<tr><td width="40%">Live Chat</td><td>:</td><td><?php echo cekproses($proses, "node server.js"); ?></td></tr>
			
			</table>
			
			</div>
			</div>
			<div class="header">
			<h4 class="title">GM STATUS</h4>
			<!-- <p class="category">Created using Roboto Font Family</p> -->
			</div>
			<div class="content">
			<div class="typo-line">
			<table width="100%">
			<tr><td width="40%">Administrator</td><td>:</td><td>Admin<?php echo ""; ?></td></tr>
			<tr><td>Nick</td><td>:</td><td>GM</td></tr>
			<tr><td>Status</td><td>:</td><td><?php echo $statusserver; ?></td></tr>
			<tr><td>Facebook</td><td>:</td><td>Rahasia Dong.</td></tr>
			
			</table>
			
			</div>
			</div>
			</div>
			</div>
			<!-------------------------------------------------------------------------------- End Status Server ------------------------------------------------------------------------------------------>					
			</div>
			</div>
			
			<!-------------------------------------------------------------------------------- End Content ------------------------------------------------------------------------------------------>
			</div>
			<!-------------------------------------------------------------------------------- Start Footer ------------------------------------------------------------------------------------------>
			<footer class="footer">
			<div class="container-fluid">
			<nav class="pull-left">
			<ul>
			<li>
			<a href="#">
			Home
			</a>
			</li>
			
			</ul>
			</nav>
			<p class="copyright pull-right">
			&copy; <script>document.write(new Date().getFullYear())</script> <a href="http://www.creative-tim.com">Creative Tim</a>, made with love for a better web
			</p>
			</div>
			</footer>
			<!-------------------------------------------------------------------------------- End Footer ------------------------------------------------------------------------------------------>
			
			
			</div>
			
			</body>
			
			<!--   Core JS Files   -->
			<script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
			<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
			
			<!--  Charts Plugin -->
			<script src="assets/js/chartist.min.js"></script>
			
			<!--  Notifications Plugin    -->
			<script src="assets/js/bootstrap-notify.js"></script>
			
			<!--  Google Maps Plugin    -->
			<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
			
			<!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
			<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>
			
			<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
			<script src="assets/js/demo.js"></script>
			
			<script type="text/javascript"
			src="https://app.midtrans.com/snap/snap.js"
			data-client-key="Mid-client-HjsAz8CO2h-HS753"></script>
			<script>
			function klikdaftar(){
			var xhttp;
			username = document.getElementById("username").value;
			password = document.getElementById("password").value;
			password1= document.getElementById("password1").value;
			email    = document.getElementById("email").value;
			nama     = document.getElementById("nama").value;
			nohp     = document.getElementById("nohp").value;
			kode     = document.getElementById("kode").value;
			if(username==""){
			alert("Username tidak boleh kosong");
			}else{
			xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
			
			if (this.readyState == 4 && this.status == 200) {
			data = this.responseText;
			if (data=="1"){
			alert("Username Sudah Digunakan");
			}else if (data=="2"){
			alert("NO. HP Sudah Digunakan");
			}else if (password==""){
			alert("Password Masih Kosong");
			}else if (password!=password1){
			alert("Password Tidak Sama");
			}else if (email==""){
			alert("E-Mail Masih Kosong");
			}else if (nohp==""){
			alert("No. Handphone Masih Kosong");
			}else if (nama==""){
			alert("Nama Asli Masih Kosong");
			}else if (kode==""){
			alert("Gunakan Kode Invite Dari Teman atau GM0001 Untuk Mendapatkan Bonus Pendaftaran");
			}else{
			document.getElementById("form-daftar").submit();
			}
			}
			};
			xhttp.open('GET', "index.php?aksi=cekuser&username="+username+"&email="+email+"", true);
			xhttp.send();
			}			
			}
			
			function gettoken(id, harga) {
			
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
			snap.pay(this.responseText);
			}
			};
			xhttp.open("GET", "token.php?order_id="+ id + "&harga="+ harga, true);
			xhttp.send();
			}
			
			function hitungrupiah(){
			point = document.getElementById("pointdonasi").value;
			if (point == ""){
			rupiah = 0;
			document.getElementById("jumlahrp").value = rupiah.toLocaleString("id-ID");	
			document.getElementById("harga").value = "";					
			}
			else {
			rupiah = (point*1000);
			document.getElementById("jumlahrp").value = "Rp. "+rupiah.toLocaleString("id-ID")+",-";
			document.getElementById("harga").value = rupiah;
			}
			//alert(point);
			}
			
			
			
			</script>
			
			</html>
			<?php }?>
						