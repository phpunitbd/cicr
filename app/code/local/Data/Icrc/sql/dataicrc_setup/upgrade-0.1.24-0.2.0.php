<?php

/**
* Sets prefix :
* - required for default website
* - not visible on internal website
*/
$this->startSetup();

list($en, $fr, $int) = Mage::helper('data_icrc/update')->getStoreIds();
$coll = Mage::getModel('sales/order_status')->getCollection();

foreach ($coll as $status) {
  $labels = null;
  switch ($status->getId()) {
    case 'pending_payment': // Pending payment
      $labels = array(
        $en  => 'Pending payment',
        $fr  => 'Paiement interrompu',
        $int => 'Pending'
      );
      break;
    case 'processing': // To Validate
      $labels = array(
        $en  => 'Pending',
        $fr  => 'En cours de traitement',
        $int => 'Pending'
      );
      break;
    case 'processing_sent_to_logistic': // Sent to antalis
      $labels = array(
        $en  => 'Processing order',
        $fr  => 'En cours de traitement',
        $int => 'Processing order'
      );
      break;
    case 'processing_validated': // Validated
      $labels = array(
        $en  => 'Processing order',
        $fr  => 'En cours de traitement',
        $int => 'Processing order'
      );
      break;
    case 'complete': // Completed
      $labels = array(
        $en  => 'Order shipped',
        $fr  => 'Commande envoyée',
        $int => 'Order shipped'
      );
      break;
    case 'closed': // Closed
      $labels = array(
        $en  => 'Refunded order',
        $fr  => 'Commande remboursée',
        $int => 'Refunded order'
      );
      break;
    case 'canceled': // Canceled
      $labels = array(
        $en  => 'Canceled',
        $fr  => 'Commande annulée',
        $int => 'Canceled'
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

