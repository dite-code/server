<?php
$data = file_get_contents('php://input');
$data = '{"appPackageName":"tkstudio.autoresponderforwa","messengerPackageName":"com.fmwhatsapp","query":{"sender":"+62 853-7388-7418","message":"tes","isGroup":true,"ruleId":1}}';
$arr= json_decode($data,true);
extract($arr);
extract ($query);
echo $sender;
?>