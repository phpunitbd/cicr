<?php

$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
$coll = Mage::getModel('sales/order_status')->getCollection();

foreach ($coll as $status) {
  $labels = null;
  switch ($status->getId()) {
    case 'processing_partial_shipment': // Partial Shipment
      $labels = array(
        $en  => 'Order partially shipped',
        $fr  => 'Commande partiellement envoyée',
        $int => 'Order partially shipped'
      );
      break;
  }
  
  if ($labels != null) {
    $data = $status->getData();
    $data['store_labels'] = $labels;
    $status->setData($data)->save();
  }
}

$this->endSetup();

