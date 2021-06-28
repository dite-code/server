<?php 
$conn = new mysqli("localhost", "root", "camelia", "pw");
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$tabel_convert = "CREATE TABLE IF NOT EXISTS pengunjung (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
ip VARCHAR(20) NOT NULL,
tanggal date NOT NULL,
jumlah INT(11) NOT NULL,
online INT(11) NOT NULL,
time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($tabel_convert) === TRUE) {
	print "";
} else {
	print "Error : " . $conn->error;
}

$ip=$_SERVER['REMOTE_ADDR'];
$tanggal=date("Ymd");
$waktu=time();
$hariini       = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM pengunjung WHERE tanggal='$tanggal' GROUP BY ip")); // Hitung jumlah pengunjung
$totalhariini  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(jumlah) as hasil FROM pengunjung WHERE tanggal='$tanggal'" )); // hitung total pengunjung
$bataswaktu       = time() - 300;
$pengunjungonline = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM pengunjung WHERE online > '$bataswaktu'")); // hitung pengunjung online
?>
<h3>Hari Ini</h3> 
Berdasarkan IP : <?php echo $hariini; ?> <br>
Total : <?php echo $totalhariini["hasil"]; ?> <br>
Pengunjung Online : <?php echo $pengunjungonline; ?> <br>
<br>

<?php
$hariini       = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM pengunjung  GROUP BY ip")); // Hitung jumlah pengunjung
$totalhariini  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(jumlah) as hasil FROM pengunjung" )); // hitung total pengunjung
$bataswaktu       = time() - 300;
$pengunjungonline = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM pengunjung WHERE online > '$bataswaktu'")); // hitung pengunjung online
?>
<h3>Keseluruhan</h3> 
Berdasarkan IP : <?php echo $hariini; ?> <br>
Total : <?php echo $totalhariini["hasil"]; ?> <br>