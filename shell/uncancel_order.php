<?php

require_once 'app/Mage.php';
Mage::app();

$incrementId = '100000000'; //replace this with the increment id of your actual order
if (array_key_exists(1, $_SERVER['argv']))
  $incrementId = $_SERVER['argv'][1];
$order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);

if ($order->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
  echo "Order# $incrementId (" . $order->getId() .
    ") is not canceled (" . $order->getState() . ")\n";
  exit(1);
}

$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
$order->setStatus('processing');

$order->setBaseDiscountCanceled(0);
$order->setBaseShippingCanceled(0);
$order->setBaseSubtotalCanceled(0);
$order->setBaseTaxCanceled(0);
$order->setBaseTotalCanceled(0);
$order->setDiscountCanceled(0);
$order->setShippingCanceled(0);
$order->setSubtotalCanceled(0);
$order->setTaxCanceled(0);
$order->setTotalCanceled(0);

foreach($order->getAllItems() as $item){
    $item->setQtyCanceled(0);
    $item->setTaxCanceled(0);
    $item->setHiddenTaxCanceled(0);
    $item->save();
}

$invoice = $order->prepareInvoice();
$invoice->register();
$order->addRelatedObject($invoice);
$order->sendNewOrderEmail()->setEmailSent(true);

$order->save();
