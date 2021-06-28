<?php
require_once(dirname(__FILE__) . '/Midtrans.php');

//Set Your server key
\Midtrans\Config::$serverKey = "Mid-server-bGt9EGInTS4dhg9fH81FLbL3";

// Uncomment for production environment
 \Midtrans\Config::$isProduction = true;

\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

extract($_GET);
$transaction = array(
    'transaction_details' => array(
        'order_id' => $order_id,
        'gross_amount' => $harga // no decimal allowed
        )
    );

$snapToken = \Midtrans\Snap::getSnapToken($transaction);
echo $snapToken;
?>