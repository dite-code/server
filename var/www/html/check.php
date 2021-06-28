<?php
include_once(dirname(__FILE__) . '/Midtrans.php'); //include PHP library
extract($_GET);
\Midtrans\Config::$serverKey = 'Mid-server-bGt9EGInTS4dhg9fH81FLbL3';
\Midtrans\Config::$isProduction = true;   // false = sandbox
echo "0";
$order_status_obj = \Midtrans\Transaction::status($order_id);
$status = $order_status_obj->transaction_status;
if($status==""){echo "ada";}
else { echo "ada";}
//print_r($order_status_obj);
$array = json_decode(json_encode($order_status_obj), true);
//print_r($array); 
extract($array);
//echo $transaction_status;
echo "transaction_time : $transaction_time"."<br>";
echo "gross_amount : $gross_amount"."<br>";
echo "currency : $currency"."<br>";
echo "order_id : $order_id"."<br>";
echo "payment_type : $payment_type"."<br>";
echo "signature_key : $signature_key"."<br>";
echo "status_code : $status_code"."<br>";
echo "transaction_id : $transaction_id"."<br>";
echo "transaction_status : $transaction_status"."<br>";
echo "fraud_status : $fraud_status"."<br>";
echo "settlement_time : $settlement_time"."<br>";
echo "status_message : $status_message"."<br>";
echo "merchant_id : $merchant_id"."<br>";
echo "payment_option_type : $payment_option_type";

?>