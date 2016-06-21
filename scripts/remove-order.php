<?php

if (!array_key_exists(1, $argv))
  die ("Usage: $argv[0] increment_id".PHP_EOL);
$id_to_delete = $argv[1];

include_once 'app/Mage.php';
Mage::app('admin');

$transactionContainer = Mage::getModel('core/resource_transaction');

try {
  $order = Mage::getModel('sales/order')->loadByIncrementId($id_to_delete);
  if ($order && $order->getId() > 0) {
    if ($order->canCancel())
      $order->cancel()->save();
    $orderId = $order->getId();
    if ($order->hasInvoices()) {
      $invoices = Mage::getResourceModel('sales/order_invoice_collection')->setOrderFilter($orderId)->load();
      if ($invoices) {
	foreach ($invoices as $invoice) {
	  $invoice = Mage::getModel('sales/order_invoice')->load($invoice->getId());
	  $transactionContainer->addObject($invoice);
	}
      }
    }
    if ($order->hasShipments()) {
      $shipments = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($orderId)->load();
      foreach ($shipments as $shipment){
	$shipment = Mage::getModel('sales/order_shipment')->load($shipment->getId());
	$transactionContainer->addObject($shipment);
      }
    }
    $pps = Mage::getModel('data_icrc/payment_pending')->getCollection()
      ->addFilter('order_id', $orderId);
    foreach ($pps as $pp) {
      $pp = Mage::getModel('data_icrc/payment_pending')->load($pp->getId());
      $transactionContainer->addObject($pp);
    }
    $transactionContainer->addObject($order)->delete();
    echo "order #".$id_to_delete." is removed".PHP_EOL;
  }
  else {
    echo "Cannot load order #".$id_to_delete.PHP_EOL;
  }
} catch(Exception $e){
  echo "order #".$id_to_delete." could not be remvoved: ".$e->getMessage().PHP_EOL;
}
