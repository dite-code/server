<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
//require 'vendor/autoload.php';
require 'mail/Exception.php';
require 'mail/PHPMailer.php';
require 'mail/SMTP.php';

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
$id=1024;
$bodi="
<table>
	<tr>
		<td>1</td>
		<td>2</td>
		<td>3</td>
	</tr>
	<tr>
		<td>tes</td>
		<td>kirim</td>
		<td>$id</td>
	</tr>
</table>
";

$kirim = sendmail('edyhandoko.s@gmail.com', 'edyhan', $bodi);
echo $kirim;

?>