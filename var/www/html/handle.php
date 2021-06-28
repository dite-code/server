<?php

require_once(dirname(__FILE__) . '/Midtrans.php');
\Midtrans\Config::$isProduction = true;
\Midtrans\Config::$serverKey = 'Mid-server-bGt9EGInTS4dhg9fH81FLbL3';
$notif = new \Midtrans\Notification();
//$notif = \Midtrans\Transaction::status("T-002");

$transaction = $notif->transaction_status;
$type = $notif->payment_type;
$order_id = $notif->order_id;
$fraud = $notif->fraud_status;

if ($transaction == 'capture') {
  // For credit card transaction, we need to check whether transaction is challenge by FDS or not
  if ($type == 'credit_card'){
    if($fraud == 'challenge'){
      // TODO set payment status in merchant's database to 'Challenge by FDS'
      // TODO merchant should decide whether this transaction is authorized or not in MAP
      echo "Transaksi order_id: " . $order_id ." Telah challenged by FDS";
      }
      else {
      // TODO set payment status in merchant's database to 'Success'
      echo "Transaksi order_id: " . $order_id ." Sukses captured Dengan " . $type;
      }
    }
  }
else if ($transaction == 'settlement'){
  // TODO set payment status in merchant's database to 'Settlement'
  echo "Transaksi order_id: " . $order_id ." Sukses Dengan menggunakan " . $type;
  }
  else if($transaction == 'pending'){
  // TODO set payment status in merchant's database to 'Pending'
  echo "Menunggu Pembayaran Dari Transaksi order_id: " . $order_id . " Dengan Pembayaran " . $type;
  }
  else if ($transaction == 'deny') {
  // TODO set payment status in merchant's database to 'Denied'
  echo "Pembayaran Dengan " . $type . " Untuk Transaksi order_id: " . $order_id . " di Tolak.";
  }
  else if ($transaction == 'expire') {
  // TODO set payment status in merchant's database to 'expire'
  echo "Pembayaran Dengan " . $type . " Untuk Transaksi order_id: " . $order_id . " Telah Kadaluarsa.";
  }
  else if ($transaction == 'cancel') {
  // TODO set payment status in merchant's database to 'Denied'
  echo "Pembayaran Dengan " . $type . " Untuk Transaksi order_id: " . $order_id . " Telah Dibatalkan.";
}
?>
