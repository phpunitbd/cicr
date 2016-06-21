<?php

class Data_Icrc_Model_Order_Pdf_Subtotal_Donations extends Mage_Tax_Model_Sales_Pdf_Subtotal {
  public function getAmount() {
    $items = $this->getSource()->getAllItems();
    $amount = 0;
    foreach ($items as $item) {
      if (strncmp($item->getOrderItem()->getSku(), 'donation-', 9) == 0) {
        $amount += $item->getRowTotal();
      }
    }
    $pos = strpos($amount, 'Fr.');
    if($pos === false) {
        $subtotal = $amount;
    } else {
        $subtotal = str_replace('Fr.', '', $amount). ' CHF';
    }
    return $subtotal;
  }
}
