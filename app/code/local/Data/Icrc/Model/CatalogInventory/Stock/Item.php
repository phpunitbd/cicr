<?php

class Data_Icrc_Model_CatalogInventory_Stock_Item extends Mage_CatalogInventory_Model_Stock_Item
{
  public function checkQuoteItemQty($qty, $summaryQty, $origQty = 0) {
    $result = parent::checkQuoteItemQty($qty, $summaryQty, $origQty);
    if ($result->getHasError() === true && 
        ($result->getQuoteMessageIndex() == 'stock' || $result->getQuoteMessageIndex() == 'qty')) {
      $result->unsetData('quote_message');
    }
    return $result;
  }
}
